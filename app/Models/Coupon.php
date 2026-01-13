<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'coupon_type',
        'discount',
        'discount_type',
        'max_usages',
        'used_count',
        'expires_at',
        'is_active',
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

        return ! ($this->max_usages && $this->used_count >= $this->max_usages);
    }

    /**
     * Calculate discount amount (returns rounded integer)
     */
    public function calculateDiscount(float $amount): int
    {
        if ($this->discount_type === 'percent') {
            $calculated = ($amount * $this->discount) / 100;

            return (int) round(min($calculated, $amount));
        }

        return (int) round(min($this->discount, $amount));
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
    protected function discountText(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function (): string {
                if ($this->discount_type === 'percent') {
                    return number_format($this->discount, 2).'%';
                }

                return '৳'.number_format($this->discount, 2);
            }
        );
    }

    /**
     * Scope for active coupons
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function active($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q): void {
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

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'discount' => 'decimal:2',
        ];
    }
}
