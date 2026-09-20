<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Account extends Model
{
    use BelongsToTenant;
    use HasFactory;

    public const TYPE_ASSET = 'asset';

    public const TYPE_LIABILITY = 'liability';

    public const TYPE_EQUITY = 'equity';

    public const TYPE_INCOME = 'income';

    public const TYPE_EXPENSE = 'expense';

    protected $fillable = [
        'name',
        'code',
        'type',
        'opening_balance',
        'current_balance',
        'description',
        'is_system',
        'is_active',
    ];

    #[\Override]
    protected static function booted(): void
    {
        static::deleting(function (Account $account): void {
            if ($account->journalEntryItems()->exists()) {
                throw ValidationException::withMessages([
                    'account' => 'Cannot delete this account because it has active transaction records. You may deactivate it instead.',
                ]);
            }
        });
    }

    public function journalEntryItems(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function purchasePayments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class);
    }

    /**
     * Recompute and save current balance based on opening balance and journal entry items.
     */
    public function recalculateBalance(): float
    {
        $totalDebit = (float) $this->journalEntryItems()->sum('debit');
        $totalCredit = (float) $this->journalEntryItems()->sum('credit');

        // Asset & Expense accounts increase with Debit, decrease with Credit
        // Liability, Equity & Income accounts increase with Credit, decrease with Debit
        if (in_array($this->type, [self::TYPE_ASSET, self::TYPE_EXPENSE], true)) {
            $balance = (float) $this->opening_balance + ($totalDebit - $totalCredit);
        } else {
            $balance = (float) $this->opening_balance + ($totalCredit - $totalDebit);
        }

        $this->updateQuietly(['current_balance' => $balance]);

        return $balance;
    }

    public function isDebitNature(): bool
    {
        return in_array($this->type, [self::TYPE_ASSET, self::TYPE_EXPENSE], true);
    }

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
