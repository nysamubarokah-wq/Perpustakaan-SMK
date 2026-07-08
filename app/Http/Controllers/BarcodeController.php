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
        $peminjamanAktifList = [];

        if ($anggota) {
            $peminjamanAktif = Peminjaman::where('buku_id', $buku->id)
                ->where('anggota_id', $anggota->id)
                ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi', 'menunggu_pengembalian'])
                ->with('eksemplar')
                ->get();

            foreach ($peminjamanAktif as $p) {
                $peminjamanAktifList[] = [
                    'id'              => $p->id,
                    'eksemplar_kode'  => $p->eksemplar?->kode_buku,
                    'status'          => $p->status,
                    'tanggal_pinjam'  => $p->tanggal_pinjam,
                    'tanggal_kembali' => $p->tanggal_kembali,
                ];
            }
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
            'peminjaman_aktif' => $peminjamanAktifList,
            'anggota_id'       => $anggota?->id,
        ]);
    }

    public function pinjamViaScan(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
            'jumlah' => 'nullable|integer|min:1',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $user = auth()->user();
        $jumlahDiminta = (int) ($request->jumlah ?? 1);

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

        $stokTersedia = $buku->eksemplarTersedia()->count();

        if ($jumlahDiminta > $stokTersedia) {
            return response()->json([
                'status' => 'error',
                'pesan'  => "Jumlah yang diminta ({$jumlahDiminta}) melebihi stok tersedia ({$stokTersedia} eksemplar).",
            ], 422);
        }

        $isVip = $user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at);
        $maxBuku = $isVip ? 6 : 3;
        $maxDurasi = $isVip ? 14 : 7;

        $totalAktif = Peminjaman::where('anggota_id', $anggota->id)
            ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
            ->count();

        $sisaSlot = $maxBuku - $totalAktif;

        if ($jumlahDiminta > $sisaSlot) {
            return response()->json([
                'status' => 'error',
                'pesan'  => $isVip
                    ? "Sisa slot pinjam VIP: {$sisaSlot} buku. Anda meminta {$jumlahDiminta} buku."
                    : "Sisa slot pinjam reguler: {$sisaSlot} buku. Anda meminta {$jumlahDiminta} buku. (Upgrade VIP untuk 6 buku)",
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

        $eksemplarList = $buku->eksemplarTersedia()->limit($jumlahDiminta)->get();

        if ($eksemplarList->count() < $jumlahDiminta) {
            return response()->json([
                'status' => 'error',
                'pesan'  => "Hanya {$eksemplarList->count()} eksemplar tersedia.",
            ], 422);
        }

        $dipinjamCount = 0;

        foreach ($eksemplarList as $eksemplar) {
            $sudahPinjam = Peminjaman::where('buku_id', $buku->id)
                ->where('anggota_id', $anggota->id)
                ->where('eksemplar_id', $eksemplar->id)
                ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
                ->exists();

            if ($sudahPinjam) {
                continue;
            }

            $eksemplar->update(['status' => 'dipinjam']);

            Peminjaman::create([
                'anggota_id'      => $anggota->id,
                'buku_id'         => $buku->id,
                'eksemplar_id'    => $eksemplar->id,
                'tanggal_pinjam'  => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status'          => 'menunggu_konfirmasi',
                'tipe_konfirmasi' => 'pinjam',
            ]);

            $dipinjamCount++;
        }

        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        $msg = $dipinjamCount == 1
            ? "Permintaan pinjam \"$buku->judul\" berhasil dikirim. Menunggu konfirmasi admin."
            : "{$dipinjamCount} permintaan pinjam \"$buku->judul\" berhasil dikirim. Menunggu konfirmasi admin.";

        return response()->json([
            'status' => 'success',
            'pesan'  => $msg,
        ]);
    }

    public function kembaliViaScan(Request $request)
    {
        $request->validate([
            'peminjaman_ids' => 'required|array|min:1',
            'peminjaman_ids.*' => 'exists:peminjaman,id',
        ]);

        $user    = auth()->user();
        $anggota = Anggota::where('user_id', $user->id)->first();

        if (!$anggota) {
            $anggota = Anggota::where('email', $user->email)->first();
            if ($anggota) {
                $anggota->update(['user_id' => $user->id]);
            }
        }

        if (!$anggota) {
            return response()->json([
                'status' => 'error',
                'pesan'  => 'Data anggota tidak ditemukan.',
            ], 422);
        }

        $ids = $request->peminjaman_ids;
        $peminjamanList = Peminjaman::with(['buku', 'eksemplar'])
            ->whereIn('id', $ids)
            ->where('anggota_id', $anggota->id)
            ->where('status', 'dipinjam')
            ->get();

        if ($peminjamanList->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'pesan'  => 'Tidak ada peminjaman valid untuk dikembalikan.',
            ], 422);
        }

        $kembaliCount = 0;
        $totalDenda = 0;
        $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

        foreach ($peminjamanList as $peminjaman) {
            $peminjaman->update([
                'status'               => 'menunggu_pengembalian',
                'tanggal_dikembalikan' => $hariIni->toDateString(),
            ]);

            $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();

            if ($hariIni->gt($tanggalKembali)) {
                $selisihHari = (int) abs($hariIni->diffInDays($tanggalKembali));
                $hitungDenda = $selisihHari * 1000;
                $totalDenda += $hitungDenda;

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

            $kembaliCount++;
        }

        $msg = $kembaliCount == 1
            ? 'Pengembalian berhasil diajukan!'
            : "{$kembaliCount} buku berhasil diajukan untuk dikembalikan!";

        if ($totalDenda > 0) {
            $msg .= ' (Denda keterlambatan: Rp ' . number_format($totalDenda, 0, ',', '.') . ')';
        }

        return response()->json([
            'status' => 'success',
            'pesan'  => $msg,
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
            'jumlah'          => 'nullable|integer|min:1',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $anggota = Anggota::findOrFail($request->anggota_id);
        $jumlahDiminta = (int) ($request->jumlah ?? 1);

        $stokTersedia = $buku->eksemplarTersedia()->count();

        if ($jumlahDiminta > $stokTersedia) {
            return back()->with('error', "Jumlah yang diminta ({$jumlahDiminta}) melebihi stok tersedia ({$stokTersedia} eksemplar).");
        }

        $eksemplarList = $buku->eksemplarTersedia()->limit($jumlahDiminta)->get();

        if ($eksemplarList->count() < $jumlahDiminta) {
            return back()->with('error', "Hanya {$eksemplarList->count()} eksemplar tersedia.");
        }

        $dipinjamCount = 0;

        foreach ($eksemplarList as $eksemplar) {
            $sudahPinjam = Peminjaman::where('buku_id', $buku->id)
                ->where('anggota_id', $anggota->id)
                ->where('eksemplar_id', $eksemplar->id)
                ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                ->exists();

            if ($sudahPinjam) {
                continue;
            }

            $eksemplar->update(['status' => 'dipinjam']);

            Peminjaman::create([
                'anggota_id'      => $anggota->id,
                'buku_id'         => $buku->id,
                'eksemplar_id'    => $eksemplar->id,
                'tanggal_pinjam'  => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status'          => 'dipinjam',
                'catatan'         => $request->catatan,
            ]);

            $dipinjamCount++;
        }

        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        $msg = $dipinjamCount == 1
            ? 'Buku "' . $buku->judul . '" berhasil dipinjamkan ke ' . $anggota->nama . '.'
            : "{$dipinjamCount} eksemplar buku \"{$buku->judul}\" berhasil dipinjamkan ke {$anggota->nama}.";

        return back()->with('success', $msg);
    }

    public function adminKembali(Request $request)
    {
        $request->validate([
            'buku_id'       => 'required|exists:buku,id',
            'anggota_id'    => 'required|exists:anggota,id',
            'peminjaman_ids' => 'required|array|min:1',
            'peminjaman_ids.*' => 'exists:peminjaman,id',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $anggota = Anggota::findOrFail($request->anggota_id);

        $ids = $request->peminjaman_ids;
        $peminjamanList = Peminjaman::whereIn('id', $ids)
            ->where('buku_id', $buku->id)
            ->where('anggota_id', $anggota->id)
            ->where('status', 'dipinjam')
            ->get();

        if ($peminjamanList->isEmpty()) {
            return back()->with('error', 'Tidak ada peminjaman valid untuk dikembalikan.');
        }

        $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();
        $kembaliCount = 0;
        $totalDenda = 0;

        foreach ($peminjamanList as $peminjaman) {
            $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
            $hitungDenda = 0;

            if ($hariIni->gt($tanggalKembali)) {
                $selisihHari = (int) ceil(abs($hariIni->diffInDays($tanggalKembali)));
                $hitungDenda = $selisihHari * 1000;
                $totalDenda += $hitungDenda;

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

            $kembaliCount++;
        }

        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        $msg = $kembaliCount == 1
            ? 'Buku "' . $buku->judul . '" berhasil dikembalikan oleh ' . $anggota->nama . '.'
            : $kembaliCount . ' eksemplar buku "' . $buku->judul . '" berhasil dikembalikan oleh ' . $anggota->nama . '.';

        if ($totalDenda > 0) {
            $msg .= ' (Denda keterlambatan: Rp ' . number_format($totalDenda, 0, ',', '.') . ').';
        }

        return back()->with('success', $msg);
    }

    public function adminCekPeminjaman(Request $request)
    {
        $request->validate([
            'buku_id'    => 'required|exists:buku,id',
            'anggota_id' => 'required|exists:anggota,id',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $anggota = Anggota::findOrFail($request->anggota_id);

        $peminjamanList = Peminjaman::where('buku_id', $buku->id)
            ->where('anggota_id', $anggota->id)
            ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
            ->with('eksemplar')
            ->get();

        if ($peminjamanList->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'pesan'  => 'Buku ini tidak sedang dipinjam oleh anggota tersebut.',
            ]);
        }

        $peminjamanData = [];
        foreach ($peminjamanList as $p) {
            $peminjamanData[] = [
                'id'              => $p->id,
                'eksemplar_kode'  => $p->eksemplar?->kode_buku,
                'status'          => $p->status,
                'tanggal_pinjam'  => $p->tanggal_pinjam,
                'tanggal_kembali' => $p->tanggal_kembali,
            ];
        }

        return response()->json([
            'status' => 'found',
            'peminjaman' => $peminjamanData,
        ]);
    }

    public function cekPeminjamanAnggota(Request $request)
    {
        $request->validate([
            'buku_id'    => 'required|exists:buku,id',
            'anggota_id' => 'required|exists:anggota,id',
        ]);

        $buku = Buku::findOrFail($request->buku_id);
        $anggota = Anggota::findOrFail($request->anggota_id);

        $peminjamanList = Peminjaman::where('buku_id', $buku->id)
            ->where('anggota_id', $anggota->id)
            ->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
            ->with('eksemplar')
            ->get();

        if ($peminjamanList->isEmpty()) {
            return response()->json([
                'status'   => 'tidak_memiliki',
                'pesan'    => 'Anggota ini belum meminjam buku ini.',
                'stok'     => $buku->eksemplarTersedia()->count(),
            ]);
        }

        $peminjamanData = [];
        foreach ($peminjamanList as $p) {
            $peminjamanData[] = [
                'id'              => $p->id,
                'eksemplar_kode'  => $p->eksemplar?->kode_buku,
                'status'          => $p->status,
                'tanggal_pinjam'  => $p->tanggal_pinjam,
                'tanggal_kembali' => $p->tanggal_kembali,
            ];
        }

        return response()->json([
            'status'   => 'sedang_memiliki',
            'pesan'    => 'Anggota ini sedang meminjam buku ini.',
            'peminjaman' => $peminjamanData,
            'stok'     => $buku->eksemplarTersedia()->count(),
        ]);
    }
}
