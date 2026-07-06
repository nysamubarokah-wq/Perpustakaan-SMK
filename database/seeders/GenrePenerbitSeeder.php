<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Penerbit;
use App\Models\Buku;
use Illuminate\Database\Seeder;

class GenrePenerbitSeeder extends Seeder
{
    public function run(): void
    {
        // Seed default genres
        $defaultGenres = ['Fiksi', 'Non-Fiksi', 'Kejuruan', 'Sains & Teknologi', 'Sejarah', 'Romance', 'Pendidikan', 'Seni & Budaya'];
        foreach ($defaultGenres as $nama) {
            Genre::firstOrCreate(['nama' => $nama]);
        }

        // Migrate existing book's genre and publisher to new tables
        $bukuList = Buku::whereNull('genre_id')->orWhereNull('penerbit_id')->get();

        foreach ($bukuList as $buku) {
            if (empty($buku->genre_id) && !empty($buku->genre)) {
                $genre = Genre::firstOrCreate(['nama' => trim($buku->genre)]);
                $buku->genre_id = $genre->id;
            }

            if (empty($buku->penerbit_id) && !empty($buku->penerbit)) {
                $penerbit = Penerbit::firstOrCreate(['nama' => trim($buku->penerbit)]);
                $buku->penerbit_id = $penerbit->id;
            }

            $buku->saveQuietly();
        }
    }
}
