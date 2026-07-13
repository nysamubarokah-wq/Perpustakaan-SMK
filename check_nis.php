<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Semua NIS unik di anggota? ===\n";
$dup = \DB::select("SELECT nis, COUNT(*) as cnt FROM anggota WHERE nis IS NOT NULL GROUP BY nis HAVING cnt > 1");
if (count($dup) == 0) { echo "Tidak ada duplikat di anggota\n"; }
foreach($dup as $d) { echo "- $d->nis ($d->cnt anggota)\n"; }

echo "\n=== Semua NIS unik di users? ===\n";
$dup2 = \DB::select("SELECT nis, COUNT(*) as cnt FROM users WHERE nis IS NOT NULL GROUP BY nis HAVING cnt > 1");
if (count($dup2) == 0) { echo "Tidak ada duplikat di users\n"; }
foreach($dup2 as $d) { echo "- $d->nis ($d->cnt users)\n"; }

echo "\n=== Semua anggota dengan NIS ===\n";
$a = \App\Models\Anggota::whereNotNull('nis')->get();
foreach($a as $x) { echo "ID:$x->id | NIS:$x->nis | Nama:$x->nama | Role:$x->role | UserID:$x->user_id\n"; }

echo "\n=== Semua users dengan NIS ===\n";
$u = \App\Models\User::whereNotNull('nis')->get();
foreach($u as $x) { echo "ID:$x->id | NIS:$x->nis | Nama:$x->name | Role:$x->role\n"; }
