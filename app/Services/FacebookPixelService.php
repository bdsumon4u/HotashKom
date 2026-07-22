<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\UserData;
use Hotash\FacebookPixel\Facades\MetaPixel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FacebookPixelService
{
    /**
     * Standard Meta Pixel events (use fbq('track', ...))
     *
     * @var array<string>
     */
    protected array $standardEvents = [
        'AddPaymentInfo', 'AddToCart', 'AddToWishlist', 'CompleteRegistration',
        'Contact', 'CustomizeProduct', 'Donate', 'FindLocation', 'InitiateCheckout',
        'Lead', 'Purchase', 'Schedule', 'Search', 'StartTrial', 'SubmitApplication',
        'Subscribe', 'ViewContent',
    ];

    /**
     * Generate a unique, deterministic event ID for deduplication.
     *
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $customData
     */
    protected function generateEventId(string $eventName, array $userData, array $customData): string
    {
        $data = [
            'event_name' => $eventName,
            'user_data' => array_intersect_key($userData, array_flip(['email', 'phone', 'client_ip_address'])),
            'custom_data' => array_intersect_key($customData, array_flip(['content_ids', 'value'])),
            'timestamp' => time(),
        ];

        return hash('sha256', json_encode($data));
    }

    /**
     * Build server-side CustomData from a flat array.
     *
     * @param  array<string, mixed>  $customData
     */
    protected function createServerCustomData(array $customData): CustomData
    {
        $obj = new CustomData;

        if (isset($customData['currency'])) {
            $obj->setCurrency($customData['currency']);
        }

        if (isset($customData['value'])) {
            $obj->setValue($customData['value']);
        }

        $obj->setContentIds($customData['content_ids'] ?? []);

        if (! empty($customData['content_ids'])) {
            $contents = [];
            foreach ($customData['content_ids'] as $id) {
                $content = new Content;
                $content->setProductId($id);
                $content->setTitle($customData['content_name'] ?? '');
                $content->setQuantity($customData['quantity'] ?? 1);
                $content->setItemPrice($customData['value'] ?? 0);
                $content->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);
                $contents[] = $content;
            }
            $obj->setContents($contents);
        }

        if (isset($customData['content_name'])) {
            $obj->setContentName($customData['content_name']);
        }

        if (isset($customData['content_type'])) {
            $obj->setContentType($customData['content_type']);
        }

        if (isset($customData['num_items'])) {
            $obj->setNumItems($customData['num_items']);
        }

        if (isset($customData['order_id'])) {
            $obj->setOrderId((string) $customData['order_id']);
        }

        return $obj;
    }

    /**
     * Get normalized user matching data from array and cookies/session.
     *
     * @param  array<string, mixed>  $userData
     * @return array<string, mixed>
     */
    public function getNormalizedUserData(array $userData = [], ?string $eventName = null): array
    {
        $email = $userData['email'] ?? $userData['em'] ?? null;
        $phone = $userData['phone'] ?? $userData['ph'] ?? null;
        $name = $userData['name'] ?? null;
        $firstName = $userData['first_name'] ?? $userData['fn'] ?? null;
        $lastName = $userData['last_name'] ?? $userData['ln'] ?? $userData['surname'] ?? null;
        $city = $userData['city'] ?? $userData['ct'] ?? null;
        $country = $userData['country'] ?? $userData['cn'] ?? null;

        // Fetch from cookies as fallback if not present
        if (empty($email)) {
            $email = Cookie::get('email');
        }
        if (empty($phone)) {
            $phone = Cookie::get('phone');
        }
        if (empty($name)) {
            $name = Cookie::get('name');
        }

        // Default country fallback to bd
        if (empty($country)) {
            $country = 'bd';
        }

        // Handle shipping area for city if empty
        if (empty($city)) {
            $shippingArea = Cookie::get('shipping');
            if ($shippingArea) {
                $city = $shippingArea;
            }
        }

        // Format phone
        if ($phone) {
            $phone = preg_replace('/[^\d]/', '', (string) $phone);
            if (strlen($phone) === 11 && str_starts_with($phone, '01')) {
                $phone = '88'.$phone;
            }
        }

        // Format email
        if ($email) {
            $email = strtolower(trim((string) $email));
        }

        // Format names
        if ($name && empty($firstName)) {
            $parts = explode(' ', trim((string) $name));
            $firstName = $parts[0];
            $lastName = $lastName ?: (count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '');
        }

        if ($firstName) {
            $firstName = strtolower(trim((string) $firstName));
        }
        if ($lastName) {
            $lastName = strtolower(trim((string) $lastName));
        }

        // Format city
        if ($city) {
            $city = str_replace(' ', '', strtolower(trim((string) $city)));
        }

        if ($country) {
            $country = strtolower(trim((string) $country));
        }

        // Filter parameters based on event name logic
        $richEvents = ['Lead', 'Purchase', 'OrderCancelled', 'OrderReturned', 'OrderDelivered'];
        $isRich = $eventName && in_array($eventName, $richEvents, true);

        $result = [
            'em' => $email,
            'ph' => $phone,
        ];

        if ($isRich) {
            $result['fn'] = $firstName;
            $result['ln'] = $lastName;
            $result['ct'] = $city;
            $result['cn'] = $country;
        }

        return array_filter($result);
    }

    /**
     * Enrich the user data using the order model from database.
     */
    protected function enrichUserDataForOrder(int|string $orderId, array &$userData): void
    {
        $order = Order::find($orderId);
        if ($order) {
            $userData['email'] = $userData['email'] ?? $order->email;
            $userData['phone'] = $userData['phone'] ?? $order->phone;
            if (empty($userData['name']) && empty($userData['first_name']) && empty($userData['fn'])) {
                $userData['name'] = $order->name;
            }
            if (empty($userData['city']) && empty($userData['ct'])) {
                $userData['city'] = $order->data['shipping_area'] ?? null;
            }
            if (empty($userData['country']) && empty($userData['cn'])) {
                $userData['country'] = 'bd';
            }
        }
    }

    /**
     * Build server-side UserData from a flat array.
     * Accepts browser signals: fbp, fbc, client_ip_address, client_user_agent, event_source_url.
     *
     * @param  array<string, mixed>  $userData
     */
    protected function createServerUserData(array $userData, ?string $eventName = null): UserData
    {
        /** @var UserData $obj */
        $obj = MetaPixel::userData();

        $normalized = $this->getNormalizedUserData($userData, $eventName);

        if (isset($normalized['em'])) {
            $obj->setEmail($normalized['em']);
        }

        if (isset($normalized['ph'])) {
            $obj->setPhone($normalized['ph']);
        }

        if (isset($normalized['fn'])) {
            $obj->setFirstName($normalized['fn']);
        }

        if (isset($normalized['ln'])) {
            $obj->setLastName($normalized['ln']);
        }

        if (isset($normalized['ct'])) {
            $obj->setCity($normalized['ct']);
        }

        if (isset($normalized['cn'])) {
            $obj->setCountryCode($normalized['cn']);
        }

        // Browser tracking signals
        if (! empty($userData['fbp'])) {
            $obj->setFbp($userData['fbp']);
        }

        if (! empty($userData['fbc'])) {
            $obj->setFbc($userData['fbc']);
        }

        if (! empty($userData['client_ip_address'])) {
            $obj->setClientIpAddress($userData['client_ip_address']);
        }

        if (! empty($userData['client_user_agent'])) {
            $obj->setClientUserAgent($userData['client_user_agent']);
        }

        return $obj;
    }

    /**
     * Get a list of unique pixel IDs from setting('pixel_ids') for browser-side pixel tracking.
     * Parses space, comma, and newline separators.
     *
     * @return array<int, string>
     */
    public function getPixelIds(): array
    {
        $rawSetting = setting('pixel_ids', '');
        if (empty($rawSetting)) {
            return [];
        }
        $ids = preg_split('/[\s\r\n,]+/', (string) $rawSetting);
        if (! $ids) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('trim', $ids))));
    }

    /**
     * Whether this is a standard Meta Pixel event.
     */
    protected function isStandardEvent(string $eventName): bool
    {
        return in_array($eventName, $this->standardEvents, true);
    }

    /**
     * Send a single pixel event to all configured pixels via the Conversions API.
     *
     * @param  array<string, mixed>  $customData
     * @param  array<string, mixed>  $userData
     */
    protected function sendToConversionsApi(string $eventName, string $eventId, array $customData, array $userData, ?string $eventSourceUrl = null): void
    {
        $serverCustomData = $this->createServerCustomData($customData);
        $serverUserData = $this->createServerUserData($userData, $eventName);

        foreach (explode('|', (string) config('meta-pixel.meta_pixel')) as $pixel) {
            $parts = explode(':', $pixel);
            if (count($parts) < 2) {
                continue;
            }
            [$id, $token, $test] = array_pad($parts, 3, null);
            MetaPixel::setPixelId($id);
            MetaPixel::setToken($token);
            if ($test) {
                MetaPixel::setTestEventCode($test);
            } else {
                MetaPixel::setTestEventCode(null);
            }

            // eventSourceUrl is passed as 5th arg to send() — not a setter method
            MetaPixel::send($eventName, $eventId, $serverCustomData, $serverUserData, $eventSourceUrl);
        }
    }

    /**
     * Core tracking method — dispatches client-side Livewire event and queues server-side CAPI call.
     * Pass $tracking array (fbp, fbc, ip, ua, event_source_url) for enriched CAPI matching.
     *
     * @param  array<string, mixed>  $customData
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $tracking  Browser signals: fbp, fbc, ip, ua, event_source_url
     */
    public function trackEvent(string $eventName, array $customData = [], array $userData = [], ?Component $component = null, array $tracking = []): void
    {
        try {
            $eventId = $tracking['event_id'] ?? $this->generateEventId($eventName, $userData, $customData);

            // Merge browser signals into userData for CAPI
            $mergedUserData = array_merge($userData, array_filter([
                'fbp' => $tracking['fbp'] ?? null,
                'fbc' => $tracking['fbc'] ?? null,
                'client_ip_address' => $tracking['ip'] ?? $tracking['client_ip_address'] ?? null,
                'client_user_agent' => $tracking['ua'] ?? $tracking['client_user_agent'] ?? null,
            ]));

            $eventSourceUrl = $tracking['event_source_url'] ?? null;

            // Ensure valid Meta Pixel format for value and currency
            if (isset($customData['value'])) {
                $customData['value'] = (float) $customData['value'];
                if (empty($customData['currency'])) {
                    $customData['currency'] = 'BDT';
                }
            }

            $normalizedUser = $this->getNormalizedUserData($userData, $eventName);

            // Client-side: dispatch Livewire browser event (fbq + dataLayer)
            if ($component instanceof Component) {
                $component->dispatch('facebookEvent', [
                    'eventName' => $eventName,
                    'customData' => $customData,
                    'eventId' => $eventId,
                    'isStandard' => $this->isStandardEvent($eventName),
                    'userData' => $normalizedUser,
                    'tracking' => array_filter([
                        'fbp' => $tracking['fbp'] ?? null,
                        'fbc' => $tracking['fbc'] ?? null,
                    ]),
                ]);
            }

            // Register event with MetaPixel facade for Blade component rendering (<x-metapixel-body />)
            if ($this->isStandardEvent($eventName)) {
                MetaPixel::track($eventName, $customData, $eventId);
                MetaPixel::flashEvent($eventName, $customData, $eventId);
            } else {
                MetaPixel::trackCustom($eventName, $customData, $eventId);
            }

            // Server-side: Conversions API (deferred)
            defer(function () use ($eventName, $eventId, $customData, $mergedUserData, $eventSourceUrl): void {
                $this->sendToConversionsApi($eventName, $eventId, $customData, $mergedUserData, $eventSourceUrl);
            });
        } catch (\Exception $e) {
            Log::error('Facebook Pixel Error: '.$e->getMessage());
        }
    }

    /**
     * Track ViewContent event.
     *
     * @param  array<string, mixed>  $tracking  Browser signals for CAPI match quality
     */
    public function trackViewContent(Product $product, ?string $eventId = null, array $tracking = []): void
    {
        try {
            $eventId = $eventId ?: $this->generateEventId('ViewContent', [], [
                'content_ids' => [$product->id],
                'value' => $product->selling_price,
            ]);

            $customData = [
                'currency' => 'BDT',
                'value' => $product->selling_price,
                'content_ids' => [$product->id],
                'content_name' => $product->name,
                'content_type' => 'product',
                'quantity' => 1,
            ];

            // Merge browser signals into userData for CAPI
            $mergedUserData = array_filter([
                'fbp' => $tracking['fbp'] ?? null,
                'fbc' => $tracking['fbc'] ?? null,
                'client_ip_address' => $tracking['ip'] ?? $tracking['client_ip_address'] ?? request()->ip(),
                'client_user_agent' => $tracking['ua'] ?? $tracking['client_user_agent'] ?? request()->userAgent(),
            ]);

            $eventSourceUrl = $tracking['event_source_url'] ?? request()->header('Referer') ?: url()->current();

            // Server-side: Conversions API (deferred)
            defer(function () use ($eventId, $customData, $mergedUserData, $eventSourceUrl): void {
                $this->sendToConversionsApi('ViewContent', $eventId, $customData, $mergedUserData, $eventSourceUrl);
            });
        } catch (\Exception $e) {
            Log::error('Facebook Pixel Error: '.$e->getMessage());
        }
    }

    /**
     * Track InitiateCheckout event when the checkout page loads (non-Livewire controller context).
     *
     * @return array<string, mixed>
     */
    public function trackInitiateCheckout(): array
    {
        try {
            $cartItems = cart()->content();
            $cartTotal = cart()->subTotal();
            $contentIds = $cartItems->pluck('id')->values()->all();

            $eventId = $this->generateEventId('InitiateCheckout', [], [
                'content_ids' => $contentIds,
                'value' => $cartTotal,
            ]);

            $customData = [
                'currency' => 'BDT',
                'value' => $cartTotal,
                'content_ids' => array_map('strval', $contentIds),
                'num_items' => $cartItems->sum('qty'),
                'content_type' => 'product',
            ];

            // Handled browser-side via checkout.blade.php script to support SPA wire:navigate transitions

            $dataLayerItems = $cartItems->map(fn ($item): array => [
                'item_id' => (string) $item->id,
                'item_name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->qty,
            ])->values()->all();

            session()->flash('datalayer_events', array_merge(
                session('datalayer_events', []),
                [[
                    'event' => 'meta_InitiateCheckout',
                    'meta_event_name' => 'InitiateCheckout',
                    'meta_event_id' => $eventId,
                    'meta_event_data' => $customData,
                    'ecommerce' => [
                        'currency' => 'BDT',
                        'value' => $cartTotal,
                        'items' => $dataLayerItems,
                    ],
                ]]
            ));

            $userData = [
                'client_ip_address' => request()->ip(),
                'client_user_agent' => request()->userAgent(),
            ];

            defer(function () use ($eventId, $customData, $userData): void {
                $this->sendToConversionsApi('InitiateCheckout', $eventId, $customData, $userData, url()->current());
            });

            return [
                'event_id' => $eventId,
                'custom_data' => $customData,
                'dataLayerItems' => $dataLayerItems,
            ];
        } catch (\Exception $e) {
            Log::error('Facebook Pixel Error: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Track AddToCart event.
     *
     * @param  array<string, mixed>  $product
     * @param  array<string, mixed>  $tracking  Browser signals for CAPI match quality
     */
    public function trackAddToCart(array $product, ?Component $component = null, array $tracking = []): void
    {
        $this->trackEvent('AddToCart', [
            'currency' => 'BDT',
            'value' => $product['price'],
            'content_ids' => [$product['id']],
            'content_name' => $product['name'],
            'quantity' => 1,
            'page_url' => $product['page_url'],
        ], [], $component, $tracking);
    }

    /**
     * Track Lead event — used at checkout when advanced_tracking is enabled.
     *
     * @param  array<string, mixed>  $order
     * @param  array<mixed>  $products
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $tracking  Browser signals stored on the order
     */
    public function trackLead(array $order, array $products, array $userData, ?Component $component = null, array $tracking = []): void
    {
        $this->enrichUserDataForOrder($order['id'] ?? 0, $userData);
        $this->trackEvent('Lead', [
            'currency' => 'BDT',
            'value' => $order['total'],
            'content_ids' => array_column($products, 'id'),
            'content_name' => 'Lead',
            'order_id' => $order['id'],
            'quantity' => array_sum(array_column($products, 'quantity')),
        ], $userData, $component, $tracking);
    }

    /**
     * Track Purchase event.
     *
     * @param  array<string, mixed>  $order
     * @param  array<mixed>  $products
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $tracking  Browser signals stored on the order
     */
    public function trackPurchase(array $order, array $products, array $userData, ?Component $component = null, array $tracking = []): void
    {
        $this->enrichUserDataForOrder($order['id'] ?? 0, $userData);
        $this->trackEvent('Purchase', [
            'currency' => 'BDT',
            'value' => $order['total'],
            'content_ids' => array_column($products, 'id'),
            'content_name' => 'Purchase',
            'order_id' => $order['id'],
            'quantity' => array_sum(array_column($products, 'quantity')),
        ], $userData, $component, $tracking);
    }

    /**
     * Track Contact event (WhatsApp / Messenger / tel click).
     *
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $tracking
     */
    public function trackContact(string $contactType, string $contactUrl, array $userData = [], ?Component $component = null, array $tracking = []): void
    {
        $this->trackEvent('Contact', [
            'content_name' => $contactType,
            'content_ids' => [],
            'page_url' => $contactUrl,
        ], $userData, $component, $tracking);
    }

    /**
     * Track custom OrderCancelled event.
     *
     * @param  array<string, mixed>  $order
     * @param  array<mixed>  $products
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $tracking
     */
    public function trackOrderCancelled(array $order, array $products, array $userData = [], ?Component $component = null, array $tracking = []): void
    {
        $this->enrichUserDataForOrder($order['id'] ?? 0, $userData);
        $this->trackEvent('OrderCancelled', [
            'currency' => 'BDT',
            'value' => $order['total'],
            'content_ids' => array_column($products, 'id'),
            'content_name' => 'OrderCancelled',
            'order_id' => $order['id'],
            'quantity' => array_sum(array_column($products, 'quantity')),
        ], $userData, $component, $tracking);
    }

    /**
     * Track custom OrderReturned event.
     *
     * @param  array<string, mixed>  $order
     * @param  array<mixed>  $products
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $tracking
     */
    public function trackOrderReturned(array $order, array $products, array $userData = [], ?Component $component = null, array $tracking = []): void
    {
        $this->enrichUserDataForOrder($order['id'] ?? 0, $userData);
        $this->trackEvent('OrderReturned', [
            'currency' => 'BDT',
            'value' => $order['total'],
            'content_ids' => array_column($products, 'id'),
            'content_name' => 'OrderReturned',
            'order_id' => $order['id'],
            'quantity' => array_sum(array_column($products, 'quantity')),
        ], $userData, $component, $tracking);
    }

    /**
     * Track custom OrderDelivered event.
     *
     * @param  array<string, mixed>  $order
     * @param  array<mixed>  $products
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $tracking
     */
    public function trackOrderDelivered(array $order, array $products, array $userData = [], ?Component $component = null, array $tracking = []): void
    {
        $this->enrichUserDataForOrder($order['id'] ?? 0, $userData);
        $this->trackEvent('OrderDelivered', [
            'currency' => 'BDT',
            'value' => $order['total'],
            'content_ids' => array_column($products, 'id'),
            'content_name' => 'OrderDelivered',
            'order_id' => $order['id'],
            'quantity' => array_sum(array_column($products, 'quantity')),
        ], $userData, $component, $tracking);
    }
}
