<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $appends = ['size_human'];

    protected $fillable = [
        'filename', 'disk', 'path', 'extension', 'mime', 'size',
    ];

    public function sizeHuman(): Attribute
    {
        $bytes = $this->size;
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $bytes /= 1024, $i++);

        return Attribute::get(fn (): string => round($bytes, 2).' '.$units[$i]);
    }

    public function src(): Attribute
    {
        return Attribute::get(fn () => asset($this->path));
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
