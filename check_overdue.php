<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$anggota = DB::selectOne("SELECT id, nama, user_id FROM anggota WHERE user_id = 2");
echo "Anggota for user 2: " . json_encode($anggota) . "\n";

if ($anggota) {
    DB::update("UPDATE peminjaman SET anggota_id = ? WHERE id IN (32, 33, 34)", [$anggota->id]);
    echo "Updated peminjaman to anggota_id: {$anggota->id}\n";
} else {
    echo "No anggota found for user_id 2\n";
}
