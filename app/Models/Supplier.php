<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'opening_balance',
        'current_due',
        'notes',
        'is_active',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchasePayments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class);
    }

    /**
     * Recalculate and update current outstanding due for this supplier.
     */
    public function recalculateDue(): float
    {
        $totalPurchases = (float) $this->purchases()->sum('total_amount');
        $totalPaidDirect = (float) $this->purchases()->sum('paid_amount');
        $totalAdditionalPayments = (float) $this->purchasePayments()->whereNull('purchase_id')->sum('amount');

        // Due = Opening Balance + Total Purchases - Total Paid on purchases - Extra direct payments
        $due = (float) $this->opening_balance + $totalPurchases - $totalPaidDirect - $totalAdditionalPayments;
        $due = max(0, $due);

        $this->updateQuietly(['current_due' => $due]);

        return $due;
    }

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'current_due' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
