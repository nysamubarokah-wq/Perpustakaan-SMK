<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\EksemplarBuku;
use App\Models\Genre;
use App\Models\Penerbit;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use ZipArchive;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $sortable = ['judul', 'pengarang', 'penerbit', 'isbn', 'genre', 'kode_buku', 'created_at', 'stok'];
        if (!in_array($sortBy, $sortable)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        $buku = Buku::query()
            ->withCount(['eksemplar', 'eksemplarTersedia'])
            ->with('genre')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('judul', 'like', "%$search%")
                        ->orWhere('pengarang', 'like', "%$search%")
                        ->orWhere('isbn', 'like', "%$search%")
                        ->orWhere('kode_buku', 'like', "%$search%")
                        ->orWhereHas('genre', fn($g) => $g->where('nama', 'like', "%$search%"));
                });
            })
            ->when($sortBy === 'genre', function ($q) use ($sortDir) {
                $q->whereHas('genre')
                  ->orderBy(Genre::select('nama')->whereColumn('genres.id', 'buku.genre_id'), $sortDir);
            }, fn($q) => $q->orderBy($sortBy, $sortDir))
            ->paginate($perPage);

        $buku->appends($request->query());

        session(['buku_index_params' => $request->query()]);

        return view('buku.index', compact('buku', 'search', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create()
    {
        $genres = Genre::orderBy('nama')->get();
        $penerbitList = Penerbit::orderBy('nama')->get();
        return view('buku.create', compact('genres', 'penerbitList'));
    }

    public function show(Buku $buku)
    {
        $buku->load('eksemplar', 'genre');
        return view('buku.show', compact('buku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'          => 'required',
            'pengarang'      => 'required',
            'penerbit_id'    => 'required_without:penerbit_baru',
            'penerbit_baru'  => 'required_without:penerbit_id',
            'tahun_terbit'   => 'required|digits:4',
            'isbn'           => 'required|unique:buku',
            'jumlah_eksemplar' => 'required|integer|min:1',
            'sampul'         => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'rekom_bg'       => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'kode_buku'      => 'nullable|unique:buku,kode_buku',
        ]);

        $data = $request->all();

        if (empty($data['kode_buku'])) {
            $data['kode_buku'] = Buku::generateKodeBuku();
        }

        if (!empty($data['genre_baru'])) {
            $genre = Genre::findOrCreate($data['genre_baru']);
            $data['genre_id'] = $genre->id;
        } else {
            $data['genre_id'] = $data['genre_id'] ?: null;
        }

        if (!empty($data['penerbit_baru'])) {
            $penerbit = Penerbit::findOrCreate($data['penerbit_baru']);
            $data['penerbit_id'] = $penerbit->id;
            $data['penerbit'] = $penerbit->nama;
        } elseif (!empty($data['penerbit_id'])) {
            $penerbit = Penerbit::find($data['penerbit_id']);
            $data['penerbit'] = $penerbit ? $penerbit->nama : '';
        }

        $data['stok'] = $request->jumlah_eksemplar;

        if ($request->hasFile('sampul')) {
            $file = $request->file('sampul');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/buku'), $filename);
            $data['sampul'] = 'images/buku/' . $filename;
        }

        if ($request->hasFile('rekom_bg')) {
            $file = $request->file('rekom_bg');
            $filename = 'bg_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/buku'), $filename);
            $data['rekom_bg'] = 'images/buku/' . $filename;
        }

        $buku = Buku::create($data);

        $jumlahEksemplar = (int) $request->jumlah_eksemplar;
        for ($i = 0; $i < $jumlahEksemplar; $i++) {
            EksemplarBuku::create([
                'buku_id'   => $buku->id,
                'kode_buku' => EksemplarBuku::generateKodeEksemplar(),
                'status'    => 'tersedia',
                'kondisi'   => 'baik',
            ]);
        }

        return redirect()->route('buku.index')->with('success', "Buku berhasil ditambahkan dengan {$jumlahEksemplar} eksemplar! QR Code dibuat otomatis.");
    }

    public function edit(Buku $buku)
    {
        $buku->load('eksemplar');
        $genres = Genre::orderBy('nama')->get();
        $penerbitList = Penerbit::orderBy('nama')->get();
        return view('buku.edit', compact('buku', 'genres', 'penerbitList'));
    }

    public function update(Request $request, Buku $buku)
    {
        $request->validate([
            'judul'        => 'required',
            'pengarang'    => 'required',
            'penerbit_id'  => 'required_without:penerbit_baru',
            'penerbit_baru'=> 'required_without:penerbit_id',
            'tahun_terbit' => 'required|digits:4',
            'isbn'         => 'required|unique:buku,isbn,' . $buku->id,
            'sampul'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'rekom_bg'     => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'kode_buku'    => 'nullable|unique:buku,kode_buku,' . $buku->id,
        ]);

        $data = $request->only(['judul', 'pengarang', 'tahun_terbit', 'isbn', 'deskripsi', 'lokasi', 'kode_buku', 'penerbit_id', 'genre_id']);

        if (!empty($request->genre_baru)) {
            $genre = Genre::findOrCreate($request->genre_baru);
            $data['genre_id'] = $genre->id;
        } else {
            $data['genre_id'] = $request->genre_id ?: null;
        }

        if (!empty($request->penerbit_baru)) {
            $penerbit = Penerbit::findOrCreate($request->penerbit_baru);
            $data['penerbit_id'] = $penerbit->id;
            $data['penerbit'] = $penerbit->nama;
        } elseif (!empty($request->penerbit_id)) {
            $penerbit = Penerbit::find($request->penerbit_id);
            $data['penerbit_id'] = $penerbit->id ?? null;
            $data['penerbit'] = $penerbit ? $penerbit->nama : '';
        }

        if ($request->hasFile('sampul')) {
            if ($buku->sampul && file_exists(public_path($buku->sampul))) {
                unlink(public_path($buku->sampul));
            }
            $file = $request->file('sampul');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/buku'), $filename);
            $data['sampul'] = 'images/buku/' . $filename;
        }

        if ($request->hasFile('rekom_bg')) {
            if ($buku->rekom_bg && file_exists(public_path($buku->rekom_bg))) {
                unlink(public_path($buku->rekom_bg));
            }
            $file = $request->file('rekom_bg');
            $filename = 'bg_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/buku'), $filename);
            $data['rekom_bg'] = 'images/buku/' . $filename;
        }

        $data['stok'] = $buku->eksemplarTersedia()->count();

        $buku->update($data);

        $params = $request->only(['search', 'sort', 'direction', 'per_page']);
        if (empty(array_filter($params))) {
            $params = session('buku_index_params', []);
        }

        return redirect()->route('buku.index', $params)->with('success', 'Buku berhasil diupdate!');
    }

    public function destroy(Request $request, Buku $buku)
    {
        $buku->delete();

        $params = $request->only(['search', 'sort', 'direction', 'per_page', 'page']);
        if (empty(array_filter($params))) {
            $params = session('buku_index_params', []);
        }

        $perPage = max(10, (int) ($params['per_page'] ?? 10));
        unset($params['page']);
        $totalAfter = Buku::count();
        $lastPage = max(1, (int) ceil($totalAfter / $perPage));
        $currentPage = (int) ($params['page'] ?? 1);
        if ($currentPage > $lastPage) {
            $params['page'] = $lastPage;
        }

        return redirect()->route('buku.index', $params)->with('success', 'Buku berhasil dihapus!');
    }

    public function hapusBanyak(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada buku yang dipilih.');
        }

        $jumlah = Buku::whereIn('id', $ids)->count();
        Buku::whereIn('id', $ids)->delete();

        $params = $request->only(['search', 'sort', 'direction', 'per_page', 'page']);
        if (empty(array_filter($params))) {
            $params = session('buku_index_params', []);
        }

        $perPage = max(10, (int) ($params['per_page'] ?? 10));
        unset($params['page']);
        $totalAfter = Buku::count();
        $lastPage = max(1, (int) ceil($totalAfter / $perPage));
        $currentPage = (int) ($params['page'] ?? 1);
        if ($currentPage > $lastPage) {
            $params['page'] = $lastPage;
        }

        return redirect()->route('buku.index', $params)->with('success', "$jumlah buku berhasil dihapus.");
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt']);

        $file = fopen($request->file('file')->getRealPath(), 'r');
        fgetcsv($file);
        $berhasil = 0;
        $dilewati = 0;
        $gagalGambar = 0;

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

            $sampulPath = null;
            $urlGambar = trim($row[9] ?? '');

            if ($urlGambar && filter_var($urlGambar, FILTER_VALIDATE_URL)) {
                try {
                    $konten = @file_get_contents($urlGambar);
                    if ($konten) {
                        $ext = pathinfo(parse_url($urlGambar, PHP_URL_PATH), PATHINFO_EXTENSION);
                        $ext = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']) ? strtolower($ext) : 'jpg';

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

            $jumlahEksemplar = max(1, (int) trim($row[5]));

            $penerbitNama = trim($row[2]);
            $penerbit = null;
            if ($penerbitNama) {
                $penerbit = \App\Models\Penerbit::firstOrCreate(['nama' => $penerbitNama]);
            }

            $genreNama = trim($row[6] ?? '');
            $genre = null;
            if ($genreNama) {
                $genre = \App\Models\Genre::firstOrCreate(['nama' => $genreNama]);
            }

            $buku = Buku::create([
                'judul'        => trim($row[0]),
                'pengarang'    => trim($row[1]),
                'penerbit'     => $penerbitNama,
                'penerbit_id'  => $penerbit?->id,
                'tahun_terbit' => trim($row[3]),
                'isbn'         => $isbn,
                'stok'         => $jumlahEksemplar,
                'genre_id'     => $genre?->id,
                'lokasi'       => trim($row[7] ?? ''),
                'deskripsi'    => trim($row[8] ?? ''),
                'sampul'       => $sampulPath,
            ]);

            for ($i = 0; $i < $jumlahEksemplar; $i++) {
                EksemplarBuku::create([
                    'buku_id'   => $buku->id,
                    'kode_buku' => EksemplarBuku::generateKodeEksemplar(),
                    'status'    => 'tersedia',
                    'kondisi'   => 'baik',
                ]);
            }

            $berhasil++;
        }

        fclose($file);

        $userId = auth()->id();

        if ($berhasil > 0) {
            NotifikasiService::importBerhasil($userId, 'buku', $berhasil);
        }

        if ($gagalGambar > 0 && $berhasil == 0) {
            NotifikasiService::importGagal($userId, 'buku', "Format tidak valid atau file corrupted. {$gagalGambar} gambar gagal.");
            return redirect()->route('buku.index')->with('error', "Import gagal. {$gagalGambar} gambar gagal didownload.");
        }

        $pesan = "$berhasil buku berhasil diimport.";
        if ($dilewati > 0) $pesan .= " $dilewati dilewati (ISBN duplikat).";
        if ($gagalGambar > 0) $pesan .= " $gagalGambar gambar gagal didownload.";

        return redirect()->route('buku.index')->with('success', $pesan);
    }

    public function tambahEksemplar(Request $request, Buku $buku)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1|max:100',
        ]);

        $jumlah = (int) $request->jumlah;

        for ($i = 0; $i < $jumlah; $i++) {
            EksemplarBuku::create([
                'buku_id'   => $buku->id,
                'kode_buku' => EksemplarBuku::generateKodeEksemplar(),
                'status'    => 'tersedia',
                'kondisi'   => 'baik',
            ]);
        }

        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        return back()->with('success', "{$jumlah} eksemplar berhasil ditambahkan ke buku \"{$buku->judul}\".");
    }

    public function updateEksemplarStatus(Request $request, EksemplarBuku $eksemplar)
    {
        $request->validate([
            'status'  => 'required|in:tersedia,dipinjam,rusak,hilang,maintenance',
            'kondisi' => 'nullable|in:baik,sedang,rusak_ringan,rusak_berat',
        ]);

        $eksemplar->update([
            'status'  => $request->status,
            'kondisi' => $request->kondisi ?? $eksemplar->kondisi,
        ]);

        $buku = $eksemplar->buku;
        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        return back()->with('success', "Status eksemplar {$eksemplar->kode_buku} berhasil diupdate.");
    }

    public function hapusEksemplar(EksemplarBuku $eksemplar)
    {
        if ($eksemplar->status === 'dipinjam') {
            return back()->with('error', 'Tidak bisa menghapus eksemplar yang sedang dipinjam.');
        }

        $buku = $eksemplar->buku;
        $kode = $eksemplar->kode_buku;
        $eksemplar->delete();

        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        return back()->with('success', "Eksemplar {$kode} berhasil dihapus.");
    }

    public function qrcode(Buku $buku)
    {
        if (!$buku->qr_exists) {
            $buku->generateQr();
        }

        return response()->json([
            'status'      => 'success',
            'kode_buku'   => $buku->kode_buku,
            'judul'       => $buku->judul,
            'isbn'        => $buku->isbn,
            'qrcode_url'  => asset($buku->qrcode_path),
        ]);
    }

    public function qrcodeDownload(Buku $buku)
    {
        if (!$buku->qr_exists) {
            $buku->generateQr();
        }

        $path = public_path($buku->qrcode_path);
        return response()->download($path, 'QR_' . $buku->kode_buku . '.svg');
    }

    public function qrcodePrint(Buku $buku)
    {
        if (!$buku->qr_exists) {
            $buku->generateQr();
        }

        return view('buku.qr-print', [
            'buku' => $buku,
            'svg'  => file_get_contents(public_path($buku->qrcode_path)),
        ]);
    }

    public function generateAllQr()
    {
        $semuaBuku = Buku::all();
        $dibuat = 0;
        $dilewati = 0;

        foreach ($semuaBuku as $buku) {
            if (!empty($buku->qrcode_path) && file_exists(public_path($buku->qrcode_path))) {
                $dilewati++;
                continue;
            }

            if (empty($buku->kode_buku)) {
                $buku->kode_buku = Buku::generateKodeBuku();
                $buku->saveQuietly();
            }

            $buku->generateQr();
            $dibuat++;
        }

        return back()->with('success', "Generate selesai! {$dibuat} QR Code dibuat, {$dilewati} dilewati.");
    }

    public function downloadAllQr()
    {
        $bukuList = Buku::whereNotNull('qrcode_path')->get();

        if ($bukuList->isEmpty()) {
            return back()->with('error', 'Belum ada QR Code yang tersedia.');
        }

        $zipPath = public_path('qrcode/all_buku_qr_' . date('Ymd_His') . '.zip');

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        foreach ($bukuList as $buku) {
            $filePath = public_path($buku->qrcode_path);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, 'QR_' . $buku->kode_buku . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $buku->judul) . '.svg');
            }
        }

        $zip->close();

        return response()->download($zipPath, 'Semua_QR_Buku_' . date('Ymd') . '.zip')->deleteFileAfterSend(true);
    }

    public function cetakSemuaQr()
    {
        $bukuList = Buku::whereNotNull('qrcode_path')->get();

        if ($bukuList->isEmpty()) {
            return back()->with('error', 'Belum ada QR Code.');
        }

        $items = [];
        foreach ($bukuList as $buku) {
            $filePath = public_path($buku->qrcode_path);
            if (file_exists($filePath)) {
                $items[] = [
                    'buku' => $buku,
                    'svg'  => file_get_contents($filePath),
                ];
            }
        }

        return view('buku.qr-print-all', compact('items'));
    }
}
