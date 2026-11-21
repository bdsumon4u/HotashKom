<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;

trait ImageUploader
{
    protected function uploadImage(UploadedFile $file, array $arg = []): string
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
        if (! Storage::disk($disk)->put($path, (string) $image->encode())) {
            throw new RuntimeException('Unable to store the uploaded image.');
        }

        return $path;
    }
}
