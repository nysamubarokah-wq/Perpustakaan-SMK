<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Favorit;
use Illuminate\Http\Request;

class KoleksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $genre  = $request->get('genre');

        $buku = Buku::query()
            ->when($search, function ($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                  ->orWhere('pengarang', 'like', "%$search%")
                  ->orWhere('genre', 'like', "%$search%");
            })
            ->when($genre, function ($q) use ($genre) {
                $q->where('genre', $genre);
            })
            ->get();

        $favoritIds = auth()->check()
            ? Favorit::where('user_id', auth()->id())->pluck('buku_id')->toArray()
            : [];

        return view('koleksi.index', compact('buku', 'search', 'genre', 'favoritIds'));
    }
}