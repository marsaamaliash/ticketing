<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Instalasi', 'description' => 'Pemasangan baru layanan'],
            ['name' => 'Maintenance', 'description' => 'Pemeliharaan berkala'],
            ['name' => 'Gangguan', 'description' => 'Laporan gangguan / kerusakan'],
            ['name' => 'Keluhan', 'description' => 'Keluhan pelanggan umum'],
            ['name' => 'Lain-lain', 'description' => 'Kategori lainnya'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                $cat
            );
        }
    }
}
