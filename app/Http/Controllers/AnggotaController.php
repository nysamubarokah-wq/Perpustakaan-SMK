<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    
    $anggota = \App\Models\Anggota::query()
        ->when($search, function($q) use ($search) {
            $q->where('nama', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('no_telepon', 'like', "%$search%");
        })
        ->get();

    return view('anggota.index', compact('anggota', 'search'));
}

    public function create()
    {
        return view('anggota.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required',
            'email'         => 'required|email|unique:anggota',
            'no_telepon'    => 'required',
            'alamat'        => 'required',
            'tanggal_daftar'=> 'required|date',
        ]);
Anggota::create($request->all());

// Simpan NIS ke users jika ada, atau sudah tersimpan di anggota via $request->all()
if ($request->nis) {
    $user = \App\Models\User::where('email', $request->email)->first();
    if ($user) {
        $user->update(['nis' => $request->nis]);
    }
}

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan!');
    }
    

    public function edit(Anggota $anggota)
    {
        return view('anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        $request->validate([
            'nama'          => 'required',
            'email'         => 'required|email|unique:anggota,email,' . $anggota->id,
            'no_telepon'    => 'required',
            'alamat'        => 'required',
            'tanggal_daftar'=> 'required|date',
        ]);

      $anggota->update($request->all());

if ($request->has('nis')) {
    $user = \App\Models\User::where('email', $anggota->email)->first();
    if ($user) {
        $user->update(['nis' => $request->nis]);
    }
}

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diupdate!');
    }

   public function destroy($id) // Ganti parameter menjadi $id biasa
    {
        // Cari data berdasarkan ID secara manual
        $anggota = Anggota::findOrFail($id); 
        
        // Eksekusi hapus
        $anggota->delete(); 
        
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus!');
    }
    // Fungsi untuk mengubah role
public function updateRole(Request $request, $id, $role)
{
    // 1. Update di tabel anggota
    $anggota = \App\Models\Anggota::findOrFail($id);
    $anggota->update(['role' => $role]);

    // 2. Update juga di tabel users (Jika ada relasi)
    // Asumsi: email di tabel anggota sama dengan email di tabel users
    $user = \App\Models\User::where('email', $anggota->email)->first();
    if ($user) {
        $user->update(['role' => $role]);
    }

    return redirect()->back()->with('success', 'Role berhasil diubah dan disinkronisasi!');
}

public function setDuty($userId)
{
    \App\Models\User::where('role', 'admin')->update(['is_on_duty' => false]);
    $user = \App\Models\User::findOrFail($userId);
    $user->update(['is_on_duty' => true]);
    return back()->with('success', $user->name . ' sekarang sedang bertugas!');
}

public function cabutDuty($userId)
{
    $user = \App\Models\User::findOrFail($userId);
    $user->update(['is_on_duty' => false]);
    return back()->with('success', 'Status bertugas ' . $user->name . ' berhasil dicabut!');
}
}