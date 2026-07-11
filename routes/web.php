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
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ImportTemplateController;
use App\Http\Controllers\NotifikasiController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/qrcode-test', function () {
    return response(
        QrCode::size(250)->generate('Halo Perpustakaan')
    )->header('Content-Type', 'image/svg+xml');
});

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

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/go', function (\Illuminate\Http\Request $request) {
        $prevUrl = url()->previous();
        $excludedPatterns = ['/notifikasi', '/login', '/register', '/password/reset', '/verify', '/email'];
        $isExcluded = false;
        foreach ($excludedPatterns as $pattern) {
            if ($prevUrl && str_contains($prevUrl, $pattern)) {
                $isExcluded = true;
                break;
            }
        }
        if ($prevUrl && !$isExcluded) {
            session(['notifikasi_back_url' => $prevUrl]);
        }
        return redirect()->route('notifikasi.index');
    })->middleware('auth')->name('notifikasi.go');
    Route::get('/notifikasi/{id}/baca', [NotifikasiController::class, 'markRead'])->name('notifikasi.baca');
    Route::get('/notifikasi/baca-semua', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.bacaSemua');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');
    Route::post('/notifikasi/hapus-semua', [NotifikasiController::class, 'destroyAll'])->name('notifikasi.destroyAll');
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'getUnreadCount'])->name('notifikasi.unreadCount');
    Route::get('/notifikasi/latest', [NotifikasiController::class, 'getLatest'])->name('notifikasi.latest');

    Route::get('/dashboard', function () {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('koleksi.index');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/koleksi', [App\Http\Controllers\KoleksiController::class, 'index'])->name('koleksi.index');
    Route::get('/koleksi/populer', [App\Http\Controllers\KoleksiController::class, 'populer'])->name('koleksi.populer');
    Route::get('/koleksi/terbaru', [App\Http\Controllers\KoleksiController::class, 'terbaru'])->name('koleksi.terbaru');
    Route::get('/buku/{buku}/detail', [PinjamController::class, 'detail'])->name('buku.detail');
    Route::post('/buku/{buku}/pinjam', [PinjamController::class, 'store'])->name('buku.pinjam');
    Route::post('/buku/{buku}/favorit', [FavoritController::class, 'toggle'])->name('buku.favorit');

    Route::get('/profil', [App\Http\Controllers\ProfilController::class, 'index'])->name('profil.index');
    Route::get('/profil/riwayat', [App\Http\Controllers\ProfilController::class, 'riwayat'])->name('profil.riwayat');
    Route::get('/profil/riwayat/{id}', [App\Http\Controllers\ProfilController::class, 'detailRiwayat'])->name('profil.riwayat.detail');
    Route::put('/profil/peminjaman/{id}/kembalikan', [App\Http\Controllers\ProfilController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    Route::post('/profil/peminjaman/kembalikan-banyak', [App\Http\Controllers\ProfilController::class, 'kembalikanBanyak'])->name('peminjaman.kembalikan.banyak');
    Route::post('/profil/foto', [App\Http\Controllers\ProfilController::class, 'uploadFoto'])->name('profil.foto');
    Route::post('/profil/background/{key}', [App\Http\Controllers\ProfilController::class, 'beliBackground'])->name('profil.background');

    Route::get('/favorit', [FavoritController::class, 'index'])->name('favorit.index');

    Route::post('/buku/{buku}/ulasan', [UlasanController::class, 'store'])->name('ulasan.store');
    Route::delete('/ulasan/{ulasan}', [UlasanController::class, 'destroy'])->name('ulasan.destroy');

    Route::get('/barcode/scanner', [BarcodeController::class, 'scanner'])->name('barcode.scanner');
    Route::post('/barcode/cek-buku', [BarcodeController::class, 'cekBuku'])->name('barcode.cekBuku');
    Route::post('/barcode/pinjam', [BarcodeController::class, 'pinjamViaScan'])->name('barcode.pinjam');
    Route::post('/barcode/kembali', [BarcodeController::class, 'kembaliViaScan'])->name('barcode.kembali');

    Route::get('/ebook', [App\Http\Controllers\EbookController::class, 'index'])->name('ebook.index');
    Route::get('/ebook/{id}', [App\Http\Controllers\EbookController::class, 'show'])->name('ebook.show');
    Route::get('/ebook/{id}/baca', [App\Http\Controllers\EbookController::class, 'baca'])->name('ebook.baca');
    Route::post('/ebook/{id}/beli', [App\Http\Controllers\EbookController::class, 'beliDenganKoin'])->name('ebook.beli');

    Route::get('/vip', [App\Http\Controllers\VipController::class, 'index'])->name('vip.index');
    Route::post('/vip/beli', [App\Http\Controllers\VipController::class, 'beliVip'])->name('vip.beli');

    Route::post('/setuju-peraturan', function () {
        auth()->user()->update(['agreed_rules' => true]);
        return response()->json(['ok' => true]);
    })->name('setuju.peraturan');

});


// ============================================================
// 3. RUTE ADMIN
// ============================================================
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/anggota', [AnggotaController::class, 'adminIndex'])->name('anggota.admin');
    Route::put('/admin/anggota/{id}/role/{role}', [AnggotaController::class, 'updateRole'])->name('admin.anggota.role');
    Route::post('/admin/anggota/import', [AnggotaController::class, 'import'])->name('admin.anggota.import');
    Route::post('/admin/anggota/hapus-banyak', [AnggotaController::class, 'hapusBanyak'])->name('admin.anggota.hapusBanyak');
    Route::resource('anggota', AnggotaController::class)->parameters(['anggota' => 'anggota']);
    Route::post('/admin/anggota/{userId}/duty', [App\Http\Controllers\AnggotaController::class, 'setDuty'])->name('admin.anggota.duty');
    Route::post('/admin/anggota/{userId}/cabut-duty', [App\Http\Controllers\AnggotaController::class, 'cabutDuty'])->name('admin.anggota.cabutDuty');

    Route::post('/buku/hapus-banyak', [BukuController::class, 'hapusBanyak'])->name('buku.hapusBanyak');
    Route::post('/buku/import', [BukuController::class, 'import'])->name('buku.import');
    Route::get('/download-template/{type}', [ImportTemplateController::class, 'download'])->name('import.template')->where('type', 'buku|anggota');
    Route::post('/buku/generate-qr', [BukuController::class, 'generateAllQr'])->name('buku.generateAllQr');
    Route::get('/buku/download-all-qr', [BukuController::class, 'downloadAllQr'])->name('buku.downloadAllQr');
    Route::get('/buku/cetak-semua-qr', [BukuController::class, 'cetakSemuaQr'])->name('buku.cetakSemuaQr');
    Route::resource('buku', BukuController::class);

    Route::get('/buku/{buku}/qrcode', [BukuController::class, 'qrcode'])->name('buku.qrcode');
    Route::get('/buku/{buku}/qrcode/download', [BukuController::class, 'qrcodeDownload'])->name('buku.qrcodeDownload');
    Route::get('/buku/{buku}/qrcode/print', [BukuController::class, 'qrcodePrint'])->name('buku.qrcodePrint');

    Route::post('/buku/{buku}/eksemplar/tambah', [BukuController::class, 'tambahEksemplar'])->name('buku.eksemplar.tambah');
    Route::put('/eksemplar/{eksemplar}/status', [BukuController::class, 'updateEksemplarStatus'])->name('eksemplar.updateStatus');
    Route::delete('/eksemplar/{eksemplar}', [BukuController::class, 'hapusEksemplar'])->name('eksemplar.hapus');

    Route::resource('background', BackgroundController::class);

    Route::resource('peminjaman', PeminjamanController::class);

    Route::get('/admin/konfirmasi-pinjam', [PinjamController::class, 'konfirmasiIndex'])->name('admin.pinjam.index');
    Route::post('/admin/pinjam/{id}/konfirmasi', [PinjamController::class, 'konfirmasiPinjam'])->name('admin.pinjam.konfirmasi');
    Route::delete('/admin/pinjam/{id}/tolak', [PinjamController::class, 'tolakPinjam'])->name('admin.pinjam.tolak');
    Route::post('/admin/pinjam/konfirmasi-semua', [PeminjamanController::class, 'konfirmasiSemua'])->name('admin.pinjam.konfirmasiSemua');

    Route::get('/admin/pengembalian', [PinjamController::class, 'persetujuanIndex'])->name('admin.pengembalian.index');
    Route::put('/admin/pengembalian/{id}/setujui', [PinjamController::class, 'setujuiKembali'])->name('admin.pengembalian.setujui');
    Route::post('/admin/pengembalian/setujui-semua', [PinjamController::class, 'setujuiSemuaKembali'])->name('admin.pengembalian.setujuiSemua');

    Route::get('/admin/ulasan', [UlasanController::class, 'index'])->name('admin.ulasan.index');
    Route::delete('/admin/ulasan/{ulasan}', [UlasanController::class, 'destroy'])->name('admin.ulasan.destroy');
    Route::post('/admin/ulasan/{ulasan}/balas', [UlasanController::class, 'balas'])->name('admin.ulasan.balas');
    Route::put('/admin/ulasan/{ulasan}/edit-balasan', [UlasanController::class, 'editBalasan'])->name('admin.ulasan.editBalasan');
    Route::delete('/admin/ulasan/{ulasan}/hapus-balasan', [UlasanController::class, 'hapusBalasan'])->name('admin.ulasan.hapusBalasan');
    Route::post('/admin/ulasan/bulk-delete', [UlasanController::class, 'bulkDelete'])->name('admin.ulasan.bulkDelete');
    Route::get('/admin/ulasan/export', [UlasanController::class, 'export'])->name('admin.ulasan.export');

    Route::get('/admin/vip', [App\Http\Controllers\VipController::class, 'adminIndex'])->name('admin.vip.index');
    Route::post('/admin/vip/{userId}/upgrade', [App\Http\Controllers\VipController::class, 'adminUpgrade'])->name('admin.vip.upgrade');
    Route::post('/admin/vip/{userId}/cabut', [App\Http\Controllers\VipController::class, 'adminCabut'])->name('admin.vip.cabut');

    Route::get('/admin/ebook', [App\Http\Controllers\EbookController::class, 'adminIndex'])->name('admin.ebook.index');
    Route::get('/admin/ebook/tambah', [App\Http\Controllers\EbookController::class, 'adminCreate'])->name('admin.ebook.create');
    Route::post('/admin/ebook', [App\Http\Controllers\EbookController::class, 'adminStore'])->name('admin.ebook.store');
    Route::delete('/admin/ebook/{id}', [App\Http\Controllers\EbookController::class, 'adminDestroy'])->name('admin.ebook.destroy');
    Route::get('/admin/ebook/{id}/edit', [App\Http\Controllers\EbookController::class, 'adminEdit'])->name('admin.ebook.edit');
    Route::put('/admin/ebook/{id}', [App\Http\Controllers\EbookController::class, 'adminUpdate'])->name('admin.ebook.update');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/admin/users/{id}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
    Route::post('/admin/users/{id}/duty', [UserController::class, 'toggleDuty'])->name('admin.users.duty');

    Route::get('/admin/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('admin.laporan.pdf');

    Route::get('/admin/denda', [App\Http\Controllers\Admin\DendaController::class, 'index'])->name('admin.denda.index');
    Route::post('/admin/denda/{id}/lunasi', [App\Http\Controllers\Admin\DendaController::class, 'lunasi'])->name('admin.denda.lunasi');
    Route::post('/admin/denda/lunasi-semua', [App\Http\Controllers\Admin\DendaController::class, 'lunasiSemua'])->name('admin.denda.lunasi-semua');
    Route::delete('/admin/denda/{id}', [App\Http\Controllers\Admin\DendaController::class, 'destroy'])->name('admin.denda.destroy');
    Route::post('/admin/denda/hapus-banyak', [App\Http\Controllers\Admin\DendaController::class, 'hapusBanyak'])->name('admin.denda.hapus-banyak');

    Route::get('/admin/scan-buku', [BarcodeController::class, 'adminScanner'])->name('admin.scanner');
    Route::post('/admin/scan-buku/cek', [BarcodeController::class, 'adminCekBuku'])->name('admin.scanner.cek');
    Route::post('/admin/scan-buku/cek-peminjaman', [BarcodeController::class, 'cekPeminjamanAnggota'])->name('admin.scanner.cek-peminjaman');
    Route::post('/admin/scan-buku/pinjam', [BarcodeController::class, 'adminPinjam'])->name('admin.scanner.pinjam');
    Route::post('/admin/scan-buku/kembali', [BarcodeController::class, 'adminKembali'])->name('admin.scanner.kembali');
});


// ============================================================
// 4. AUTENTIKASI
// ============================================================
require __DIR__.'/auth.php';
