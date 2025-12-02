<?php

namespace App\Models;

use Codebyray\ReviewRateable\Models\Review as BaseReview;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends BaseReview
{
    /**
     * Get the user that owns the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
