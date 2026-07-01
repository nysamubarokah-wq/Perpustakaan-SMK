<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggota';
    
    protected $fillable = [
    'user_id',  // tambah ini
    'nama',
    'email',
    'no_telepon',
    'alamat',
    'tanggal_daftar',
    'role',
    'nis',
];

public function peminjaman()
{
    return $this->hasMany(\App\Models\Peminjaman::class, 'anggota_id');
}

public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}
}