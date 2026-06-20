<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggota';
    
    protected $fillable = [
        'nama',
        'email', 
        'no_telepon',
        'alamat',
        'tanggal_daftar',
        'role',
    ];
}