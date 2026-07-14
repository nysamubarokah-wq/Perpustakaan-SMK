<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$b = App\Models\Buku::with('genre')->first();
echo "Buku[0]: id=" . $b->id . PHP_EOL;
echo "  genre_id=" . $b->genre_id . PHP_EOL;
echo "  genre_col=" . ($b->genre ?? 'NULL') . PHP_EOL;
echo "  genre rel=" . ($b->genre ? 'ADA' : 'NULL') . PHP_EOL;
if ($b->genre) {
    echo "  genre->id=" . $b->genre->id . PHP_EOL;
    echo "  genre->nama=" . $b->genre->nama . PHP_EOL;
}

echo PHP_EOL;
$genres = App\Models\Genre::take(5)->get();
foreach ($genres as $g) {
    echo "Genre[$g->id]: $g->nama" . PHP_EOL;
}

echo PHP_EOL;
$bukuWithGenre2 = App\Models\Buku::where('genre_id', 2)->with('genre')->first();
if ($bukuWithGenre2) {
    echo "Buku with genre_id=2: " . $bukuWithGenre2->judul . PHP_EOL;
    echo "  genre rel=" . ($bukuWithGenre2->genre ? 'ADA' : 'NULL') . PHP_EOL;
    if ($bukuWithGenre2->genre) {
        echo "  genre->nama=" . $bukuWithGenre2->genre->nama . PHP_EOL;
    }
}
