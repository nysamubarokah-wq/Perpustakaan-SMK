<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $fillable = ['user_id', 'ulasan_id', 'tipe', 'judul', 'pesan', 'dibaca_pada'];

    protected $casts = [
        'dibaca_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ulasan()
    {
        return $this->belongsTo(Ulasan::class);
    }

    public function markAsRead()
    {
        $this->update(['dibaca_pada' => now()]);
    }

    public function isRead(): bool
    {
        return $this->dibaca_pada !== null;
    }
}
