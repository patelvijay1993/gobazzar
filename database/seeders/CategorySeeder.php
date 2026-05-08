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
            // Classifieds
            ['name' => 'Real Estate',   'icon' => '🏠', 'type' => 'classifieds', 'sort_order' => 1],
            ['name' => 'Buy & Sell',    'icon' => '🛒', 'type' => 'classifieds', 'sort_order' => 2],
            ['name' => 'Services',      'icon' => '🔧', 'type' => 'classifieds', 'sort_order' => 3],
            ['name' => 'Roommates',     'icon' => '🏡', 'type' => 'classifieds', 'sort_order' => 4],
            ['name' => 'Autos',         'icon' => '🚗', 'type' => 'classifieds', 'sort_order' => 5],
            ['name' => 'Matrimonial',   'icon' => '💍', 'type' => 'classifieds', 'sort_order' => 6],
            ['name' => 'Travel',        'icon' => '✈️', 'type' => 'classifieds', 'sort_order' => 7],
            ['name' => 'General',       'icon' => '📦', 'type' => 'classifieds', 'sort_order' => 8],
            // Jobs
            ['name' => 'IT & Software', 'icon' => '💻', 'type' => 'jobs', 'sort_order' => 1],
            ['name' => 'Accounting',    'icon' => '📊', 'type' => 'jobs', 'sort_order' => 2],
            ['name' => 'Healthcare',    'icon' => '🏥', 'type' => 'jobs', 'sort_order' => 3],
            ['name' => 'Education',     'icon' => '📚', 'type' => 'jobs', 'sort_order' => 4],
            ['name' => 'Retail',        'icon' => '🏪', 'type' => 'jobs', 'sort_order' => 5],
            // Events
            ['name' => 'Festival',      'icon' => '🎆', 'type' => 'events', 'sort_order' => 1],
            ['name' => 'Music & Dance', 'icon' => '🎵', 'type' => 'events', 'sort_order' => 2],
            ['name' => 'Religious',     'icon' => '🙏', 'type' => 'events', 'sort_order' => 3],
            ['name' => 'Sports',        'icon' => '🏏', 'type' => 'events', 'sort_order' => 4],
            ['name' => 'Food',          'icon' => '🍽️', 'type' => 'events', 'sort_order' => 5],
            // Directory
            ['name' => 'Restaurant',    'icon' => '🍛', 'type' => 'directory', 'sort_order' => 1],
            ['name' => 'Immigration',   'icon' => '🛂', 'type' => 'directory', 'sort_order' => 2],
            ['name' => 'Real Estate Agent', 'icon' => '🏠', 'type' => 'directory', 'sort_order' => 3],
            ['name' => 'Travel Agency', 'icon' => '✈️', 'type' => 'directory', 'sort_order' => 4],
            ['name' => 'Grocery',       'icon' => '🛒', 'type' => 'directory', 'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, ['slug' => Str::slug($cat['name'])])
            );
        }
    }
}
