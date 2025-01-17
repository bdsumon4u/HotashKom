<?php

namespace App\Models;

use App\Notifications\Admin\ResetPassword;
use App\Notifications\Admin\VerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;

class Admin extends Authenticatable
{
    use CausesActivity;
    use Notifiable;

    const ADMIN = 0;

    const MANAGER = 1;

    const SALESMAN = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'is_active',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role_id' => 'integer',
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
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
        if (! is_string($role)) {
            return parent::is($role);
        }

        return $this->role_id == static::ADMIN && $role === 'admin'
            || $this->role_id == static::MANAGER && $role === 'manager'
            || $this->role_id == static::SALESMAN && $role === 'salesman';
    }
}
