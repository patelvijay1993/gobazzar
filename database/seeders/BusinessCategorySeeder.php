<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessCategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Automotive', 'icon' => '🚗', 'subs' => [
                'Auto Repair', 'Car Dealers', 'Used Cars', 'Car Wash', 'Auto Detailing',
                'Tire Shops', 'Oil Change', 'Auto Glass', 'Towing', 'Auto Parts', 'EV Charging',
            ]],
            ['name' => 'Home Services', 'icon' => '🏠', 'subs' => [
                'Electricians', 'Plumbers', 'HVAC', 'Roofing', 'Flooring', 'Painting',
                'Handyman', 'Landscaping', 'Snow Removal', 'Pest Control', 'Cleaning Services',
                'Window Cleaning', 'Junk Removal', 'Locksmiths',
            ]],
            ['name' => 'Construction & Contractors', 'icon' => '🏗️', 'subs' => [
                'General Contractors', 'Renovation', 'Basement Development', 'Kitchen Remodeling',
                'Bathroom Remodeling', 'Deck Builders', 'Concrete', 'Masonry', 'Drywall', 'Excavation',
            ]],
            ['name' => 'Real Estate', 'icon' => '🏡', 'subs' => [
                'Realtors', 'Property Management', 'Mortgage Brokers', 'Home Inspectors',
                'Appraisers', 'Real Estate Lawyers', 'Commercial Real Estate',
            ]],
            ['name' => 'Legal Services', 'icon' => '⚖️', 'subs' => [
                'Lawyers', 'Notaries', 'Immigration Lawyers', 'Family Law',
                'Criminal Law', 'Personal Injury', 'Wills & Estates',
            ]],
            ['name' => 'Financial Services', 'icon' => '💰', 'subs' => [
                'Accountants', 'Tax Consultants', 'Financial Advisors', 'Mortgage Brokers',
                'Insurance Agencies', 'Investment Advisors', 'Payroll Services',
            ]],
            ['name' => 'Health & Medical', 'icon' => '🏥', 'subs' => [
                'Family Doctors', 'Dentists', 'Orthodontists', 'Physiotherapy', 'Massage Therapy',
                'Chiropractors', 'Optometrists', 'Hearing Clinics', 'Pharmacies', 'Walk-in Clinics',
            ]],
            ['name' => 'Beauty & Personal Care', 'icon' => '💅', 'subs' => [
                'Hair Salons', 'Barbers', 'Nail Salons', 'Spa', 'Makeup Artists',
                'Skincare', 'Tattoo Studios', 'Laser Clinics',
            ]],
            ['name' => 'Fitness & Wellness', 'icon' => '💪', 'subs' => [
                'Gyms', 'Yoga Studios', 'Personal Trainers', 'Martial Arts',
                'Dance Studios', 'Nutritionists', 'Wellness Clinics',
            ]],
            ['name' => 'Restaurants & Food', 'icon' => '🍽️', 'subs' => [
                'Restaurants', 'Cafés', 'Bakeries', 'Catering', 'Food Trucks', 'Fast Food',
                'Pizza', 'Indian Restaurants', 'Chinese Restaurants', 'Grocery Stores', 'Butcher Shops',
            ]],
            ['name' => 'Hotels & Travel', 'icon' => '✈️', 'subs' => [
                'Hotels', 'Motels', 'Bed & Breakfast', 'Travel Agencies',
                'Tour Operators', 'Car Rentals', 'Airport Shuttle',
            ]],
            ['name' => 'Education', 'icon' => '🎓', 'subs' => [
                'Schools', 'Colleges', 'Universities', 'Daycares', 'Tutors',
                'Driving Schools', 'Music Schools', 'Language Schools',
            ]],
            ['name' => 'Professional Services', 'icon' => '💼', 'subs' => [
                'Consultants', 'Business Consultants', 'Marketing Agencies', 'Graphic Designers',
                'Web Designers', 'Printing Services', 'Translation Services',
            ]],
            ['name' => 'Technology', 'icon' => '💻', 'subs' => [
                'Computer Repair', 'IT Support', 'Software Companies', 'Cybersecurity',
                'Web Development', 'Mobile App Development', 'Cloud Services',
            ]],
            ['name' => 'Retail Stores', 'icon' => '🛍️', 'subs' => [
                'Clothing Stores', 'Shoe Stores', 'Jewelry Stores', 'Furniture Stores',
                'Electronics Stores', 'Gift Shops', 'Pet Stores',
            ]],
            ['name' => 'Entertainment', 'icon' => '🎉', 'subs' => [
                'DJs', 'Event Planners', 'Wedding Services', 'Photographers',
                'Videographers', 'Musicians', 'Party Rentals',
            ]],
            ['name' => 'Community & Organizations', 'icon' => '🤝', 'subs' => [
                'Nonprofits', 'Religious Organizations', 'Cultural Associations',
                'Community Centers', 'Charities',
            ]],
            ['name' => 'Manufacturing & Industrial', 'icon' => '🏭', 'subs' => [
                'Manufacturers', 'Industrial Equipment', 'Packaging', 'Welding', 'Fabrication', 'Machine Shops',
            ]],
            ['name' => 'Agriculture', 'icon' => '🌾', 'subs' => [
                'Farms', 'Garden Centers', 'Greenhouses', 'Livestock', 'Farm Equipment',
            ]],
            ['name' => 'Pet Services', 'icon' => '🐾', 'subs' => [
                'Veterinarians', 'Grooming', 'Boarding', 'Pet Sitting', 'Dog Walking', 'Pet Training',
            ]],
            ['name' => 'Transportation', 'icon' => '🚌', 'subs' => [
                'Taxi', 'Limousine', 'Courier', 'Freight', 'Moving',
            ]],
        ];

        $existingNames = DB::table('categories')
            ->where('type', 'directory')
            ->pluck('name')
            ->map(fn ($n) => strtolower(trim($n)))
            ->toArray();

        $sort = 100; // start after existing categories

        foreach ($data as $parent) {
            $parentNameLower = strtolower(trim($parent['name']));

            // Insert parent if not exists
            if (!in_array($parentNameLower, $existingNames)) {
                $slug = 'biz-' . Str::slug($parent['name']);
                // ensure slug uniqueness
                $slugBase = $slug;
                $i = 2;
                while (DB::table('categories')->where('slug', $slug)->exists()) {
                    $slug = $slugBase . '-' . $i++;
                }

                DB::table('categories')->insert([
                    'type'       => 'directory',
                    'name'       => $parent['name'],
                    'slug'       => $slug,
                    'icon'       => $parent['icon'],
                    'parent_id'  => null,
                    'is_active'  => 1,
                    'sort_order' => $sort,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $existingNames[] = $parentNameLower;
            }

            $parentId = DB::table('categories')
                ->where('type', 'directory')
                ->whereNull('parent_id')
                ->whereRaw('LOWER(name) = ?', [$parentNameLower])
                ->value('id');

            if (!$parentId) { $sort++; continue; }

            // Insert subcategories
            $subSort = 1;
            foreach ($parent['subs'] as $subName) {
                $subNameLower = strtolower(trim($subName));
                $alreadyExists = DB::table('categories')
                    ->where('type', 'directory')
                    ->where('parent_id', $parentId)
                    ->whereRaw('LOWER(name) = ?', [$subNameLower])
                    ->exists();

                if (!$alreadyExists) {
                    $slug = 'biz-' . Str::slug($subName);
                    $slugBase = $slug;
                    $i = 2;
                    while (DB::table('categories')->where('slug', $slug)->exists()) {
                        $slug = $slugBase . '-' . $i++;
                    }

                    DB::table('categories')->insert([
                        'type'       => 'directory',
                        'name'       => $subName,
                        'slug'       => $slug,
                        'icon'       => null,
                        'parent_id'  => $parentId,
                        'is_active'  => 1,
                        'sort_order' => $subSort,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $subSort++;
            }

            $sort++;
        }

        $this->command->info('Business categories seeded successfully.');
    }
}
