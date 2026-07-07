<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotifikasiService
{
    public static function pinjamDisetujui(int $userId, Peminjaman $peminjaman): ?Notification
    {
        $buku = $peminjaman->buku;
        if (!$buku) return null;

        return Notification::createNotification(
            userId: $userId,
            judul: 'Peminjaman Disetujui',
            pesan: "Buku '{$buku->judul}' berhasil dipinjam.",
            type: Notification::TYPE_PINJAM_DISETUJI,
            icon: 'book',
            warna: '#27ae60',
            link: route('profil.riwayat')
        );
    }

    public static function pinjamDitolak(int $userId, string $judulBuku): ?Notification
    {
        return Notification::createNotification(
            userId: $userId,
            judul: 'Peminjaman Ditolak',
            pesan: "Permintaan pinjam buku '{$judulBuku}' ditolak oleh admin.",
            type: Notification::TYPE_PINJAM_DITOLAK,
            icon: 'x-circle',
            warna: '#e74c3c',
            link: route('koleksi.index')
        );
    }

    public static function pengembalianBerhasil(int $userId, Peminjaman $peminjaman): ?Notification
    {
        $buku = $peminjaman->buku;
        if (!$buku) return null;

        return Notification::createNotification(
            userId: $userId,
            judul: 'Pengembalian Berhasil',
            pesan: "Buku '{$buku->judul}' berhasil dikembalikan.",
            type: Notification::TYPE_PENGEMBALIAN_BERHASIL,
            icon: 'arrow-repeat',
            warna: '#3498db',
            link: route('profil.riwayat')
        );
    }

    public static function pengingatJatuhTempo(int $userId, Peminjaman $peminjaman): ?Notification
    {
        $buku = $peminjaman->buku;
        if (!$buku) return null;

        $exists = Notification::existsByTypeAndUser(
            $userId,
            Notification::TYPE_PENGINGAT_JATUH_TEMPO,
            $peminjaman->id
        );

        if ($exists) return null;

        return Notification::createNotification(
            userId: $userId,
            judul: 'Pengingat Pengembalian',
            pesan: "Buku '{$buku->judul}' akan jatuh tempo dalam 2 hari.",
            type: Notification::TYPE_PENGINGAT_JATUH_TEMPO,
            icon: 'clock',
            warna: '#f39c12',
            link: route('profil.riwayat')
        );
    }

    public static function bukuTerlambat(int $userId, Peminjaman $peminjaman, int $hariTerlambat, int $denda): ?Notification
    {
        $buku = $peminjaman->buku;
        if (!$buku) return null;

        $key = "notif_terlambat_{$peminjaman->id}";
        $cacheKey = "notifikasi_terlambat_{$peminjaman->id}";

        if (\Cache::has($cacheKey)) return null;

        \Cache::put($cacheKey, true, now()->addDays(30));

        return Notification::createNotification(
            userId: $userId,
            judul: 'Buku Terlambat',
            pesan: "Kamu terlambat {$hariTerlambat} hari. Denda berjalan Rp " . number_format($denda, 0, ',', '.') . ".",
            type: Notification::TYPE_BUKU_TERLAMBAT,
            icon: 'exclamation-triangle',
            warna: '#e74c3c',
            link: route('profil.riwayat')
        );
    }

    public static function coinBertambah(int $userId, int $jumlahCoin, string $alasan = 'Peminjaman buku'): ?Notification
    {
        return Notification::createNotification(
            userId: $userId,
            judul: 'Coin Bertambah',
            pesan: "Kamu mendapatkan {$jumlahCoin} Coin dari {$alasan}.",
            type: Notification::TYPE_COIN_BERTAMBAH,
            icon: 'coin',
            warna: '#f1c40f',
            link: route('profil.index')
        );
    }

    public static function vipHampirHabis(int $userId, int $hariTersisa): ?Notification
    {
        if ($hariTersisa > 3) return null;

        $key = "notif_vip_hampir_habis_{$userId}";

        if (\Cache::has($key)) return null;

        \Cache::put($key, true, now()->addDays($hariTersisa + 1));

        return Notification::createNotification(
            userId: $userId,
            judul: 'Masa VIP Hampir Berakhir',
            pesan: "VIP akan berakhir dalam {$hariTersisa} hari.",
            type: Notification::TYPE_VIP_HABIS,
            icon: 'star',
            warna: '#f1c40f',
            link: route('vip.index')
        );
    }

    public static function permintaanPinjamBaru(int $adminUserId, string $namaSiswa, string $judulBuku, int $peminjamanId): ?Notification
    {
        return Notification::createNotification(
            userId: $adminUserId,
            judul: 'Permintaan Peminjaman Baru',
            pesan: "{$namaSiswa} meminta pinjam buku '{$judulBuku}'.",
            type: Notification::TYPE_PERMINTAAN_BARU,
            icon: 'book',
            warna: '#27ae60',
            link: route('admin.pinjam.index')
        );
    }

    public static function permintaanPengembalianBaru(int $adminUserId, string $namaSiswa, string $judulBuku): ?Notification
    {
        return Notification::createNotification(
            userId: $adminUserId,
            judul: 'Permintaan Pengembalian Baru',
            pesan: "{$namaSiswa} mengajukan pengembalian buku '{$judulBuku}'.",
            type: Notification::TYPE_PENGEMBALIAN_PENDING,
            icon: 'arrow-counterclockwise',
            warna: '#3498db',
            link: route('admin.pengembalian.index')
        );
    }

    public static function stokBukuHabis(int $adminUserId, string $judulBuku): ?Notification
    {
        return Notification::createNotification(
            userId: $adminUserId,
            judul: 'Stok Buku Habis',
            pesan: "Buku '{$judulBuku}' stok habis.",
            type: Notification::TYPE_STOK_HABIS,
            icon: 'exclamation-triangle',
            warna: '#e74c3c',
            link: route('buku.index')
        );
    }

    public static function importBerhasil(int $userId, string $jenis, int $jumlah): ?Notification
    {
        return Notification::createNotification(
            userId: $userId,
            judul: 'Import CSV Berhasil',
            pesan: "Import {$jenis} berhasil. {$jumlah} data diimport.",
            type: Notification::TYPE_IMPORT_BERHASIL,
            icon: 'check-circle',
            warna: '#27ae60',
            link: null
        );
    }

    public static function importGagal(int $userId, string $jenis, string $alasan): ?Notification
    {
        return Notification::createNotification(
            userId: $userId,
            judul: 'Import CSV Gagal',
            pesan: "Import {$jenis} gagal. {$alasan}",
            type: Notification::TYPE_IMPORT_GAGAL,
            icon: 'x-circle',
            warna: '#e74c3c',
            link: null
        );
    }

    public static function kirimKeSemuaAdmin(string $judul, string $pesan, string $icon = 'bell', string $warna = '#6c757d'): int
    {
        $adminIds = User::where('role', 'admin')->pluck('id');
        $count = 0;

        foreach ($adminIds as $adminId) {
            Notification::createNotification(
                userId: $adminId,
                judul: $judul,
                pesan: $pesan,
                type: Notification::TYPE_SISTEM,
                icon: $icon,
                warna: $warna
            );
            $count++;
        }

        return $count;
    }

    public static function markRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    public static function markAllReadForUser(int $userId): int
    {
        return Notification::markAllAsRead($userId);
    }

    public static function deleteAllForUser(int $userId): int
    {
        return Notification::deleteAll($userId);
    }

    public static function unreadCount(int $userId): int
    {
        return Notification::unreadCount($userId);
    }

    public static function getLatest(int $userId, int $limit = 5)
    {
        return Notification::getLatestForUser($userId, $limit);
    }

    public static function getUnreadIds(int $userId): array
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->pluck('id')
            ->toArray();
    }

    public static function createCustomNotification(
        int $userId,
        string $judul,
        string $pesan,
        string $type = self::TYPE_SISTEM,
        string $icon = 'bell',
        string $warna = '#6c757d',
        ?string $link = null
    ): self {
        return Notification::createNotification(
            userId: $userId,
            judul: $judul,
            pesan: $pesan,
            type: $type,
            icon: $icon,
            warna: $warna,
            link: $link
        );
    }
}
