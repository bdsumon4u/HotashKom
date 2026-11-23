<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Throwable;

class UtmTrackingService
{
    public const COOKIE_NAME = 'utm_data';

    public const BROWSER_COOKIE_NAME = 'utm_browser_data';

    public const COOKIE_LIFETIME = 43200;

    private const FIELDS = [
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'utm_id', 'utm_source_platform', 'fbclid', 'gclid', 'ttclid', 'msclkid', 'gbraid', 'wbraid',
    ];

    /** @return array<string, mixed>|null */
    public function captureFromRequest(Request $request): ?array
    {
        $data = $this->getUtmData($request);

        if ($data === []) {
            return null;
        }

        if ($request->hasSession()) {
            $request->session()->put(self::COOKIE_NAME, $data);
        }

        $encoded = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);

        if (strlen($encoded) <= 2400) {
            Cookie::queue(self::COOKIE_NAME, $encoded, self::COOKIE_LIFETIME);
            Cookie::queue(Cookie::make(self::BROWSER_COOKIE_NAME, $encoded, self::COOKIE_LIFETIME, '/', null, $request->isSecure(), false, false, 'lax'));
        }

        return $data;
    }

    /** @return array<string, mixed> */
    public function getUtmData(?Request $request = null): array
    {
        $request ??= request();

        if ($request->attributes->has('resolved_utm')) {
            return $request->attributes->get('resolved_utm');
        }

        $browser = $this->browserPayload($request);
        $storedCandidates = [
            'session' => $request->hasSession() ? $request->session()->get(self::COOKIE_NAME) : null,
            'cookie' => $request->cookie(self::COOKIE_NAME),
            'browser_cookie' => $request->cookie(self::BROWSER_COOKIE_NAME),
        ];
        uasort($storedCandidates, fn (mixed $first, mixed $second): int => (strtotime($this->validCapturedAt($this->decode($second)['captured_at'] ?? null) ?? '') ?: 0) <=>
            (strtotime($this->validCapturedAt($this->decode($first)['captured_at'] ?? null) ?? '') ?: 0));
        $candidates = [
            'query' => $request->query(),
            'body' => $request->request->all(),
            'json' => $request->isJson() ? $request->json()->all() : [],
            'submitted_utm' => $request->input('utm'),
            'submitted_utm_data' => $request->input(self::COOKIE_NAME),
            'submitted_tracking' => $request->input('tracking.utm'),
            'submitted_tracking_fields' => $request->input('tracking'),
            'browser_request' => $browser['utm'] ?? $browser,
            'page_url' => $this->urlParameters($browser['page_url'] ?? $request->input('event_source_url') ?? $request->input('tracking.event_source_url') ?? $request->input('page_url')),
            'browser_referrer_query' => $this->urlParameters($browser['referrer'] ?? null),
            'referrer_query' => $this->urlParameters($request->headers->get('referer')),
            'submitted_referrer_query' => $this->urlParameters($request->input('referrer')),
            'individual_cookies' => array_intersect_key($request->cookies->all(), array_flip(self::FIELDS)),
            ...$storedCandidates,
        ];

        $resolved = [];
        $providedMetadata = [];
        $checked = $request->attributes->get('utm_browser_payload_invalid') ? ['browser_payload' => 'invalid'] : [];
        $selected = [];

        foreach ($candidates as $source => $candidate) {
            $data = $this->decode($candidate);
            $fields = $this->fields($data);
            $checked[$source] = $candidate === null || $candidate === [] || $candidate === '' ? 'absent' : 'no_campaign_fields';

            if ($fields === []) {
                if ((is_string($candidate) && $candidate !== '' && $data === []) || array_intersect(self::FIELDS, array_keys($data)) !== []) {
                    $checked[$source] = 'invalid';
                }

                continue;
            }

            if (isset($data['captured_at']) && $this->validCapturedAt($data['captured_at']) === null) {
                $checked[$source] = 'invalid_timestamp';

                continue;
            }

            if ($this->isExpired($data)) {
                $checked[$source] = 'expired';

                continue;
            }

            $checked[$source] = 'available';
            $pagePath = $request->isMethod('GET') ? $request->path() : ($this->pagePath($browser['page_url'] ?? $request->headers->get('referer')) ?: $request->path());
            $fields['landing_page'] = $this->sanitize($data['landing_page'] ?? null, 200) ?: $pagePath;
            $fields['captured_at'] = $this->validCapturedAt($data['captured_at'] ?? null) ?? now()->toIso8601String();

            if ($resolved === []) {
                $resolved = $fields;
                $providedMetadata = [
                    'landing_page' => ! empty($data['landing_page']),
                    'captured_at' => ! empty($data['captured_at']),
                ];
                $selected[] = $source;
            } elseif ($this->sameCampaign($resolved, $fields)) {
                foreach (['landing_page', 'captured_at'] as $metadata) {
                    if (! $providedMetadata[$metadata] && ! empty($data[$metadata])) {
                        $resolved[$metadata] = $fields[$metadata];
                        $providedMetadata[$metadata] = true;
                    }
                }

                $resolved = array_replace($fields, $resolved);
                $selected[] = $source;
            } else {
                $checked[$source] = 'not_combined';
            }
        }

        if (! $request->hasSession()) {
            $checked['session'] = 'unavailable';
        }

        if (! $request->cookie(self::COOKIE_NAME) && preg_match('/(?:^|;\s*)'.self::COOKIE_NAME.'=/', (string) $request->header('Cookie'))) {
            $checked['cookie'] = 'unreadable_cookie';
        }

        if ($resolved === []) {
            $fbc = $this->sanitize($request->input('tracking.fbc') ?? $request->input('fbc') ?? $request->cookie('_fbc'), 300);

            if ($fbc && preg_match('/^fb\.\d+\.\d+\.(.+)$/', $fbc, $matches)) {
                $resolved = ['fbclid' => $matches[1]];
                $selected[] = 'facebook_click_cookie';
            }
        }

        if ($resolved === []) {
            $referrer = $browser['referrer'] ?? $request->input('referrer') ?? $request->headers->get('referer');
            $resolved = $this->referrerData($referrer, $request);

            if ($resolved !== []) {
                $resolved['landing_page'] = $this->pagePath($browser['entry_url'] ?? $browser['page_url'] ?? null) ?: $request->path();
                $selected[] = 'referrer';
            }
        }

        $resolved = $this->normalize($resolved);
        $request->attributes->set('utm_checked_sources', $checked);
        $request->attributes->set('utm_selected_sources', $selected);
        $request->attributes->set('resolved_utm', $resolved);

        return $resolved;
    }

    /**
     * @param  array<string, mixed>  $tracking
     * @return array<string, mixed>
     */
    public function trackingForOrder(Order $order, Request $request, array $tracking): array
    {
        $data = $this->getUtmData($request);
        $existing = $this->fields($this->decode($tracking['utm'] ?? $tracking));
        $sources = $request->attributes->get('utm_selected_sources', []);
        $checked = $request->attributes->get('utm_checked_sources', []);

        if ($data === [] && $existing !== []) {
            $data = $this->normalize($existing);
            $sources[] = 'order_payload';
        }

        if ($data === [] && ! empty($tracking['fbc']) && preg_match('/^fb\.\d+\.\d+\.(.+)$/', (string) $tracking['fbc'], $matches)) {
            $data = $this->normalize(['fbclid' => $matches[1]]);
            $sources[] = 'order_click_id';
        }

        if ($data === [] || empty($data['utm_campaign'])) {
            try {
                $identifiers = ['api_'.preg_replace('/\D/', '', (string) $order->phone)];

                if ($request->hasSession()) {
                    $identifiers[] = $request->session()->getId();
                }

                $carts = $order->getConnection()->table('shopping_cart')
                    ->whereIn('identifier', $identifiers)
                    ->where('instance', 'default')
                    ->where('phone', $order->phone)
                    ->where('updated_at', '>=', now()->subDays(30))
                    ->orderByDesc('updated_at')
                    ->limit(5)
                    ->pluck('tracking');

                $checked['saved_cart'] = 'absent';

                foreach ($carts as $cart) {
                    $cartTracking = $this->decode($cart);
                    $stored = $this->decode($cartTracking['utm'] ?? []);
                    $fields = $this->fields($stored);

                    if ($fields !== []) {
                        $checked['saved_cart'] = $this->isExpired($stored) ? 'expired' : 'not_combined';
                    }

                    $isSameReferrer = $sources === ['referrer'] && ($data['utm_source'] ?? null) === ($fields['utm_source'] ?? null);

                    if ($fields !== [] && ! $this->isExpired($stored) && ($data === [] || $this->sameCampaign($data, $fields) || $isSameReferrer)) {
                        $data = $this->normalize($data + $this->fields($stored) + array_intersect_key($stored, array_flip(['landing_page', 'captured_at'])));
                        $sources[] = 'saved_cart';
                        $checked['saved_cart'] = 'available';

                        break;
                    }
                }
            } catch (Throwable $exception) {
                $checked['saved_cart'] = 'lookup_failed';
                report($exception);
            }
        }

        $browser = $this->browserPayload($request);
        $hasCampaign = ! empty($data['utm_campaign']);
        $hasSource = ! empty($data['utm_source']);
        $reasons = [];

        if ($data === []) {
            $reasons[] = 'No usable UTM parameters or advertising click IDs reached checkout through the URL, request body, browser backup, session, cookies, or saved cart.';
        } elseif (! $hasCampaign) {
            $reasons[] = 'Some attribution data was captured, but no utm_campaign value was available. A click ID or referrer alone cannot identify the campaign name.';
        } elseif (! $hasSource) {
            $reasons[] = 'A campaign was supplied, but no traffic source or usable click ID was available to identify its platform.';
        }

        foreach ($checked as $source => $result) {
            if (in_array($result, ['invalid', 'invalid_timestamp', 'expired', 'lookup_failed', 'unavailable', 'unreadable_cookie'], true)) {
                $explanation = match ($result) {
                    'invalid' => 'contained unreadable or invalid attribution data',
                    'invalid_timestamp' => 'had an invalid attribution timestamp',
                    'expired' => 'contained attribution older than the 30-day retention period',
                    'lookup_failed' => 'could not be read; the server logged the failure',
                    'unavailable' => 'was not available on this request',
                    'unreadable_cookie' => 'was sent by the browser but could not be read after cookie decryption',
                };
                $reasons[] = Str::headline($source).' '.$explanation.'.';
            }
        }

        if (! $hasCampaign && in_array('not_combined', $checked, true)) {
            $reasons[] = 'Other attribution was available but could not be tied to the same campaign, so its campaign name was not assigned to this visit.';
        }

        $browserChecks = [];

        if (($browser['transport_limited'] ?? false) === true) {
            $reasons[] = 'The browser reduced optional attribution fields to keep checkout request headers within size limits.';
        }

        if ($data === [] && $browser === []) {
            $reasons[] = 'No browser capture report reached checkout. The capture script may not have loaded, or the API client may not have forwarded attribution.';
        }

        if ($data === [] && ! $this->host($browser['referrer'] ?? $request->input('referrer') ?? $request->headers->get('referer'))) {
            $reasons[] = 'No referrer was supplied. The record cannot distinguish a direct visit from a browser or redirect that omitted referral information.';
        }

        foreach (['cookies', 'local_storage', 'session_storage'] as $storage) {
            $value = $browser['storage'][$storage] ?? null;

            if (is_bool($value)) {
                $browserChecks[$storage] = $value;

                if (! $value) {
                    $reasons[] = 'The browser reported that '.str_replace('_', ' ', $storage).' was unavailable.';
                }
            }
        }

        $tracking['ip'] ??= $request->ip();
        $tracking['ua'] ??= $request->userAgent();
        $tracking['event_source_url'] ??= $this->publicUrl($browser['page_url'] ?? $request->headers->get('referer')) ?: $request->url();
        $tracking['fbp'] ??= $this->sanitize($request->input('tracking.fbp') ?? $request->input('fbp') ?? $request->cookie('_fbp'), 300);
        $tracking['fbc'] ??= $this->sanitize($request->input('tracking.fbc') ?? $request->input('fbc') ?? $request->cookie('_fbc'), 300);
        $tracking['utm'] = $data ?: null;
        $tracking['utm_diagnostics'] = [
            'version' => 1,
            'status' => $hasCampaign && $hasSource ? 'captured' : ($data === [] ? 'missing' : 'partial'),
            'reasons' => $reasons,
            'sources' => array_values(array_unique($sources)),
            'checked_sources' => $checked,
            'browser_storage' => $browserChecks,
            'browser_report_received' => $browser !== [],
            'referrer_host' => $this->host($browser['referrer'] ?? $request->input('referrer') ?? $request->headers->get('referer')),
            'request_path' => $request->path(),
            'recorded_at' => now()->toIso8601String(),
        ];

        return $tracking;
    }

    /** @return array<string, mixed> */
    private function browserPayload(Request $request): array
    {
        $header = (string) $request->header('X-Order-Attribution', '');
        $payload = str_starts_with($header, 'b64.') ? base64_decode(substr($header, 4), true) : rawurldecode($header);

        $decoded = $this->decode($request->input('attribution')) ?: $this->decode($payload);

        if ($decoded === [] && ($header !== '' || $request->input('attribution') !== null)) {
            $request->attributes->set('utm_browser_payload_invalid', true);
        }

        return $decoded;
    }

    /** @return array<string, mixed> */
    private function decode(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || strlen($value) > 12000) {
            return [];
        }

        $decoded = json_decode($value, true, 16);

        return is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function fields(array $data): array
    {
        $data = array_change_key_case($data, CASE_LOWER);
        $fields = [];

        foreach (self::FIELDS as $field) {
            $value = $this->sanitize($data[$field] ?? null);

            if ($value !== null) {
                $fields[$field] = $value;
            }
        }

        return $fields;
    }

    private function sanitize(mixed $value, int $maxLength = 200): ?string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        $value = trim((string) preg_replace('/[\x00-\x1F\x7F]/u', '', strip_tags((string) $value)));

        if ($value === '' || in_array(strtolower($value), ['undefined', 'null', '(not set)'], true) || preg_match('/^\{\{.*\}\}$/s', $value)) {
            return null;
        }

        return Str::limit($value, $maxLength, '');
    }

    /** @return array<string, mixed> */
    private function urlParameters(mixed $url): array
    {
        if (! is_string($url) || strlen($url) > 8000) {
            return [];
        }

        parse_str((string) parse_url($url, PHP_URL_QUERY), $parameters);

        return $parameters;
    }

    private function pagePath(mixed $url): ?string
    {
        return is_string($url) ? $this->sanitize(parse_url($url, PHP_URL_PATH), 200) : null;
    }

    private function publicUrl(mixed $url): ?string
    {
        $host = $this->host($url);
        $scheme = is_string($url) ? parse_url($url, PHP_URL_SCHEME) : null;

        return $host && in_array($scheme, ['https', 'http'], true) ? $scheme.'://'.$host.($this->pagePath($url) ?? '/') : null;
    }

    private function host(mixed $url): ?string
    {
        return is_string($url) ? $this->sanitize(parse_url($url, PHP_URL_HOST)) : null;
    }

    /** @param array<string, mixed> $data */
    private function isExpired(array $data): bool
    {
        $capturedAt = $this->validCapturedAt($data['captured_at'] ?? null);

        return isset($data['captured_at']) && ($capturedAt === null || strtotime($capturedAt) < now()->subDays(30)->getTimestamp());
    }

    private function validCapturedAt(mixed $value): ?string
    {
        if (! is_string($value) || strlen($value) > 50) {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp !== false && $timestamp <= now()->addMinutes(5)->getTimestamp() ? date(DATE_ATOM, $timestamp) : null;
    }

    /** @param array<string, mixed> $first
     * @param  array<string, mixed>  $second
     */
    private function sameCampaign(array $first, array $second): bool
    {
        foreach (['utm_source', 'utm_campaign', 'fbclid', 'gclid', 'ttclid', 'msclkid', 'gbraid', 'wbraid'] as $field) {
            if (isset($first[$field], $second[$field]) && $first[$field] !== $second[$field]) {
                return false;
            }
        }

        foreach (['utm_campaign', 'fbclid', 'gclid', 'ttclid', 'msclkid', 'gbraid', 'wbraid'] as $field) {
            if (isset($first[$field], $second[$field]) && $first[$field] === $second[$field]) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function normalize(array $data): array
    {
        if ($data === []) {
            return [];
        }

        if (empty($data['utm_source'])) {
            foreach (['gclid' => 'google', 'gbraid' => 'google', 'wbraid' => 'google', 'fbclid' => 'facebook', 'ttclid' => 'tiktok', 'msclkid' => 'bing'] as $field => $source) {
                if (! empty($data[$field])) {
                    $data['utm_source'] = $source;
                    $data['utm_medium'] ??= $source === 'facebook' ? 'social' : 'cpc';

                    break;
                }
            }
        }

        $data['captured_at'] ??= now()->toIso8601String();

        return $data;
    }

    /** @return array<string, mixed> */
    private function referrerData(mixed $referrer, Request $request): array
    {
        $host = $this->host($referrer);
        $ownHost = preg_replace('/^www\./i', '', $request->getHost());
        $host = $host ? preg_replace('/^www\./i', '', strtolower($host)) : null;

        if (! $host || $host === $ownHost || Str::endsWith($host, '.'.$ownHost)) {
            return [];
        }

        $source = $host;
        $medium = 'referral';

        foreach (['facebook.com' => 'facebook', 'fb.com' => 'facebook', 'google.com' => 'google', 'google.com.bd' => 'google', 'tiktok.com' => 'tiktok', 'instagram.com' => 'instagram', 'youtube.com' => 'youtube', 'youtu.be' => 'youtube', 'bing.com' => 'bing'] as $domain => $platform) {
            if ($host === $domain || Str::endsWith($host, '.'.$domain)) {
                $source = $platform;
                $medium = in_array($platform, ['google', 'bing'], true) ? 'organic' : 'social';

                break;
            }
        }

        return ['utm_source' => $source, 'utm_medium' => $medium, 'landing_page' => $request->path(), 'captured_at' => now()->toIso8601String()];
    }
}
