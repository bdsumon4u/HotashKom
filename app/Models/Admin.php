<?php

namespace App\Models;

use App\Notifications\Admin\ResetPassword;
use App\Notifications\Admin\VerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Traits\CausesActivity;

class Admin extends Authenticatable implements FilamentUser, HasTenants
{
    use CausesActivity;
    use Notifiable;

    const ADMIN = 0;

    const MANAGER = 1;

    const SALESMAN = 2;

    const UPLOADER = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'is_active', 'last_order_received_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

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

    public function is($role)
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->is($r)) {
                    return true;
                }
            }

            return false;
        }

        if (! is_string($role)) {
            return parent::is($role);
        }

        return $this->role_id == static::ADMIN && $role === 'admin'
            || $this->role_id == static::MANAGER && $role === 'manager'
            || $this->role_id == static::SALESMAN && $role === 'salesman'
            || $this->role_id == static::UPLOADER && $role === 'uploader';
    }

    /**
     * The attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'role_id' => 'integer',
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return true;
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return [];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
