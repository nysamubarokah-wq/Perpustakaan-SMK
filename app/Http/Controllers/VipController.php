<?php

namespace App\Http\Controllers;

use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class VipController extends Controller
{
    const HARGA_VIP  = 100;
    const DURASI_VIP = 7; // hari

    // Halaman upgrade VIP (untuk siswa)
    public function index()
    {
        $user = auth()->user();
        $isVip = $user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at);
        $sisaHari = $isVip ? now()->diffInDays($user->vip_expired_at) : 0;

        return view('vip.index', compact('user', 'isVip', 'sisaHari'));
    }

    // Siswa beli VIP pakai koin
    public function beliVip()
    {
        $user = auth()->user();

        if ($user->coin < self::HARGA_VIP) {
            return back()->with('error', 'Koin tidak cukup! Kamu butuh ' . self::HARGA_VIP . ' koin.');
        }

        $mulai = ($user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at))
            ? $user->vip_expired_at
            : now();

        $user->update([
            'coin'           => $user->coin - self::HARGA_VIP,
            'is_vip'         => true,
            'vip_expired_at' => $mulai->addDays(self::DURASI_VIP),
        ]);

        NotifikasiService::createCustomNotification(
            userId: $user->id,
            judul: 'VIP Aktif!',
            pesan: "Selamat! Kamu sekarang VIP selama " . self::DURASI_VIP . " hari. Nikmati batas pinjam 6 buku dan durasi 14 hari!",
            type: 'vip_aktif',
            icon: 'star-fill',
            warna: '#f1c40f',
            link: route('vip.index')
        );

        return back()->with('success', 'Selamat! Kamu sekarang VIP selama 7 hari');
    }

    // Admin: lihat daftar VIP
    public function adminIndex()
    {
        $vipUsers = \App\Models\User::where('is_vip', true)
                                    ->whereNotNull('vip_expired_at')
                                    ->orderBy('vip_expired_at', 'desc')
                                    ->paginate(15);
        return view('admin.vip.index', compact('vipUsers'));
    }

    // Admin: upgrade VIP manual
    public function adminUpgrade(Request $request, $userId)
    {
        $user = \App\Models\User::findOrFail($userId);

        $mulai = ($user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at))
            ? $user->vip_expired_at
            : now();

        $user->update([
            'is_vip'         => true,
            'vip_expired_at' => $mulai->addDays(self::DURASI_VIP),
        ]);

        return back()->with('success', $user->name . ' berhasil di-upgrade ke VIP 7 hari!');
    }

    // Admin: cabut VIP
    public function adminCabut($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $user->update([
            'is_vip'         => false,
            'vip_expired_at' => null,
        ]);

        return back()->with('success', 'VIP ' . $user->name . ' berhasil dicabut!');
    }
}