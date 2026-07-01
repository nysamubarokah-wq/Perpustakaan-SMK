<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\EksemplarBuku;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
   public function index(Request $request)
{
    $search = $request->get('search');
    
    $peminjaman = \App\Models\Peminjaman::with(['anggota', 'buku', 'eksemplar'])
        ->when($search, function($q) use ($search) {
            $q->whereHas('anggota', function($q2) use ($search) {
                $q2->where('nama', 'like', "%$search%");
            })->orWhereHas('buku', function($q2) use ($search) {
                $q2->where('judul', 'like', "%$search%");
            })->orWhereHas('eksemplar', function($q2) use ($search) {
                $q2->where('kode_buku', 'like', "%$search%");
            });
        })
        ->latest()
        ->get();

    return view('peminjaman.index', compact('peminjaman', 'search'));
}

    public function create()
    {
        $anggota = Anggota::all();
        $buku = Buku::whereHas('eksemplarTersedia')->get();
        return view('peminjaman.create', compact('anggota', 'buku'));
    }

    public function store(Request $request)
    {
        $buku = Buku::findOrFail($request->buku_id);

        // Cari eksemplar tersedia
        $eksemplar = $buku->eksemplarTersedia()->first();

        if (!$eksemplar) {
            return redirect()->back()->with('error', 'Gagal meminjam! Semua eksemplar buku "' . $buku->judul . '" sedang tidak tersedia.');
        }

        // Update status eksemplar
        $eksemplar->update(['status' => 'dipinjam']);

        // Update stok buku
        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        Peminjaman::create([
            'anggota_id' => $request->anggota_id,
            'buku_id'    => $request->buku_id,
            'eksemplar_id' => $eksemplar->id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali'=> $request->tanggal_kembali,
            'status'     => 'dipinjam', 
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman buku berhasil dicatat! Eksemplar: ' . $eksemplar->kode_buku);
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
            // Kembalikan status eksemplar
            if ($peminjaman->eksemplar) {
                $peminjaman->eksemplar->update(['status' => 'tersedia']);
            }
            // Update stok buku
            $buku = Buku::find($peminjaman->buku_id);
            if ($buku) {
                $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);
            }
        }

        $peminjaman->update($request->only('anggota_id', 'buku_id', 'tanggal_pinjam', 'tanggal_kembali', 'status'));

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil diupdate!');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        // Jika data dihapus tapi statusnya masih dipinjam, kembalikan eksemplar
        if ($peminjaman->status == 'dipinjam') {
            if ($peminjaman->eksemplar) {
                $peminjaman->eksemplar->update(['status' => 'tersedia']);
            }
            $buku = Buku::find($peminjaman->buku_id);
            if ($buku) {
                $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);
            }
        }

        $peminjaman->delete();
        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dihapus!');
    }

    public function konfirmasiSemua()
    {
        $pending = Peminjaman::with(['buku', 'anggota', 'eksemplar'])
            ->where('status', 'menunggu_konfirmasi')
            ->where('tipe_konfirmasi', 'pinjam')
            ->get();

        if ($pending->isEmpty()) {
            return back()->with('error', 'Tidak ada permintaan pinjam yang menunggu konfirmasi.');
        }

        foreach ($pending as $peminjaman) {
            // Pastikan eksemplar status dipinjam
            if ($peminjaman->eksemplar) {
                $peminjaman->eksemplar->update(['status' => 'dipinjam']);
            }
            $peminjaman->update(['status' => 'dipinjam']);

            // Update stok buku
            if ($peminjaman->buku) {
                $peminjaman->buku->update(['stok' => $peminjaman->buku->eksemplarTersedia()->count()]);
            }

            $user = \App\Models\User::where('email', $peminjaman->anggota->email)->first();
            if ($user) {
                $user->increment('coin', 10000);
            }
        }

        return back()->with('success', $pending->count() . ' permintaan peminjaman berhasil dikonfirmasi!');
    }

}
