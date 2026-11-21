<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use App\Jobs\RemoveResourceFromResellers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Model
{
    protected $appends = ['size_human'];

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
        return Attribute::get(function (): string {
            if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
                return $this->path;
            }

            $cleanPath = ltrim(Str::after($this->path, 'storage/'), '/');
            $dir = dirname($cleanPath);
            $base = rawurlencode(basename($cleanPath));
            $encodedPath = ($dir === '.' || $dir === '/' || $dir === '') ? $base : $dir.'/'.$base;

            $disk = config('filesystems.default');

            // If the active disk or image disk is not public (e.g. R2 / S3), serve directly from that disk without prepending oninda_url
            if ($disk !== 'public' || ($this->disk && $this->disk !== 'public')) {
                $targetDisk = $disk !== 'public' ? $disk : $this->disk;

                return Storage::disk($targetDisk)->url($encodedPath);
            }

            // Only prepend oninda_url when the master site uses the local public disk and this image is sourced
            if ($this->source_id && config('app.oninda_url')) {
                return rtrim(config('app.oninda_url'), '/').'/storage/'.$encodedPath;
            }

            return Storage::disk('public')->url($encodedPath);
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
