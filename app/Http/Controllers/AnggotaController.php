<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $anggota = Anggota::query()
            ->when($search, function ($q) use ($search) {
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
            'nama'           => 'required',
            'email'          => 'required|email|unique:anggota',
            'no_telepon'     => 'required',
            'alamat'         => 'required',
            'tanggal_daftar' => 'required|date',
        ]);

        // Cari user berdasarkan email yang sama
        $user = User::where('email', $request->email)->first();

        // Buat anggota, langsung isi user_id jika user ditemukan
        Anggota::create(array_merge($request->only('nama', 'email', 'no_telepon', 'alamat', 'tanggal_daftar', 'nis'), [
            'user_id' => $user?->id,
        ]));

        // Sinkron NIS ke tabel users jika ada
        if ($request->nis && $user) {
            $user->update(['nis' => $request->nis]);
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
            'nama'           => 'required',
            'email'          => 'required|email|unique:anggota,email,' . $anggota->id,
            'no_telepon'     => 'required',
            'alamat'         => 'required',
            'tanggal_daftar' => 'required|date',
        ]);

        // Jika email berubah, cari user baru berdasarkan email baru
        $user = User::where('email', $request->email)->first();

        $anggota->update(array_merge($request->only('nama', 'email', 'no_telepon', 'alamat', 'tanggal_daftar', 'nis'), [
            'user_id' => $user?->id,
        ]));

        if ($request->has('nis') && $user) {
            $user->update(['nis' => $request->nis]);
        }

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diupdate!');
    }

    public function destroy($id)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->delete();

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus!');
    }

    public function updateRole(Request $request, $id, $role)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->update(['role' => $role]);

        $user = User::where('email', $anggota->email)->first();
        if ($user) {
            $user->update(['role' => $role]);
        }

        return redirect()->back()->with('success', 'Role berhasil diubah dan disinkronisasi!');
    }

    public function setDuty($userId)
    {
        User::where('role', 'admin')->update(['is_on_duty' => false]);
        $user = User::findOrFail($userId);
        $user->update(['is_on_duty' => true]);

        return back()->with('success', $user->name . ' sekarang sedang bertugas!');
    }

    public function cabutDuty($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_on_duty' => false]);

        return back()->with('success', 'Status bertugas ' . $user->name . ' berhasil dicabut!');
    }
}