<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EksemplarBuku extends Model
{
    protected $table = 'eksemplar_buku';

    protected $fillable = ['buku_id', 'kode_buku', 'qrcode_path', 'status', 'kondisi'];

    protected static function booted()
    {
        static::created(function ($eksemplar) {
            $eksemplar->generateQr();
        });

        static::updating(function ($eksemplar) {
            if ($eksemplar->isDirty('kode_buku')) {
                $eksemplar->hapusQrFile();
            }
        });

        static::updated(function ($eksemplar) {
            if ($eksemplar->wasChanged('kode_buku')) {
                $eksemplar->generateQr();
            }
        });

        static::deleting(function ($eksemplar) {
            $eksemplar->hapusQrFile();
        });
    }

    public static function generateKodeEksemplar(): string
    {
        $last = self::whereNotNull('kode_buku')
            ->where('kode_buku', 'LIKE', 'BK%')
            ->orderByRaw("CAST(SUBSTRING(kode_buku, 3) AS UNSIGNED) DESC")
            ->first();

        if ($last && preg_match('/BK(\d+)/', $last->kode_buku, $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1;
        }

        return 'BK' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function generateQr(): void
    {
        if (empty($this->kode_buku)) return;

        $dir = public_path('qrcode/eksemplar');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $this->kode_buku . '.svg';
        $path = $dir . '/' . $filename;

        $svg = QrCode::size(300)->generate($this->kode_buku);
        file_put_contents($path, $svg);

        $this->updateQuietly(['qrcode_path' => 'qrcode/eksemplar/' . $filename]);
    }

    public function hapusQrFile(): void
    {
        if ($this->qrcode_path && file_exists(public_path($this->qrcode_path))) {
            unlink(public_path($this->qrcode_path));
        }
    }

    public function getQrExistsAttribute(): bool
    {
        return !empty($this->qrcode_path) && file_exists(public_path($this->qrcode_path));
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'eksemplar_id');
    }

    public function peminjamanAktif()
    {
        return $this->hasOne(Peminjaman::class, 'eksemplar_id')
            ->whereIn('status', ['dipinjam', 'menunggu_konfirmasi', 'menunggu_pengembalian']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => 'Tersedia',
            'dipinjam' => 'Dipinjam',
            'rusak' => 'Rusak',
            'hilang' => 'Hilang',
            'maintenance' => 'Maintenance',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => '#1a6e35',
            'dipinjam' => '#856404',
            'rusak' => '#721c24',
            'hilang' => '#721c24',
            'maintenance' => '#6d28d9',
            default => '#555',
        };
    }

    public function getStatusBgAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => '#d4edda',
            'dipinjam' => '#fff3cd',
            'rusak' => '#f8d7da',
            'hilang' => '#f8d7da',
            'maintenance' => '#ede9fe',
            default => '#f0f0f0',
        };
    }
}
