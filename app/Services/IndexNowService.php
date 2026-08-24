<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class IndexNowService
{
    public function submit(string|array $urls): bool
    {
        if (! config('indexnow.enabled', true)) {
            return false;
        }

        $key = trim((string) config('indexnow.key'));
        $endpoint = (string) config(
            'indexnow.endpoint',
            'https://api.indexnow.org/indexnow'
        );

        $appUrl = rtrim((string) config('app.url'), '/');
        $host = parse_url($appUrl, PHP_URL_HOST);

        if ($key === '' || ! $host) {
            Log::warning('IndexNow skipped: missing key or valid APP_URL.');

            return false;
        }

        $urls = is_array($urls) ? $urls : [$urls];

        $urls = array_values(array_unique(array_filter(
            array_map(
                static fn ($url): string => trim((string) $url),
                $urls
            ),
            static function (string $url) use ($host): bool {
                if (! filter_var($url, FILTER_VALIDATE_URL)) {
                    return false;
                }

                return parse_url($url, PHP_URL_HOST) === $host;
            }
        )));

        if ($urls === []) {
            return false;
        }

        $allSuccessful = true;

        foreach (array_chunk($urls, 10000) as $urlList) {
            try {
                $response = Http::asJson()
                    ->connectTimeout(2)
                    ->timeout(4)
                    ->post($endpoint, [
                        'host' => $host,
                        'key' => $key,
                        'keyLocation' => $appUrl.'/'.$key.'.txt',
                        'urlList' => $urlList,
                    ]);

                if (! in_array($response->status(), [200, 202], true)) {
                    $allSuccessful = false;

                    Log::warning('IndexNow submission failed.', [
                        'status' => $response->status(),
                        'body' => mb_substr((string) $response->body(), 0, 500),
                        'urls' => $urlList,
                    ]);
                }
            } catch (Throwable $exception) {
                $allSuccessful = false;

                Log::warning('IndexNow request exception.', [
                    'message' => $exception->getMessage(),
                    'urls' => $urlList,
                ]);
            }
        }

        return $allSuccessful;
    }
}
