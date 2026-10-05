<?php

declare(strict_types=1);

namespace App\Models;

use Bavix\Wallet\Interfaces\Confirmable;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\CanConfirm;
use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Investor extends Authenticatable implements Confirmable, Wallet
{
    use CanConfirm;
    use HasWallet;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone_number',
        'bkash_number',
        'bank_details',
        'address',
        'password',
        'referral_code',
        'referred_by_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    #[\Override]
    public static function booted(): void
    {
        static::creating(function (Investor $investor): void {
            if (empty($investor->referral_code)) {
                $investor->referral_code = self::generateReferralCode();
            }
        });
    }

    /**
     * Generate unique referral code.
     */
    public static function generateReferralCode(): string
    {
        do {
            $code = 'INV'.strtoupper(Str::random(6));
        } while (self::query()->where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Get the full referral link.
     */
    public function getReferralLink(): string
    {
        return url('/investor/login?ref='.$this->referral_code);
    }

    /**
     * Investments relationship.
     */
    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    /**
     * Installments relationship.
     */
    public function installments(): HasMany
    {
        return $this->hasMany(InvestmentInstallment::class);
    }

    /**
     * The investor who referred this investor.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Investor::class, 'referred_by_id');
    }

    /**
     * Direct investors referred by this investor.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Investor::class, 'referred_by_id');
    }

    /**
     * Get the total amount of pending withdrawals for this investor.
     */
    public function getPendingWithdrawalAmount(): float
    {
        return abs((float) $this->wallet->transactions()
            ->where('type', 'withdraw')
            ->where('confirmed', false)
            ->sum('amount'));
    }

    /**
     * Get the available balance (wallet balance minus pending withdrawals).
     */
    public function getAvailableBalance(): float
    {
        return (float) ($this->balance - $this->getPendingWithdrawalAmount());
    }

    /**
     * Check if the investor can withdraw the specified amount.
     */
    public function canWithdraw(int|string|float $amount, bool $allowZero = false): bool
    {
        $availableBalance = $this->getAvailableBalance();

        if ($allowZero && $availableBalance == 0) {
            return true;
        }

        return (float) $amount <= $availableBalance;
    }

    /**
     * Total amount invested by this investor across all investments.
     */
    public function getTotalInvestedAmount(): float
    {
        return (float) $this->investments()->where('status', '!=', 'cancelled')->sum('invested_amount');
    }

    /**
     * Total expected return amount across all investments.
     */
    public function getTotalExpectedReturnAmount(): float
    {
        return (float) $this->investments()->where('status', '!=', 'cancelled')->sum('total_return_amount');
    }

    /**
     * Total return amount received so far.
     */
    public function getTotalReceivedReturnAmount(): float
    {
        return (float) $this->installments()->where('status', 'processed')->sum('net_investor_amount');
    }

    /**
     * Total referral bonuses earned across all time.
     */
    public function getTotalReferralBonusEarned(): float
    {
        return (float) $this->wallet->transactions()
            ->where('type', 'deposit')
            ->where('confirmed', true)
            ->whereJsonContains('meta->type', 'referral_bonus')
            ->sum('amount');
    }

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
