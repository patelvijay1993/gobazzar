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
            ['name' => 'Buy & Sell', 'icon' => '🛍️', 'subs' => [
                'Electronics', 'Mobile Phones', 'Computers & Tablets', 'TV & Audio', 'Cameras',
                'Home Appliances', 'Furniture', 'Home Décor', 'Clothing', 'Shoes', 'Jewelry',
                'Watches', 'Sports Equipment', 'Musical Instruments', 'Books', 'Collectibles',
                'Toys & Games', 'Baby Items', 'Health & Beauty', 'Office Supplies', 'Free Stuff', 'Other Items',
            ]],
            ['name' => 'Vehicles', 'icon' => '🚗', 'subs' => [
                'Cars', 'SUVs', 'Trucks', 'Vans', 'Motorcycles', 'ATVs & UTVs', 'RVs & Campers',
                'Boats', 'Trailers', 'Heavy Equipment', 'Commercial Vehicles',
                'Auto Parts', 'Tires & Wheels', 'Vehicle Accessories',
            ]],
            ['name' => 'Real Estate', 'icon' => '🏠', 'subs' => [
                'Apartments for Rent', 'Houses for Rent', 'Basement Suites', 'Condos', 'Townhouses',
                'Rooms for Rent', 'Roommates', 'Vacation Rentals', 'Commercial Space',
                'Office Space', 'Retail Space', 'Land for Sale', 'Houses for Sale', 'Condos for Sale',
            ]],
            ['name' => 'Jobs', 'icon' => '💼', 'subs' => [
                'Full-Time', 'Part-Time', 'Contract', 'Temporary', 'Internship', 'Remote Jobs',
                'Government Jobs', 'Healthcare', 'IT & Technology', 'Construction', 'Retail',
                'Hospitality', 'Drivers', 'Skilled Trades', 'Customer Service',
                'Education', 'Accounting', 'Sales & Marketing',
            ]],
            ['name' => 'Services', 'icon' => '🔧', 'subs' => [
                'Home Cleaning', 'Plumbing', 'Electrical', 'HVAC', 'Roofing', 'Painting',
                'Renovation', 'Landscaping', 'Snow Removal', 'Appliance Repair', 'Auto Repair',
                'Computer Repair', 'Web Design', 'Graphic Design', 'Photography', 'Videography',
                'Event Planning', 'Catering', 'DJ Services', 'Moving Services', 'Tutoring',
                'Tax Preparation', 'Legal Services', 'Immigration Services',
            ]],
            ['name' => 'Community', 'icon' => '🤝', 'subs' => [
                'Local Events', 'Classes', 'Workshops', 'Volunteers Wanted', 'Lost & Found',
                'Announcements', 'Local Groups', 'Charity Events', 'Religious Events', 'Cultural Programs',
            ]],
            ['name' => 'Pets', 'icon' => '🐾', 'subs' => [
                'Dogs', 'Cats', 'Birds', 'Fish', 'Small Pets', 'Pet Supplies', 'Pet Food',
                'Pet Adoption', 'Pet Grooming', 'Pet Boarding', 'Veterinary Services',
            ]],
            ['name' => 'Business Directory', 'icon' => '🏢', 'subs' => [
                'Restaurants', 'Grocery Stores', 'Retail Stores', 'Pharmacies', 'Clinics',
                'Salons', 'Auto Shops', 'Hotels', 'Travel Agencies', 'Financial Services',
                'Real Estate Agents', 'Contractors', 'Lawyers', 'Accountants',
            ]],
            ['name' => 'Deals & Coupons', 'icon' => '🏷️', 'subs' => [
                'Grocery Deals', 'Restaurant Offers', 'Retail Discounts', 'Clearance Sales', 'Coupons', 'Daily Deals',
            ]],
            ['name' => 'Events & Tickets', 'icon' => '🎫', 'subs' => [
                'Concerts', 'Sports Events', 'Cultural Events', 'Community Festivals',
                'Theatre', 'Workshops', 'Conferences', 'Tickets Wanted',
            ]],
            ['name' => 'Education', 'icon' => '🎓', 'subs' => [
                'Schools', 'Colleges', 'Universities', 'Tutors', 'Online Courses',
                'Driving Schools', 'Music Classes', 'Language Classes',
            ]],
            ['name' => 'Matrimony', 'icon' => '💍', 'subs' => [
                'Bride', 'Groom', 'Professional Match', 'Community Match', 'NRI Match',
            ]],
            ['name' => 'Travel & Rideshare', 'icon' => '✈️', 'subs' => [
                'Carpool', 'Airport Ride', 'Ride Sharing', 'Vacation Packages', 'Travel Partners',
            ]],
            ['name' => 'Business Opportunities', 'icon' => '📈', 'subs' => [
                'Franchise Opportunities', 'Businesses for Sale', 'Investments', 'Partnerships', 'Distributors Wanted',
            ]],
            ['name' => 'Agriculture', 'icon' => '🌾', 'subs' => [
                'Farm Equipment', 'Livestock', 'Seeds', 'Fertilizers', 'Greenhouses',
            ]],
            ['name' => 'Industrial & Commercial', 'icon' => '🏭', 'subs' => [
                'Machinery', 'Manufacturing Equipment', 'Warehouse Equipment', 'Safety Equipment',
            ]],
            ['name' => 'Health & Fitness', 'icon' => '💪', 'subs' => [
                'Gym Memberships', 'Personal Trainers', 'Yoga', 'Martial Arts', 'Wellness Services',
            ]],
            ['name' => 'Free Stuff', 'icon' => '🎁', 'subs' => [
                'Furniture', 'Electronics', 'Household Items', 'Building Materials', 'Garden Supplies', 'Miscellaneous',
            ]],
            ['name' => 'Wanted', 'icon' => '🔍', 'subs' => [
                'Wanted to Buy', 'Wanted to Rent', 'Wanted Jobs', 'Wanted Services', 'Wanted Roommate',
            ]],
            ['name' => 'Garage Sales', 'icon' => '🏷️', 'subs' => [
                'Garage Sales', 'Estate Sales', 'Yard Sales', 'Flea Markets',
            ]],
            ['name' => 'Announcements', 'icon' => '📢', 'subs' => [
                'Birthdays', 'Engagements', 'Weddings', 'Birth Announcements', 'Obituaries', 'Public Notices',
            ]],
            ['name' => 'Lost & Found', 'icon' => '🔎', 'subs' => [
                'Lost Pets', 'Lost Items', 'Found Pets', 'Found Items',
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
