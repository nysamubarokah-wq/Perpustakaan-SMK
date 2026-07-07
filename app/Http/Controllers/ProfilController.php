<?php

namespace App\Http\Controllers;

use App\Helpers\Backgrounds;
use App\Models\Anggota;
use App\Models\Denda;
use App\Models\Ebook;
use App\Models\Favorit;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user()->fresh();

        $anggota = Anggota::where('user_id', $user->id)->first();

        if (! $anggota) {
            $anggota = Anggota::where('email', $user->email)->first();
            if ($anggota) {
                $anggota->update(['user_id' => $user->id]);
            }
        }

        $anggotaId = $anggota ? $anggota->id : 0;

        $riwayat = Peminjaman::with(['buku', 'eksemplar', 'denda'])
            ->where('anggota_id', $anggotaId)
            ->latest()
            ->take(3)
            ->get();

        $totalRiwayatCount = Peminjaman::where('anggota_id', $anggotaId)->count();

        $tarifDendaPerHari = 1000;
        $totalDendaBelumBayar = 0;

        foreach ($riwayat as $item) {
            $item->taksiran_denda = 0;
            $item->terlambat_hari = 0;

            if ($item->status === 'dipinjam' || $item->status === 'menunggu_konfirmasi') {
                $tanggalKembali = Carbon::parse($item->tanggal_kembali)->startOfDay();
                $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

                if ($hariIni->gt($tanggalKembali)) {
                    $selisihHari = (int) abs($hariIni->diffInDays($tanggalKembali));
                    $item->taksiran_denda = $selisihHari * $tarifDendaPerHari;
                    $item->terlambat_hari = $selisihHari;
                    $totalDendaBelumBayar += $item->taksiran_denda;
                }
            }
        }

        $totalFavorit = Favorit::where('user_id', $user->id)->count();
        $totalEbook = Ebook::count();

        return view('profil.index', compact('riwayat', 'totalDendaBelumBayar', 'user', 'totalFavorit', 'totalEbook', 'totalRiwayatCount'));
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' => 'menunggu_konfirmasi',
            'tipe_konfirmasi' => 'kembali',
        ]);

        $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
        $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

        if ($hariIni->gt($tanggalKembali)) {
            $selisihHari = (int) abs($hariIni->diffInDays($tanggalKembali));
            $hitungDenda = $selisihHari * 1000;

            $peminjaman->update(['denda' => $hitungDenda]);

            $sudahAda = Denda::where('peminjaman_id', $peminjaman->id)->exists();
            if (! $sudahAda) {
                Denda::create([
                    'peminjaman_id' => $peminjaman->id,
                    'jumlah_denda' => $hitungDenda,
                    'status' => 'belum_dibayar',
                    'keterangan' => 'Terlambat '.$selisihHari.' hari',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Buku berhasil diajukan untuk dikembalikan! Menunggu konfirmasi admin.');
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,bmp,webp|max:5120',
        ]);

        $user = auth()->user();
        $oldFoto = $user->foto;

        try {
            $file = $request->file('foto');
            $filename = 'foto_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'profile-photos/' . $filename;

            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

            $user->foto = 'storage/' . $path;
            $user->save();

            if ($oldFoto && str_contains($oldFoto, 'profile-photos') && Storage::disk('public')->exists(str_replace('storage/', '', $oldFoto))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldFoto));
            }

            return back()->with('success', 'Foto profil berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupload foto!');
        }
    }

    public function riwayat(Request $request)
    {
        $user = auth()->user();
        $anggota = Anggota::where('user_id', $user->id)->first();

        if (!$anggota) {
            $anggota = Anggota::where('email', $user->email)->first();
            if ($anggota) {
                $anggota->update(['user_id' => $user->id]);
            }
        }

        $anggotaId = $anggota ? $anggota->id : 0;

        $query = Peminjaman::with(['buku', 'eksemplar', 'denda'])
            ->where('anggota_id', $anggotaId)
            ->latest('tanggal_pinjam');

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->whereHas('buku', function ($q) use ($cari) {
                $q->where('judul', 'like', "%{$cari}%")
                  ->orWhere('kode_buku', 'like', "%{$cari}%");
            });
        }

        $filter = $request->filter;
        if ($filter === 'dipinjam') {
            $query->where('status', 'dipinjam');
        } elseif ($filter === 'dikembalikan') {
            $query->where('status', 'dikembalikan');
        } elseif ($filter === 'terlambat') {
            $query->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
                  ->whereDate('tanggal_kembali', '<', now()->toDateString());
        } elseif ($filter === 'menunggu') {
            $query->whereIn('status', ['menunggu_konfirmasi', 'menunggu_pengembalian']);
        }

        $peminjaman = $query->paginate(10)->withQueryString();

        foreach ($peminjaman as $item) {
            $item->terlambat_hari = 0;
            $item->taksiran_denda = 0;

            if ($item->status === 'dipinjam' || $item->status === 'menunggu_konfirmasi') {
                $tanggalKembali = Carbon::parse($item->tanggal_kembali)->startOfDay();
                $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

                if ($hariIni->gt($tanggalKembali)) {
                    $item->terlambat_hari = (int) $hariIni->diffInDays($tanggalKembali);
                    $item->taksiran_denda = $item->terlambat_hari * 1000;
                }
            }
        }

        return view('profil.riwayat', compact('peminjaman', 'user'));
    }

    public function detailRiwayat($id)
    {
        $user = auth()->user();
        $anggota = Anggota::where('user_id', $user->id)->first();

        if (!$anggota) {
            $anggota = Anggota::where('email', $user->email)->first();
        }

        $anggotaId = $anggota ? $anggota->id : 0;

        $peminjaman = Peminjaman::with(['buku', 'eksemplar', 'denda'])
            ->where('id', $id)
            ->where('anggota_id', $anggotaId)
            ->firstOrFail();

        $peminjaman->terlambat_hari = 0;
        $peminjaman->taksiran_denda = 0;

        if ($peminjaman->status === 'dipinjam' || $peminjaman->status === 'menunggu_konfirmasi') {
            $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
            $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

            if ($hariIni->gt($tanggalKembali)) {
                $peminjaman->terlambat_hari = (int) $hariIni->diffInDays($tanggalKembali);
                $peminjaman->taksiran_denda = $peminjaman->terlambat_hari * 1000;
            }
        }

        return view('profil.riwayat-detail', compact('peminjaman', 'user'));
    }

    public function beliBackground($key)
    {
        $backgrounds = Backgrounds::list();

        if (! isset($backgrounds[$key])) {
            return back()->with('error', 'Background tidak ditemukan!');
        }

        $bg = $backgrounds[$key];
        $user = auth()->user();

        if ($user->background === $key) {
            return back()->with('error', 'Kamu sudah menggunakan background ini!');
        }

        $owned = $user->owned_backgrounds ?? [];
        $sudahDimiliki = $bg['harga'] == 0 || in_array($key, $owned);

        if (! $sudahDimiliki && $user->coin < $bg['harga']) {
            return back()->with('error', 'Coin tidak cukup! Kamu butuh '.$bg['harga'].' coin.');
        }

        $update = [
            'background' => $key,
        ];

        if (! $sudahDimiliki) {
            $owned[] = $key;
            $update['owned_backgrounds'] = $owned;
            $update['coin'] = $user->coin - $bg['harga'];
        }

        $user->update($update);
        auth()->setUser($user->fresh());

        $pesan = $sudahDimiliki
            ? 'Background '.$bg['nama'].' berhasil dipasang!'
            : 'Background '.$bg['nama'].' berhasil dibeli & dipasang!';

        return back()->with('success', $pesan);
    }
}
