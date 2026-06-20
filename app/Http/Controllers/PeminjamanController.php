<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
   public function index(Request $request)
{
    $search = $request->get('search');
    
    $peminjaman = \App\Models\Peminjaman::with(['anggota', 'buku'])
        ->when($search, function($q) use ($search) {
            $q->whereHas('anggota', function($q2) use ($search) {
                $q2->where('nama', 'like', "%$search%");
            })->orWhereHas('buku', function($q2) use ($search) {
                $q2->where('judul', 'like', "%$search%");
            });
        })
        ->latest()
        ->get();

    return view('peminjaman.index', compact('peminjaman', 'search'));
}

    public function create()
    {
        $anggota = Anggota::all();
        $buku = Buku::where('stok', '>', 0)->get();
        return view('peminjaman.create', compact('anggota', 'buku'));
    }

    public function store(Request $request)
    {
        // 1. Ambil data buku yang ingin dipinjam
        $buku = Buku::findOrFail($request->buku_id);

        // 2. VALIDASI: Cek apakah stok buku masih tersedia
        if ($buku->stok <= 0) {
            return redirect()->back()->with('error', 'Gagal meminjam! Stok buku "' . $buku->judul . '" sudah habis.');
        }

        // 3. Simpan data ke tabel peminjaman
        Peminjaman::create([
            'anggota_id' => $request->anggota_id,
            'buku_id'    => $request->buku_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali'=> $request->tanggal_kembali,
            'status'     => 'dipinjam', 
        ]);

        // 4. OTOMATISASI: Kurangi stok buku sebanyak 1
        $buku->decrement('stok'); 

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman buku berhasil dicatat dan stok berkurang!');
    }

    public function edit(Peminjaman $peminjaman)
    {
        $anggota = Anggota::all();
        $buku = Buku::all();
        return view('peminjaman.edit', compact('peminjaman', 'anggota', 'buku'));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'anggota_id'     => 'required',
            'buku_id'        => 'required',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali'=> 'required|date|after:tanggal_pinjam',
            'status'         => 'required',
        ]);

        // Jika status diubah manual lewat edit oleh admin jadi dikembalikan
        if ($request->status == 'dikembalikan' && $peminjaman->status == 'dipinjam') {
            $buku = Buku::find($peminjaman->buku_id);
            $buku->increment('stok');
        }

        $peminjaman->update($request->all());

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil diupdate!');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        // Jika data dihapus tapi statusnya masih dipinjam, kembalikan stoknya
        if ($peminjaman->status == 'dipinjam') {
            $buku = Buku::find($peminjaman->buku_id);
            $buku->increment('stok');
        }

        $peminjaman->delete();
        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dihapus!');
    }

 public function persetujuanIndex()
{
    $persetujuan = Peminjaman::with(['anggota', 'buku'])
        ->where('status', 'menunggu_konfirmasi')
        ->latest()
        ->get();

    $tarifDendaPerHari = 1000; 

    foreach ($persetujuan as $item) {
        // Ambil string tanggal saja (format: Y-m-d seperti 2026-06-10)
        $tglKembaliString = Carbon::parse($item->tanggal_kembali)->toDateString();
        $tglHariIniString = Carbon::now('Asia/Jakarta')->toDateString();

        // Lakukan pembandingan: jika hari ini secara tanggal lebih besar dari tanggal kembali
        if ($tglHariIniString > $tglKembaliString) {
            // Hitung selisih harinya secara bersih
            $tanggalKembali = Carbon::parse($tglKembaliString);
            $hariIni = Carbon::parse($tglHariIniString);
            
            $selisihHari = $hariIni->diffInDays($tanggalKembali);
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
    $peminjaman = Peminjaman::findOrFail($id);
    
    if ($peminjaman->status === 'menunggu_konfirmasi') {
        $tglKembaliString = Carbon::parse($peminjaman->tanggal_kembali)->toDateString();
        $tglHariIniString = Carbon::now('Asia/Jakarta')->toDateString();
        $hitungDenda = 0;

        if ($tglHariIniString > $tglKembaliString) {
            $tanggalKembali = Carbon::parse($tglKembaliString);
            $hariIni = Carbon::parse($tglHariIniString);
            
            $selisihHari = $hariIni->diffInDays($tanggalKembali);
            $tarifDendaPerHari = 1000;
            $hitungDenda = $selisihHari * $tarifDendaPerHari;
        }
        
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_pengembalian' => $tglHariIniString,
            'denda' => $hitungDenda
        ]);

        $buku = Buku::findOrFail($peminjaman->buku_id);
        $buku->increment('stok'); 
    }

    return redirect()->back()->with('success', 'Pengembalian buku berhasil disetujui dan denda telah dicatat!');
}
}