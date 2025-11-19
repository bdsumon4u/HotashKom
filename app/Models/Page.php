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
        $field ??= $this->getRouteKeyName();

        // Only decode URL-encoded value when the field is 'slug'
        if ($field === 'slug') {
            $decodedValue = rawurldecode((string) $value);

            return $this->where($field, $decodedValue)->first();
        }

        // For other fields (like 'id'), use the value as-is
        return $this->where($field, $value)->first();
    }
}
