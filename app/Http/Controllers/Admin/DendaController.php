<?php

namespace App\Http\Controllers\Admin;

use App\Models\Denda;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DendaController extends Controller
{
    public function index()
    {
        $dendas = Denda::with(['peminjaman.anggota', 'peminjaman.buku'])
            ->orderByDesc('created_at')
            ->get();

        $totalBelumBayar = Denda::where('status', 'belum_dibayar')->sum('jumlah_denda');
        $totalSudahBayar = Denda::where('status', 'sudah_dibayar')->sum('jumlah_denda');

        return view('admin.denda.index', compact('dendas', 'totalBelumBayar', 'totalSudahBayar'));
    }

    public function lunasi($id)
    {
        $denda = Denda::findOrFail($id);
        $denda->update([
            'status' => 'sudah_dibayar',
            'tanggal_bayar' => now()->toDateString()
        ]);

        return redirect()->back()->with('success', 'Denda berhasil dilunasi!');
    }

    public function lunasiSemua()
    {
        Denda::where('status', 'belum_dibayar')->update([
            'status' => 'sudah_dibayar',
            'tanggal_bayar' => now()->toDateString()
        ]);

        return redirect()->back()->with('success', 'Semua denda berhasil dilunasi!');
    }
}