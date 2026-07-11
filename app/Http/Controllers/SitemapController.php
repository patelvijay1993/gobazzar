<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Event;
use App\Models\Job;
use App\Models\Business;
use App\Models\BusinessPost;
use App\Models\BlogPost;
use App\Models\Location;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemaps = [
            'sitemap-static.xml',
            'sitemap-listings.xml',
            'sitemap-events.xml',
            'sitemap-jobs.xml',
            'sitemap-businesses.xml',
            'sitemap-blog.xml',
        ];

        $content = view('sitemap.index', compact('sitemaps'))->render();

        return Response::make($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    public function static()
    {
        $urls = [
            ['url' => route('home'),              'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('classifieds.index'), 'priority' => '0.9', 'changefreq' => 'hourly'],
            ['url' => route('events.index'),      'priority' => '0.9', 'changefreq' => 'hourly'],
            ['url' => route('jobs.index'),        'priority' => '0.9', 'changefreq' => 'hourly'],
            ['url' => route('directory.index'),   'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('blog.index'),        'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => route('about'),             'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => route('contact'),           'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => route('advertise'),         'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => route('pricing'),           'priority' => '0.6', 'changefreq' => 'weekly'],
        ];

        return $this->xmlResponse('sitemap.static', compact('urls'));
    }

    public function listings()
    {
        $items = Listing::where('status', 'active')
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        return $this->xmlResponse('sitemap.listings', compact('items'));
    }

    public function events()
    {
        $items = Event::where('status', 'active')
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        return $this->xmlResponse('sitemap.events', compact('items'));
    }

    public function jobs()
    {
        $items = Job::where('status', 'active')
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        return $this->xmlResponse('sitemap.jobs', compact('items'));
    }

    public function businesses()
    {
        $items = Business::where('is_active', true)
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        return $this->xmlResponse('sitemap.businesses', compact('items'));
    }

    public function blog()
    {
        $items = BlogPost::where('status', 'published')
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        return $this->xmlResponse('sitemap.blog', compact('items'));
    }

    private function xmlResponse(string $view, array $data)
    {
        $content = view($view, $data)->render();
        return Response::make($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
