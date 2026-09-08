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
        $data = $request->json()->all();
        if (empty($data) && ! empty($request->getContent())) {
            $decoded = json_decode($request->getContent(), true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        foreach ($data as $field => $value) {
            longCookie($field, $value);
        }

        if (isset($data['phone']) && isset($data['name'])) {
            storeOrUpdateCart($data['phone'], $data['name'], $data['address'] ?? null);
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
        if (function_exists('tenancy') && tenancy()->initialized) {
            // Tenant context: invalidate tenant-specific cache
            bumpCacheNamespace('section_products');
            bumpCacheNamespace('categories');
            bumpCacheNamespace('brands');
            \cacheMemo()->forget(tenantCachePrefix().'settings');
            \cacheMemo()->forget(tenantCachePrefix().'slides');
            \cacheMemo()->forget(tenantCachePrefix().'homesections');
            \cacheMemo()->forget(tenantCachePrefix().'categories:carousel');
            \cacheMemo()->forget(tenantCachePrefix().'brands:carousel');

            if (cacheSupportsTags()) {
                \cache()->tags(tenantCachePrefix().'section_products')->flush();
                \cache()->tags(tenantCachePrefix().'categories')->flush();
                \cache()->tags(tenantCachePrefix().'brands')->flush();
            }
        } else {
            // Central context: run optimize:clear
            Artisan::call('optimize:clear');
        }

        // Only clear response cache if it's enabled
        if (config('cache.response_cache.enabled', false)) {
            try {
                Artisan::call('responsecache:clear');
            } catch (\Exception) {
                // Silently fail if Redis is not available
                // This prevents errors when Redis is not configured
            }
        }

        return back()->with('success', 'Cache has been cleared');
    }
}
