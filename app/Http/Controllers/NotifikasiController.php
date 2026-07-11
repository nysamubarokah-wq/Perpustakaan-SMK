<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
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

        $userId = auth()->id();
        $query = Notification::where('user_id', $userId)
            ->orderByDesc('created_at');

        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter === 'belum_dibaca') {
                $query->where('is_read', false);
            } elseif ($filter === 'dibaca') {
                $query->where('is_read', true);
            }
        }

        $notifikasi = $query->paginate(15);
        $belumDibaca = NotifikasiService::unreadCount($userId);

        return view('notifikasi.index', compact('notifikasi', 'belumDibaca'));
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $notifikasi = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$notifikasi) {
            \Log::info("Notifikasi {$id} tidak ditemukan atau bukan milik user ini");
        }

        $result = NotifikasiService::markRead($id, auth()->id());

        if ($request->wantsJson()) {
            return response()->json(['success' => $result]);
        }

        $linkMap = [
            'coin_bertambah' => '/profil',
            'pinjam_disetujui' => '/profil/riwayat',
            'pinjam_ditolak' => '/koleksi',
            'pengembalian_berhasil' => '/profil/riwayat',
            'pengingat_jatuh_tempo' => '/profil/riwayat',
            'buku_terlambat' => '/profil/riwayat',
            'vip_hampir_habis' => '/vip',
            'permintaan_baru' => '/admin/pinjam',
            'pengembalian_pending' => '/admin/pengembalian',
            'stok_habis' => '/admin/buku',
            'import_berhasil' => '/',
            'import_gagal' => '/',
        ];

        $redirectUrl = $notifikasi ? ($linkMap[$notifikasi->type] ?? '/profil') : '/profil';

        return redirect($redirectUrl);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $count = NotifikasiService::markAllReadForUser(auth()->id());

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan.'], 404);
        }

        $notification->delete();

        if ($request->ajax() || $request->wantsJson() || $request->is('notifikasi/*')) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $count = NotifikasiService::deleteAllForUser(auth()->id());

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function getUnreadCount(): JsonResponse
    {
        $count = NotifikasiService::unreadCount(auth()->id());

        return response()->json(['count' => $count]);
    }

    public function getLatest(): JsonResponse
    {
        $notifikasi = NotifikasiService::getLatest(auth()->id(), 5);

        return response()->json([
            'notifikasi' => $notifikasi,
            'count' => $notifikasi->where('is_read', false)->count(),
        ]);
    }
}
