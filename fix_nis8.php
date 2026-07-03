<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$siswa = DB::table('anggota')->where('role', 'siswa')->get();
foreach ($siswa as $s) {
    DB::table('anggota')->where('id', $s->id)->update(['nis' => (string) rand(10000000, 99999999)]);
}

echo "NIS diupdate ke 8 digit\n";
$sample = DB::table('anggota')->where('role', 'siswa')->limit(3)->get();
foreach($sample as $s) {
    echo "- " . $s->nama . " (NIS: " . $s->nis . ")\n";
}
