<?php

namespace App\models; // 👈 خليه M كبيرة مش m صغيرة

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'employee_id'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function treasuries()
    {
        return $this->belongsToMany(
            \App\Models\Finance\slocker::class,
            'fin_s_locker_user',
            'user_id',
            'fin_s_locker_id'
        )->withTrashed();
    }

    public function bankPermission()
    {
        return $this->hasOne(\App\Models\Finance\BankPermission::class, 'user_id');
    }

    public function hasBankPermission($permission)
    {
        $perm = $this->bankPermission;

        if (!$perm) {
            return false;
        }

        return isset($perm->{$permission}) && $perm->{$permission} == 1;
    }
}
