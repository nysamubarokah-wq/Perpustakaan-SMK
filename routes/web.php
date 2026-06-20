<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\PinjamController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackgroundController;
use App\Http\Controllers\FavoritController;
use App\Http\Controllers\UlasanController;

// 1. Rute Halaman Utama
Route::get('/', function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('dashboard');
});

// 2. Rute untuk User Umum / Siswa
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/koleksi', [App\Http\Controllers\KoleksiController::class, 'index'])->name('koleksi.index');
    Route::get('/buku/{buku}/detail', [PinjamController::class, 'detail'])->name('buku.detail');
    Route::post('/buku/{buku}/pinjam', [PinjamController::class, 'store'])->name('buku.pinjam');
    Route::get('/profil', [App\Http\Controllers\ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil/peminjaman/{id}/kembalikan', [App\Http\Controllers\ProfilController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    Route::post('/profil/foto', [App\Http\Controllers\ProfilController::class, 'uploadFoto'])->name('profil.foto');
    // Tambahkan baris ini di routes/web.php
Route::post('/profil/background/{key}', [App\Http\Controllers\ProfilController::class, 'beliBackground'])
    ->name('profil.background')
    ->middleware('auth'); // Jangan lupa tambahkan middleware auth agar aman
    Route::get('/favorit', [FavoritController::class, 'index'])->name('favorit.index');
Route::post('/buku/{buku}/favorit', [FavoritController::class, 'toggle'])->name('buku.favorit');
Route::post('/buku/{buku}/ulasan', [UlasanController::class, 'store'])->name('ulasan.store');
Route::delete('/ulasan/{ulasan}', [UlasanController::class, 'destroy'])->name('ulasan.destroy');
    
});

// 3. Rute Khusus Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Rute Role Switching (Ditaruh di atas agar tidak tertutup resource)
    Route::put('/admin/anggota/{id}/role/{role}', [AnggotaController::class, 'updateRole'])->name('admin.anggota.role');
    
    // Rute Resource
    Route::resource('buku', BukuController::class);Route::resource('background', BackgroundController::class);
    Route::resource('anggota', AnggotaController::class)->parameters([
    'anggota' => 'anggota'
    
]);
Route::get('/admin/ulasan', [UlasanController::class, 'index'])->name('admin.ulasan.index');
Route::delete('/admin/ulasan/{ulasan}', [UlasanController::class, 'destroy'])->name('admin.ulasan.destroy');
    Route::resource('peminjaman', PeminjamanController::class);

    // Rute Lainnya
    Route::get('/admin/pengembalian', [PinjamController::class, 'persetujuanIndex'])->name('admin.pengembalian.index');
    Route::put('/admin/pengembalian/{id}/setujui', [PinjamController::class, 'setujuiKembali'])->name('admin.pengembalian.setujui');
    Route::get('/admin/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('admin.laporan.pdf');
    
    // User Management
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/admin/users/{id}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
});

// 4. Rute Otentikasi
require __DIR__.'/auth.php';