<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PinjamController extends Controller
{
   public function detail(Buku $buku)
{
    $isFavorit = auth()->check()
        ? \App\Models\Favorit::where('user_id', auth()->id())->where('buku_id', $buku->id)->exists()
        : false;

    $ulasanList = \App\Models\Ulasan::with('user')
        ->where('buku_id', $buku->id)
        ->latest()
        ->get();

    $avgRating = $ulasanList->avg('rating');
    $totalUlasan = $ulasanList->count();

    $ulasanSaya = auth()->check()
        ? \App\Models\Ulasan::where('user_id', auth()->id())->where('buku_id', $buku->id)->first()
        : null;

    $anggotaLogin = auth()->check()
        ? \App\Models\Anggota::where('email', auth()->user()->email)->first()
        : null;

    $bolehUlasan = $anggotaLogin
        ? \App\Models\Peminjaman::where('anggota_id', $anggotaLogin->id)->where('buku_id', $buku->id)->exists()
        : false;

    // Rekomendasi buku dengan genre yang sama
    $rekomendasi = $buku->genre
        ? Buku::where('genre', $buku->genre)
            ->where('id', '!=', $buku->id)
            ->inRandomOrder()
            ->limit(4)
            ->get()
        : collect();

    return view('pinjam.detail', compact(
        'buku', 'isFavorit', 'ulasanList', 'avgRating', 'totalUlasan', 'ulasanSaya', 'bolehUlasan', 'rekomendasi'
    ));
}


  public function store(Request $request, Buku $buku)
{
    $request->validate([
        'tanggal_pinjam'  => 'required|date',
        'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
    ]);

    $user = auth()->user();

    // Cek VIP
    $isVip = $user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at);

    // Batas maksimal buku & durasi berdasarkan VIP
    $maxBuku    = $isVip ? 6 : 3;
    $maxDurasi  = $isVip ? 14 : 7;

    $anggota = \App\Models\Anggota::where('email', $user->email)->first();

    if (!$anggota) {
        $anggota = \App\Models\Anggota::create([
            'nama'           => $user->name,
            'email'          => $user->email,
            'no_telepon'     => '-',
            'alamat'         => '-',
            'tanggal_daftar' => now()->toDateString(),
        ]);
    }

    // Cek jumlah buku yang sedang dipinjam
    $jumlahDipinjam = \App\Models\Peminjaman::where('anggota_id', $anggota->id)
        ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
        ->count();

    if ($jumlahDipinjam >= $maxBuku) {
        return back()->with('error',
            $isVip
                ? "Batas pinjam VIP maksimal {$maxBuku} buku sekaligus."
                : "Kamu sudah meminjam {$jumlahDipinjam} buku. Maksimal {$maxBuku} buku (upgrade VIP untuk 6 buku)."
        );
    }

    // Cek durasi peminjaman
    $tglPinjam  = \Carbon\Carbon::parse($request->tanggal_pinjam);
    $tglKembali = \Carbon\Carbon::parse($request->tanggal_kembali);
    $durasi     = $tglPinjam->diffInDays($tglKembali);

    if ($durasi > $maxDurasi) {
        return back()->with('error',
            $isVip
                ? "Durasi pinjam VIP maksimal {$maxDurasi} hari."
                : "Durasi pinjam maksimal {$maxDurasi} hari (upgrade VIP untuk 14 hari)."
        );
    }

    // Semua validasi lolos — simpan peminjaman
    \App\Models\Peminjaman::create([
        'anggota_id'      => $anggota->id,
        'buku_id'         => $buku->id,
        'tanggal_pinjam'  => $request->tanggal_pinjam,
        'tanggal_kembali' => $request->tanggal_kembali,
        'status'          => 'menunggu_konfirmasi',
         'tipe_konfirmasi' => 'pinjam', // ← tambah
    ]);

    return redirect()->route('koleksi.index')
                     ->with('success', 'Permintaan peminjaman berhasil dikirim! Menunggu konfirmasi admin.');
}
public function konfirmasiPinjam($id)
{
    $peminjaman = \App\Models\Peminjaman::with(['buku', 'anggota'])->findOrFail($id);

    if ($peminjaman->status !== 'menunggu_konfirmasi') {
        return back()->with('error', 'Status peminjaman tidak valid.');
    }

    // Kurangi stok buku
    $peminjaman->buku->decrement('stok');

    // Update status jadi dipinjam
    $peminjaman->update(['status' => 'dipinjam']);

    // Tambah coin ke user
    $user = \App\Models\User::where('email', $peminjaman->anggota->email)->first();
    if ($user) {
        $user->increment('coin', 10000);
    }

    return back()->with('success', 'Peminjaman "'.$peminjaman->buku->judul.'" oleh '.$peminjaman->anggota->nama.' berhasil dikonfirmasi!');
}

public function tolakPinjam($id)
{
    $peminjaman = \App\Models\Peminjaman::with('buku')->findOrFail($id);

    if ($peminjaman->status !== 'menunggu_konfirmasi') {
        return back()->with('error', 'Status peminjaman tidak valid.');
    }

    $peminjaman->delete();

    return back()->with('success', 'Permintaan peminjaman berhasil ditolak.');
}

    public function persetujuanIndex()
{
    $persetujuan = \App\Models\Peminjaman::with(['anggota', 'buku'])
        ->where('status', 'menunggu_konfirmasi')
         ->where('tipe_konfirmasi', 'kembali') // ← tambah
        ->latest()
        ->get();

    $tarifDendaPerHari = 1000; 

    foreach ($persetujuan as $item) {
        $tanggalKembali = \Carbon\Carbon::parse($item->tanggal_kembali)->startOfDay();
        $hariIni = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();

        if ($hariIni->gt($tanggalKembali)) {
            // Kita bungkus dengan ceil() agar desimalnya dibulatkan ke atas menjadi hari utuh
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
    $permintaan = \App\Models\Peminjaman::with(['anggota', 'buku'])
        ->where('status', 'menunggu_konfirmasi')
        ->where('tipe_konfirmasi', 'pinjam') // ← tambah
        ->latest()
        ->get();

    return view('admin.konfirmasi-pinjam', compact('permintaan'));
}
public function setujuiKembali($id)
{
    $peminjaman = \App\Models\Peminjaman::findOrFail($id);
    
    if ($peminjaman->status === 'menunggu_konfirmasi') {
        $tanggalKembali = \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
        $hariIni = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $hitungDenda = 0;

        if ($hariIni->gt($tanggalKembali)) {
            // Gunakan ceil() juga di sini biar nominal yang disimpan ke database ikut bulat
            $selisihHari = ceil(abs($hariIni->diffInDays($tanggalKembali)));
            $tarifDendaPerHari = 1000;
            $hitungDenda = $selisihHari * $tarifDendaPerHari;
        }
        
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_pengembalian' => $hariIni->toDateString(),
            'denda' => $hitungDenda
        ]);

        $buku = \App\Models\Buku::findOrFail($peminjaman->buku_id);
        $buku->increment('stok'); 
    }

    return redirect()->back()->with('success', 'Pengembalian buku berhasil disetujui dan denda telah dicatat!');
}
public function setujuiSemuaKembali()
{
    $data = \App\Models\Peminjaman::where('status', 'menunggu_konfirmasi')
        ->where('tipe_konfirmasi', 'kembali')
        ->get();

    $jumlah = 0;

    foreach ($data as $peminjaman) {

        $tanggalKembali = \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
        $hariIni = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();

        $hitungDenda = 0;

        if ($hariIni->gt($tanggalKembali)) {
            $selisihHari = ceil(abs($hariIni->diffInDays($tanggalKembali)));
            $hitungDenda = $selisihHari * 1000;
        }

        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_pengembalian' => $hariIni->toDateString(),
            'denda' => $hitungDenda
        ]);

        if ($buku = \App\Models\Buku::find($peminjaman->buku_id)) {
            $buku->increment('stok');
        }

        $jumlah++;
    }

    return back()->with(
        'success',
        $jumlah . ' pengembalian berhasil disetujui.'
    );
}
}