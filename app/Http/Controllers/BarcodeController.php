<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Anggota;
use App\Models\Denda;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function scanner()
    {
        $user = auth()->user();
        $isVip = $user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at);
        return view('barcode.scanner', compact('isVip'));
    }

    public function cekBuku(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $input = trim($request->kode);

        $buku = Buku::where('kode_buku', $input)->first();
        if (!$buku) {
            $buku = Buku::where('isbn', $input)->first();
        }

        if (!$buku) {
            return response()->json([
                'status' => 'not_found',
                'pesan'  => 'Buku dengan kode/ISBN tersebut tidak ditemukan.',
            ], 404);
        }

        $anggota = Anggota::where('user_id', auth()->id())->first();
        $peminjamanAktif = null;

        if ($anggota) {
            $peminjamanAktif = Peminjaman::where('buku_id', $buku->id)
                ->where('anggota_id', $anggota->id)
                ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi', 'menunggu_pengembalian'])
                ->latest()
                ->first();
        }

        $stokTersedia = $buku->eksemplarTersedia()->count();

        return response()->json([
            'status' => 'found',
            'buku'   => [
                'id'        => $buku->id,
                'judul'     => $buku->judul,
                'pengarang' => $buku->pengarang,
                'isbn'      => $buku->isbn,
                'kode_buku' => $buku->kode_buku,
                'stok'      => $stokTersedia,
                'sampul'    => $buku->sampul ? asset($buku->sampul) : null,
                'genre'     => $buku->genre,
                'lokasi'    => $buku->lokasi,
            ],
            'peminjaman_aktif' => $peminjamanAktif ? [
                'id'              => $peminjamanAktif->id,
                'eksemplar_kode'  => $peminjamanAktif->eksemplar?->kode_buku,
                'status'          => $peminjamanAktif->status,
                'tanggal_pinjam'  => $peminjamanAktif->tanggal_pinjam,
                'tanggal_kembali' => $peminjamanAktif->tanggal_kembali,
            ] : null,
            'anggota_id'         => $anggota?->id,
        ]);
    }

    public function pinjamViaScan(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $user = auth()->user();
        $anggota = Anggota::where('user_id', $user->id)->first();

        if (!$anggota) {
            $anggota = Anggota::where('email', $user->email)->first();
            if ($anggota) {
                $anggota->update(['user_id' => $user->id]);
            }
        }

        if (!$anggota) {
            $anggota = Anggota::create([
                'user_id'         => $user->id,
                'nama'            => $user->name,
                'email'           => $user->email,
                'tanggal_daftar'  => now()->toDateString(),
            ]);
        }

        $eksemplar = $buku->eksemplarTersedia()->first();

        if (!$eksemplar) {
            return response()->json([
                'status' => 'error',
                'pesan'  => 'Semua eksemplar buku ini sedang tidak tersedia.',
            ], 422);
        }

        $sudahPinjam = Peminjaman::where('buku_id', $buku->id)
            ->where('anggota_id', $anggota->id)
            ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
            ->exists();

        if ($sudahPinjam) {
            return response()->json([
                'status' => 'error',
                'pesan'  => 'Kamu sudah meminjam buku ini dan belum mengembalikannya.',
            ], 422);
        }

        $isVip = $user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at);
        $maxBuku = $isVip ? 6 : 3;
        $maxDurasi = $isVip ? 14 : 7;

        $totalAktif = Peminjaman::where('anggota_id', $anggota->id)
            ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
            ->count();

        if ($totalAktif >= $maxBuku) {
            return response()->json([
                'status' => 'error',
                'pesan'  => $isVip
                    ? "Batas pinjam VIP maksimal {$maxBuku} buku sekaligus."
                    : "Kamu sudah mencapai batas maksimal {$maxBuku} buku. Upgrade VIP untuk pinjam 6 buku.",
            ], 422);
        }

        $tglPinjam = \Carbon\Carbon::parse($request->tanggal_pinjam);
        $tglKembali = \Carbon\Carbon::parse($request->tanggal_kembali);
        $durasi = $tglPinjam->diffInDays($tglKembali);

        if ($durasi > $maxDurasi) {
            return response()->json([
                'status' => 'error',
                'pesan'  => $isVip
                    ? "Durasi pinjam VIP maksimal {$maxDurasi} hari."
                    : "Durasi pinjam maksimal {$maxDurasi} hari. Upgrade VIP untuk 14 hari.",
            ], 422);
        }

        $eksemplar->update(['status' => 'dipinjam']);
        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        Peminjaman::create([
            'anggota_id'      => $anggota->id,
            'buku_id'         => $buku->id,
            'eksemplar_id'    => $eksemplar->id,
            'tanggal_pinjam'  => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status'          => 'menunggu_konfirmasi',
            'tipe_konfirmasi' => 'pinjam',
        ]);

        return response()->json([
            'status' => 'success',
            'pesan'  => "Permintaan pinjam \"$buku->judul\" berhasil dikirim. Menunggu konfirmasi admin.",
        ]);
    }

    public function kembaliViaScan(Request $request)
    {
        $request->validate(['peminjaman_id' => 'required|exists:peminjaman,id']);

        $peminjaman = Peminjaman::with(['buku', 'eksemplar'])->findOrFail($request->peminjaman_id);
        $user       = auth()->user();
        $anggota    = Anggota::where('user_id', $user->id)->first();

        if (!$anggota) {
            $anggota = Anggota::where('email', $user->email)->first();
            if ($anggota) {
                $anggota->update(['user_id' => $user->id]);
            }
        }

        if (!$anggota || $peminjaman->anggota_id !== $anggota->id) {
            return response()->json([
                'status' => 'error',
                'pesan'  => 'Peminjaman ini bukan milikmu.',
            ], 403);
        }

        if ($peminjaman->status !== 'dipinjam') {
            return response()->json([
                'status' => 'error',
                'pesan'  => 'Buku ini tidak bisa dikembalikan (status: ' . $peminjaman->status . ').',
            ], 422);
        }

        $peminjaman->update([
            'status'               => 'menunggu_pengembalian',
            'tanggal_dikembalikan' => Carbon::now()->toDateString(),
        ]);

        $tanggalKembali = \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
        $hariIni = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();

        if ($hariIni->gt($tanggalKembali)) {
            $selisihHari = (int) abs($hariIni->diffInDays($tanggalKembali));
            $hitungDenda = $selisihHari * 1000;

            $peminjaman->update(['denda' => $hitungDenda]);

            $sudahAda = Denda::where('peminjaman_id', $peminjaman->id)->exists();
            if (!$sudahAda) {
                Denda::create([
                    'peminjaman_id' => $peminjaman->id,
                    'jumlah_denda'  => $hitungDenda,
                    'status'        => 'belum_dibayar',
                    'keterangan'    => 'Terlambat ' . $selisihHari . ' hari',
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'pesan'  => 'Pengembalian berhasil diajukan!',
        ]);
    }

    public function adminScanner()
    {
        return view('admin.admin-scanner');
    }

    public function adminCekBuku(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $input = trim($request->kode);

        $buku = Buku::where('kode_buku', $input)->first();
        if (!$buku) {
            $buku = Buku::where('isbn', $input)->first();
        }

        if (!$buku) {
            return response()->json([
                'status' => 'not_found',
                'pesan'  => 'Buku tidak ditemukan.',
            ], 404);
        }

        $stokTersedia = $buku->eksemplarTersedia()->count();
        $anggotaList = Anggota::orderBy('nama')->get(['id', 'nama', 'nis', 'kelas', 'jurusan']);

        return response()->json([
            'status' => 'found',
            'buku'   => [
                'id'        => $buku->id,
                'judul'     => $buku->judul,
                'pengarang' => $buku->pengarang,
                'isbn'      => $buku->isbn,
                'kode_buku' => $buku->kode_buku,
                'stok'      => $stokTersedia,
                'sampul'    => $buku->sampul ? asset($buku->sampul) : null,
                'lokasi'    => $buku->lokasi,
            ],
            'anggota' => $anggotaList,
        ]);
    }

    public function adminPinjam(Request $request)
    {
        $request->validate([
            'buku_id'         => 'required|exists:buku,id',
            'anggota_id'      => 'required|exists:anggota,id',
            'tanggal_pinjam'  => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $anggota = Anggota::findOrFail($request->anggota_id);

        $eksemplar = $buku->eksemplarTersedia()->first();

        if (!$eksemplar) {
            return back()->with('error', 'Semua eksemplar buku "' . $buku->judul . '" sedang tidak tersedia.');
        }

        $sudahPinjam = Peminjaman::where('buku_id', $buku->id)
            ->where('anggota_id', $anggota->id)
            ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
            ->exists();

        if ($sudahPinjam) {
            return back()->with('error', 'Anggota "' . $anggota->nama . '" masih memiliki peminjaman aktif untuk buku ini.');
        }

        $eksemplar->update(['status' => 'dipinjam']);
        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        Peminjaman::create([
            'anggota_id'      => $anggota->id,
            'buku_id'         => $buku->id,
            'eksemplar_id'    => $eksemplar->id,
            'tanggal_pinjam'  => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status'          => 'dipinjam',
            'catatan'         => $request->catatan,
        ]);

        return back()->with('success', 'Buku "' . $buku->judul . '" berhasil dipinjamkan ke ' . $anggota->nama . '.');
    }

    public function adminKembali(Request $request)
    {
        $request->validate([
            'buku_id'    => 'required|exists:buku,id',
            'anggota_id' => 'required|exists:anggota,id',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $anggota = Anggota::findOrFail($request->anggota_id);

        $peminjaman = Peminjaman::where('buku_id', $buku->id)
            ->where('anggota_id', $anggota->id)
            ->where('status', 'dipinjam')
            ->first();

        if (!$peminjaman) {
            return back()->with('error', 'Buku ini tidak sedang dipinjam oleh anggota tersebut.');
        }

        $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
        $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();
        $hitungDenda = 0;

        if ($hariIni->gt($tanggalKembali)) {
            $selisihHari = (int) ceil(abs($hariIni->diffInDays($tanggalKembali)));
            $hitungDenda = $selisihHari * 1000;

            Denda::updateOrCreate(
                ['peminjaman_id' => $peminjaman->id],
                [
                    'jumlah_denda' => $hitungDenda,
                    'status'       => 'belum_dibayar',
                    'keterangan'   => 'Terlambat ' . $selisihHari . ' hari',
                ]
            );
        }

        $peminjaman->update([
            'status'               => 'dikembalikan',
            'tanggal_dikembalikan' => $hariIni->toDateString(),
            'denda'                => $hitungDenda,
        ]);

        if ($peminjaman->eksemplar) {
            $peminjaman->eksemplar->update(['status' => 'tersedia']);
        }

        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        $pesan = 'Buku "' . $buku->judul . '" berhasil dikembalikan oleh ' . $anggota->nama . '.';
        if ($hitungDenda > 0) {
            $pesan .= ' Denda keterlambatan: Rp ' . number_format($hitungDenda, 0, ',', '.') . '.';
        }

        return back()->with('success', $pesan);
    }

    public function adminCekPeminjaman(Request $request)
    {
        $request->validate([
            'buku_id'    => 'required|exists:buku,id',
            'anggota_id' => 'required|exists:anggota,id',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $anggota = Anggota::findOrFail($request->anggota_id);

        $peminjaman = Peminjaman::where('buku_id', $buku->id)
            ->where('anggota_id', $anggota->id)
            ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
            ->with('eksemplar')
            ->first();

        if (!$peminjaman) {
            return response()->json([
                'status' => 'not_found',
                'pesan'  => 'Buku ini tidak sedang dipinjam oleh anggota tersebut.',
            ]);
        }

        return response()->json([
            'status' => 'found',
            'peminjaman' => [
                'id'              => $peminjaman->id,
                'eksemplar_kode'  => $peminjaman->eksemplar?->kode_buku,
                'status'          => $peminjaman->status,
                'tanggal_pinjam'  => $peminjaman->tanggal_pinjam,
                'tanggal_kembali' => $peminjaman->tanggal_kembali,
            ],
        ]);
    }
}
