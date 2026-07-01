<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('max_listings')->default(3)->after('post_days');
            $table->boolean('verified_badge')->default(false)->after('max_listings');
            $table->boolean('auto_renew')->default(false)->after('verified_badge');
            $table->boolean('favorites')->default(false)->after('auto_renew');
            $table->unsignedInteger('featured_credits')->default(0)->after('favorites');
            $table->boolean('bulk_upload')->default(false)->after('featured_credits');
        });

        DB::table('plans')->truncate();

        DB::table('plans')->insert([
            [
                'slug'               => 'free',
                'name'               => 'Free',
                'icon'               => '🆓',
                'icon_bg'            => '#f0ede8',
                'price'              => '0.00',
                'period'             => 'monthly',
                'tagline'            => 'Get started at no cost',
                'is_popular'         => false,
                'is_active'          => true,
                'sort_order'         => 0,
                'post_days'          => 3,
                'max_listings'       => 3,
                'biz_listings'       => 0,
                'verified_badge'     => false,
                'featured_placement' => false,
                'unlimited_posts'    => false,
                'priority_support'   => false,
                'analytics'          => false,
                'auto_renew'         => false,
                'favorites'          => false,
                'featured_credits'   => 0,
                'bulk_upload'        => false,
                'features'           => json_encode([
                    ['text' => 'Up to 3 days active per listing',   'included' => true,  'highlight' => false],
                    ['text' => 'Up to 3 active listings',           'included' => true,  'highlight' => false],
                    ['text' => '3 photos per listing',              'included' => true,  'highlight' => false],
                    ['text' => 'Phone, Email & Chat Conversation',  'included' => true,  'highlight' => false],
                    ['text' => 'Standard support',                  'included' => true,  'highlight' => false],
                    ['text' => 'Verified Badge',                    'included' => false, 'highlight' => false],
                    ['text' => 'Priority Search Placement',         'included' => false, 'highlight' => false],
                    ['text' => 'Listing Analytics',                 'included' => false, 'highlight' => false],
                    ['text' => 'Featured Listing Credits',          'included' => false, 'highlight' => false],
                    ['text' => 'Auto Renew Listings',               'included' => false, 'highlight' => false],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug'               => 'verified',
                'name'               => 'Verified',
                'icon'               => '✅',
                'icon_bg'            => '#eff6ff',
                'price'              => '4.99',
                'period'             => 'monthly',
                'tagline'            => 'Build trust & reach more buyers',
                'is_popular'         => true,
                'is_active'          => true,
                'sort_order'         => 1,
                'post_days'          => 30,
                'max_listings'       => 10,
                'biz_listings'       => 1,
                'verified_badge'     => true,
                'featured_placement' => true,
                'unlimited_posts'    => false,
                'priority_support'   => false,
                'analytics'          => true,
                'auto_renew'         => false,
                'favorites'          => true,
                'featured_credits'   => 0,
                'bulk_upload'        => false,
                'features'           => json_encode([
                    ['text' => 'Everything in Free plus:',              'included' => true,  'highlight' => false],
                    ['text' => 'Verified Badge',                        'included' => true,  'highlight' => true],
                    ['text' => 'Priority Search Placement',             'included' => true,  'highlight' => true],
                    ['text' => 'Up to 10 active listings',              'included' => true,  'highlight' => false],
                    ['text' => 'Up to 30 days active listing',          'included' => true,  'highlight' => false],
                    ['text' => 'Listing Analytics (Insights)',          'included' => true,  'highlight' => false],
                    ['text' => 'Unlimited Favorites (Follow/Unfollow)', 'included' => true,  'highlight' => false],
                    ['text' => 'Featured Listing Credits',              'included' => false, 'highlight' => false],
                    ['text' => 'Auto Renew Listings',                   'included' => false, 'highlight' => false],
                    ['text' => 'Bulk Listing Upload',                   'included' => false, 'highlight' => false],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug'               => 'power_seller',
                'name'               => 'Power Seller',
                'icon'               => '⚡',
                'icon_bg'            => '#fef9c3',
                'price'              => '14.99',
                'period'             => 'monthly',
                'tagline'            => 'For serious sellers & resellers',
                'is_popular'         => false,
                'is_active'          => true,
                'sort_order'         => 2,
                'post_days'          => 0,
                'max_listings'       => 9999,
                'biz_listings'       => 999,
                'verified_badge'     => true,
                'featured_placement' => true,
                'unlimited_posts'    => true,
                'priority_support'   => true,
                'analytics'          => true,
                'auto_renew'         => true,
                'favorites'          => true,
                'featured_credits'   => 5,
                'bulk_upload'        => true,
                'features'           => json_encode([
                    ['text' => 'Everything in Verified plus:',          'included' => true,  'highlight' => false],
                    ['text' => 'Unlimited Listings',                    'included' => true,  'highlight' => true],
                    ['text' => 'Auto Renew Listings',                   'included' => true,  'highlight' => true],
                    ['text' => '3 photos per listing',                  'included' => true,  'highlight' => false],
                    ['text' => 'Advanced Analytics',                    'included' => true,  'highlight' => false],
                    ['text' => 'Featured Listing Credits (5/month)',    'included' => true,  'highlight' => true],
                    ['text' => 'Bulk Listing Upload',                   'included' => true,  'highlight' => false],
                    ['text' => 'Priority Support',                      'included' => true,  'highlight' => false],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Reset all users to free since old plan slugs (basic/premium/business) are removed
        DB::table('users')->whereNotIn('plan', ['free'])->update(['plan' => 'free', 'plan_expires_at' => null]);
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['max_listings','verified_badge','auto_renew','favorites','featured_credits','bulk_upload']);
        });
    }
};
