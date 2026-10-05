<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'investor_id',
        'invested_amount',
        'multiplier',
        'total_return_amount',
        'duration_months',
        'monthly_installment_amount',
        'initial_deduction_rate',
        'initial_referrer_bonus_rate',
        'monthly_deduction_rate',
        'monthly_referrer_bonus_rate',
        'initial_deduction_amount',
        'initial_referrer_bonus_amount',
        'initial_company_amount',
        'start_date',
        'installments_paid_count',
        'total_paid_amount',
        'status',
        'notes',
    ];

    /**
     * Investor relationship.
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    /**
     * Installments relationship.
     */
    public function installments(): HasMany
    {
        return $this->hasMany(InvestmentInstallment::class);
    }

    /**
     * Company earnings logged for this investment.
     */
    public function earnings(): HasMany
    {
        return $this->hasMany(InvestmentEarning::class);
    }

    /**
     * Next pending installment.
     */
    public function nextInstallment(): ?InvestmentInstallment
    {
        return $this->installments()
            ->where('status', 'pending')
            ->orderBy('installment_number')
            ->first();
    }

    /**
     * Calculate progress percentage (0 - 100).
     */
    public function getProgressPercentage(): float
    {
        if ($this->duration_months <= 0) {
            return 0.0;
        }

        return round(($this->installments_paid_count / $this->duration_months) * 100, 1);
    }

    /**
     * Get remaining return amount.
     */
    public function getRemainingReturnAmount(): float
    {
        return (float) max(0, $this->total_return_amount - $this->total_paid_amount);
    }

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'invested_amount' => 'float',
            'multiplier' => 'float',
            'total_return_amount' => 'float',
            'duration_months' => 'integer',
            'monthly_installment_amount' => 'float',
            'initial_deduction_rate' => 'float',
            'initial_referrer_bonus_rate' => 'float',
            'monthly_deduction_rate' => 'float',
            'monthly_referrer_bonus_rate' => 'float',
            'initial_deduction_amount' => 'float',
            'initial_referrer_bonus_amount' => 'float',
            'initial_company_amount' => 'float',
            'installments_paid_count' => 'integer',
            'total_paid_amount' => 'float',
            'start_date' => 'date',
        ];
    }
}
