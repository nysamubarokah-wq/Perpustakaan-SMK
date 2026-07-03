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

        $query = Anggota::query()
            ->where('role', 'siswa')
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('no_telepon', 'like', "%$search%")
                  ->orWhere('kelas', 'like', "%$search%");
            })
            ->orderBy('nama');

        $anggota = $query->get();

        return view('anggota.index', compact('anggota', 'search'));
    }

    public function adminIndex(Request $request)
    {
        $search = $request->get('search');

        $query = Anggota::query()
            ->where('role', 'admin')
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('no_telepon', 'like', "%$search%");
            })
            ->orderBy('nama');

        $anggota = $query->get();

        return view('anggota.admin-index', compact('anggota', 'search'));
    }

    public function create()
    {
        return view('anggota.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'nis'            => 'nullable|string|max:50|unique:anggota,nis|unique:users,nis',
            'email'          => 'required|email|unique:anggota,email|unique:users,email',
            'no_telepon'     => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'kelas'          => 'nullable|string|max:50',
            'jurusan'        => 'nullable|string|max:100',
            'jenis_kelamin'  => 'nullable|string|in:L,P',
        ]);

        $defaultPassword = $request->nis ?: '12345678';

        // Create User account
        $user = User::create([
            'name'  => $request->nama,
            'nis'   => $request->nis,
            'email' => $request->email,
            'role'  => 'siswa',
        ]);

        // Create Anggota linked to User
        Anggota::create([
            'user_id'        => $user->id,
            'nama'           => $request->nama,
            'email'          => $request->email,
            'nis'            => $request->nis,
            'no_telepon'    => $request->no_telepon,
            'alamat'         => $request->alamat,
            'tanggal_daftar' => now()->toDateString(),
            'kelas'          => $request->kelas,
            'jurusan'        => $request->jurusan,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'status'         => 'aktif',
            'role'           => 'siswa',
        ]);

        return redirect()->route('anggota.index')->with('success', 'Anggota ' . $request->nama . ' berhasil ditambahkan!');
    }

    public function edit(Anggota $anggota)
    {
        return view('anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'nis'            => 'nullable|string|max:50|unique:anggota,nis,' . $anggota->id . '|unique:users,nis,' . optional($anggota->user)->id,
            'email'          => 'required|email|unique:anggota,email,' . $anggota->id . '|unique:users,email,' . optional($anggota->user)->id,
            'no_telepon'     => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'kelas'          => 'nullable|string|max:50',
            'jurusan'        => 'nullable|string|max:100',
            'jenis_kelamin'  => 'nullable|string|in:L,P',
            'status'         => 'nullable|string|in:aktif,nonaktif',
        ]);

        $anggota->update($request->only('nama', 'email', 'nis', 'no_telepon', 'alamat', 'kelas', 'jurusan', 'jenis_kelamin', 'status'));

        // Sync to User
        $user = $anggota->user;
        if ($user) {
            $user->update([
                'name'  => $request->nama,
                'email' => $request->email,
                'nis'   => $request->nis,
                'no_hp' => $request->no_telepon,
            ]);
        }

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diupdate!');
    }

    public function destroy($id)
    {
        $anggota = Anggota::findOrFail($id);

        // Check for active peminjaman
        $activePeminjaman = $anggota->peminjaman()
            ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
            ->count();

        if ($activePeminjaman > 0) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus anggota yang masih memiliki peminjaman aktif!');
        }

        // Delete associated User
        $user = $anggota->user;
        $anggota->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus!');
    }

    public function updateRole(Request $request, $id, $role)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->update(['role' => $role]);

        $user = $anggota->user;
        if ($user) {
            $user->update(['role' => $role]);
        }

        return redirect()->back()->with('success', 'Role berhasil diubah!');
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

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt']);

        $file = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($file);
        $berhasil = 0;
        $gagal = 0;

        while ($row = fgetcsv($file)) {
            if (count($row) >= 2) {
                $nis   = trim($row[0]);
                $nama  = trim($row[1]);
                $kelas = trim($row[2] ?? '');
                $jurusan = trim($row[3] ?? '');
                $jenisKelamin = trim($row[4] ?? '');
                $noHp  = trim($row[5] ?? '');
                $email = trim($row[6] ?? '');

                if (empty($nama)) {
                    $gagal++;
                    continue;
                }

                // Generate email if empty
                if (empty($email)) {
                    $email = $nis ? $nis . '@siswa.sch.id' : strtolower(str_replace(' ', '.', $nama)) . '@siswa.sch.id';
                }

                // Check if anggota already exists
                $existing = null;
                if ($nis) {
                    $existing = Anggota::where('nis', $nis)->first();
                }
                if (!$existing) {
                    $existing = Anggota::where('email', $email)->first();
                }

                if ($existing) {
                    // Update existing
                    $existing->update([
                        'nama'          => $nama,
                        'kelas'         => $kelas ?: $existing->kelas,
                        'jurusan'       => $jurusan ?: $existing->jurusan,
                        'jenis_kelamin' => $jenisKelamin ?: $existing->jenis_kelamin,
                        'no_telepon'    => $noHp ?: $existing->no_telepon,
                    ]);
                } else {
                    // Create new User + Anggota
                    $defaultPassword = $nis ?: '12345678';

                    $user = User::create([
                        'name'  => $nama,
                        'nis'   => $nis ?: null,
                        'email' => $email,
                        'role'  => 'siswa',
                    ]);

                    Anggota::create([
                        'user_id'        => $user->id,
                        'nama'           => $nama,
                        'email'          => $email,
                        'nis'            => $nis ?: null,
                        'no_telepon'     => $noHp,
                        'alamat'         => '',
                        'tanggal_daftar' => now()->toDateString(),
                        'kelas'          => $kelas,
                        'jurusan'        => $jurusan,
                        'jenis_kelamin'  => $jenisKelamin,
                        'status'         => 'aktif',
                        'role'           => 'siswa',
                    ]);
                }

                $berhasil++;
            }
        }

        fclose($file);

        return back()->with('success', "$berhasil anggota berhasil diimport." . ($gagal > 0 ? " $gagal baris dilewati." : ''));
    }

    public function hapusBanyak(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $berhasilDihapus = 0;
        $tidakBisaDihapus = 0;

        foreach ($ids as $id) {
            $anggota = Anggota::find($id);
            if (!$anggota) continue;

            $activePeminjaman = $anggota->peminjaman()
                ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
                ->count();

            if ($activePeminjaman > 0) {
                $tidakBisaDihapus++;
                continue;
            }

            $user = $anggota->user;
            $anggota->delete();
            if ($user) $user->delete();
            $berhasilDihapus++;
        }

        $pesan = "$berhasilDihapus anggota berhasil dihapus.";
        if ($tidakBisaDihapus > 0) {
            $pesan .= " $tidakBisaDihapus dilewati karena masih punya peminjaman aktif.";
        }

        return back()->with('success', $pesan);
    }

}
