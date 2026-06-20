<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Ulasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{

public function index()
{
    $ulasanList = Ulasan::with(['user', 'buku'])
        ->latest()
        ->get();

    return view('admin.ulasan.index', compact('ulasanList'));
}
    public function store(Request $request, Buku $buku)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $anggota = Anggota::where('email', $user->email)->first();

        $pernahPinjam = $anggota
            ? Peminjaman::where('anggota_id', $anggota->id)->where('buku_id', $buku->id)->exists()
            : false;

        if (!$pernahPinjam) {
            return back()->with('error', 'Kamu belum pernah meminjam buku ini, jadi belum bisa memberi ulasan.');
        }

        Ulasan::updateOrCreate(
            ['user_id' => $user->id, 'buku_id' => $buku->id],
            ['rating' => $request->rating, 'komentar' => $request->komentar]
        );

        return back()->with('success', 'Ulasan kamu berhasil disimpan. Terima kasih!');
    }

   public function destroy(Ulasan $ulasan)
{
    $isPemilik = $ulasan->user_id === auth()->id();
    $isAdmin = auth()->user()->role === 'admin';

    if (!$isPemilik && !$isAdmin) {
        abort(403);
    }

    $ulasan->delete();

    return back()->with('success', 'Ulasan berhasil dihapus.');
}
}