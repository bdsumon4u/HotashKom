<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentEarning extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'investment_id',
        'installment_id',
        'investor_id',
        'type',
        'amount',
        'description',
    ];

    /**
     * Investment relationship.
     */
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    /**
     * Installment relationship.
     */
    public function installment(): BelongsTo
    {
        return $this->belongsTo(InvestmentInstallment::class);
    }

    /**
     * Investor relationship.
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }
}
