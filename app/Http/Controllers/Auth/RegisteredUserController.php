<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Anggota;
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

   public function store(Request $request): RedirectResponse
    {
        // 1. Validasi tetap sama
      $request->validate([
    'name'     => ['required', 'string', 'max:255'],
    'nis'      => ['required', 'string', 'max:20', 'unique:users'],
    'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
    'no_hp'    => ['required', 'string', 'max:20'], // Tambahkan ini
    'alamat'   => ['required', 'string'],           // Tambahkan ini
    'password' => ['required', 'confirmed', Rules\Password::defaults()],
]);
        // 2. Buat User
        $user = User::create([
            'name'     => $request->name,
            'nis'      => $request->nis,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Buat Anggota (Pastikan key di bawah sesuai dengan nama kolom di database!)
       Anggota::create([
            // 'nis' => $request->nis,  <-- HAPUS INI
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