<?php

namespace App\Console\Commands;

use App\Helpers\Denda as DendaHelper;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\NotifikasiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAutoNotifications extends Command
{
    protected $signature = 'notifikasi:auto';
    protected $description = 'Kirim notifikasi otomatis (jatuh tempo, keterlambatan, VIP)';

    public function handle(): int
    {
        $this->info('Memulai pengiriman notifikasi otomatis...');

        $this->kirimPengingatJatuhTempo();
        $this->kirimNotifikasiTerlambat();
        $this->kirimNotifikasiVipHampirHabis();

        $this->info('Selesai.');
        return Command::SUCCESS;
    }

    private function kirimPengingatJatuhTempo(): void
    {
        $besok = Carbon::tomorrow('Asia/Jakarta')->toDateString();

        $peminjaman = Peminjaman::with(['buku', 'anggota'])
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_kembali', $besok)
            ->get();

        $count = 0;
        foreach ($peminjaman as $pinjam) {
            $user = User::where('email', $pinjam->anggota->email)->first();
            if ($user) {
                $result = NotifikasiService::pengingatJatuhTempo($user->id, $pinjam);
                if ($result) $count++;
            }
        }

        $this->info("Pengingat jatuh tempo: {$count} notifikasi dikirim.");
    }

    private function kirimNotifikasiTerlambat(): void
    {
        $hariIni = Carbon::today('Asia/Jakarta')->toDateString();

        $peminjaman = Peminjaman::with(['buku', 'anggota'])
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_kembali', '<', $hariIni)
            ->get();

        $count = 0;

        foreach ($peminjaman as $pinjam) {
            $user = User::where('email', $pinjam->anggota->email)->first();
            if (!$user) continue;

            $tanggalKembali = Carbon::parse($pinjam->tanggal_kembali)->startOfDay();
            $selisihHari = ceil(abs(Carbon::today('Asia/Jakarta')->startOfDay()->diffInDays($tanggalKembali)));
            $denda = DendaHelper::hitung($selisihHari);

            $result = NotifikasiService::bukuTerlambat($user->id, $pinjam, $selisihHari, $denda);
            if ($result) $count++;
        }

        $this->info("Notifikasi keterlambatan: {$count} notifikasi dikirim.");
    }

    private function kirimNotifikasiVipHampirHabis(): void
    {
        $tigaHariLagi = Carbon::today('Asia/Jakarta')->addDays(3)->toDateString();

        $users = User::where('is_vip', true)
            ->whereNotNull('vip_expired_at')
            ->whereDate('vip_expired_at', '<=', $tigaHariLagi)
            ->whereDate('vip_expired_at', '>=', Carbon::today('Asia/Jakarta')->toDateString())
            ->get();

        $count = 0;
        foreach ($users as $user) {
            $hariTersisa = (int) Carbon::today('Asia/Jakarta')->diffInDays(Carbon::parse($user->vip_expired_at));
            $result = NotifikasiService::vipHampirHabis($user->id, $hariTersisa);
            if ($result) $count++;
        }

        $this->info("Notifikasi VIP hampir habis: {$count} notifikasi dikirim.");
    }
}
