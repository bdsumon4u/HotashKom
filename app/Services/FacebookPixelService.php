<?php

namespace App\Services;

use Combindma\FacebookPixel\Facades\MetaPixel;
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
     * Track an event with deduplication
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

            // Track server-side
            MetaPixel::track($eventName, $customData, $eventId);

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
            'content_name' => $product['name']
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
    public function trackPurchase(array $order, array $products, ?Component $component = null)
    {
        $this->trackEvent('Purchase', [
            'currency' => 'BDT',
            'value' => $order['total'],
            'content_ids' => array_column($products, 'id'),
            'content_name' => 'Purchase',
            'transaction_id' => $order['id']
        ], [], $component);
    }
}
