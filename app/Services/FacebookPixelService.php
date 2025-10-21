<?php

namespace App\Services;

use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\UserData;
use Hotash\FacebookPixel\Facades\MetaPixel;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FacebookPixelService
{
    /**
     * Generate a unique event ID
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
     * Create server-side custom data object
     */
    protected function createServerCustomData(array $customData): \FacebookAds\Object\ServerSide\CustomData
    {
        $customDataObj = new CustomData;

        if (isset($customData['currency'])) {
            $customDataObj->setCurrency($customData['currency']);
        }
        if (isset($customData['value'])) {
            $customDataObj->setValue($customData['value']);
        }
        $customDataObj->setContentIds($customData['content_ids']);
        if (isset($customData['content_ids'])) {
            $contents = [];
            foreach ($customData['content_ids'] as $id) {
                $content = new Content;
                $content->setProductId($id);
                $content->setTitle($customData['content_name']);
                $content->setQuantity($customData['quantity'] ?? 1);
                $content->setItemPrice($customData['value']);
                $content->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);
                $contents[] = $content;
            }
            $customDataObj->setContents($contents);
        }
        if (isset($customData['content_name'])) {
            $customDataObj->setContentName($customData['content_name']);
        }

        return $customDataObj;
    }

    protected function createServerUserData(array $userData)
    {
        /** @var UserData $userDataObj */
        $userDataObj = MetaPixel::userData();
        if (isset($userData['name'])) {
            $nameParts = explode(' ', trim($userData['name']));
            $lastName = '';

            $firstName = $nameParts[0];
            if (count($nameParts) === 2) {
                $lastName = $nameParts[1];
            } elseif (count($nameParts) > 2) {
                $firstName .= ' '.$nameParts[1];
                $lastName = implode(' ', array_slice($nameParts, 2));
            }
            $userDataObj->setFirstName($firstName);
            $userDataObj->setLastName($lastName);
        }

        if (isset($userData['email'])) {
            $userDataObj->setEmail($userData['email']);
        }
        if (isset($userData['phone'])) {
            $userDataObj->setPhone($userData['phone']);
        }
        if (isset($userData['external_id'])) {
            $userDataObj->setExternalId($userData['external_id']);
        }

        return $userDataObj;
    }

    /**
     * Track an event with both client and server-side tracking
     */
    public function trackEvent(string $eventName, array $customData = [], array $userData = [], ?Component $component = null): void
    {
        try {
            // Generate event ID
            $eventId = $this->generateEventId($eventName, $userData, $customData);

            // Client-side tracking
            // MetaPixel::flashEvent($eventName, $customData, $eventId);

            // If component is provided, dispatch event to browser
            if ($component instanceof \Livewire\Component) {
                info('dispatching event to browser', [
                    'eventName' => $eventName,
                    'customData' => $customData,
                    'eventId' => $eventId,
                ]);
                $component->dispatch('facebookEvent', [
                    'eventName' => $eventName,
                    'customData' => $customData,
                    'eventId' => $eventId,
                ]);
            }

            defer(function () use ($eventName, $eventId, $customData, $userData): void {
                // info('dispatching event to server', [
                //     'eventName' => $eventName,
                //     'customData' => $customData,
                //     'eventId' => $eventId,
                // ]);
                // Server-side tracking
                $serverCustomData = $this->createServerCustomData($customData);
                $serverUserData = $this->createServerUserData($userData);

                foreach (explode('|', (string) config('meta-pixel.meta_pixel')) as $pixel) {
                    [$id, $token, $test] = explode(':', $pixel);
                    MetaPixel::setPixelId($id);
                    MetaPixel::setToken($token);
                    MetaPixel::setTestEventCode($test);
                    MetaPixel::send($eventName, $eventId, $serverCustomData, $serverUserData);
                }
            });

            // Log for debugging
            // Log::info('Facebook Event Tracked', [
            //     'event_name' => $eventName,
            //     'event_id' => $eventId,
            //     'custom_data' => $customData,
            //     'user_data' => $userData,
            // ]);
        } catch (\Exception $e) {
            Log::error('Facebook Pixel Error: '.$e->getMessage());
        }
    }

    /**
     * Track AddToCart event
     */
    public function trackAddToCart(array $product, ?Component $component = null): void
    {
        $this->trackEvent('AddToCart', [
            'currency' => 'BDT',
            'value' => $product['price'],
            'content_ids' => [$product['id']],
            'content_name' => $product['name'],
            'quantity' => 1,
            'page_url' => $product['page_url'],
        ], [], $component);
    }

    /**
     * Track Purchase event
     */
    public function trackPurchase(array $order, array $products, array $userData, ?Component $component = null): void
    {
        $this->trackEvent('Purchase', [
            'currency' => 'BDT',
            'value' => $order['total'],
            'content_ids' => array_column($products, 'id'),
            'content_name' => 'Purchase',
            'transaction_id' => $order['id'],
            'quantity' => array_sum(array_column($products, 'quantity')),
            'page_url' => route('thank-you'),
        ], $userData, $component);
    }
}
