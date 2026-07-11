<?php

namespace App\Helpers;

class Denda
{
    public static function perHari(): int
    {
        return config('lib.denda_per_hari', 1000);
    }

    public static function hitung(int $hariTerlambat): int
    {
        return $hariTerlambat * self::perHari();
    }
}
