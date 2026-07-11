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
    'name', 'nis', 'email', 'password', 'role', 'foto', 'coin', 'background', 'owned_backgrounds', 'no_hp', 'is_on_duty', 'is_vip', 'vip_expired_at', 'agreed_rules', 'rules_session_token',
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

public function ulasan()
{
    return $this->hasMany(\App\Models\Ulasan::class);
}

public function favorit()
{
    return $this->hasMany(\App\Models\Favorit::class);
}

public function ebookAccess()
{
    return $this->hasMany(\App\Models\EbookAccess::class);
}

    public function notifikasi()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

public function notifications()
{
    return $this->hasMany(\App\Models\Notification::class);
}

public function anggota()
{
    return $this->hasOne(\App\Models\Anggota::class, 'user_id');
}
}