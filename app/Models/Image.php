<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use App\Jobs\RemoveResourceFromResellers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $appends = ['size_human', 'src'];

    protected $fillable = [
        'filename', 'disk', 'path', 'extension', 'mime', 'size',
    ];

    #[\Override]
    public static function booted(): void
    {
        static::saved(function ($image): void {
            // Dispatch job to copy image to reseller databases
            if (isOninda() && $image->wasRecentlyCreated) {
                dispatch(new CopyResourceToResellers($image));
            }
        });

        static::deleting(function (self $image): void {
            // throw_if(isReseller() && $image->source_id !== null, \Exception::class, 'Cannot delete a resource that has been sourced.');

            // Dispatch job to remove image from reseller databases
            if (isOninda()) {
                dispatch(new RemoveResourceFromResellers($image->getTable(), $image->id));
            }
        });
    }

    protected function sizeHuman(): Attribute
    {
        $bytes = (int) $this->size;
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $bytes /= 1024, $i++);

        return Attribute::get(fn (): string => round($bytes, 2).' '.$units[$i]);
    }

    protected function src(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->source_id && isReseller() && config('app.oninda_url')) {
                $assetUrl = config('app.oninda_asset_url');
                $path = $this->path;
                if (preg_match('~^(?:https?:)?//~i', $path) && storage_key($path, $assetUrl) === null) {
                    $path = parse_url($path, PHP_URL_PATH);
                }

                return storage_url($path, $assetUrl);
            }

            return storage_url($this->path);
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
