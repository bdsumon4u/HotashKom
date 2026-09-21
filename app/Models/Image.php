<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use App\Jobs\RemoveResourceFromResellers;
use App\Scopes\ImageTenantScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Image extends Model
{
    protected $appends = ['size_human'];

    protected $fillable = [
        'tenant_id', 'filename', 'disk', 'path', 'extension', 'mime', 'size',
    ];

    #[\Override]
    public static function booted(): void
    {
        if (config('tenancy.enabled', false)) {
            static::addGlobalScope(new ImageTenantScope);

            static::creating(function (self $image): void {
                if (! $image->getAttribute('tenant_id') && ! $image->relationLoaded('tenant')) {
                    if (function_exists('tenancy') && tenancy()->initialized) {
                        $image->setAttribute('tenant_id', tenant()->getTenantKey());
                    }
                }
            });
        }

        static::saved(function ($image): void {
            // Dispatch job to copy image to reseller databases
            if (isOninda() && $image->wasRecentlyCreated) {
                dispatch(new CopyResourceToResellers($image));
            }
        });

        static::deleting(function (self $image): void {
            // Protect central platform images from being deleted by tenant stores
            if (config('tenancy.enabled', true) && function_exists('tenancy') && tenancy()->initialized) {
                if ($image->tenant_id === null || $image->tenant_id !== tenant()->getTenantKey()) {
                    throw new \Exception('Cannot delete central platform or global images.');
                }
            }

            // throw_if(isReseller() && $image->source_id !== null, \Exception::class, 'Cannot delete a resource that has been sourced.');

            // Dispatch job to remove image from reseller databases
            if (isOninda()) {
                dispatch(new RemoveResourceFromResellers($image->getTable(), $image->id));
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function isGlobal(): bool
    {
        return is_null($this->tenant_id);
    }

    protected function sizeHuman(): Attribute
    {
        $bytes = $this->size;
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $bytes /= 1024, $i++);

        return Attribute::get(fn (): string => round($bytes, 2).' '.$units[$i]);
    }

    protected function src(): Attribute
    {
        return Attribute::get(function () {
            $encodedPath = Str::of($this->path)
                ->dirname()
                ->append('/')
                ->append(rawurlencode(Str::of($this->path)->basename()));

            if ($this->source_id || ! file_exists(public_path($this->path))) {
                return config('app.oninda_url').$encodedPath;
            }

            return asset($encodedPath);
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
