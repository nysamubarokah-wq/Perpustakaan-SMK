<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Favorit;
use App\Models\Genre;
use App\Models\Peminjaman;
use App\Models\Penerbit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalBuku       = Buku::count();
        $totalAnggota    = Anggota::count();
        $totalPeminjaman = Peminjaman::count();
        $sedangDipinjam  = Peminjaman::where('status', 'dipinjam')->count();
        $genre = $request->get('genre');

        $genreList = Genre::whereHas('buku')->orderBy('nama')->get();
        $penerbitList = Penerbit::whereHas('buku')->orderBy('nama')->get();

        $bukuPopuler = Buku::withCount('peminjaman')
            ->orderByDesc('peminjaman_count')
            ->take(10)
            ->get();

        $bukuTerbaru = Buku::latest()->take(10)->get();

        $allBuku = Buku::withCount('peminjaman')->get();

        $favoritIds = auth()->check()
            ? Favorit::where('user_id', auth()->id())->pluck('buku_id')->toArray()
            : [];

        $cacheKey = 'rekomendasi_hari_ini_' . now()->format('Y-m-d');
        $rekomendasiIds = Cache::remember($cacheKey, now()->addDay(), function () {
            $populer = Buku::withCount('peminjaman')
                ->having('peminjaman_count', '>', 0)
                ->orderByDesc('peminjaman_count')
                ->take(3)
                ->pluck('id');

            if ($populer->count() >= 3) {
                return $populer->toArray();
            }

            $terbaru = Buku::latest()->take(3)->pluck('id');
            if ($terbaru->count() >= 3) {
                return $terbaru->toArray();
            }

            return Buku::inRandomOrder()->take(3)->pluck('id')->toArray();
        });

        $rekomendasi = Buku::whereIn('id', $rekomendasiIds)
            ->withCount('peminjaman')
            ->get()
            ->sortBy(fn ($buku) => array_search($buku->id, $rekomendasiIds))
            ->values();

        return view('dashboard', compact(
            'totalBuku',
            'totalAnggota',
            'totalPeminjaman',
            'sedangDipinjam',
            'genre',
            'genreList',
            'penerbitList',
            'bukuPopuler',
            'bukuTerbaru',
            'allBuku',
            'favoritIds',
            'rekomendasi'
        ));
    }
}
