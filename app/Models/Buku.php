<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'buku';
   

    protected $fillable = ['judul', 'pengarang', 'penerbit', 'tahun_terbit', 'isbn', 'stok', 'genre', 'sampul', 'deskripsi', 'lokasi'];

    public function peminjaman()
{
    return $this->hasMany(\App\Models\Peminjaman::class, 'buku_id');
}
}
