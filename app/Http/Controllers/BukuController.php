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

    public function hapusBanyak(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return back()->with('error', 'Tidak ada buku yang dipilih.');
    }

    $jumlah = Buku::whereIn('id', $ids)->count();
    Buku::whereIn('id', $ids)->delete();

    return back()->with('success', "$jumlah buku berhasil dihapus.");
}
    public function import(Request $request)
{
    $request->validate(['file' => 'required|mimes:csv,txt']);

    $file = fopen($request->file('file')->getRealPath(), 'r');
    fgetcsv($file); // skip header
    $berhasil = 0;
    $dilewati = 0;
    $gagalGambar = 0;

    // Pastikan folder ada
    if (!file_exists(public_path('images/buku'))) {
        mkdir(public_path('images/buku'), 0755, true);
    }

    while ($row = fgetcsv($file)) {
        if (count($row) < 6) continue;

        $isbn = trim($row[4]);

        if (Buku::where('isbn', $isbn)->exists()) {
            $dilewati++;
            continue;
        }

        // Download gambar dari URL kalau ada
        $sampulPath = null;
        $urlGambar = trim($row[9] ?? '');

        if ($urlGambar && filter_var($urlGambar, FILTER_VALIDATE_URL)) {
            try {
                $konten = @file_get_contents($urlGambar);
                if ($konten) {
                    // Deteksi ekstensi dari URL atau default ke jpg
                    $ext = pathinfo(parse_url($urlGambar, PHP_URL_PATH), PATHINFO_EXTENSION);
                    $ext = in_array(strtolower($ext), ['jpg','jpeg','png','webp']) ? strtolower($ext) : 'jpg';
                    
                    $filename = 'buku_' . $isbn . '_' . time() . '.' . $ext;
                    file_put_contents(public_path('images/buku/' . $filename), $konten);
                    $sampulPath = 'images/buku/' . $filename;
                } else {
                    $gagalGambar++;
                }
            } catch (\Exception $e) {
                $gagalGambar++;
            }
        }

        Buku::create([
            'judul'        => trim($row[0]),
            'pengarang'    => trim($row[1]),
            'penerbit'     => trim($row[2]),
            'tahun_terbit' => trim($row[3]),
            'isbn'         => $isbn,
            'stok'         => (int) trim($row[5]),
            'genre'        => trim($row[6] ?? ''),
            'lokasi'       => trim($row[7] ?? ''),
            'deskripsi'    => trim($row[8] ?? ''),
            'sampul'       => $sampulPath,
        ]);
        $berhasil++;
    }

    fclose($file);

    $pesan = "$berhasil buku berhasil diimport.";
    if ($dilewati > 0) $pesan .= " $dilewati dilewati (ISBN duplikat).";
    if ($gagalGambar > 0) $pesan .= " $gagalGambar gambar gagal didownload.";

    return back()->with('success', $pesan);
}
}