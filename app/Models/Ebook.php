<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    protected $fillable = [
        'judul',
        'penulis',
        'cover',
        'file_pdf',
        'sinopsis',
        'is_vip',
        'harga_koin',
    ];

    protected $casts = [
        'is_vip' => 'boolean',
        'harga_koin' => 'integer',
    ];

    // Cek apakah user bisa akses ebook ini
  public function ebookAccess()
{
    return $this->hasMany(\App\Models\EbookAccess::class);
}

  public function bisaDiakses($user): bool
{
    if (!$user) return false;

    // Gratis
    if ($this->harga_koin === 0 && !$this->is_vip) return true;

    // User VIP dan belum expired
    if ($user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at)) return true;

    // Sudah pernah beli dengan koin
    return \App\Models\EbookAccess::where('user_id', $user->id)
                                  ->where('ebook_id', $this->id)
                                  ->exists();
}
}