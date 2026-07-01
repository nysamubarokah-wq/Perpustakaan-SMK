<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\EksemplarBuku;
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

        // Cari berdasarkan kode eksemplar terlebih dahulu
        $eksemplar = EksemplarBuku::where('kode_buku', $input)->first();

        if ($eksemplar) {
            $buku = $eksemplar->buku;
            $anggota = Anggota::where('user_id', auth()->id())->first();

            $peminjamanAktif = null;
            if ($anggota) {
                $peminjamanAktif = Peminjaman::where('eksemplar_id', $eksemplar->id)
                    ->where('anggota_id', $anggota->id)
                    ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi', 'menunggu_pengembalian'])
                    ->latest()
                    ->first();
            }

            return response()->json([
                'status'    => 'found',
                'eksemplar' => [
                    'id'        => $eksemplar->id,
                    'kode_buku' => $eksemplar->kode_buku,
                    'status'    => $eksemplar->status,
                ],
                'buku' => [
                    'id'        => $buku->id,
                    'judul'     => $buku->judul,
                    'pengarang' => $buku->pengarang,
                    'isbn'      => $buku->isbn,
                    'kode_buku' => $buku->kode_buku,
                    'stok'      => $buku->eksemplarTersedia()->count(),
                    'sampul'    => $buku->sampul ? asset($buku->sampul) : null,
                    'genre'     => $buku->genre,
                    'lokasi'    => $buku->lokasi,
                ],
                'peminjaman_aktif' => $peminjamanAktif ? [
                    'id'              => $peminjamanAktif->id,
                    'status'          => $peminjamanAktif->status,
                    'tanggal_pinjam'  => $peminjamanAktif->tanggal_pinjam,
                    'tanggal_kembali' => $peminjamanAktif->tanggal_kembali,
                ] : null,
                'anggota_id' => $anggota?->id,
                'dipinjam_orang_lain' => $eksemplar->status === 'dipinjam' && !$peminjamanAktif,
            ]);
        }

        // Fallback: cari berdasarkan kode_buku buku atau ISBN (backward compat)
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
            'eksemplar' => null,
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
                'status'          => $peminjamanAktif->status,
                'tanggal_pinjam'  => $peminjamanAktif->tanggal_pinjam,
                'tanggal_kembali' => $peminjamanAktif->tanggal_kembali,
            ] : null,
            'anggota_id'         => $anggota?->id,
            'dipinjam_orang_lain' => false,
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

        // Cari eksemplar tersedia
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

        // Update status eksemplar
        $eksemplar->update(['status' => 'dipinjam']);

        // Update stok buku
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
            'pesan'  => "Permintaan pinjam \"$buku->judul\" ({$eksemplar->kode_buku}) berhasil dikirim. Menunggu konfirmasi admin.",
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

            $sudahAda = \App\Models\Denda::where('peminjaman_id', $peminjaman->id)->exists();
            if (!$sudahAda) {
                \App\Models\Denda::create([
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

        // Cari berdasarkan kode eksemplar terlebih dahulu
        $eksemplar = EksemplarBuku::where('kode_buku', $input)->first();

        if ($eksemplar) {
            $buku = $eksemplar->buku;

            $peminjamanAktif = Peminjaman::where('eksemplar_id', $eksemplar->id)
                ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                ->with('anggota')
                ->latest()
                ->first();

            $anggota = Anggota::orderBy('nama')->get(['id', 'nama', 'nis']);

            return response()->json([
                'status'    => 'found',
                'eksemplar' => [
                    'id'        => $eksemplar->id,
                    'kode_buku' => $eksemplar->kode_buku,
                    'status'    => $eksemplar->status,
                ],
                'buku'   => [
                    'id'        => $buku->id,
                    'judul'     => $buku->judul,
                    'pengarang' => $buku->pengarang,
                    'isbn'      => $buku->isbn,
                    'kode_buku' => $buku->kode_buku,
                    'stok'      => $buku->eksemplarTersedia()->count(),
                    'sampul'    => $buku->sampul ? asset($buku->sampul) : null,
                    'lokasi'    => $buku->lokasi,
                ],
                'peminjaman_aktif' => $peminjamanAktif ? [
                    'id'              => $peminjamanAktif->id,
                    'status'          => $peminjamanAktif->status,
                    'tanggal_pinjam'  => $peminjamanAktif->tanggal_pinjam,
                    'tanggal_kembali' => $peminjamanAktif->tanggal_kembali,
                    'anggota_nama'    => $peminjamanAktif->anggota->nama ?? '-',
                ] : null,
                'anggota' => $anggota,
            ]);
        }

        // Fallback: cari berdasarkan kode_buku buku atau ISBN
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

        $peminjamanAktif = Peminjaman::where('buku_id', $buku->id)
            ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
            ->with('anggota')
            ->latest()
            ->first();

        $anggota = Anggota::orderBy('nama')->get(['id', 'nama', 'nis']);

        return response()->json([
            'status'    => 'found',
            'eksemplar' => null,
            'buku'   => [
                'id'        => $buku->id,
                'judul'     => $buku->judul,
                'pengarang' => $buku->pengarang,
                'isbn'      => $buku->isbn,
                'kode_buku' => $buku->kode_buku,
                'stok'      => $buku->eksemplarTersedia()->count(),
                'sampul'    => $buku->sampul ? asset($buku->sampul) : null,
                'lokasi'    => $buku->lokasi,
            ],
            'peminjaman_aktif' => $peminjamanAktif ? [
                'id'              => $peminjamanAktif->id,
                'status'          => $peminjamanAktif->status,
                'tanggal_pinjam'  => $peminjamanAktif->tanggal_pinjam,
                'tanggal_kembali' => $peminjamanAktif->tanggal_kembali,
                'anggota_nama'    => $peminjamanAktif->anggota->nama ?? '-',
            ] : null,
            'anggota' => $anggota,
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

        // Cari eksemplar tersedia
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

        // Update status eksemplar
        $eksemplar->update(['status' => 'dipinjam']);

        // Update stok buku
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

        return back()->with('success', 'Buku "' . $buku->judul . '" ({$eksemplar->kode_buku}) berhasil dipinjamkan ke ' . $anggota->nama . '.');
    }

    public function adminKembali(Request $request)
    {
        $request->validate(['buku_id' => 'required|exists:buku,id']);

        $buku = Buku::findOrFail($request->buku_id);

        $peminjaman = Peminjaman::where('buku_id', $buku->id)
            ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
            ->latest()
            ->first();

        if (!$peminjaman) {
            return back()->with('error', 'Tidak ada peminjaman aktif untuk buku ini.');
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

        // Kembalikan status eksemplar
        if ($peminjaman->eksemplar) {
            $peminjaman->eksemplar->update(['status' => 'tersedia']);
        }

        // Update stok buku
        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        $pesan = 'Buku "' . $buku->judul . '" (' . ($peminjaman->eksemplar->kode_buku ?? '-') . ') berhasil dikembalikan oleh ' . ($peminjaman->anggota->nama ?? '-') . '.';
        if ($hitungDenda > 0) {
            $pesan .= ' Denda keterlambatan: Rp ' . number_format($hitungDenda, 0, ',', '.') . '.';
        }

        return back()->with('success', $pesan);
    }
}
