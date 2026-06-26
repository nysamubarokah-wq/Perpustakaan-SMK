<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

   protected $fillable = [
    'name', 'nis', 'email', 'password', 'role', 'foto', 'coin', 'background', 'owned_backgrounds', 'no_hp', 'is_on_duty', 'is_vip', 'vip_expired_at', 'agreed_rules','vip_expired_at', 
];
    protected $hidden = [
        'password', 'remember_token',
    ];
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'owned_backgrounds' => 'array',
        'is_vip' => 'boolean',
        'vip_expired_at' => 'datetime',
    ];
}
}