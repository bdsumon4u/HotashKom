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
        'faqs',
    ];

    protected function casts(): array
    {
        return [
            'faqs' => 'array',
        ];
    }

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

        if ($field === 'slug') {
            $decodedValue = rawurldecode((string) $value);

            return $this->where($field, $decodedValue)->first();
        }

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
