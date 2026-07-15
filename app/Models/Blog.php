<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Blog extends Model
{
    use HasSEO;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
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
     * @return Model|null
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

    /**
     * Get dynamic SEO data fallback.
     */
    public function getDynamicSEOData(): SEOData
    {
        $title = $this->seo?->title ?: $this->title;

        $description = $this->seo?->description;
        if (! $description && $this->content) {
            $description = strip_tags($this->content);
            $description = (string) str($description)->limit(160);
        }

        $image = $this->seo?->image;
        if (! $image && $this->image) {
            $image = asset($this->image);
        }

        return new SEOData(
            title: $title,
            description: $description,
            image: $image,
        );
    }
}
