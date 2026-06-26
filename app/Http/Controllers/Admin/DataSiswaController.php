<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataSiswa;
use Illuminate\Http\Request;

class DataSiswaController extends Controller
{
  public function index(Request $request)
{
    $search = $request->get('search');

    $siswa = DataSiswa::query()
        ->when($search, function($q) use ($search) {
            $q->where('nis', 'like', "%$search%")
              ->orWhere('nama', 'like', "%$search%")
              ->orWhere('kelas', 'like', "%$search%");
        })
        ->orderBy('kelas')
        ->orderBy('nama')
        ->paginate(15);

    return view('admin.data-siswa.index', compact('siswa'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nis'   => ['required', 'string', 'unique:data_siswa'],
            'nama'  => ['required', 'string'],
            'kelas' => ['nullable', 'string'],
        ]);

        DataSiswa::create($request->only('nis', 'nama', 'kelas'));

        return back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function destroy(DataSiswa $dataSiswa)
    {
        $dataSiswa->delete();
        return back()->with('success', 'Data siswa dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt']);

        $file = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($file); // skip baris pertama (header)
        $berhasil = 0;

        while ($row = fgetcsv($file)) {
            if (count($row) >= 2) {
                DataSiswa::updateOrCreate(
                    ['nis' => trim($row[0])],
                    ['nama' => trim($row[1]), 'kelas' => trim($row[2] ?? '')]
                );
                $berhasil++;
            }
        }

        fclose($file);
        return back()->with('success', "$berhasil data siswa berhasil diimport.");
    }
    public function hapusBanyak(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return back()->with('error', 'Tidak ada data yang dipilih.');
    }

    // Hanya hapus yang belum pernah daftar akun
    $nisYangSudahDaftar = \App\Models\User::pluck('nis')->toArray();

    $dataSiswa = \App\Models\DataSiswa::whereIn('id', $ids)->get();

    $tidakBisaDihapus = 0;
    $berhasilDihapus = 0;

    foreach ($dataSiswa as $s) {
        if (in_array($s->nis, $nisYangSudahDaftar)) {
            $tidakBisaDihapus++;
        } else {
            $s->delete();
            $berhasilDihapus++;
        }
    }

    $pesan = "$berhasilDihapus data berhasil dihapus.";
    if ($tidakBisaDihapus > 0) {
        $pesan .= " $tidakBisaDihapus data dilewati karena sudah punya akun.";
    }

    return back()->with('success', $pesan);
}
}