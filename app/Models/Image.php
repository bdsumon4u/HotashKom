<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use App\Jobs\RemoveResourceFromResellers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Image extends Model
{
    protected $appends = ['size_human'];

    protected $fillable = [
        'filename', 'disk', 'path', 'extension', 'mime', 'size',
    ];

    public static function booted(): void
    {
        static::saved(function ($image): void {
            // Dispatch job to copy image to reseller databases
            if (isOninda() && $image->wasRecentlyCreated) {
                CopyResourceToResellers::dispatch($image);
            }
        });

        static::deleting(function ($image): void {
            if (!isOninda() && $image->source_id !== null) {
                throw new \Exception('Cannot delete a resource that has been sourced.');
            }

            // Dispatch job to remove image from reseller databases
            if (isOninda()) {
                RemoveResourceFromResellers::dispatch($image->getTable(), $image->id);
            }
        });
    }

    public function sizeHuman(): Attribute
    {
        $bytes = $this->size;
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $bytes /= 1024, $i++);

        return Attribute::get(fn (): string => round($bytes, 2).' '.$units[$i]);
    }

    public function src(): Attribute
    {
        return Attribute::get(function () {
            $encodedPath = Str::of($this->path)
                ->dirname()
                ->append('/')
                ->append(rawurlencode(Str::of($this->path)->basename()));

            if ($this->source_id || !file_exists(public_path($this->path))) {
                return config('app.oninda_url') . $encodedPath;
            }

            return asset($encodedPath);
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
