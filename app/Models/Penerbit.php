<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerbit extends Model
{
    protected $table = 'penerbit';
    protected $fillable = ['nama'];
    public $timestamps = false;

    public static function findOrCreate(string $nama): self
    {
        $nama = trim($nama);
        return static::firstOrCreate(['nama' => $nama]);
    }

    public function buku()
    {
        return $this->hasMany(Buku::class, 'penerbit_id');
    }
}
