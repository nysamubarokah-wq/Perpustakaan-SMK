<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class UlasanController extends Controller
{

public function index(Request $request)
{
    $query = Ulasan::with(['user', 'buku']);

    // Filter rating
    if ($request->filled('rating')) {
        $query->where('rating', $request->rating);
    }

    // Filter buku
    if ($request->filled('buku_id')) {
        $query->where('buku_id', $request->buku_id);
    }

    // Filter status balasan
    if ($request->filled('status_balasan')) {
        if ($request->status_balasan === 'sudah') {
            $query->whereNotNull('balasan_admin');
        } elseif ($request->status_balasan === 'belum') {
            $query->whereNull('balasan_admin');
        }
    }

    // Search keyword
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('user', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('buku', function ($q2) use ($search) {
                $q2->where('judul', 'like', "%{$search}%");
            })
            ->orWhere('komentar', 'like', "%{$search}%");
        });
    }

    $ulasanList = $query->latest()->paginate(15)->appends($request->query());
    $bukuList = Buku::orderBy('judul')->get();

    return view('admin.ulasan.index', compact('ulasanList', 'bukuList'));
}

    public function store(Request $request, Buku $buku)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $anggota = Anggota::where('email', $user->email)->first();

        $pernahPinjam = $anggota
            ? Peminjaman::where('anggota_id', $anggota->id)->where('buku_id', $buku->id)->exists()
            : false;

        if (!$pernahPinjam) {
            return back()->with('error', 'Kamu belum pernah meminjam buku ini, jadi belum bisa memberi ulasan.');
        }

        Ulasan::updateOrCreate(
            ['user_id' => $user->id, 'buku_id' => $buku->id],
            ['rating' => $request->rating, 'komentar' => $request->komentar]
        );

        $redirectUrl = session()->get('koleksiFilterUrl', route('koleksi.index'));
        return redirect($redirectUrl)->with('success', 'Ulasan kamu berhasil disimpan. Terima kasih!');
    }

   public function destroy(Ulasan $ulasan)
{
    $isPemilik = $ulasan->user_id === auth()->id();
    $isAdmin = auth()->user()->role === 'admin';

    if (!$isPemilik && !$isAdmin) {
        abort(403);
    }

    $ulasan->delete();

    return back()->with('success', 'Ulasan berhasil dihapus.');
}

public function balas(Request $request, Ulasan $ulasan)
{
    $request->validate([
        'balasan_admin' => 'required|string|max:1000',
    ]);

    $ulasan->update([
        'balasan_admin' => $request->balasan_admin,
    ]);

    // Buat notifikasi untuk user
    Notifikasi::create([
        'user_id' => $ulasan->user_id,
        'ulasan_id' => $ulasan->id,
        'tipe' => 'balasan_admin',
        'judul' => 'Admin Membalas Ulasanmu',
        'pesan' => 'Admin membalas ulasan kamu di buku "' . ($ulasan->buku->judul ?? '-') . '": ' . Str::limit($request->balasan_admin, 100),
    ]);

    return back()->with('success', 'Balasan berhasil dikirim.');
}

public function editBalasan(Request $request, Ulasan $ulasan)
{
    $request->validate([
        'balasan_admin' => 'required|string|max:1000',
    ]);

    $ulasan->update([
        'balasan_admin' => $request->balasan_admin,
    ]);

    return back()->with('success', 'Balasan berhasil diperbarui.');
}

public function hapusBalasan(Ulasan $ulasan)
{
    $ulasan->update([
        'balasan_admin' => null,
    ]);

    return back()->with('success', 'Balasan berhasil dihapus.');
}

public function bulkDelete(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:ulasan,id',
    ]);

    Ulasan::whereIn('id', $request->ids)->delete();

    return back()->with('success', count($request->ids) . ' ulasan berhasil dihapus.');
}

public function export(Request $request)
{
    $query = Ulasan::with(['user', 'buku']);

    if ($request->filled('rating')) {
        $query->where('rating', $request->rating);
    }
    if ($request->filled('buku_id')) {
        $query->where('buku_id', $request->buku_id);
    }
    if ($request->filled('status_balasan')) {
        if ($request->status_balasan === 'sudah') {
            $query->whereNotNull('balasan_admin');
        } elseif ($request->status_balasan === 'belum') {
            $query->whereNull('balasan_admin');
        }
    }

    $ulasanList = $query->latest()->get();

    $csv = "No,User,Buku,Rating,Komentar,Balasan Admin,Tanggal\n";

    foreach ($ulasanList as $i => $item) {
        $csv .= ($i + 1) . ",";
        $csv .= '"' . str_replace('"', '""', $item->user->name ?? '-') . '",';
        $csv .= '"' . str_replace('"', '""', $item->buku->judul ?? '-') . '",';
        $csv .= $item->rating . ",";
        $csv .= '"' . str_replace('"', '""', $item->komentar ?? '-') . '",';
        $csv .= '"' . str_replace('"', '""', $item->balasan_admin ?? '-') . '",';
        $csv .= $item->created_at->format('d M Y H:i') . "\n";
    }

    return Response::make($csv, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="ulasan_export_' . date('Y-m-d') . '.csv"',
    ]);
}
}
