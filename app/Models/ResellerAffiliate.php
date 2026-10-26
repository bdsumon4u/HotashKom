<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResellerAffiliate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'referrer_id',
        'referral_code',
        'direct_leg',
        'left_total',
        'right_total',
        'direct_left_count',
        'direct_right_count',
        'verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'left_total' => 'integer',
            'right_total' => 'integer',
            'direct_left_count' => 'integer',
            'direct_right_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function directReferrals(): HasMany
    {
        return $this->hasMany(ResellerAffiliate::class, 'referrer_id', 'user_id');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('verified_at');
    }

    public function scopeLeft(Builder $query): Builder
    {
        return $query->where('direct_leg', 'left');
    }

    public function scopeRight(Builder $query): Builder
    {
        return $query->where('direct_leg', 'right');
    }
}
