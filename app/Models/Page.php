<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content',
    ];

    #[\Override]
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Retrieve the model for route model binding.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?: 'slug';

        // Decode URL-encoded slug
        $decodedValue = rawurldecode((string) $value);

        return $this->where($field, $decodedValue)->first();
    }
}
