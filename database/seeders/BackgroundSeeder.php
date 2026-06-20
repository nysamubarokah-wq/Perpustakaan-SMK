<?php

namespace Database\Seeders;

use App\Models\Background;
use Illuminate\Database\Seeder;

class BackgroundSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['slug' => 'default',  'nama' => 'Default',        'harga' => 0,  'type' => 'color', 'value' => 'background: linear-gradient(135deg, #1a6e35, #27ae60);'],
            ['slug' => 'ocean',    'nama' => 'Ocean Blue',     'harga' => 10, 'type' => 'color', 'value' => 'background: linear-gradient(135deg, #2c3e50, #3498db);'],
            ['slug' => 'sunset',   'nama' => 'Sunset',         'harga' => 10, 'type' => 'color', 'value' => 'background: linear-gradient(135deg, #d35400, #e74c3c);'],
            ['slug' => 'purple',   'nama' => 'Purple Dream',   'harga' => 15, 'type' => 'color', 'value' => 'background: linear-gradient(135deg, #8e44ad, #e056fd);'],
            ['slug' => 'midnight', 'nama' => 'Midnight',       'harga' => 20, 'type' => 'color', 'value' => 'background: linear-gradient(135deg, #1a1a2e, #16213e);'],
            ['slug' => 'cherry',   'nama' => 'Cherry Blossom', 'harga' => 25, 'type' => 'color', 'value' => 'background: linear-gradient(135deg, #fd79a8, #e84393);'],
            ['slug' => 'gold',     'nama' => 'Gold',           'harga' => 30, 'type' => 'color', 'value' => 'background: linear-gradient(135deg, #f9ca24, #f0932b);'],
            ['slug' => 'galaxy',   'nama' => 'Galaxy',         'harga' => 50, 'type' => 'color', 'value' => 'background: linear-gradient(135deg, #0c0c0c, #6c5ce7, #a29bfe);'],
        ];

        foreach ($data as $bg) {
            Background::updateOrCreate(['slug' => $bg['slug']], $bg);
        }
    }
}