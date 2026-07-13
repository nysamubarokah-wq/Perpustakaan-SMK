<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$orphan = \App\Models\User::where('nis', 12345)
    ->whereNotIn('id', function($q) {
        $q->select('user_id')->from('anggota')->whereNotNull('user_id');
    })
    ->first();

if ($orphan) {
    echo "Found orphan user: ID {$orphan->id}, NIS {$orphan->nis}, Name {$orphan->name}\n";
    $orphan->nis = 'ORPHAN-12345';
    $orphan->save();
    echo "Updated orphan user NIS to ORPHAN-12345\n";
} else {
    echo "No orphan user found with NIS 12345\n";
}

echo "\n=== User ID 1 sekarang ===\n";
$u1 = \App\Models\User::find(1);
if ($u1) echo "ID:{$u1->id} | NIS:{$u1->nis} | Name:{$u1->name}\n";
