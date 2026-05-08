<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DirectoryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $existing = DB::table('categories')->where('type', 'directory')->pluck('name')->toArray();

        $cats = [
            ['name' => 'Education',  'icon' => '🎓', 'slug' => 'dir-education',  'sort_order' => 7],
            ['name' => 'Sports',     'icon' => '🏅', 'slug' => 'dir-sports',     'sort_order' => 8],
            ['name' => 'Medical',    'icon' => '🏥', 'slug' => 'dir-medical',    'sort_order' => 9],
            ['name' => 'Dental',     'icon' => '🦷', 'slug' => 'dir-dental',     'sort_order' => 10],
            ['name' => 'Salon & Spa','icon' => '💅', 'slug' => 'dir-salon-spa',  'sort_order' => 11],
            ['name' => 'Fashion',    'icon' => '👗', 'slug' => 'dir-fashion',    'sort_order' => 12],
            ['name' => 'Jewelry',    'icon' => '💎', 'slug' => 'dir-jewelry',    'sort_order' => 13],
        ];

        foreach ($cats as $cat) {
            if (!in_array($cat['name'], $existing)) {
                DB::table('categories')->insert([
                    'type'       => 'directory',
                    'name'       => $cat['name'],
                    'slug'       => $cat['slug'],
                    'icon'       => $cat['icon'],
                    'is_active'  => 1,
                    'sort_order' => $cat['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
