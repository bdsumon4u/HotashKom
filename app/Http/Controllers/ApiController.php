<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ApiController extends Controller
{
    public function categories()
    {
        return view('categories');
    }

    public function brands()
    {
        return view('brands');
    }

    public function saveCheckoutProgress(Request $request): void
    {
        foreach ($data = $request->json()->all() as $field => $value) {
            longCookie($field, $value);
        }

        if (isset($data['phone']) && isset($data['name'])) {
            storeOrUpdateCart($data['phone'], $data['name']);
        }
    }

    public function storageLink()
    {
        return Artisan::call('storage:link');
    }

    public function scoutFlush()
    {
        return Artisan::call('scout:flush', ['model' => "App\Product"]);
    }

    public function scoutImport()
    {
        return Artisan::call('scout:import', ['model' => "App\Product"]);
    }

    public function linkOptimize(): void
    {
        Artisan::call('storage:link');
        Artisan::call('optimize:clear');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        // Only clear response cache if it's enabled
        if (config('cache.response_cache.enabled', false)) {
            try {
                Artisan::call('responsecache:clear');
            } catch (\Exception $e) {
                // Silently fail if Redis is not available
                // This prevents errors when Redis is not configured
            }
        }

        return back()->with('success', 'Cache has been cleared');
    }
}
