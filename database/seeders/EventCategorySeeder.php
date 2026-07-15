<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['icon' => '🎵', 'name' => 'Music & Concerts', 'subs' => [
                'Live Concerts', 'Music Festivals', 'Club Nights & DJ Events',
                'Open Mic & Jam Sessions', 'Classical & Opera', 'Jazz & Blues',
                'Hip Hop & R&B', 'Country & Folk',
            ]],
            ['icon' => '🎭', 'name' => 'Arts & Theatre', 'subs' => [
                'Theatre & Drama', 'Comedy Shows', 'Dance Performances',
                'Art Exhibitions & Galleries', 'Film Screenings', 'Poetry & Spoken Word',
                'Circus & Variety Shows',
            ]],
            ['icon' => '🍕', 'name' => 'Food & Drink', 'subs' => [
                'Food Festivals', 'Wine & Beer Tastings', 'Cooking Classes',
                'Pop-Up Restaurants', 'Farmers Markets', 'BBQ & Grill Events',
                'Cocktail & Mixology Events',
            ]],
            ['icon' => '💼', 'name' => 'Business & Networking', 'subs' => [
                'Networking Mixers', 'Conferences & Summits', 'Trade Shows & Expos',
                'Workshops & Seminars', 'Startup & Entrepreneur Events',
                'Women in Business', 'Chamber of Commerce Events',
            ]],
            ['icon' => '🎓', 'name' => 'Education & Learning', 'subs' => [
                'Workshops & Classes', 'Lectures & Talks', 'Online Webinars',
                'Career Development', 'Language & Cultural Classes',
                'Science & Technology Talks', 'Children\'s Learning Events',
            ]],
            ['icon' => '⚽', 'name' => 'Sports & Fitness', 'subs' => [
                'Marathons & Runs', 'Sports Tournaments', 'Yoga & Wellness Classes',
                'Cycling Events', 'Fitness Bootcamps', 'Martial Arts & Self-Defence',
                'Water Sports & Outdoor Adventures',
            ]],
            ['icon' => '🙏', 'name' => 'Community & Culture', 'subs' => [
                'Cultural Festivals', 'Charity & Fundraisers', 'Religious & Spiritual Events',
                'Neighbourhood Events', 'Volunteer Opportunities',
                'Multicultural Celebrations', 'Pride & LGBTQ+ Events',
            ]],
            ['icon' => '👨‍👩‍👧', 'name' => 'Family & Kids', 'subs' => [
                'Kids Parties & Entertainment', 'Family Festivals', 'School Events',
                'Story Time & Reading', 'Science & Discovery for Kids',
                'Outdoor Family Activities', 'Holiday & Seasonal Events',
            ]],
            ['icon' => '💍', 'name' => 'Weddings & Social', 'subs' => [
                'Wedding Expos & Bridal Shows', 'Engagement Parties',
                'Birthday Parties', 'Anniversary Celebrations',
                'Reunions', 'Galas & Formal Dinners',
            ]],
            ['icon' => '🎮', 'name' => 'Gaming & Hobbies', 'subs' => [
                'Gaming Tournaments & Expos', 'Board Game Nights',
                'Trivia & Quiz Nights', 'Collectibles & Hobby Shows',
                'Photography Walks', 'Book Clubs',
            ]],
            ['icon' => '🌿', 'name' => 'Outdoor & Nature', 'subs' => [
                'Hiking & Trail Events', 'Camping Trips', 'Nature Walks & Birdwatching',
                'Garden & Flower Shows', 'Eco & Sustainability Events',
            ]],
            ['icon' => '🛍️', 'name' => 'Markets & Sales', 'subs' => [
                'Craft & Artisan Markets', 'Flea Markets & Swap Meets',
                'Holiday & Seasonal Markets', 'Antique Fairs',
                'Fashion & Clothing Pop-Ups',
            ]],
            ['icon' => '🏛️', 'name' => 'Government & Civic', 'subs' => [
                'Town Halls & Public Meetings', 'Election & Voting Events',
                'Community Consultations', 'Citizenship Ceremonies',
                'Emergency Preparedness',
            ]],
            ['icon' => '✈️', 'name' => 'Travel & Tourism', 'subs' => [
                'Travel Expos', 'Group Tours & Trips', 'Cultural Exchange Events',
                'Language & Travel Meetups',
            ]],
            ['icon' => '🎉', 'name' => 'Seasonal & Holidays', 'subs' => [
                'New Year Events', 'Canada Day Celebrations', 'Halloween Events',
                'Christmas & Holiday Events', 'Diwali & Cultural Holidays',
                'Eid Celebrations', 'Easter & Spring Events',
            ]],
        ];

        foreach ($categories as $cat) {
            $slug = 'evt-' . Str::slug($cat['name']);
            $i = 1;
            $baseSlug = $slug;
            while (DB::table('categories')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }

            $parentId = DB::table('categories')->insertGetId([
                'name'       => $cat['name'],
                'slug'       => $slug,
                'icon'       => $cat['icon'],
                'type'       => 'events',
                'parent_id'  => null,
                'is_active'  => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($cat['subs'] as $subName) {
                $subSlug = 'evt-' . Str::slug($subName);
                $j = 1;
                $baseSubSlug = $subSlug;
                while (DB::table('categories')->where('slug', $subSlug)->exists()) {
                    $subSlug = $baseSubSlug . '-' . $j++;
                }

                DB::table('categories')->insert([
                    'name'       => $subName,
                    'slug'       => $subSlug,
                    'icon'       => null,
                    'type'       => 'events',
                    'parent_id'  => $parentId,
                    'is_active'  => true,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Event categories seeded: ' . count($categories) . ' parents.');
    }
}
