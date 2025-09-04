<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount',
        'max_usages',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'discount' => 'decimal:2',
    ];

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_usages && $this->used_count >= $this->max_usages) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        return min($this->discount, $amount);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Get formatted discount text
     */
    public function getDiscountTextAttribute(): string
    {
        return '৳'.number_format($this->discount, 2);
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Find coupon by code
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('code', strtoupper($code))->first();
    }
}
