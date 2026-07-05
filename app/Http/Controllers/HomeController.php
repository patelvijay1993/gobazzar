<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Business;
use App\Models\Category;
use App\Models\Event;
use App\Models\Job;
use App\Models\Listing;
use App\Models\BlogPost;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $now      = Carbon::now();
        $city     = $request->get('city');
        $province = $request->get('province');

        // ── Blog ──────────────────────────────────────────────────────
        $blogPosts = BlogPost::where('status', 'published')
            ->latest('published_at')
            ->limit(4)
            ->get();

        // ── Upcoming Events ───────────────────────────────────────────
        $upcomingEvents = Event::where('status', 'active')
            ->where('start_date', '>=', $now)
            ->when($province, fn ($q) => $q->where('province', $province))
            ->when($city,     fn ($q) => $q->where('city', $city))
            ->orderBy('start_date')
            ->limit(6)
            ->get();

        // ── Classifieds ───────────────────────────────────────────────
        $classifiedCategories = Category::where('type', 'classifieds')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $latestListings = Listing::with('category')
            ->live()
            ->when($province, fn ($q) => $q->where('province', $province))
            ->when($city,     fn ($q) => $q->where('city', $city))
            ->latest()
            ->limit(4)
            ->get();

        // ── Business Directory ────────────────────────────────────────
        $directoryCategories = Category::where('type', 'directory')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $latestBusinesses = Business::with('category')
            ->where('status', 'active')
            ->when($province, fn ($q) => $q->where('province', $province))
            ->when($city,     fn ($q) => $q->where('city', $city))
            ->latest()
            ->limit(4)
            ->get();

        // ── Directory sections by category group ─────────────────────
        $dirBiz = function (array $names) use ($province, $city) {
            $ids = Category::where('type', 'directory')
                ->whereIn('name', $names)
                ->pluck('id');
            $base = Business::with('category')
                ->where('status', 'active')
                ->whereIn('category_id', $ids)
                ->when($province, fn ($q) => $q->where('province', $province))
                ->when($city,     fn ($q) => $q->where('city', $city));

            // Featured first (all of them), then fill remaining slots with random non-featured
            $featured    = (clone $base)->where('is_featured', true)->get();
            $remaining   = 5 - $featured->count();
            $nonFeatured = $remaining > 0
                ? (clone $base)->where('is_featured', false)->inRandomOrder()->limit($remaining)->get()
                : collect();

            return $featured->concat($nonFeatured);
        };

        $professionalServices = $dirBiz(['Professional Services', 'Immigration', 'Real Estate Agent', 'Travel Agency']);
        $educationSports      = $dirBiz(['Education', 'Sports']);
        $medicalDental        = $dirBiz(['Medical', 'Dental']);
        $diningBusinesses     = $dirBiz(['Restaurant']);
        $salonSpa             = $dirBiz(['Salon & Spa']);
        $fashionBiz           = $dirBiz(['Fashion']);
        $groceryStores        = $dirBiz(['Grocery']);
        $jewelryBiz           = $dirBiz(['Jewelry']);

        // ── Community Events ──────────────────────────────────────────
        $communityEvents = Event::where('status', 'active')
            ->where('start_date', '>=', $now)
            ->when($province, fn ($q) => $q->where('province', $province))
            ->when($city,     fn ($q) => $q->where('city', $city))
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // ── Jobs ──────────────────────────────────────────────────────
        $jobCategories = Category::where('type', 'classifieds')->get();

        $latestJobs = Job::live()
            ->when($province, fn ($q) => $q->where('province', $province))
            ->when($city,     fn ($q) => $q->where('city', $city))
            ->latest()
            ->limit(5)
            ->get();

        // ── Featured Businesses ───────────────────────────────────────
        $featuredBusinesses = Business::with('category')
            ->where('status', 'active')
            ->where('is_featured', true)
            ->when($province, fn ($q) => $q->where('province', $province))
            ->when($city,     fn ($q) => $q->where('city', $city))
            ->latest()
            ->limit(4)
            ->get();

        // ── Sidebar ───────────────────────────────────────────────────
        $sidebarFeatured = Listing::with('category')
            ->live()
            ->where('is_featured', true)
            ->when($province, fn ($q) => $q->where('province', $province))
            ->when($city,     fn ($q) => $q->where('city', $city))
            ->latest()
            ->limit(4)
            ->get();

        $trendingListings = Listing::with('category')
            ->live()
            ->when($province, fn ($q) => $q->where('province', $province))
            ->when($city,     fn ($q) => $q->where('city', $city))
            ->orderByDesc('views')
            ->limit(4)
            ->get();

        $latestSidebarBiz = Business::with('category')
            ->where('status', 'active')
            ->when($province, fn ($q) => $q->where('province', $province))
            ->when($city,     fn ($q) => $q->where('city', $city))
            ->latest()
            ->limit(5)
            ->get();

        // ── Hero Stats (filtered by location if selected) ─────────────
        $stats = [
            'businesses' => Business::where('status', 'active')
                ->when($province, fn ($q) => $q->where('province', $province))
                ->when($city,     fn ($q) => $q->where('city', $city))
                ->count(),
            'listings'   => Listing::live()
                ->when($province, fn ($q) => $q->where('province', $province))
                ->when($city,     fn ($q) => $q->where('city', $city))
                ->count(),
            'events'     => Event::where('status', 'active')
                ->whereMonth('start_date', $now->month)
                ->whereYear('start_date', $now->year)
                ->when($province, fn ($q) => $q->where('province', $province))
                ->when($city,     fn ($q) => $q->where('city', $city))
                ->count(),
            'jobs'       => Job::live()
                ->when($province, fn ($q) => $q->where('province', $province))
                ->when($city,     fn ($q) => $q->where('city', $city))
                ->count(),
        ];

        // ── Advertisements ────────────────────────────────────────────
        $ads = collect([
            'home-banner' => Advertisement::forPosition('home-banner', $city, $province),
            'sidebar'     => Advertisement::forPosition('sidebar', $city, $province),
            'inline'      => Advertisement::forPosition('inline', $city, $province),
        ])->collapse()->unique('id');

        // ── Active Poll ───────────────────────────────────────────────
        $poll = \App\Models\Poll::current($city, $province);

        // ── Location dropdowns ────────────────────────────────────────
        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities($province);

        // ── Category shortcuts for home nav tabs & quick links ───────
        $realEstateCatId   = Category::where('slug', 'real-estate')->value('id');
        $roommatesCatId    = Category::where('slug', 'roommates')->value('id');
        $housingCategories = implode(',', array_filter([$realEstateCatId, $roommatesCatId]));
        $autosCategoryId   = Category::where('slug', 'autos')->value('id');
        $diningCategoryId  = Category::where('slug', 'restaurant')->value('id');
        $travelAgentCatId  = Category::where('slug', 'travel-agency')->value('id');

        // ── Hero background from uploaded city image ───────────────────
        $heroBg = null;
        if ($city) {
            $loc = Location::where('city', $city)
                ->when($province, fn ($q) => $q->where('province', $province))
                ->whereNotNull('city_image')
                ->first();
            if ($loc && $loc->city_image) {
                $heroBg = \Storage::disk(config('filesystems.default'))->url($loc->city_image);
            }
        }

        return view('home', compact(
            'blogPosts',
            'upcomingEvents',
            'classifiedCategories',
            'latestListings',
            'directoryCategories',
            'latestBusinesses',
            'professionalServices',
            'educationSports',
            'medicalDental',
            'diningBusinesses',
            'salonSpa',
            'fashionBiz',
            'groceryStores',
            'jewelryBiz',
            'communityEvents',
            'jobCategories',
            'latestJobs',
            'featuredBusinesses',
            'sidebarFeatured',
            'trendingListings',
            'latestSidebarBiz',
            'stats',
            'poll',
            'provinces',
            'cities',
            'heroBg',
            'ads',
            'housingCategories',
            'roommatesCatId',
            'autosCategoryId',
            'diningCategoryId',
            'travelAgentCatId'
        ));
    }
}

