<?php

namespace App\Http\Controllers;

use App\Helpers\Denda as DendaHelper;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Denda;
use App\Models\EksemplarBuku;
use App\Models\Favorit;
use App\Models\Peminjaman;
use App\Models\Ulasan;
use App\Models\User;
use App\Services\NotifikasiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PinjamController extends Controller
{
    public function detail(Buku $buku)
    {
        $buku->load('genre');

        $isFavorit = auth()->check()
            ? Favorit::where('user_id', auth()->id())->where('buku_id', $buku->id)->exists()
            : false;

        $ulasanList = Ulasan::with('user')
            ->where('buku_id', $buku->id)
            ->latest()
            ->get();

        $avgRating = $ulasanList->avg('rating');
        $totalUlasan = $ulasanList->count();

        $ulasanSaya = auth()->check()
            ? Ulasan::where('user_id', auth()->id())->where('buku_id', $buku->id)->first()
            : null;

        $anggotaLogin = auth()->check()
            ? Anggota::where('email', auth()->user()->email)->first()
            : null;

        $bolehUlasan = $anggotaLogin
            ? Peminjaman::where('anggota_id', $anggotaLogin->id)->where('buku_id', $buku->id)->exists()
            : false;

        $rekomendasi = $buku->genre_id
            ? Buku::with('genre')
                ->where('genre_id', $buku->genre_id)
                ->where('id', '!=', $buku->id)
                ->inRandomOrder()
                ->get()
            : collect();

        $favoritIds = auth()->check()
            ? Favorit::where('user_id', auth()->id())->pluck('buku_id')->toArray()
            : [];

        $unreadCount = 0;
        if (auth()->check()) {
            $unreadCount = NotifikasiService::unreadCount(auth()->id());
        }

        $stokTersedia = $buku->eksemplarTersedia()->count();

        return view('pinjam.detail', compact(
            'buku', 'isFavorit', 'ulasanList', 'avgRating', 'totalUlasan', 'ulasanSaya', 'bolehUlasan', 'rekomendasi', 'favoritIds', 'unreadCount', 'stokTersedia'
        ));
    }

    public function store(Request $request, Buku $buku)
    {
        $request->validate([
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
            'jumlah' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        $jumlahDiminta = (int) $request->jumlah;

        $isVip = $user->is_vip && $user->vip_expired_at && now()->lt($user->vip_expired_at);

        $maxBuku = $isVip ? 6 : 3;
        $maxDurasi = $isVip ? 14 : 7;

        $anggota = Anggota::where('user_id', $user->id)->first();

        if (! $anggota) {
            $anggota = Anggota::where('email', $user->email)->first();
            if ($anggota) {
                $anggota->update(['user_id' => $user->id]);
            }
        }

        if (! $anggota) {
            $anggota = Anggota::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'email' => $user->email,
                'no_telepon' => '-',
                'alamat' => '-',
                'tanggal_daftar' => now()->toDateString(),
            ]);
        }

        $stokTersedia = $buku->eksemplarTersedia()->count();

        if ($jumlahDiminta > $stokTersedia) {
            return back()->with('error', "Jumlah yang diminta ({$jumlahDiminta}) melebihi stok tersedia ({$stokTersedia} eksemplar).");
        }

        $jumlahDipinjam = Peminjaman::where('anggota_id', $anggota->id)
            ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
            ->count();

        $sisaSlot = $maxBuku - $jumlahDipinjam;

        if ($jumlahDiminta > $sisaSlot) {
            return back()->with('error',
                $isVip
                    ? "Sisa slot pinjam VIP: {$sisaSlot} buku. Anda meminta {$jumlahDiminta} buku."
                    : "Sisa slot pinjam reguler: {$sisaSlot} buku. Anda meminta {$jumlahDiminta} buku. (Upgrade VIP untuk 6 buku)"
            );
        }

        $eksemplarTersedia = $buku->eksemplarTersedia()->pluck('id')->toArray();

        $sudahDipinjamIds = Peminjaman::where('anggota_id', $anggota->id)
            ->whereIn('eksemplar_id', $eksemplarTersedia)
            ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi'])
            ->pluck('eksemplar_id')
            ->toArray();

        $eksemplarTersedia = array_diff($eksemplarTersedia, $sudahDipinjamIds);

        if (count($eksemplarTersedia) < $jumlahDiminta) {
            return back()->with('error', "Hanya " . count($eksemplarTersedia) . " eksemplar yang bisa dipinjam (sisanya sudah Anda pinjam).");
        }

        $tglPinjam = Carbon::parse($request->tanggal_pinjam);
        $tglKembali = Carbon::parse($request->tanggal_kembali);
        $durasi = $tglPinjam->diffInDays($tglKembali);

        if ($durasi > $maxDurasi) {
            return back()->with('error',
                $isVip
                    ? "Durasi pinjam VIP maksimal {$maxDurasi} hari."
                    : "Durasi pinjam maksimal {$maxDurasi} hari (upgrade VIP untuk 14 hari)."
            );
        }

        $eksemplarList = EksemplarBuku::whereIn('id', $eksemplarTersedia)
            ->limit($jumlahDiminta)
            ->get();

        $dipinjamCount = 0;
        $dipinjamList = [];

        foreach ($eksemplarList as $eksemplar) {
            $eksemplar->update(['status' => 'dipinjam']);

            $peminjaman = Peminjaman::create([
                'anggota_id' => $anggota->id,
                'buku_id' => $buku->id,
                'eksemplar_id' => $eksemplar->id,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status' => 'menunggu_konfirmasi',
                'tipe_konfirmasi' => 'pinjam',
            ]);

            $dipinjamList[] = $peminjaman;
            $dipinjamCount++;
        }

        $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

        $adminUsers = User::where('role', 'admin')->get();
        foreach ($dipinjamList as $peminjaman) {
            foreach ($adminUsers as $admin) {
                NotifikasiService::permintaanPinjamBaru($admin->id, $user->name, $buku->judul, $peminjaman->id);
            }
        }

        $redirectUrl = $request->input('redirect_url') ?: route('koleksi.index');

        $msg = $dipinjamCount == 1
            ? 'Permintaan peminjaman berhasil dikirim! Menunggu konfirmasi admin.'
            : "{$dipinjamCount} permintaan peminjaman berhasil dikirim! Menunggu konfirmasi admin.";

        return redirect($redirectUrl)->with('success', $msg);
    }

    public function konfirmasiPinjam($id)
    {
        $peminjaman = Peminjaman::with(['buku', 'anggota', 'eksemplar'])->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Status peminjaman tidak valid.');
        }

        if ($peminjaman->eksemplar) {
            $peminjaman->eksemplar->update(['status' => 'dipinjam']);
        }

        if ($peminjaman->buku) {
            $peminjaman->buku->update(['stok' => $peminjaman->buku->eksemplarTersedia()->count()]);
        }

        $peminjaman->update(['status' => 'dipinjam', 'tipe_konfirmasi' => null]);

        $user = User::where('email', $peminjaman->anggota->email)->first();
        if ($user) {
            $user->increment('coin', 10);
            NotifikasiService::coinBertambah($user->id, 10, 'Peminjaman buku');
            NotifikasiService::pinjamDisetujui($user->id, $peminjaman);
        }

        return back()->with('success', 'Peminjaman "'.$peminjaman->buku->judul.'" ('.($peminjaman->eksemplar->kode_buku ?? '-').') oleh '.$peminjaman->anggota->nama.' berhasil dikonfirmasi!');
    }

    public function tolakPinjam($id)
    {
        $peminjaman = Peminjaman::with(['buku', 'eksemplar', 'anggota'])->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Status peminjaman tidak valid.');
        }

        if ($peminjaman->eksemplar) {
            $peminjaman->eksemplar->update(['status' => 'tersedia']);
        }

        if ($peminjaman->buku) {
            $peminjaman->buku->update(['stok' => $peminjaman->buku->eksemplarTersedia()->count()]);
        }

        $user = User::where('email', $peminjaman->anggota->email)->first();
        $judulBuku = $peminjaman->buku ? $peminjaman->buku->judul : 'buku';

        if ($user) {
            NotifikasiService::pinjamDitolak($user->id, $judulBuku);
        }

        $peminjaman->delete();

        return back()->with('success', 'Permintaan peminjaman berhasil ditolak.');
    }

    public function persetujuanIndex()
    {
        $persetujuan = Peminjaman::with(['anggota', 'buku', 'eksemplar'])
            ->where(function ($q) {
                $q->where('status', 'menunggu_pengembalian')
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'menunggu_konfirmasi')
                            ->where('tipe_konfirmasi', 'kembali');
                    });
            })
            ->latest()
            ->get();

        foreach ($persetujuan as $item) {
            $tanggalKembali = Carbon::parse($item->tanggal_kembali)->startOfDay();
            $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

            if ($hariIni->gt($tanggalKembali)) {
                $selisihHari = ceil(abs($hariIni->diffInDays($tanggalKembali)));
                $item->taksiran_denda = DendaHelper::hitung($selisihHari);
                $item->terlambat_hari = $selisihHari;
            } else {
                $item->taksiran_denda = 0;
                $item->terlambat_hari = 0;
            }
        }

        return view('admin.pengembalian', compact('persetujuan'));
    }

    public function konfirmasiIndex()
    {
        $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

        // Auto-tolak pinjam yang sudah lewat tanggal_kembali belum dikonfirmasi
        $kedaluwarsa = Peminjaman::with(['buku', 'eksemplar'])
            ->where('status', 'menunggu_konfirmasi')
            ->where('tipe_konfirmasi', 'pinjam')
            ->whereDate('tanggal_kembali', '<', $hariIni)
            ->get();

        foreach ($kedaluwarsa as $p) {
            if ($p->eksemplar) {
                $p->eksemplar->update(['status' => 'tersedia']);
            }
            if ($p->buku) {
                $p->buku->update(['stok' => $p->buku->eksemplarTersedia()->count()]);
            }
            $p->delete();
        }

        $permintaan = Peminjaman::with(['anggota', 'buku', 'eksemplar'])
            ->where('status', 'menunggu_konfirmasi')
            ->where('tipe_konfirmasi', 'pinjam')
            ->latest()
            ->get();

        return view('admin.konfirmasi-pinjam', compact('permintaan'));
    }

    public function setujuiKembali($id)
    {
        $peminjaman = Peminjaman::with(['buku', 'anggota'])->findOrFail($id);

        if ($peminjaman->status === 'menunggu_pengembalian' ||
            ($peminjaman->status === 'menunggu_konfirmasi' && $peminjaman->tipe_konfirmasi === 'kembali')) {
            $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
            $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();
            $hitungDenda = 0;

            if ($hariIni->gt($tanggalKembali)) {
                $selisihHari = ceil(abs($hariIni->diffInDays($tanggalKembali)));
                $hitungDenda = DendaHelper::hitung($selisihHari);
            }

            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_dikembalikan' => $hariIni->toDateString(),
                'denda' => $hitungDenda,
            ]);

            if ($hitungDenda > 0) {
                Denda::updateOrCreate(
                    ['peminjaman_id' => $peminjaman->id],
                    [
                        'jumlah_denda' => $hitungDenda,
                        'status' => 'belum_dibayar',
                        'keterangan' => 'Terlambat '.(int) abs($hariIni->diffInDays($tanggalKembali)).' hari',
                    ]
                );
            }

            if ($peminjaman->eksemplar) {
                $peminjaman->eksemplar->update(['status' => 'tersedia']);
            }

            $buku = Buku::findOrFail($peminjaman->buku_id);
            $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);

            $user = User::where('email', $peminjaman->anggota->email)->first();
            if ($user) {
                NotifikasiService::pengembalianBerhasil($user->id, $peminjaman);
            }

            return redirect()->back()->with('success', 'Pengembalian buku berhasil disetujui dan eksemplar telah dikembalikan!');
        }

        return redirect()->back()->with('error', 'Status peminjaman tidak valid.');
    }

    public function setujuiSemuaKembali()
    {
        $data = Peminjaman::where(function ($q) {
            $q->where('status', 'menunggu_pengembalian')
                ->orWhere(function ($q2) {
                    $q2->where('status', 'menunggu_konfirmasi')
                        ->where('tipe_konfirmasi', 'kembali');
                });
        })
            ->get();

        $jumlah = 0;

        foreach ($data as $peminjaman) {

            $tanggalKembali = Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
            $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

            $hitungDenda = 0;

            if ($hariIni->gt($tanggalKembali)) {
                $selisihHari = ceil(abs($hariIni->diffInDays($tanggalKembali)));
                $hitungDenda = DendaHelper::hitung($selisihHari);
            }

            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_dikembalikan' => $hariIni->toDateString(),
                'denda' => $hitungDenda,
            ]);

            if ($hitungDenda > 0) {
                Denda::updateOrCreate(
                    ['peminjaman_id' => $peminjaman->id],
                    [
                        'jumlah_denda' => $hitungDenda,
                        'status' => 'belum_dibayar',
                        'keterangan' => 'Terlambat '.$selisihHari.' hari',
                    ]
                );
            }

            // Kembalikan status eksemplar
            if ($peminjaman->eksemplar) {
                $peminjaman->eksemplar->update(['status' => 'tersedia']);
            }

            // Update stok buku
            if ($buku = Buku::find($peminjaman->buku_id)) {
                $buku->update(['stok' => $buku->eksemplarTersedia()->count()]);
            }

            $jumlah++;
        }

        return back()->with(
            'success',
            $jumlah.' pengembalian berhasil disetujui.'
        );
    }
}
