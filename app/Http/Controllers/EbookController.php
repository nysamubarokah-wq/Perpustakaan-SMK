<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;

class EbookController extends Controller
{
    public function index()
{
    $ebooks = Ebook::latest()->paginate(12);
    return view('ebook.index', compact('ebooks'));
}

public function show($id)
{
    $ebook = Ebook::findOrFail($id);
    $user = auth()->user();
    $bisaDiakses = $ebook->bisaDiakses($user);
    return view('ebook.show', compact('ebook', 'bisaDiakses'));
}

public function baca($id)
{
    $ebook = Ebook::findOrFail($id);
    $user = auth()->user();

    // Cek akses
    if (!$ebook->bisaDiakses($user)) {
        return redirect()->route('ebook.show', $id)
                         ->with('error', 'Kamu belum punya akses ke e-book ini!');
    }

    $pdfUrl = asset($ebook->file_pdf);
    return view('ebook.baca', compact('ebook', 'pdfUrl'));
}

    // ===== ADMIN =====
    public function adminIndex()
    {
        $ebooks = Ebook::latest()->paginate(15);
        return view('admin.ebook.index', compact('ebooks'));
    }

    public function adminCreate()
    {
        return view('admin.ebook.create');
    }

    public function adminEdit($id)
{
    $ebook = Ebook::findOrFail($id);

    return view('admin.ebook.edit', compact('ebook'));
}

public function adminUpdate(Request $request, $id)
{
    $ebook = Ebook::findOrFail($id);

    $request->validate([
        'judul'      => 'required|string|max:255',
        'penulis'    => 'required|string|max:255',
        'sinopsis'   => 'nullable|string',
        'file_pdf'   => 'nullable|mimes:pdf|max:20480',
        'cover'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'harga_koin' => 'required|integer|min:0',
        'is_vip'     => 'nullable|boolean',
    ]);

    // Update data
    $ebook->judul = $request->judul;
    $ebook->penulis = $request->penulis;
    $ebook->sinopsis = $request->sinopsis;
    $ebook->harga_koin = $request->harga_koin;
    $ebook->is_vip = $request->boolean('is_vip');

    // Ganti PDF jika dipilih
    if ($request->hasFile('file_pdf')) {

        if ($ebook->file_pdf && file_exists(public_path($ebook->file_pdf))) {
            unlink(public_path($ebook->file_pdf));
        }

        $pdfFile = $request->file('file_pdf');
        $pdfName = 'ebook_' . time() . '.' . $pdfFile->getClientOriginalExtension();
        $pdfFile->move(public_path('ebooks/pdf'), $pdfName);

        $ebook->file_pdf = 'ebooks/pdf/' . $pdfName;
    }

    // Ganti Cover jika dipilih
    if ($request->hasFile('cover')) {

        if ($ebook->cover && file_exists(public_path($ebook->cover))) {
            unlink(public_path($ebook->cover));
        }

        $coverFile = $request->file('cover');
        $coverName = 'cover_' . time() . '.' . $coverFile->getClientOriginalExtension();
        $coverFile->move(public_path('ebooks/cover'), $coverName);

        $ebook->cover = 'ebooks/cover/' . $coverName;
    }

    $ebook->save();

    return redirect()
            ->route('admin.ebook.index')
            ->with('success','E-book berhasil diperbarui!');
}
    public function adminStore(Request $request)
    {
        $request->validate([
            'judul'      => 'required|string|max:255',
            'penulis'    => 'required|string|max:255',
            'sinopsis'   => 'nullable|string',
            'file_pdf'   => 'required|mimes:pdf|max:20480', // max 20MB
            'cover'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'harga_koin' => 'required|integer|min:0',
            'is_vip'     => 'nullable|boolean',
        ]);

        // Upload PDF
        $pdfFile = $request->file('file_pdf');
        $pdfName = 'ebook_' . time() . '.' . $pdfFile->getClientOriginalExtension();
        $pdfFile->move(public_path('ebooks/pdf'), $pdfName);

        // Upload Cover (opsional)
        $coverName = null;
        if ($request->hasFile('cover')) {
            $coverFile = $request->file('cover');
            $coverName = 'cover_' . time() . '.' . $coverFile->getClientOriginalExtension();
            $coverFile->move(public_path('ebooks/cover'), $coverName);
        }

        Ebook::create([
            'judul'      => $request->judul,
            'penulis'    => $request->penulis,
            'sinopsis'   => $request->sinopsis,
            'file_pdf'   => 'ebooks/pdf/' . $pdfName,
            'cover'      => $coverName ? 'ebooks/cover/' . $coverName : null,
            'harga_koin' => $request->harga_koin,
            'is_vip'     => $request->boolean('is_vip'),
        ]);

        return redirect()->route('admin.ebook.index')
                         ->with('success', 'E-book berhasil ditambahkan!');
    }

    public function adminDestroy($id)
    {
        $ebook = Ebook::findOrFail($id);

        // Hapus file PDF & cover dari public
        if ($ebook->file_pdf && file_exists(public_path($ebook->file_pdf))) {
            unlink(public_path($ebook->file_pdf));
        }
        if ($ebook->cover && file_exists(public_path($ebook->cover))) {
            unlink(public_path($ebook->cover));
        }

        $ebook->delete();

        return back()->with('success', 'E-book berhasil dihapus!');
    }
    public function beliDenganKoin($id)
{
    $user = auth()->user();
    $ebook = Ebook::findOrFail($id);

    if ($user->coin < $ebook->harga_koin) {
        return back()->with('error', 'Koin tidak cukup!');
    }

    // Potong koin
    $user->decrement('coin', $ebook->harga_koin);

    // Catat akses (buat tabel ebook_access dulu kalau belum ada)
    \App\Models\EbookAccess::firstOrCreate([
        'user_id'  => $user->id,
        'ebook_id' => $ebook->id,
    ]);

    return redirect()->route('ebook.baca', $ebook->id)
                     ->with('success', 'Berhasil! Selamat membaca 📖');
}
}