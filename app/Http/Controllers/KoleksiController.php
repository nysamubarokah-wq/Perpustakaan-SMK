<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Favorit;
use Illuminate\Http\Request;
use App\Models\Peminjaman;

class KoleksiController extends Controller
{
 public function index(Request $request)
{
    
   $search   = $request->get('search');
$genre    = is_array($request->get('genre')) ? $request->get('genre')[0] : $request->get('genre');
$penerbit = is_array($request->get('penerbit')) ? $request->get('penerbit')[0] : $request->get('penerbit');
$sort     = is_array($request->get('sort')) ? $request->get('sort')[0] : $request->get('sort');

    $buku = Buku::query()
    ->withCount('peminjaman')
        ->when($search, function ($q) use ($search) {
            $q->where('judul', 'like', "%$search%")
              ->orWhere('pengarang', 'like', "%$search%")
              ->orWhere('genre', 'like', "%$search%");
        })
        ->when($genre, function ($q) use ($genre) {
            $q->where('genre', $genre);
        })
        ->when($penerbit, function ($q) use ($penerbit) {
            $q->where('penerbit', $penerbit);
        })
        ->when($sort === 'terbaru', function ($q) { // ← tambah ini
            $q->latest();
        })
        ->when($sort === 'populer', function ($q) { // ← tambah ini
            $q->withCount('peminjaman')->orderByDesc('peminjaman_count');
        })
        ->when(!$sort, function ($q) {
    $q->orderByDesc('peminjaman_count')
      ->orderByDesc('created_at');
})
        ->get();

    $favoritIds = auth()->check()
        ? Favorit::where('user_id', auth()->id())->pluck('buku_id')->toArray()
        : [];

    $bukuPopuler = Buku::withCount('peminjaman')
        ->orderByDesc('peminjaman_count')
        ->take(8)
        ->get();

    $bukuTerbaru = Buku::latest()
        ->take(8)
        ->get();

    $penerbitList = Buku::select('penerbit')
        ->whereNotNull('penerbit')
        ->distinct()
        ->orderBy('penerbit')
        ->pluck('penerbit');

    $bukuAcak = Buku::inRandomOrder()
        ->take(8)
        ->get();

        

    return view('koleksi.index', compact(
        'buku', 'search', 'genre', 'penerbit', 'sort', 'favoritIds',
        'bukuPopuler', 'bukuTerbaru', 'penerbitList', 'bukuAcak'
    ));
}
}