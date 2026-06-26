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

// ============================================================
// 1. HALAMAN UTAMA
// ============================================================
Route::get('/', function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('dashboard');
});

// ============================================================
// 2. RUTE SISWA / USER UMUM
// ============================================================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Laravel default
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Koleksi & Detail Buku
    Route::get('/koleksi', [App\Http\Controllers\KoleksiController::class, 'index'])->name('koleksi.index');
    Route::get('/buku/{buku}/detail', [PinjamController::class, 'detail'])->name('buku.detail');
    Route::post('/buku/{buku}/pinjam', [PinjamController::class, 'store'])->name('buku.pinjam');
    Route::post('/buku/{buku}/favorit', [FavoritController::class, 'toggle'])->name('buku.favorit');

    // Profil Siswa
    Route::get('/profil', [App\Http\Controllers\ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil/peminjaman/{id}/kembalikan', [App\Http\Controllers\ProfilController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    Route::post('/profil/foto', [App\Http\Controllers\ProfilController::class, 'uploadFoto'])->name('profil.foto');
    Route::post('/profil/background/{key}', [App\Http\Controllers\ProfilController::class, 'beliBackground'])->name('profil.background');

    // Favorit
    Route::get('/favorit', [FavoritController::class, 'index'])->name('favorit.index');

    // Ulasan
    Route::post('/buku/{buku}/ulasan', [UlasanController::class, 'store'])->name('ulasan.store');
    Route::delete('/ulasan/{ulasan}', [UlasanController::class, 'destroy'])->name('ulasan.destroy');

    // E-book
    Route::get('/ebook', [App\Http\Controllers\EbookController::class, 'index'])->name('ebook.index');
    Route::get('/ebook/{id}', [App\Http\Controllers\EbookController::class, 'show'])->name('ebook.show');
    Route::get('/ebook/{id}/baca', [App\Http\Controllers\EbookController::class, 'baca'])->name('ebook.baca');
    Route::post('/ebook/{id}/beli', [App\Http\Controllers\EbookController::class, 'beliDenganKoin'])->name('ebook.beli');
    Route::get('/admin/ebook/{id}/edit', [App\Http\Controllers\EbookController::class, 'adminEdit'])
    ->name('admin.ebook.edit');

Route::put('/admin/ebook/{id}', [App\Http\Controllers\EbookController::class, 'adminUpdate'])
    ->name('admin.ebook.update');

    // VIP
    Route::get('/vip', [App\Http\Controllers\VipController::class, 'index'])->name('vip.index');
    Route::post('/vip/beli', [App\Http\Controllers\VipController::class, 'beliVip'])->name('vip.beli');

    // Setuju peraturan
    Route::post('/setuju-peraturan', function () {
        auth()->user()->update(['agreed_rules' => true]);
        return response()->json(['ok' => true]);
    })->name('setuju.peraturan');
});

// ============================================================
// 3. RUTE ADMIN
// ============================================================
Route::middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Anggota
    Route::put('/admin/anggota/{id}/role/{role}', [AnggotaController::class, 'updateRole'])->name('admin.anggota.role');
    Route::resource('anggota', AnggotaController::class)->parameters(['anggota' => 'anggota']);
    Route::post('/admin/users/{userId}/duty', [App\Http\Controllers\AnggotaController::class, 'setDuty'])->name('admin.users.duty');
Route::post('/admin/users/{userId}/cabut-duty', [App\Http\Controllers\AnggotaController::class, 'cabutDuty'])->name('admin.users.cabutDuty');

    // Buku
    Route::post('/buku/hapus-banyak', [BukuController::class, 'hapusBanyak'])->name('buku.hapusBanyak');
    Route::post('/buku/import', [BukuController::class, 'import'])->name('buku.import');
    Route::resource('buku', BukuController::class);

    // Background
    Route::resource('background', BackgroundController::class);

    // Peminjaman
    Route::resource('peminjaman', PeminjamanController::class);

    // Konfirmasi Pinjam
    Route::get('/admin/konfirmasi-pinjam', [PinjamController::class, 'konfirmasiIndex'])->name('admin.pinjam.index');
    Route::post('/admin/pinjam/{id}/konfirmasi', [PinjamController::class, 'konfirmasiPinjam'])->name('admin.pinjam.konfirmasi');
    Route::delete('/admin/pinjam/{id}/tolak', [PinjamController::class, 'tolakPinjam'])->name('admin.pinjam.tolak');
    Route::post(
    '/admin/pinjam/konfirmasi-semua',
    [PeminjamanController::class, 'konfirmasiSemua']
)->name('admin.pinjam.konfirmasiSemua');

    // Persetujuan Kembali
    Route::get('/admin/pengembalian', [PinjamController::class, 'persetujuanIndex'])->name('admin.pengembalian.index');
    Route::put('/admin/pengembalian/{id}/setujui', [PinjamController::class, 'setujuiKembali'])->name('admin.pengembalian.setujui');
   Route::post(
    '/admin/pengembalian/setujui-semua',
    [PinjamController::class, 'setujuiSemuaKembali']
)->name('admin.pengembalian.setujuiSemua');
    // Ulasan
    Route::get('/admin/ulasan', [UlasanController::class, 'index'])->name('admin.ulasan.index');
    Route::delete('/admin/ulasan/{ulasan}', [UlasanController::class, 'destroy'])->name('admin.ulasan.destroy');

    // Data Siswa
    Route::post('/admin/data-siswa/hapus-banyak', [App\Http\Controllers\Admin\DataSiswaController::class, 'hapusBanyak'])->name('admin.siswa.hapusBanyak');
    Route::post('/admin/data-siswa/import', [App\Http\Controllers\Admin\DataSiswaController::class, 'import'])->name('admin.siswa.import');
    Route::get('/admin/data-siswa', [App\Http\Controllers\Admin\DataSiswaController::class, 'index'])->name('admin.siswa.index');
    Route::post('/admin/data-siswa', [App\Http\Controllers\Admin\DataSiswaController::class, 'store'])->name('admin.siswa.store');
    Route::delete('/admin/data-siswa/{dataSiswa}', [App\Http\Controllers\Admin\DataSiswaController::class, 'destroy'])->name('admin.siswa.destroy');

    // VIP Admin
    Route::get('/admin/vip', [App\Http\Controllers\VipController::class, 'adminIndex'])->name('admin.vip.index');
    Route::post('/admin/vip/{userId}/upgrade', [App\Http\Controllers\VipController::class, 'adminUpgrade'])->name('admin.vip.upgrade');
   Route::post('/admin/vip/{userId}/cabut',
    [App\Http\Controllers\VipController::class, 'adminCabut'])
    ->name('admin.vip.cabut');
    // E-book Admin
    Route::get('/admin/ebook', [App\Http\Controllers\EbookController::class, 'adminIndex'])->name('admin.ebook.index');
    Route::get('/admin/ebook/tambah', [App\Http\Controllers\EbookController::class, 'adminCreate'])->name('admin.ebook.create');
    Route::post('/admin/ebook', [App\Http\Controllers\EbookController::class, 'adminStore'])->name('admin.ebook.store');
    Route::delete('/admin/ebook/{id}', [App\Http\Controllers\EbookController::class, 'adminDestroy'])->name('admin.ebook.destroy');

    // User Management
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/admin/users/{id}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
    Route::post('/admin/users/{id}/duty', [UserController::class, 'toggleDuty'])->name('admin.users.duty');

    // Laporan
    Route::get('/admin/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('admin.laporan.pdf');
});


// ============================================================
// 4. AUTENTIKASI
// ============================================================
require __DIR__.'/auth.php';