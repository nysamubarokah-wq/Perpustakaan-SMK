<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Favorit;
use App\Models\Genre;
use App\Models\Penerbit;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Cache;

class KoleksiController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->get('search', '');
        $genre    = $request->get('genre', '');
        $penerbit = $request->get('penerbit', '');
        $sort     = $request->get('sort', '');

        $query = Buku::query()
            ->withCount('peminjaman');

        if ($genre) {
            $query->where('genre_id', $genre);
        }
        if ($penerbit) {
            $query->where('penerbit_id', $penerbit);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                  ->orWhere('pengarang', 'like', "%$search%");
            });
        }

        if ($sort === 'terbaru') {
            $query->latest();
        } elseif ($sort === 'populer') {
            $query->orderByDesc('peminjaman_count');
        } else {
            $query->orderByDesc('peminjaman_count')
                  ->orderByDesc('created_at');
        }

        $buku = $query->get();

        $favoritIds = auth()->check()
            ? Favorit::where('user_id', auth()->id())->pluck('buku_id')->toArray()
            : [];

        $penerbitList = Penerbit::whereHas('buku')->orderBy('nama')->get();
        $genreList = Genre::whereHas('buku')->orderBy('nama')->get();

        $populerQuery = Buku::withCount('peminjaman')
            ->orderByDesc('peminjaman_count');
        if ($genre) {
            $populerQuery->where('genre_id', $genre);
        }
        if ($penerbit) {
            $populerQuery->where('penerbit_id', $penerbit);
        }
        $bukuPopuler = $populerQuery->take(10)->get();

        $terbaruQuery = Buku::latest();
        if ($genre) {
            $terbaruQuery->where('genre_id', $genre);
        }
        if ($penerbit) {
            $terbaruQuery->where('penerbit_id', $penerbit);
        }
        $bukuTerbaru = $terbaruQuery->take(10)->get();

        $bukuAcak = Buku::inRandomOrder()
            ->take(8)
            ->get();

        $allBuku = Buku::withCount('peminjaman')->get();

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

        return view('koleksi.index', compact(
            'buku', 'search', 'genre', 'penerbit', 'sort', 'favoritIds',
            'bukuPopuler', 'bukuTerbaru', 'penerbitList', 'genreList', 'bukuAcak', 'allBuku', 'rekomendasi'
        ));
    }

    public function populer(Request $request)
    {
        $penerbit = $request->get('penerbit', '');
        $genre    = $request->get('genre', '');

        $query = Buku::withCount('peminjaman')
            ->having('peminjaman_count', '>', 0)
            ->orderByDesc('peminjaman_count')
            ->take(10);

        if ($penerbit) {
            $query->where('penerbit_id', $penerbit);
        }
        if ($genre) {
            $query->where('genre_id', $genre);
        }

        $buku = $query->get();

        $favoritIds = auth()->check()
            ? Favorit::where('user_id', auth()->id())->pluck('buku_id')->toArray()
            : [];

        $penerbitList = Penerbit::whereHas('buku')->orderBy('nama')->get();
        $genreList = Genre::whereHas('buku')->orderBy('nama')->get();

        return view('koleksi.populer', compact(
            'buku', 'penerbit', 'genre', 'favoritIds', 'penerbitList', 'genreList'
        ));
    }

    public function terbaru(Request $request)
    {
        $search   = $request->get('search', '');
        $penerbit = $request->get('penerbit', '');
        $genre    = $request->get('genre', '');

        $query = Buku::withCount('peminjaman')->latest();

        if ($penerbit) {
            $query->where('penerbit_id', $penerbit);
        }
        if ($genre) {
            $query->where('genre_id', $genre);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                  ->orWhere('pengarang', 'like', "%$search%");
            });
        }

        $buku = $query->get();

        $favoritIds = auth()->check()
            ? Favorit::where('user_id', auth()->id())->pluck('buku_id')->toArray()
            : [];

        $penerbitList = Penerbit::whereHas('buku')->orderBy('nama')->get();
        $genreList = Genre::whereHas('buku')->orderBy('nama')->get();

        return view('koleksi.terbaru', compact(
            'buku', 'search', 'penerbit', 'genre', 'favoritIds', 'penerbitList', 'genreList'
        ));
    }
}
