<?php

declare(strict_types=1);

namespace App\Console\Commands;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

class GenerateLlmsTxt extends Command
{
    protected $signature = 'llms:generate';

    protected $description = 'Generate llms.txt from generated sitemaps';

    public function handle(): int
    {
        $publicDirectory = public_path();
        $sitemapDirectory = $publicDirectory.'/sitemaps';
        $outputFile = $publicDirectory.'/llms.txt';
        $metadataCacheFile = storage_path('app/llms-blog-metadata.json');

        $siteBaseUrl = rtrim(config('app.url'), '/');
        $siteHost = parse_url($siteBaseUrl, PHP_URL_HOST) ?: 'localhost';
        $brandName = data_get(setting('company'), 'name') ?: (config('app.name') ?: 'Store');

        if (! is_dir($sitemapDirectory)) {
            $this->error('Sitemap directory was not found: '.$sitemapDirectory);

            return self::FAILURE;
        }

        if (! is_dir($publicDirectory) || ! is_writable($publicDirectory)) {
            $this->error('Public directory is missing or not writable: '.$publicDirectory);

            return self::FAILURE;
        }

        $metadataCache = $this->loadMetadataCache($metadataCacheFile);
        $blogEntries = $this->readSitemapFiles(
            [$sitemapDirectory.'/blogs.xml'],
            $siteHost
        );
        $blogEntries = $this->normaliseBlogUrls($blogEntries, $siteBaseUrl);
        $blogEntries = $this->enrichBlogEntries($blogEntries, $metadataCache, $siteBaseUrl);
        $this->saveMetadataCache($metadataCacheFile, $metadataCache);

        $sections = [
            'Product Categories' => $this->readSitemapFiles(
                [$sitemapDirectory.'/categories.xml'],
                $siteHost
            ),
            'Shopping Guides and Blog Articles' => $blogEntries,
            'Products' => $this->readSitemapFiles(
                glob($sitemapDirectory.'/products-*.xml') ?: [],
                $siteHost
            ),
            'Information Pages' => $this->readSitemapFiles(
                [$sitemapDirectory.'/pages.xml', $sitemapDirectory.'/static.xml'],
                $siteHost
            ),
            'Brands' => $this->readSitemapFiles(
                [$sitemapDirectory.'/brands.xml'],
                $siteHost
            ),
        ];

        $generatedAt = new DateTimeImmutable('now', new DateTimeZone('Asia/Dhaka'));
        $lines = [
            '# '.$brandName,
            '',
            '> '.$brandName.' is an online shopping platform offering practical products with nationwide delivery.',
            '',
            'This file helps AI systems discover '.$brandName.'\'s public product, category, information and educational content. Product availability, price and specifications should always be verified on the linked live page.',
            '',
            '## Main Links',
            '',
            '- ['.$brandName.' Home]('.$siteBaseUrl.'/): Main website.',
            '- [Shop All Products]('.$siteBaseUrl.'/shop): Browse the current product catalogue.',
            '- [XML Sitemap]('.$siteBaseUrl.'/sitemap.xml): Machine-readable index of public pages.',
        ];

        foreach ($sections as $heading => $entries) {
            if ($entries === []) {
                continue;
            }

            $lines[] = '';
            $lines[] = '## '.$heading;
            $lines[] = '';

            foreach ($entries as $entry) {
                $description = $heading === 'Shopping Guides and Blog Articles'
                    && ($entry['description'] ?? '') !== ''
                        ? (string) $entry['description']
                        : $this->sectionDescription($heading, $brandName);
                $updated = $entry['lastmod'] !== ''
                    ? ' Last updated '.$this->normaliseDate($entry['lastmod']).'.'
                    : '';

                $lines[] = '- ['.$this->markdownEscape($entry['title']).']('.$entry['url'].'): '
                    .$this->markdownEscape($description).$updated;
            }
        }

        $lines[] = '';
        $lines[] = '## File Information';
        $lines[] = '';
        $lines[] = '- Generated automatically from XML sitemaps.';
        $lines[] = '- Generated at: '.$generatedAt->format(DateTimeInterface::ATOM).'.';
        $lines[] = '';

        $content = implode("\n", $lines);
        $temporaryFile = tempnam($publicDirectory, '.llms-');

        if ($temporaryFile === false) {
            $this->error('Could not create a temporary llms.txt file.');

            return self::FAILURE;
        }

        $bytesWritten = file_put_contents($temporaryFile, $content, LOCK_EX);

        if ($bytesWritten === false) {
            @unlink($temporaryFile);
            $this->error('Could not write the temporary llms.txt file.');

            return self::FAILURE;
        }

        @chmod($temporaryFile, 0644);

        if (! rename($temporaryFile, $outputFile)) {
            @unlink($temporaryFile);
            $this->error('Could not publish llms.txt to: '.$outputFile);

            return self::FAILURE;
        }

        $totalUrls = array_sum(array_map('count', $sections));
        $enrichedBlogs = count(array_filter(
            $blogEntries,
            static fn (array $entry): bool => ($entry['description'] ?? '') !== ''
        ));

        $this->info('llms.txt generated: '.$outputFile);
        $this->info('URLs included: '.$totalUrls);
        $this->info('Blog metadata enriched: '.$enrichedBlogs.' of '.count($blogEntries));

        return self::SUCCESS;
    }

    /**
     * @param  array<int, string>  $files
     * @return array<int, array{url: string, lastmod: string, title: string, description: string}>
     */
    private function readSitemapFiles(array $files, string $allowedHost): array
    {
        $entriesByUrl = [];

        foreach ($files as $file) {
            if (! is_file($file) || ! is_readable($file)) {
                continue;
            }

            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($file, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);

            if ($xml === false) {
                libxml_clear_errors();

                continue;
            }

            $xml->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $urlNodes = $xml->xpath('//sm:url');

            if ($urlNodes === false) {
                continue;
            }

            foreach ($urlNodes as $urlNode) {
                $urlNode->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
                $locationNodes = $urlNode->xpath('./sm:loc');
                $lastModifiedNodes = $urlNode->xpath('./sm:lastmod');
                $url = isset($locationNodes[0]) ? trim((string) $locationNodes[0]) : '';
                $lastModified = isset($lastModifiedNodes[0]) ? trim((string) $lastModifiedNodes[0]) : '';

                if (! $this->isAllowedPublicUrl($url, $allowedHost)) {
                    continue;
                }

                $existing = $entriesByUrl[$url] ?? null;
                if ($existing !== null && strcmp($existing['lastmod'], $lastModified) >= 0) {
                    continue;
                }

                $entriesByUrl[$url] = [
                    'url' => $url,
                    'lastmod' => $lastModified,
                    'title' => $this->titleFromUrl($url),
                    'description' => '',
                ];
            }
        }

        $entries = array_values($entriesByUrl);
        usort($entries, static function (array $first, array $second): int {
            $dateComparison = strcmp($second['lastmod'], $first['lastmod']);

            return $dateComparison !== 0
                ? $dateComparison
                : strcasecmp($first['title'], $second['title']);
        });

        return $entries;
    }

    /**
     * @param  array<int, array{url: string, lastmod: string, title: string, description: string}>  $entries
     * @return array<int, array{url: string, lastmod: string, title: string, description: string}>
     */
    private function normaliseBlogUrls(array $entries, string $siteBaseUrl): array
    {
        foreach ($entries as $index => $entry) {
            $path = trim((string) parse_url($entry['url'], PHP_URL_PATH), '/');

            if ($path === '' || str_starts_with($path, 'blogs/')) {
                continue;
            }

            $segments = explode('/', $path);
            $slug = trim((string) end($segments));
            if ($slug === '') {
                continue;
            }

            $entries[$index]['url'] = rtrim($siteBaseUrl, '/').'/blogs/'.$slug;
        }

        return $entries;
    }

    /**
     * @param  array<int, array{url: string, lastmod: string, title: string, description: string}>  $entries
     * @param  array<string, array{lastmod: string, title: string, description: string}>  $cache
     * @return array<int, array{url: string, lastmod: string, title: string, description: string}>
     */
    private function enrichBlogEntries(array $entries, array &$cache, string $siteBaseUrl): array
    {
        foreach ($entries as $index => $entry) {
            $cached = $cache[$entry['url']] ?? null;

            if (is_array($cached)
                && ($cached['lastmod'] ?? '') === $entry['lastmod']
                && ($cached['title'] ?? '') !== ''
            ) {
                $entries[$index]['title'] = (string) $cached['title'];
                $entries[$index]['description'] = (string) ($cached['description'] ?? '');

                continue;
            }

            $metadata = $this->fetchPageMetadata($entry['url'], $siteBaseUrl);
            if ($metadata === null) {
                continue;
            }

            $title = $metadata['title'] !== '' ? $metadata['title'] : $entry['title'];
            $description = $metadata['description'];

            $entries[$index]['title'] = $title;
            $entries[$index]['description'] = $description;
            $cache[$entry['url']] = [
                'lastmod' => $entry['lastmod'],
                'title' => $title,
                'description' => $description,
            ];
        }

        $currentUrls = array_fill_keys(array_column($entries, 'url'), true);
        foreach (array_keys($cache) as $cachedUrl) {
            if (! isset($currentUrls[$cachedUrl])) {
                unset($cache[$cachedUrl]);
            }
        }

        return $entries;
    }

    /**
     * @return array{title: string, description: string}|null
     */
    private function fetchPageMetadata(string $url, string $siteBaseUrl): ?array
    {
        $html = $this->fetchHtml($url, $siteBaseUrl);
        if ($html === null) {
            return null;
        }

        $title = '';
        $description = '';

        if (class_exists(DOMDocument::class)) {
            $previousSetting = libxml_use_internal_errors(true);
            $document = new DOMDocument;
            $loaded = $document->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
            libxml_use_internal_errors($previousSetting);

            if ($loaded) {
                $xpath = new DOMXPath($document);
                $title = $this->firstXpathValue($xpath, [
                    "//meta[translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='og:title']/@content",
                    '//title',
                ]);
                $description = $this->firstXpathValue($xpath, [
                    "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='description']/@content",
                    "//meta[translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='og:description']/@content",
                ]);
            }
        }

        if ($title === '') {
            $title = $this->firstRegexMetadata($html, [
                '/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/iu',
                '/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:title["\'][^>]*>/iu',
                '/<title[^>]*>(.*?)<\/title>/isu',
            ]);
        }

        if ($description === '') {
            $description = $this->firstRegexMetadata($html, [
                '/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']*)["\'][^>]*>/iu',
                '/<meta[^>]+content=["\']([^"\']*)["\'][^>]+name=["\']description["\'][^>]*>/iu',
                '/<meta[^>]+property=["\']og:description["\'][^>]+content=["\']([^"\']*)["\'][^>]*>/iu',
            ]);
        }

        $title = $this->cleanMetadataText($title, 180);
        $description = $this->cleanMetadataText($description, 320);

        return ($title === '' && $description === '')
            ? null
            : ['title' => $title, 'description' => $description];
    }

    private function fetchHtml(string $url, string $siteBaseUrl): ?string
    {
        if (function_exists('curl_init')) {
            $handle = curl_init($url);
            if ($handle === false) {
                return null;
            }

            curl_setopt_array($handle, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_TIMEOUT => 8,
                CURLOPT_USERAGENT => 'LLMSTxt-Generator/1.1 (+'.$siteBaseUrl.'/llms.txt)',
                CURLOPT_HTTPHEADER => ['Accept: text/html,application/xhtml+xml'],
            ]);

            $html = curl_exec($handle);
            $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
            curl_close($handle);

            return is_string($html) && $status >= 200 && $status < 300
                ? $html
                : null;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 8,
                'follow_location' => 0,
                'header' => "User-Agent: LLMSTxt-Generator/1.1\r\nAccept: text/html,application/xhtml+xml\r\n",
            ],
        ]);

        $html = @file_get_contents($url, false, $context);

        return is_string($html) ? $html : null;
    }

    /**
     * @param  array<int, string>  $queries
     */
    private function firstXpathValue(DOMXPath $xpath, array $queries): string
    {
        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes !== false && $nodes->length > 0) {
                $value = trim((string) $nodes->item(0)?->nodeValue);
                if ($value !== '') {
                    return $value;
                }
            }
        }

        return '';
    }

    /**
     * @param  array<int, string>  $patterns
     */
    private function firstRegexMetadata(string $html, array $patterns): string
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches) === 1 && isset($matches[1])) {
                return (string) $matches[1];
            }
        }

        return '';
    }

    private function cleanMetadataText(string $value, int $maximumLength): string
    {
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);

        if (mb_strlen($value, 'UTF-8') <= $maximumLength) {
            return $value;
        }

        $shortened = mb_substr($value, 0, $maximumLength - 1, 'UTF-8');
        $shortened = preg_replace('/\s+\S*$/u', '', $shortened) ?? $shortened;

        return rtrim($shortened, " \t\n\r\0\x0B,.;:-").'…';
    }

    /**
     * @return array<string, array{lastmod: string, title: string, description: string}>
     */
    private function loadMetadataCache(string $cacheFile): array
    {
        if (! is_file($cacheFile) || ! is_readable($cacheFile)) {
            return [];
        }

        $json = file_get_contents($cacheFile);
        if (! is_string($json)) {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param  array<string, array{lastmod: string, title: string, description: string}>  $cache
     */
    private function saveMetadataCache(string $cacheFile, array $cache): void
    {
        $directory = dirname($cacheFile);
        if (! is_dir($directory) || ! is_writable($directory)) {
            return;
        }

        $json = json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (! is_string($json)) {
            return;
        }

        $temporaryFile = tempnam($directory, '.llms-cache-');
        if ($temporaryFile === false || file_put_contents($temporaryFile, $json, LOCK_EX) === false) {
            if (is_string($temporaryFile)) {
                @unlink($temporaryFile);
            }

            return;
        }

        @chmod($temporaryFile, 0640);
        if (! rename($temporaryFile, $cacheFile)) {
            @unlink($temporaryFile);
        }
    }

    private function isAllowedPublicUrl(string $url, string $allowedHost): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parts = parse_url($url);
        if (! is_array($parts)) {
            return false;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $allowedHost = strtolower($allowedHost);

        return $host === $allowedHost
            || $host === Str::after($allowedHost, 'www.')
            || $host === 'www.'.$allowedHost;
    }

    private function titleFromUrl(string $url): string
    {
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        $segments = $path === '' ? [] : explode('/', $path);
        $slug = $segments === [] ? 'Home' : (string) end($segments);
        $slug = rawurldecode($slug);
        $title = preg_replace('/[-_]+/u', ' ', $slug) ?? $slug;
        $title = preg_replace('/\s+/u', ' ', trim($title)) ?? trim($title);

        if ($title === '') {
            return 'Page';
        }

        return mb_convert_case($title, MB_CASE_TITLE, 'UTF-8');
    }

    private function normaliseDate(string $date): string
    {
        try {
            return (new DateTimeImmutable($date))->format('Y-m-d');
        } catch (Throwable) {
            return $date;
        }
    }

    private function markdownEscape(string $value): string
    {
        return str_replace(['\\', '[', ']'], ['\\\\', '\\[', '\\]'], $value);
    }

    private function sectionDescription(string $heading, string $brandName): string
    {
        return match ($heading) {
            'Product Categories' => 'Browse products in this '.$brandName.' category.',
            'Products' => 'Current '.$brandName.' product page.',
            'Shopping Guides and Blog Articles' => $brandName.' shopping guide or educational article.',
            'Information Pages' => 'Official '.$brandName.' information page.',
            'Brands' => 'Browse products associated with this brand.',
            default => 'Official '.$brandName.' page.',
        };
    }
}
