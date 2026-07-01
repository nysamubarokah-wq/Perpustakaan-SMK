<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
   protected $fillable = ['anggota_id', 'buku_id', 'eksemplar_id', 'tanggal_pinjam', 'tanggal_kembali', 'tanggal_dikembalikan', 'status', 'tipe_konfirmasi', 'denda', 'catatan'];
    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function eksemplar()
    {
        return $this->belongsTo(EksemplarBuku::class, 'eksemplar_id');
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }
}
