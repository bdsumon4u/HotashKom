<?php

namespace App\Services;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

class FacebookConversionService
{
    protected $api;
    protected $pixelIds;
    protected $accessToken;
    protected $isEnabled = false;
    protected $testEventCode;

    public function __construct()
    {
        $this->pixelIds = array_filter(explode(' ', setting('pixel_ids')));
        $this->accessToken = config('services.facebook.access_token');
        $this->testEventCode = config('services.facebook.test_event_code');

        if (!$this->accessToken) {
            Log::info('Facebook Conversion API is disabled: access token not configured');
            return;
        }

        if (empty($this->pixelIds)) {
            Log::info('Facebook Conversion API is disabled: no pixel IDs configured');
            return;
        }

        $this->isEnabled = true;

        // Initialize the Facebook API
        Api::init(null, null, $this->accessToken);
        Api::instance()->setLogger(new CurlLogger());

        // Process fbclid if present in URL
        $this->processFbclid();
    }

    /**
     * Check if server-side tracking is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Process fbclid from URL if present
     */
    protected function processFbclid()
    {
        $fbclid = Request::get('fbclid');
        if ($fbclid) {
            $fbc = 'fb.1.' . time() . '.' . $fbclid;
            Cookie::queue('_fbc', $fbc, 60 * 24 * 90); // 90 days

            // Also store the raw fbclid for potential future use
            Cookie::queue('fbclid', $fbclid, 60 * 24 * 90); // 90 days

            // Log the fbclid capture for debugging
            Log::info('Facebook Click ID captured', [
                'fbclid' => $fbclid,
                'fbc' => $fbc,
                'url' => Request::fullUrl()
            ]);
        }
    }

    /**
     * Get Facebook Browser ID (_fbp) from cookie or generate new one
     *
     * @return string
     */
    protected function getFbp()
    {
        $fbp = Cookie::get('_fbp');
        if (!$fbp) {
            $fbp = 'fb.1.' . time() . '.' . rand(1000000000, 9999999999);
            Cookie::queue('_fbp', $fbp, 60 * 24 * 90); // 90 days
        }
        return $fbp;
    }

    /**
     * Get Facebook Click ID (_fbc) from URL parameters or cookie
     *
     * @return string|null
     */
    protected function getFbc()
    {
        // First check URL parameters
        $fbclid = Request::get('fbclid');
        if ($fbclid) {
            $fbc = 'fb.1.' . time() . '.' . $fbclid;
            Cookie::queue('_fbc', $fbc, 60 * 24 * 90); // 90 days
            return $fbc;
        }

        // Then check cookie
        return Cookie::get('_fbc');
    }

    /**
     * Get the raw fbclid value if available
     *
     * @return string|null
     */
    protected function getFbclid()
    {
        return Request::get('fbclid') ?? Cookie::get('fbclid');
    }

    /**
     * Create UserData object from array
     *
     * @param array $userData
     * @return UserData
     */
    protected function createUserData(array $userData): UserData
    {
        $userDataObj = new UserData();

        if (isset($userData['email'])) {
            $userDataObj->setEmail($userData['email']);
        }
        if (isset($userData['phone'])) {
            $userDataObj->setPhone($userData['phone']);
        }
        if (isset($userData['client_ip_address'])) {
            $userDataObj->setClientIpAddress($userData['client_ip_address']);
        }
        if (isset($userData['client_user_agent'])) {
            $userDataObj->setClientUserAgent($userData['client_user_agent']);
        }
        if (isset($userData['fbp'])) {
            $userDataObj->setFbp($userData['fbp']);
        }
        if (isset($userData['fbc'])) {
            $userDataObj->setFbc($userData['fbc']);
        }

        return $userDataObj;
    }

    /**
     * Create CustomData object from array
     *
     * @param array $customData
     * @return CustomData
     */
    protected function createCustomData(array $customData): CustomData
    {
        $customDataObj = new CustomData();

        if (isset($customData['currency'])) {
            $customDataObj->setCurrency($customData['currency']);
        }
        if (isset($customData['value'])) {
            $customDataObj->setValue($customData['value']);
        }
        if (isset($customData['content_ids'])) {
            $customDataObj->setContentIds($customData['content_ids']);
        }
        if (isset($customData['content_name'])) {
            $customDataObj->setContentName($customData['content_name']);
        }
        if (isset($customData['fbclid'])) {
            $customDataObj->setCustomProperties(['fbclid' => $customData['fbclid']]);
        }

        return $customDataObj;
    }

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
        // Create a unique identifier based on event data
        $data = [
            'event_name' => $eventName,
            'user_data' => array_intersect_key($userData, array_flip(['email', 'phone', 'client_ip_address'])),
            'custom_data' => array_intersect_key($customData, array_flip(['content_ids', 'value'])),
            'timestamp' => time(),
        ];

        // Generate a deterministic hash
        return hash('sha256', json_encode($data));
    }

    /**
     * Create and send event request
     *
     * @param string $pixelId
     * @param Event $event
     * @param string $eventId
     * @return bool
     */
    protected function sendEventRequest(string $pixelId, Event $event, string $eventId): bool
    {
        try {
            $request = new EventRequest($pixelId);
            $request->setEvents([$event]);

            if ($this->testEventCode) {
                $request->setTestEventCode($this->testEventCode);
            }

            // Set event ID for deduplication
            $event->setEventId($eventId);

            $request->execute();
            return true;
        } catch (\Exception $e) {
            Log::error("Facebook Conversion API Error for Pixel {$pixelId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Track a conversion event for all configured pixels
     *
     * @param string $eventName The name of the event (e.g., 'Purchase', 'Lead', 'CompleteRegistration')
     * @param array $userData User data for matching
     * @param array $customData Custom data for the event
     * @return array Array of results for each pixel
     */
    public function trackEvent(string $eventName, array $userData, array $customData = [])
    {
        if (!$this->isEnabled) {
            return [];
        }

        $results = [];

        // Add _fbp and _fbc to user data
        $userData['fbp'] = $this->getFbp();
        $fbc = $this->getFbc();
        if ($fbc) {
            $userData['fbc'] = $fbc;
        }

        // Add fbclid to custom data if available
        $fbclid = $this->getFbclid();
        if ($fbclid) {
            $customData['fbclid'] = $fbclid;
        }

        // Generate a unique event ID for deduplication
        $eventId = $this->generateEventId($eventName, $userData, $customData);

        foreach ($this->pixelIds as $pixelId) {
            try {
                $userDataObj = $this->createUserData($userData);
                $customDataObj = $this->createCustomData($customData);

                // Create the event
                $event = new Event();
                $event->setEventName($eventName);
                $event->setEventTime(time());
                $event->setUserData($userDataObj);
                $event->setCustomData($customDataObj);
                $event->setActionSource(ActionSource::WEBSITE);

                $results[$pixelId] = $this->sendEventRequest($pixelId, $event, $eventId);
            } catch (\Exception $e) {
                Log::error("Facebook Conversion API Error for Pixel {$pixelId}: " . $e->getMessage());
                $results[$pixelId] = false;
            }
        }

        return $results;
    }

    /**
     * Track a conversion event for a specific pixel
     *
     * @param string $pixelId The specific pixel ID to track
     * @param string $eventName The name of the event
     * @param array $userData User data for matching
     * @param array $customData Custom data for the event
     * @return bool
     */
    public function trackEventForPixel(string $pixelId, string $eventName, array $userData, array $customData = [])
    {
        if (!in_array($pixelId, $this->pixelIds)) {
            Log::error("Attempted to track event for unconfigured pixel ID: {$pixelId}");
            return false;
        }

        // Add _fbp and _fbc to user data
        $userData['fbp'] = $this->getFbp();
        $fbc = $this->getFbc();
        if ($fbc) {
            $userData['fbc'] = $fbc;
        }

        // Add fbclid to custom data if available
        $fbclid = $this->getFbclid();
        if ($fbclid) {
            $customData['fbclid'] = $fbclid;
        }

        // Generate a unique event ID for deduplication
        $eventId = $this->generateEventId($eventName, $userData, $customData);

        try {
            $userDataObj = $this->createUserData($userData);
            $customDataObj = $this->createCustomData($customData);

            // Create the event
            $event = new Event();
            $event->setEventName($eventName);
            $event->setEventTime(time());
            $event->setUserData($userDataObj);
            $event->setCustomData($customDataObj);
            $event->setActionSource(ActionSource::WEBSITE);

            return $this->sendEventRequest($pixelId, $event, $eventId);
        } catch (\Exception $e) {
            Log::error("Facebook Conversion API Error for Pixel {$pixelId}: " . $e->getMessage());
            return false;
        }
    }
}
