<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Anggota;
use App\Models\DataSiswa;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    // ← hapus baris "use App\Models\DataSiswa;" yang ada di sini

    public function store(Request $request): RedirectResponse
    {
       $request->validate([
    'name'     => ['required', 'string', 'max:255'],
    'nis'      => ['required', 'string', 'max:20', 'unique:users'],
    'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
    'no_hp'    => [
        'required',
        'regex:/^(?:\+62|0)[0-9]{9,13}$/',
    ],
    'alamat'   => ['required', 'string'],
    'password' => ['required', 'confirmed', Rules\Password::defaults()],
], [
    'no_hp.regex' => 'Nomor HP harus diawali 08 atau +62 dan hanya boleh berisi angka.',
]);

        $dataSiswa = DataSiswa::where('nis', $request->nis)->first();

        if (!$dataSiswa) {
            return back()->withErrors(['nis' => 'NIS tidak terdaftar. Hubungi admin perpustakaan.'])->withInput();
        }

        if (strtolower(trim($dataSiswa->nama)) !== strtolower(trim($request->name))) {
            return back()->withErrors(['name' => 'Nama tidak sesuai dengan data NIS tersebut.'])->withInput();
        }

        if (User::where('nis', $request->nis)->exists()) {
            return back()->withErrors(['nis' => 'NIS ini sudah digunakan untuk akun lain.'])->withInput();
        }

        $user = User::create([
            'name'     => $request->name,
            'nis'      => $request->nis,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Anggota::create([
            'nama'           => $request->name,
            'email'          => $request->email,
            'no_telepon'     => $request->no_hp,
            'alamat'         => $request->alamat,
            'tanggal_daftar' => now()->toDateString(),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}