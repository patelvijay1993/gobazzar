<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Ontario', 'Toronto'],
            ['Ontario', 'Brampton'],
            ['Ontario', 'Mississauga'],
            ['Ontario', 'Markham'],
            ['Ontario', 'Scarborough'],
            ['Ontario', 'North York'],
            ['Ontario', 'Etobicoke'],
            ['Ontario', 'Hamilton'],
            ['Ontario', 'Ottawa'],
            ['Ontario', 'London'],
            ['Ontario', 'Windsor'],
            ['Ontario', 'Kitchener'],
            ['Ontario', 'Vaughan'],
            ['Ontario', 'Ajax'],
            ['Ontario', 'Pickering'],
            ['British Columbia', 'Vancouver'],
            ['British Columbia', 'Surrey'],
            ['British Columbia', 'Burnaby'],
            ['British Columbia', 'Richmond'],
            ['British Columbia', 'Abbotsford'],
            ['British Columbia', 'Kelowna'],
            ['British Columbia', 'Victoria'],
            ['Alberta', 'Calgary'],
            ['Alberta', 'Edmonton'],
            ['Alberta', 'Red Deer'],
            ['Alberta', 'Lethbridge'],
            ['Quebec', 'Montreal'],
            ['Quebec', 'Quebec City'],
            ['Quebec', 'Laval'],
            ['Manitoba', 'Winnipeg'],
            ['Saskatchewan', 'Saskatoon'],
            ['Saskatchewan', 'Regina'],
            ['Nova Scotia', 'Halifax'],
            ['New Brunswick', 'Moncton'],
            ['New Brunswick', 'Fredericton'],
        ];

        foreach ($data as $i => [$province, $city]) {
            Location::firstOrCreate(
                ['province' => $province, 'city' => $city],
                ['is_active' => true, 'sort_order' => $i]
            );
        }
    }
}
