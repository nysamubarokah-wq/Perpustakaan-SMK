<?php

namespace App\Http\Controllers\Admin;

use App\Models\Denda;
use App\Models\Peminjaman;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DendaController extends Controller
{
    public function index(Request $request)
    {
        $this->syncDendaTerlambat();

        $query = Denda::with(['peminjaman.anggota', 'peminjaman.buku']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('peminjaman.anggota', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('peminjaman.buku', fn($q) => $q->where('judul', 'like', "%{$search}%"));
        }

        if ($request->filled('filter_pengembalian')) {
            if ($request->filter_pengembalian === 'belum') {
                $query->whereHas('peminjaman', fn($q) => $q->where('status', '!=', 'dikembalikan'));
            } elseif ($request->filter_pengembalian === 'sudah') {
                $query->whereHas('peminjaman', fn($q) => $q->where('status', 'dikembalikan'));
            }
        }

        $dendas = $query->orderByDesc('created_at')->get();

        $totalBelumBayar = Denda::where('status', 'belum_dibayar')->sum('jumlah_denda');
        $totalSudahBayar = Denda::where('status', 'sudah_dibayar')->sum('jumlah_denda');

        return view('admin.denda.index', compact('dendas', 'totalBelumBayar', 'totalSudahBayar'));
    }

    private function syncDendaTerlambat()
    {
        $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();

        $terlambat = Peminjaman::where('status', 'dipinjam')
            ->whereNotNull('tanggal_kembali')
            ->whereDate('tanggal_kembali', '<', $hariIni)
            ->get();

        foreach ($terlambat as $p) {
            $sudahAda = Denda::where('peminjaman_id', $p->id)->exists();
            if (!$sudahAda) {
                $tglKembali = Carbon::parse($p->tanggal_kembali)->startOfDay();
                $selisihHari = (int) $tglKembali->diffInDays($hariIni);
                $jumlahDenda = $selisihHari * 1000;

                $p->update(['denda' => $jumlahDenda]);

                Denda::create([
                    'peminjaman_id' => $p->id,
                    'jumlah_denda'  => $jumlahDenda,
                    'status'        => 'belum_dibayar',
                    'keterangan'    => "Terlambat {$selisihHari} hari",
                ]);
            }
        }
    }

    public function lunasi($id)
    {
        $denda = Denda::findOrFail($id);
        $denda->update([
            'status' => 'sudah_dibayar',
            'tanggal_bayar' => now()->toDateString()
        ]);

        return redirect()->back()->with('success', 'Denda berhasil dilunasi!');
    }

    public function lunasiSemua()
    {
        $updated = Denda::where('status', 'belum_dibayar')
            ->whereHas('peminjaman', fn($q) => $q->where('status', 'dikembalikan'))
            ->update([
                'status' => 'sudah_dibayar',
                'tanggal_bayar' => now()->toDateString()
            ]);

        if ($updated > 0) {
            return redirect()->back()->with('success', "{$updated} denda berhasil dilunasi!");
        }
        return redirect()->back()->with('error', 'Tidak ada denda yang bisa dilunasi.');
    }

    public function hapusBanyak(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih denda yang akan dihapus.');
        }
        Denda::destroy($ids);
        return redirect()->back()->with('success', count($ids) . ' denda berhasil dihapus!');
    }

    public function destroy($id)
    {
        Denda::destroy($id);
        return redirect()->back()->with('success', 'Denda berhasil dihapus!');
    }
}