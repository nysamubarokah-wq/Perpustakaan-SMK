<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Anggota;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalBuku       = Buku::count();
        $totalAnggota    = Anggota::count();
        $totalPeminjaman = Peminjaman::count();
        $sedangDipinjam  = Peminjaman::where('status', 'dipinjam')->count();
        $terlambat       = Peminjaman::where('status', 'dipinjam')
                            ->where('tanggal_kembali', '<', now()->toDateString())
                            ->count();

        // Peminjaman per bulan (6 bulan terakhir)
        $peminjamanPerBulan = Peminjaman::select(
                DB::raw('MONTH(tanggal_pinjam) as bulan'),
                DB::raw('YEAR(tanggal_pinjam) as tahun'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal_pinjam', now()->year)
            ->groupBy('tahun', 'bulan')
            ->orderBy('bulan')
            ->get();

        // Buku terpopuler
        $bukuPopuler = Buku::withCount('peminjaman')
                        ->orderBy('peminjaman_count', 'desc')
                        ->take(5)
                        ->get();

        // Peminjaman terbaru
        $peminjamanTerbaru = Peminjaman::with(['anggota', 'buku'])
                            ->latest()
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact(
            'totalBuku', 'totalAnggota', 'totalPeminjaman',
            'sedangDipinjam', 'terlambat', 'peminjamanPerBulan',
            'bukuPopuler', 'peminjamanTerbaru'
        ));
    }
}