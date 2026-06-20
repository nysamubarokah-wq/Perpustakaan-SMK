<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
   public function index(Request $request)
{
    $search = $request->get('search');
    
    $buku = Buku::query()
        ->when($search, function($q) use ($search) {
            $q->where('judul', 'like', "%$search%")
              ->orWhere('pengarang', 'like', "%$search%")
              ->orWhere('isbn', 'like', "%$search%")
              ->orWhere('genre', 'like', "%$search%");
        })
        ->get();

    return view('buku.index', compact('buku', 'search'));
}

    public function create()
    {
        return view('buku.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'judul'        => 'required',
        'pengarang'    => 'required',
        'penerbit'     => 'required',
        'tahun_terbit' => 'required|digits:4',
        'isbn'         => 'required|unique:buku',
        'stok'         => 'required|integer|min:0',
        'sampul'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data = $request->all();

    if ($request->hasFile('sampul')) {
        $file = $request->file('sampul');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/buku'), $filename);
        $data['sampul'] = 'images/buku/' . $filename;
    }

    Buku::create($data);

    return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan!');
}

public function edit(Buku $buku)
{
    return view('buku.edit', compact('buku'));
}

public function update(Request $request, Buku $buku)
{
    $request->validate([
        'judul'        => 'required',
        'pengarang'    => 'required',
        'penerbit'     => 'required',
        'tahun_terbit' => 'required|digits:4',
        'isbn'         => 'required|unique:buku,isbn,' . $buku->id,
        'stok'         => 'required|integer|min:0',
        'sampul'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data = $request->all();

    if ($request->hasFile('sampul')) {
        if ($buku->sampul && file_exists(public_path($buku->sampul))) {
            unlink(public_path($buku->sampul));
        }
        $file = $request->file('sampul');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/buku'), $filename);
        $data['sampul'] = 'images/buku/' . $filename;
    }

    $buku->update($data);

    return redirect()->route('buku.index')->with('success', 'Buku berhasil diupdate!');
}

    public function destroy(Buku $buku)
    {
        $buku->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus!');
    }
}