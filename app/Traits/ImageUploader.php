<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

trait ImageUploader
{
    protected function uploadImage($file, $arg = [])
    {
        $arg += [
            'dir' => 'images',
            'width' => 250,
            'height' => 66,
        ];
        $path = implode('/', [
            date('d-M-Y'),
            $arg['dir'],
            time(),
        ]).'-'.preg_replace('/\s+/', '-', $file->getClientOriginalName());

        $image = Image::read($file);
        if (data_get($arg, 'resize', true)) {
            $image = $image->{$arg['method'] ?? 'resize'}($arg['width'], $arg['height']);
        }
        $disk = $arg['disk'] ?? config('filesystems.default');
        Storage::disk($disk)->put($path, (string) $image->encode());

        return Storage::disk($disk)->url($path);
    }
}
