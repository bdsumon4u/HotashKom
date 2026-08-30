<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class UtmTrackingService
{
    public const COOKIE_NAME = 'utm_data';

    public const COOKIE_LIFETIME = 43200; // 30 days in minutes

    /**
     * Capture UTM parameters from incoming request and queue cookie if present.
     *
     * @return array<string, mixed>|null
     */
    public function captureFromRequest(Request $request): ?array
    {
        $utmParams = [
            'utm_source' => $this->sanitize($request->query('utm_source')),
            'utm_medium' => $this->sanitize($request->query('utm_medium')),
            'utm_campaign' => $this->sanitize($request->query('utm_campaign')),
            'utm_content' => $this->sanitize($request->query('utm_content')),
            'utm_term' => $this->sanitize($request->query('utm_term')),
            'fbclid' => $this->sanitize($request->query('fbclid'), 100),
            'gclid' => $this->sanitize($request->query('gclid'), 100),
            'ttclid' => $this->sanitize($request->query('ttclid'), 100),
        ];

        $hasUtmQuery = ! empty(array_filter($utmParams));

        if ($hasUtmQuery) {
            $utmData = array_filter($utmParams);
            $utmData['landing_page'] = Str::limit($request->path(), 150);
            $utmData['captured_at'] = now()->toIso8601String();

            Cookie::queue(self::COOKIE_NAME, json_encode($utmData), self::COOKIE_LIFETIME);
            session()->put(self::COOKIE_NAME, $utmData);

            return $utmData;
        }

        // If no UTM query and no existing cookie, check for organic referrer
        if (! $this->hasExistingUtmCookie($request)) {
            $referrer = $request->headers->get('referer');
            if ($referrer && ! Str::contains($referrer, $request->getHost())) {
                $organicData = $this->detectOrganicReferrer($referrer, $request);
                if ($organicData) {
                    Cookie::queue(self::COOKIE_NAME, json_encode($organicData), self::COOKIE_LIFETIME);
                    session()->put(self::COOKIE_NAME, $organicData);

                    return $organicData;
                }
            }
        }

        return null;
    }

    /**
     * Retrieve currently attributed UTM tracking data.
     *
     * @return array<string, mixed>
     */
    public function getUtmData(?Request $request = null): array
    {
        $request ??= request();

        // 1. Direct query parameters (if active on current request)
        $directParams = array_filter([
            'utm_source' => $this->sanitize($request->query('utm_source')),
            'utm_medium' => $this->sanitize($request->query('utm_medium')),
            'utm_campaign' => $this->sanitize($request->query('utm_campaign')),
            'utm_content' => $this->sanitize($request->query('utm_content')),
            'utm_term' => $this->sanitize($request->query('utm_term')),
            'fbclid' => $this->sanitize($request->query('fbclid'), 100),
            'gclid' => $this->sanitize($request->query('gclid'), 100),
            'ttclid' => $this->sanitize($request->query('ttclid'), 100),
        ]);

        if (! empty($directParams)) {
            $directParams['landing_page'] = Str::limit($request->path(), 150);
            $directParams['captured_at'] = now()->toIso8601String();

            return $directParams;
        }

        // 2. Session
        if ($sessionData = session()->get(self::COOKIE_NAME)) {
            if (is_array($sessionData)) {
                return $sessionData;
            }
        }

        // 3. Cookie
        $cookieValue = $request->cookie(self::COOKIE_NAME) ?? Cookie::get(self::COOKIE_NAME);
        if ($cookieValue) {
            $decoded = json_decode((string) $cookieValue, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function hasExistingUtmCookie(Request $request): bool
    {
        return $request->hasCookie(self::COOKIE_NAME)
            || Cookie::hasQueued(self::COOKIE_NAME)
            || session()->has(self::COOKIE_NAME);
    }

    /**
     * Detect organic search/social referrer if external.
     *
     * @return array<string, mixed>|null
     */
    private function detectOrganicReferrer(string $referrer, Request $request): ?array
    {
        $host = parse_url($referrer, PHP_URL_HOST);
        if (! $host) {
            return null;
        }

        $source = null;
        $medium = 'referral';

        if (Str::contains($host, ['facebook.com', 'fb.com', 'm.facebook.com'])) {
            $source = 'facebook';
            $medium = 'social';
        } elseif (Str::contains($host, ['google.com', 'google.com.bd'])) {
            $source = 'google';
            $medium = 'organic';
        } elseif (Str::contains($host, ['tiktok.com'])) {
            $source = 'tiktok';
            $medium = 'social';
        } elseif (Str::contains($host, ['instagram.com'])) {
            $source = 'instagram';
            $medium = 'social';
        } elseif (Str::contains($host, ['youtube.com', 'youtu.be'])) {
            $source = 'youtube';
            $medium = 'social';
        }

        if (! $source) {
            return null;
        }

        return [
            'utm_source' => $source,
            'utm_medium' => $medium,
            'utm_campaign' => 'organic_referral',
            'landing_page' => Str::limit($request->path(), 150),
            'captured_at' => now()->toIso8601String(),
        ];
    }

    private function sanitize(?string $value, int $maxLength = 80): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim(strip_tags($value));
        if ($trimmed === '') {
            return null;
        }

        return Str::limit(preg_replace('/[^\p{L}\p{N}_\-\.\s\:]/u', '', $trimmed), $maxLength);
    }
}
