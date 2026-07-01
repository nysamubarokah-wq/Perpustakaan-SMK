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
    $search   = $request->get('search', '');
    $genre    = $request->get('genre', '');
    $penerbit = $request->get('penerbit', '');
    $sort     = $request->get('sort', '');

    $query = Buku::query()
    ->withCount('peminjaman');

if ($genre) {
    $query->where('genre', $genre);
}
if ($penerbit) {
    $query->where('penerbit', $penerbit);
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