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

        $riwayat = Peminjaman::with(['buku', 'eksemplar'])
            ->where('anggota_id', $anggotaId)
            ->where('status', '!=', 'dikembalikan')
            ->latest()
            ->get();

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

        return view('profil.index', compact('riwayat', 'totalDendaBelumBayar', 'user', 'totalFavorit', 'totalEbook'));
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
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp,gif,bmp,svg|max:10240',
        ]);

        $user = auth()->user();

        if ($user->foto && file_exists(public_path($user->foto))) {
            unlink(public_path($user->foto));
        }

        $file = $request->file('foto');
        $filename = 'foto_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('images/profil'), $filename);

        $user->foto = 'images/profil/'.$filename;
        $user->save();

        return back()->with('success', 'Foto profil berhasil diupdate!');
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
