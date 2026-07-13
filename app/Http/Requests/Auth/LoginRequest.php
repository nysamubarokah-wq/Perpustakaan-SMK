<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Models\Anggota;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_login' => ['required', 'string'],
            'nis_login'  => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_login.required' => 'Nama harus diisi.',
            'nis_login.required'  => 'NIS harus diisi.',
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $nis  = trim($this->input('nis_login'));
        $nama = trim($this->input('nama_login'));

        $anggota = Anggota::where('nis', $nis)->first();

        if (!$anggota) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'nis_login' => 'NIS tidak ditemukan. Hubungi admin perpustakaan.',
            ]);
        }

        if (strtolower(trim($anggota->nama)) !== strtolower($nama)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'nama_login' => 'Nama tidak cocok dengan NIS tersebut.',
            ]);
        }

        $user = User::where('nis', $nis)->first();

        if (!$user) {
            $user = User::create([
                'name'     => $anggota->nama,
                'nis'      => $anggota->nis,
                'email'    => $anggota->email ?? strtolower(str_replace(' ', '.', $anggota->nama)) . '@school.sch.id',
                'password' => bcrypt('login-nis-' . $anggota->nis),
                'role'     => $anggota->role ?? 'siswa',
            ]);
            $anggota->update(['user_id' => $user->id]);
        }

        auth()->login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 10)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nis_login' => 'Terlalu banyak percobaan. Coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('nis_login')) . '|' . $this->ip());
    }
}
