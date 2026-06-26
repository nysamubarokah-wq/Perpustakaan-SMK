<?php

namespace App\Helpers;

use App\Models\Background;

class Backgrounds
{
 public static function list()
{
    $backgrounds = Background::orderBy('harga')->get()
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

    return [
        'default' => [
            'nama'  => 'Default',
            'harga' => 0,
            'type'  => 'color',
            'value' => 'background: linear-gradient(135deg,#1a6e35,#27ae60)',
        ]
    ] + $backgrounds;
}
}