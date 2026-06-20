<?php

namespace App\Helpers;

use App\Models\Background;

class Backgrounds
{
    public static function list()
    {
        return Background::orderBy('harga')->get()
            ->keyBy('slug')
            ->map(function ($bg) {
                return [
                    'nama'  => $bg->nama,
                    'harga' => $bg->harga,
                    'type'  => $bg->type,
                    'value' => $bg->value,
                ];
            })
            ->toArray();
    }
}