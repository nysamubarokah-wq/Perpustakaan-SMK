<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select('SHOW COLUMNS FROM anggota');
echo "Kolom di tabel anggota:\n";
foreach($columns as $c) {
    echo "- " . $c->Field . "\n";
}
