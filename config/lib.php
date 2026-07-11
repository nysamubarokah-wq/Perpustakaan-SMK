<?php

return [
    'denda_per_hari' => env('DENDA_PER_HARI', 1000),
    'max_pinjam_reguler' => env('MAX_PINJAM_REGULER', 3),
    'max_pinjam_vip' => env('MAX_PINJAM_VIP', 6),
    'durasi_pinjam_reguler' => env('DURASI_PINJAM_REGULER', 7),
    'durasi_pinjam_vip' => env('DURASI_PINJAM_VIP', 14),
    'harga_vip' => env('HARGA_VIP', 100),
    'durasi_vip_hari' => env('DURASI_VIP_HARI', 7),
    'coin_per_pinjam' => env('COIN_PER_PINJAM', 10),
];
