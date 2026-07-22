<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FacebookPixelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactTrackingController extends Controller
{
    public function __construct(protected FacebookPixelService $facebookPixelService) {}

    /**
     * Track a Contact event server-side via Conversions API.
     *
     * Called by the frontend when a WhatsApp / Messenger / tel link is clicked.
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (! config('meta-pixel.meta_pixel') && ! setting('pixel_ids')) {
            return response()->json(['success' => true]);
        }

        $validated = $request->validate([
            'type' => 'required|string|in:whatsapp,messenger,tel',
            'url' => 'required|string|max:500',
            'fbp' => 'nullable|string|max:200',
            'fbc' => 'nullable|string|max:200',
            'event_id' => 'nullable|string|max:100',
        ]);

        $userData = [
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
        ];

        $tracking = array_filter([
            'fbp' => $validated['fbp'] ?? null,
            'fbc' => $validated['fbc'] ?? null,
            'ip' => $request->ip(),
            'ua' => $request->userAgent(),
            'event_source_url' => $validated['url'],
            'event_id' => $validated['event_id'] ?? null,
        ]);

        $this->facebookPixelService->trackContact(
            $validated['type'],
            $validated['url'],
            $userData,
            null,
            $tracking,
        );

        return response()->json(['success' => true]);
    }
}
