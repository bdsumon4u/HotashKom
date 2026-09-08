<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Codebyray\ReviewRateable\Models\Review as BaseReview;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends BaseReview
{
    use BelongsToTenant;

    /**
     * Get the user that owns the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
