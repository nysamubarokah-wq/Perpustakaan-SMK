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

        // Cari atau buat anggota otomatis dari user login
        $anggota = \App\Models\Anggota::where('email', auth()->user()->email)->first();

        if (!$anggota) {
            // Buat data anggota otomatis jika belum terdaftar
            $anggota = \App\Models\Anggota::create([
                'nama'           => auth()->user()->name,
                'email'          => auth()->user()->email,
                'no_telepon'     => '-',
                'alamat'         => '-',
                'tanggal_daftar' => now()->toDateString(),
            ]);
        }

        // Buat data peminjaman
        Peminjaman::create([
            'anggota_id'      => $anggota->id,
            'buku_id'         => $buku->id,
            'tanggal_pinjam'  => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status'          => 'dipinjam',
        ]);

        // Kurangi stok buku
        $buku->stok -= 1;
        $buku->save();
        // Tambah coin +5 setiap pinjam buku
        auth()->user()->increment('coin', 10);

        return redirect()->route('koleksi.index')->with('success', 'Buku berhasil dipinjam!');
    }

    public function persetujuanIndex()
{
    $persetujuan = \App\Models\Peminjaman::with(['anggota', 'buku'])
        ->where('status', 'menunggu_konfirmasi')
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
}