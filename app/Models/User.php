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

    public static function booted(): void
    {
        static::updated(function (User $user) {
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
     * Get the database configuration for this reseller.
     *
     * @return array
     */
    public function getDatabaseConfig()
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
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/'.$this->logo) : null;
    }

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
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
