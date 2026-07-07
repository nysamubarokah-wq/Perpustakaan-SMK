<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'user_id',
        'type',
        'judul',
        'pesan',
        'icon',
        'warna',
        'link',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const TYPE_PINJAM_DISETUJI = 'pinjam_disetujui';
    const TYPE_PINJAM_DITOLAK = 'pinjam_ditolak';
    const TYPE_PENGEMBALIAN_BERHASIL = 'pengembalian_berhasil';
    const TYPE_PENGINGAT_JATUH_TEMPO = 'pengingat_jatuh_tempo';
    const TYPE_BUKU_TERLAMBAT = 'buku_terlambat';
    const TYPE_COIN_BERTAMBAH = 'coin_bertambah';
    const TYPE_VIP_HABIS = 'vip_hampir_habis';
    const TYPE_PERMINTAAN_BARU = 'permintaan_baru';
    const TYPE_PENGEMBALIAN_PENDING = 'pengembalian_pending';
    const TYPE_STOK_HABIS = 'stok_habis';
    const TYPE_IMPORT_BERHASIL = 'import_berhasil';
    const TYPE_IMPORT_GAGAL = 'import_gagal';
    const TYPE_SISTEM = 'sistem';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): self
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
        return $this;
    }

    public static function markAllAsRead(int $userId): int
    {
        return static::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public static function deleteAll(int $userId): int
    {
        return static::where('user_id', $userId)->delete();
    }

    public static function unreadCount(int $userId): int
    {
        return static::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public static function getLatestForUser(int $userId, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public static function createNotification(
        int $userId,
        string $judul,
        string $pesan,
        string $type = self::TYPE_SISTEM,
        string $icon = 'bell',
        string $warna = '#6c757d',
        ?string $link = null
    ): self {
        return static::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'type' => $type,
            'icon' => $icon,
            'warna' => $warna,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    public static function existsByTypeAndUser(int $userId, string $type, ?int $peminjamanId = null): bool
    {
        $query = static::where('user_id', $userId)->where('type', $type);
        if ($peminjamanId) {
            $query->where('link', 'like', '%peminjaman%' . $peminjamanId . '%')
                  ->where('created_at', '>=', now()->subDays(7));
        }
        return $query->exists();
    }
}
