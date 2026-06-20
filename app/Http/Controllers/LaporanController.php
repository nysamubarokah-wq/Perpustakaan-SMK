<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\Anggota;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function exportPdf()
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->orderBy('created_at', 'desc')->get();
        $totalBuku = Buku::count();
        $totalAnggota = Anggota::count();
        $totalPeminjaman = Peminjaman::count();
        $sedangDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        $terlambat = Peminjaman::where('status', 'dipinjam')
                        ->where('tanggal_kembali', '<', now()->toDateString())
                        ->count();

        $pdf = Pdf::loadView('admin.laporan-pdf', compact(
            'peminjaman', 'totalBuku', 'totalAnggota',
            'totalPeminjaman', 'sedangDipinjam', 'terlambat'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-peminjaman-'.now()->format('d-m-Y').'.pdf');
    }
}