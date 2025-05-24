<?php

namespace App\Services;

use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FacebookPixelService
{
    /**
     * Generate a unique event ID
     *
     * @param string $eventName
     * @param array $userData
     * @param array $customData
     * @return string
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
     *
     * @param array $customData
     * @return CustomData
     */
    protected function createServerCustomData(array $customData)
    {
        $customDataObj = new CustomData();

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
                $content = new Content();
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
        $userDataObj = new UserData($userData);
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
     *
     * @param string $eventName
     * @param array $customData
     * @param array $userData
     * @param Component|null $component
     * @return void
     */
    public function trackEvent(string $eventName, array $customData = [], array $userData = [], ?Component $component = null)
    {
        try {
            // Generate event ID
            $eventId = $this->generateEventId($eventName, $userData, $customData);

            // Client-side tracking
            // MetaPixel::track($eventName, $customData, $eventId);

            // Server-side tracking
            $serverCustomData = $this->createServerCustomData($customData);
            $serverUserData = $this->createServerUserData($userData);
            MetaPixel::send($eventName, $eventId, $serverCustomData, $serverUserData);

            // If component is provided, dispatch event to browser
            if ($component) {
                $component->dispatch('facebookEvent', [
                    'eventName' => $eventName,
                    'customData' => $customData,
                    'eventId' => $eventId
                ]);
            }

            // Log for debugging
            Log::info('Facebook Event Tracked', [
                'event_name' => $eventName,
                'event_id' => $eventId,
                'custom_data' => $customData,
                'user_data' => $userData
            ]);
        } catch (\Exception $e) {
            Log::error('Facebook Pixel Error: ' . $e->getMessage());
        }
    }

    /**
     * Track AddToCart event
     *
     * @param array $product
     * @param Component|null $component
     * @return void
     */
    public function trackAddToCart(array $product, ?Component $component = null)
    {
        $this->trackEvent('AddToCart', [
            'currency' => 'BDT',
            'value' => $product['price'],
            'content_ids' => [$product['id']],
            'content_name' => $product['name'],
            'quantity' => 1
        ], [], $component);
    }

    /**
     * Track Purchase event
     *
     * @param array $order
     * @param array $products
     * @param Component|null $component
     * @return void
     */
    public function trackPurchase(array $order, array $products, array $userData, ?Component $component = null)
    {
        $this->trackEvent('Purchase', [
            'currency' => 'BDT',
            'value' => $order['total'],
            'content_ids' => array_column($products, 'id'),
            'content_name' => 'Purchase',
            'transaction_id' => $order['id'],
            'quantity' => array_sum(array_column($products, 'quantity'))
        ], $userData, $component);
    }
}
