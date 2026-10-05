<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentInstallment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'investment_id',
        'investor_id',
        'installment_number',
        'due_date',
        'gross_amount',
        'deduction_amount',
        'referrer_bonus_amount',
        'company_amount',
        'net_investor_amount',
        'paid_at',
        'status',
    ];

    /**
     * Investment relationship.
     */
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    /**
     * Investor relationship.
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    /**
     * Check if installment is due today or overdue.
     */
    public function isDue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'installment_number' => 'integer',
            'due_date' => 'date',
            'gross_amount' => 'float',
            'deduction_amount' => 'float',
            'referrer_bonus_amount' => 'float',
            'company_amount' => 'float',
            'net_investor_amount' => 'float',
            'paid_at' => 'datetime',
        ];
    }
}
