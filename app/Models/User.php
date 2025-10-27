<?php

namespace App\Models;

use App\Notifications\User\ResetPassword;
use App\Notifications\User\VerifyEmail;
use Bavix\Wallet\Interfaces\Confirmable;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\CanConfirm;
use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements Confirmable, Wallet
{
    use CanConfirm;
    use HasWallet;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'shop_name', 'email', 'phone_number', 'bkash_number', 'address',
        'website', 'order_prefix', 'domain', 'is_active', 'password', 'is_verified',
        'db_name', 'db_username', 'db_password', 'logo',
        'inside_dhaka_shipping', 'outside_dhaka_shipping',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
        'db_password', // Hide database password
    ];

    #[\Override]
    public static function booted(): void
    {
        static::updated(function (User $user): void {
            if (Arr::get($user->getChanges(), 'is_verified')) {
                $user->deposit(0, [
                    'reason' => 'Verify Reseller Account',
                ]);
            }
        });
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the total amount of pending withdrawals for this user.
     */
    public function getPendingWithdrawalAmount(): float
    {
        return abs($this->wallet->transactions()
            ->where('type', 'withdraw')
            ->where('confirmed', false)
            ->sum('amount'));
    }

    /**
     * Get the available balance (total balance minus pending withdrawals).
     */
    public function getAvailableBalance(): float
    {
        return $this->balance - $this->getPendingWithdrawalAmount();
    }

    /**
     * Check if the user can withdraw the specified amount.
     */
    public function canWithdraw(int|string $amount, bool $allowZero = false): bool
    {
        $availableBalance = $this->getAvailableBalance();

        if ($allowZero && $availableBalance == 0) {
            return true;
        }

        return $amount <= $availableBalance;
    }

    /**
     * Get the database configuration for this reseller.
     */
    public function getDatabaseConfig(): array
    {
        return [
            'driver' => 'mysql',
            'host' => $this->db_host ?? $this->domain ?? config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => $this->db_name,
            'username' => $this->db_username,
            'password' => $this->db_password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'options' => [
                // Connection timeout (how long to wait for initial connection)
                \PDO::ATTR_TIMEOUT => 10,

                // Don't use persistent connections for queue jobs
                \PDO::ATTR_PERSISTENT => false,

                // Set error mode to throw exceptions
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,

                // MySQL specific options
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
                \PDO::MYSQL_ATTR_LOCAL_INFILE => false, // Security: disable local infile
            ],
        ];
    }

    /**
     * Get the full URL for the user's logo.
     */
    protected function logoUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn (): ?string => $this->logo ? asset('storage/'.$this->logo) : null);
    }

    /**
     * Get shipping cost based on shipping area.
     */
    public function getShippingCost(string $shippingArea): int
    {
        return match ($shippingArea) {
            __('frontend.checkout.inside_dhaka') => (int) ($this->inside_dhaka_shipping ?? 0),
            __('frontend.checkout.outside_dhaka') => (int) ($this->outside_dhaka_shipping ?? 0),
            default => 0,
        };
    }

    /**
     * Check if user has custom shipping costs set.
     */
    public function hasCustomShippingCosts(): bool
    {
        return ($this->inside_dhaka_shipping > 0) || ($this->outside_dhaka_shipping > 0);
    }

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'inside_dhaka_shipping' => 'integer',
            'outside_dhaka_shipping' => 'integer',
        ];
    }

    /**
     * Get cache keys to clear based on table
     */
    protected function getCacheKeysToClear(string $table): ?array
    {
        return match ($table) {
            'categories' => ['categories:nested', 'homesections'],
            'brands' => ['brands'],
            default => null
        };
    }

    /**
     * Clear reseller's cache
     */
    public function clearResellerCache(string $table): void
    {
        // Get cache keys to clear
        $cacheKeys = $this->getCacheKeysToClear($table);

        // Only proceed if we have keys to clear
        if ($cacheKeys) {
            // Clear specific cache keys
            DB::connection('reseller')
                ->table('cache')
                ->whereIn('key', $cacheKeys)
                ->delete();
        }
    }
}
