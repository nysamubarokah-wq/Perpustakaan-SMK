<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'anggota_id', 'buku_id', 'eksemplar_id', 'tanggal_pinjam',
        'tanggal_kembali', 'tanggal_dikembalikan', 'status',
        'tipe_konfirmasi', 'denda', 'catatan',
        'total_denda', 'status_denda', 'tanggal_bayar',
    ];

    protected function casts(): array
    {
        return [
            'total_denda' => 'integer',
            'tanggal_bayar' => 'date',
        ];
    }

    public function anggota()
    {
        return $this->belongsTo(\App\Models\Anggota::class);
    }

    public function buku()
    {
        return $this->belongsTo(\App\Models\Buku::class);
    }

    public function eksemplar()
    {
        return $this->belongsTo(\App\Models\EksemplarBuku::class, 'eksemplar_id');
    }

    public function dendaRecord()
    {
        return $this->hasOne(\App\Models\Denda::class, 'peminjaman_id');
    }
}
