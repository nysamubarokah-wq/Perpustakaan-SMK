<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Buku extends Model
{
    protected $table = 'buku';

    protected $fillable = ['judul', 'pengarang', 'penerbit', 'tahun_terbit', 'isbn', 'kode_buku', 'stok', 'genre', 'genre_id', 'penerbit_id', 'sampul', 'rekom_bg', 'deskripsi', 'lokasi', 'qrcode_path'];

    protected static function booted()
    {
        static::creating(function ($buku) {
            if (empty($buku->kode_buku)) {
                $buku->kode_buku = self::generateKodeBuku();
            }
        });

        static::created(function ($buku) {
            $buku->generateQr();
        });

        static::updating(function ($buku) {
            if ($buku->isDirty('kode_buku')) {
                $buku->hapusQrFile();
            }
        });

        static::updated(function ($buku) {
            if ($buku->wasChanged('kode_buku')) {
                $buku->generateQr();
            }
        });

        static::deleting(function ($buku) {
            $buku->hapusQrFile();
        });
    }

    public static function generateKodeBuku(): string
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

        return 'BK' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function generateQr(): void
    {
        if (empty($this->kode_buku)) return;

        $dir = public_path('qrcode');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $this->kode_buku . '.svg';
        $path = $dir . '/' . $filename;

        $svg = QrCode::size(300)->generate($this->kode_buku);
        file_put_contents($path, $svg);

        $this->updateQuietly(['qrcode_path' => 'qrcode/' . $filename]);
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

    // ============================================================
    // RELATIONS
    // ============================================================

    public function eksemplar()
    {
        return $this->hasMany(EksemplarBuku::class, 'buku_id');
    }

    public function eksemplarTersedia()
    {
        return $this->hasMany(EksemplarBuku::class, 'buku_id')->where('status', 'tersedia');
    }

    public function peminjaman()
    {
        return $this->hasMany(\App\Models\Peminjaman::class, 'buku_id');
    }

    public function favorit()
    {
        return $this->hasMany(\App\Models\Favorit::class, 'buku_id');
    }

    public function ulasan()
    {
        return $this->hasMany(\App\Models\Ulasan::class, 'buku_id');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    public function penerbit()
    {
        return $this->belongsTo(\App\Models\Penerbit::class, 'penerbit_id');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    public function getStokTersediaAttribute(): int
    {
        return $this->eksemplar()->where('status', 'tersedia')->count();
    }

    public function getTotalEksemplarAttribute(): int
    {
        return $this->eksemplar()->count();
    }
}
