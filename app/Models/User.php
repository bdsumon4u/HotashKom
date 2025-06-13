<?php

namespace App\Models;

use App\Notifications\User\ResetPassword;
use App\Notifications\User\VerifyEmail;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class User extends Authenticatable implements Wallet
{
    use HasWallet;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'shop_name', 'email', 'phone_number', 'bkash_number', 'address',
        'website', 'api_token', 'domain', 'is_active', 'password', 'is_verified',
        'db_name', 'db_username', 'db_password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
        'db_password', // Hide database password
    ];

    public static function booted(): void
    {
        static::creating(function ($user) {
            $user->api_token = Str::random(60);
        });

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
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => $this->db_name,
            'username' => $this->db_username,
            'password' => $this->db_password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ];
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
}
