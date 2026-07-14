<?php

namespace App\Http\Controllers;

use App\Models\Favorit;
use App\Models\Buku;

class FavoritController extends Controller
{
    public function index()
    {
        $favorit = Favorit::with('buku.genre')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('favorit.index', compact('favorit'));
    }

    public function toggle(Buku $buku)
{
    $existing = Favorit::where('user_id', auth()->id())
        ->where('buku_id', $buku->id)
        ->first();

    if ($existing) {
        $existing->delete();
        $status = 'removed';
        $pesan = 'dihapus dari';
    } else {
        Favorit::create([
            'user_id' => auth()->id(),
            'buku_id' => $buku->id,
        ]);
        $status = 'added';
        $pesan = 'ditambahkan ke';
    }

    if (request()->wantsJson()) {
        return response()->json([
            'status' => $status,
            'message' => "\"{$buku->judul}\" berhasil {$pesan} favorit!",
        ]);
    }

    return back()->with('success', "\"{$buku->judul}\" berhasil {$pesan} favorit!");
}
}