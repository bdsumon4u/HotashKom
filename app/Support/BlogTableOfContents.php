<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

final class BlogTableOfContents
{
    /**
     * Generate TOC items and add stable IDs to H2/H3 headings.
     *
     * @return array{
     *     content: string,
     *     items: array<int, array{id: string, title: string, level: int}>
     * }
     */
    public function build(?string $content): array
    {
        $originalContent = (string) $content;

        if (trim(strip_tags($originalContent)) === '') {
            return [
                'content' => $originalContent,
                'items' => [],
            ];
        }

        $items = [];
        $usedIds = [];
        $headingNumber = 0;

        $processedContent = preg_replace_callback(
            '~<h([23])\b([^>]*)>(.*?)</h\1\s*>~isu',
            function (array $match) use (&$items, &$usedIds, &$headingNumber): string {
                $level = (int) $match[1];
                $attributes = $match[2];
                $innerHtml = $match[3];

                $title = html_entity_decode(
                    strip_tags($innerHtml),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                $title = trim(
                    (string) preg_replace('/\s+/u', ' ', $title)
                );

                if ($title === '') {
                    return $match[0];
                }

                $headingNumber++;

                $hasExistingId = preg_match(
                    '~\bid\s*=\s*(["\'])(.*?)\1~isu',
                    $attributes,
                    $idMatch
                ) === 1;

                $existingId = $hasExistingId
                    ? trim(html_entity_decode(
                        $idMatch[2],
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    ))
                    : '';

                $keepExistingId = $existingId !== ''
                    && preg_match('/\s/u', $existingId) !== 1
                    && ! isset($usedIds[$existingId]);

                if ($keepExistingId) {
                    $headingId = $existingId;
                } else {
                    $slug = Str::slug($title);

                    if ($slug === '') {
                        $slug = 'section-'.substr(sha1($title), 0, 10);
                    }

                    $headingId = $this->makeUniqueId(
                        'toc-'.$slug,
                        $usedIds
                    );
                }

                $usedIds[$headingId] = true;

                $items[] = [
                    'id' => $headingId,
                    'title' => $title,
                    'level' => $level,
                ];

                if ($keepExistingId) {
                    return $match[0];
                }

                // Remove an invalid or duplicate ID before adding the new one.
                $attributes = (string) preg_replace(
                    '~\s+id\s*=\s*(["\']).*?\1~isu',
                    '',
                    $attributes,
                    1
                );

                $attributes = rtrim($attributes);

                return '<h'.$level
                    .$attributes
                    .' id="'.htmlspecialchars(
                        $headingId,
                        ENT_QUOTES,
                        'UTF-8'
                    ).'">'
                    .$innerHtml
                    .'</h'.$level.'>';
            },
            $originalContent
        );

        return [
            'content' => $processedContent ?? $originalContent,
            'items' => $items,
        ];
    }

    /**
     * @param  array<string, bool>  $usedIds
     */
    private function makeUniqueId(string $baseId, array $usedIds): string
    {
        $candidate = $baseId;
        $suffix = 2;

        while (isset($usedIds[$candidate])) {
            $candidate = $baseId.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
