<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Denda;
use App\Models\EksemplarBuku;
use App\Models\Favorit;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Ulasan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PinjamController extends Controller
{
    public function detail(Buku $buku)
    {
        $isFavorit = auth()->check()
            ? Favorit::where('user_id', auth()->id())->where('buku_id', $buku->id)->exists()
            : false;

        $ulasanList = Ulasan::with('user')
            ->where('buku_id', $buku->id)
            ->latest()
            ->get();

        $avgRating = $ulasanList->avg('rating');
        $totalUlasan = $ulasanList->count();

        $ulasanSaya = auth()->check()
            ? Ulasan::where('user_id', auth()->id())->where('buku_id', $buku->id)->first()
            : null;

        $anggotaLogin = auth()->check()
            ? Anggota::where('email', auth()->user()->email)->first()
            : null;

        $bolehUlasan = $anggotaLogin
            ? Peminjaman::where('anggota_id', $anggotaLogin->id)->where('buku_id', $buku->id)->exists()
            : false;

        $rekomendasi = $buku->genre
            ? Buku::where('genre', $buku->genre)
                ->where('id', '!=', $buku->id)
                ->inRandomOrder()
                ->get()
            : collect();

        $notifikasiList = collect();
        $notifikasiCount = 0;
        if (auth()->check()) {
            $notifikasiList = Notifikasi::where('user_id', auth()->id())
                ->whereNull('dibaca_pada')
                ->with('ulasan.buku')
                ->latest()
                ->limit(10)
                ->get();
            $notifikasiCount = $notifikasiList->count();

            $ulasanIds = $ulasanList->where('user_id', auth()->id())->pluck('id');
            Notifikasi::where('user_id', auth()->id())
                ->whereIn('ulasan_id', $ulasanIds)
                ->whereNull('dibaca_pada')
                ->update(['dibaca_pada' => now()]);
        }

        $stokTersedia = $buku->eksemplarTersedia()->count();

        return view('pinjam.detail', compact(
            'buku', 'isFavorit', 'ulasanList', 'avgRating', 'totalUlasan', 'ulasanSaya', 'bolehUlasan', 'rekomendasi', 'notifikasiList', 'notifikasiCount', 'stokTersedia'
        ));
    }

    public function store(Request $request, Buku $buku)
    {
        $request->validate([
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
        ]);

        $user = auth()->user();

        $isVip = $user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at);

        $maxBuku = $isVip ? 6 : 3;
        $maxDurasi = $isVip ? 14 : 7;

        $anggota = Anggota::where('user_id', $user->id)->first();

        if (! $anggota) {
            $anggota = Anggota::where('email', $user->email)->first();
            if ($anggota) {
                $anggota->update(['user_id' => $user->id]);
            }
        }

        if (! $anggota) {
            $anggota = Anggota::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'email' => $user->email,
                'no_telepon' => '-',
                'alamat' => '-',
                'tanggal_daftar' => now()->toDateString(),
            ]);
        }

        $jumlahDipinjam = Peminjaman::where('anggota_id', $anggota->id)
            ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
            ->count();

        if ($jumlahDipinjam >= $maxBuku) {
            return back()->with('error',
                $isVip
                    ? "Batas pinjam VIP maksimal {$maxBuku} buku sekaligus."
                    : "Kamu sudah meminjam {$jumlahDipinjam} buku. Maksimal {$maxBuku} buku (upgrade VIP untuk 6 buku)."
            );
        }

        $tglPinjam = Carbon::parse($request->tanggal_pinjam);
        $tglKembali = Carbon::parse($request->tanggal_kembali);
        $durasi = $tglPinjam->diffInDays($tglKembali);

        if ($durasi > $maxDurasi) {
            return back()->with('error',
                $isVip
                    ? "Durasi pinjam VIP maksimal {$maxDurasi} hari."
                    : "Durasi pinjam maksimal {$maxDurasi} hari (upgrade VIP untuk 14 hari)."
            );
        }

        // Cari eksemplar tersedia
        $eksemplar = $buku->eksemplarTersedia()->first();

        if (! $eksemplar) {
            return back()->with('error', 'Maaf, semua eksemplar buku "' . $buku->judul . '" sedang tidak tersedia.');
        }

        // Cek apakah user sudah pinjam eksemplar yang sama
        $sudahPinjamEksemplar = Peminjaman::where('anggota_id', $anggota->id)
            ->where('eksemplar_id', $eksemplar->id)
            ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
            ->exists();

        if ($sudahPinjamEksemplar) {
            return back()->with('error', 'Kamu sudah meminjam eksemplar ini.');
        }

        // Update status eksemplar
        $eksemplar->update(['status' => 'dipinjam']);

        // Update stok buku
        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        Peminjaman::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'eksemplar_id' => $eksemplar->id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => 'menunggu_konfirmasi',
            'tipe_konfirmasi' => 'pinjam',
        ]);

        $redirectUrl = $request->input('redirect_url') ?: route('koleksi.index');

        return redirect($redirectUrl)
            ->with('success', 'Permintaan peminjaman berhasil dikirim! Menunggu konfirmasi admin.');
    }

    public function konfirmasiPinjam($id)
    {
        $peminjaman = Peminjaman::with(['buku', 'anggota', 'eksemplar'])->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Status peminjaman tidak valid.');
        }

        // Eksemplar sudah di-status-kan 'dipinjam' saat request, jadi tidak perlu ubah lagi
        // Tapi pastikan status benar
        if ($peminjaman->eksemplar) {
            $peminjaman->eksemplar->update(['status' => 'dipinjam']);
        }

        // Update stok buku
        if ($peminjaman->buku) {
            $peminjaman->buku->update(['stok' => $peminjaman->buku->eksemplarTersedia()->count()]);
        }

        $peminjaman->update(['status' => 'dipinjam', 'tipe_konfirmasi' => null]);

        $user = User::where('email', $peminjaman->anggota->email)->first();
        if ($user) {
            $user->increment('coin', 10000);
        }

        return back()->with('success', 'Peminjaman "'.$peminjaman->buku->judul.'" ('.($peminjaman->eksemplar->kode_buku ?? '-').') oleh '.$peminjaman->anggota->nama.' berhasil dikonfirmasi!');
    }

    public function tolakPinjam($id)
    {
        $peminjaman = Peminjaman::with(['buku', 'eksemplar'])->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Status peminjaman tidak valid.');
        }

        // Kembalikan status eksemplar ke tersedia
        if ($peminjaman->eksemplar) {
            $peminjaman->eksemplar->update(['status' => 'tersedia']);
        }

        // Update stok buku
        if ($peminjaman->buku) {
            $peminjaman->buku->update(['stok' => $peminjaman->buku->eksemplarTersedia()->count()]);
        }

        $peminjaman->delete();

        return back()->with('success', 'Permintaan peminjaman berhasil ditolak.');
    }

    public function persetujuanIndex()
    {
        $persetujuan = Peminjaman::with(['anggota', 'buku', 'eksemplar'])
            ->where(function ($q) {
                $q->where('status', 'menunggu_pengembalian')
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'menunggu_konfirmasi')
                            ->where('tipe_konfirmasi', 'kembali');
                    });
            })
            ->latest()
            ->get();

        $tarifDendaPerHari = 1000;

        foreach ($persetujuan as $item) {
            $tanggalKembali = Carbon::parse($item->tanggal_kembali)->startOfDay();
            $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

            if ($hariIni->gt($tanggalKembali)) {
                $selisihHari = ceil(abs($hariIni->diffInDays($tanggalKembali)));
                $item->taksiran_denda = $selisihHari * $tarifDendaPerHari;
                $item->terlambat_hari = $selisihHari;
            } else {
                $item->taksiran_denda = 0;
                $item->terlambat_hari = 0;
            }
        }

        return view('admin.pengembalian', compact('persetujuan'));
    }

    public function konfirmasiIndex()
    {
        $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

        // Auto-tolak pinjam yang sudah lewat tanggal_kembali belum dikonfirmasi
        $kedaluwarsa = Peminjaman::with(['buku', 'eksemplar'])
            ->where('status', 'menunggu_konfirmasi')
            ->where('tipe_konfirmasi', 'pinjam')
            ->whereDate('tanggal_kembali', '<', $hariIni)
            ->get();

        foreach ($kedaluwarsa as $p) {
            if ($p->eksemplar) {
                $p->eksemplar->update(['status' => 'tersedia']);
            }
            if ($p->buku) {
                $p->buku->update(['stok' => $p->buku->eksemplarTersedia()->count()]);
            }
            $p->delete();
        }

        $permintaan = Peminjaman::with(['anggota', 'buku', 'eksemplar'])
            ->where('status', 'menunggu_konfirmasi')
            ->where('tipe_konfirmasi', 'pinjam')
            ->latest()
            ->get();

        return view('admin.konfirmasi-pinjam', compact('permintaan'));
    }

    public function setujuiKembali($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status === 'menunggu_pengembalian' ||
            ($peminjaman->status === 'menunggu_konfirmasi' && $peminjaman->tipe_konfirmasi === 'kembali')) {
            $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
            $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();
            $hitungDenda = 0;

            if ($hariIni->gt($tanggalKembali)) {
                $selisihHari = ceil(abs($hariIni->diffInDays($tanggalKembali)));
                $tarifDendaPerHari = 1000;
                $hitungDenda = $selisihHari * $tarifDendaPerHari;
            }

            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_dikembalikan' => $hariIni->toDateString(),
                'denda' => $hitungDenda,
            ]);

            if ($hitungDenda > 0) {
                Denda::updateOrCreate(
                    ['peminjaman_id' => $peminjaman->id],
                    [
                        'jumlah_denda' => $hitungDenda,
                        'status' => 'belum_dibayar',
                        'keterangan' => 'Terlambat '.(int) abs($hariIni->diffInDays($tanggalKembali)).' hari',
                    ]
                );
            }

            // Kembalikan status eksemplar
            if ($peminjaman->eksemplar) {
                $peminjaman->eksemplar->update(['status' => 'tersedia']);
            }

            // Update stok buku
            $buku = Buku::findOrFail($peminjaman->buku_id);
            $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

            return redirect()->back()->with('success', 'Pengembalian buku berhasil disetujui dan eksemplar telah dikembalikan!');
        }

        return redirect()->back()->with('error', 'Status peminjaman tidak valid.');
    }

    public function setujuiSemuaKembali()
    {
        $data = Peminjaman::where(function ($q) {
            $q->where('status', 'menunggu_pengembalian')
                ->orWhere(function ($q2) {
                    $q2->where('status', 'menunggu_konfirmasi')
                        ->where('tipe_konfirmasi', 'kembali');
                });
        })
            ->get();

        $jumlah = 0;

        foreach ($data as $peminjaman) {

            $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
            $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

            $hitungDenda = 0;

            if ($hariIni->gt($tanggalKembali)) {
                $selisihHari = ceil(abs($hariIni->diffInDays($tanggalKembali)));
                $hitungDenda = $selisihHari * 1000;
            }

            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_dikembalikan' => $hariIni->toDateString(),
                'denda' => $hitungDenda,
            ]);

            if ($hitungDenda > 0) {
                Denda::updateOrCreate(
                    ['peminjaman_id' => $peminjaman->id],
                    [
                        'jumlah_denda' => $hitungDenda,
                        'status' => 'belum_dibayar',
                        'keterangan' => 'Terlambat '.$selisihHari.' hari',
                    ]
                );
            }

            // Kembalikan status eksemplar
            if ($peminjaman->eksemplar) {
                $peminjaman->eksemplar->update(['status' => 'tersedia']);
            }

            // Update stok buku
            if ($buku = Buku::find($peminjaman->buku_id)) {
                $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);
            }

            $jumlah++;
        }

        return back()->with(
            'success',
            $jumlah.' pengembalian berhasil disetujui.'
        );
    }
}
