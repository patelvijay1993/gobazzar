<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\Event;
use App\Models\Job;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Matrimonial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────

    private function resolveModel(string $type): string
    {
        return match($type) {
            'classified'  => Listing::class,
            'job'         => Job::class,
            'event'       => Event::class,
            'business'    => Business::class,
            'matrimonial' => Matrimonial::class,
            default       => abort(404),
        };
    }

    private function findOwned(string $type, int $id)
    {
        $model  = $this->resolveModel($type);
        $record = $model::findOrFail($id);
        abort_if($record->user_id !== Auth::id(), 403);
        return $record;
    }

    private function uniqueSlug(string $text, string $table): string
    {
        $base = Str::slug($text);
        $slug = $base;
        $i    = 1;
        while (\DB::table($table)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function getCategories(): \Illuminate\Support\Collection
    {
        return Category::where('is_active', true)->orderBy('sort_order')->get()->groupBy('type');
    }

    /** Calculate expires_at based on user plan. Null = permanent. */
    private function expiresAt(): ?Carbon
    {
        $days = Auth::user()->postDays();
        return $days ? Carbon::now()->addDays($days) : null;
    }

    // ── Create ────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $type       = $request->get('type', 'classified');
        $categories = $this->getCategories();
        $user       = Auth::user();
        $provinces  = Location::activeProvinces();
        $cities     = Location::activeCities();
        return view('post.create', compact('type', 'categories', 'user', 'provinces', 'cities'));
    }

    // ── Feed (public) ─────────────────────────────────────────────

    public function feed(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $classifieds = $filter === 'all' || $filter === 'classifieds'
            ? Listing::with(['user', 'category'])->where('status', 'active')->latest()->limit(6)->get()
            : collect();

        $jobs = $filter === 'all' || $filter === 'jobs'
            ? Job::with(['user', 'category'])->where('status', 'active')->latest()->limit(6)->get()
            : collect();

        $events = $filter === 'all' || $filter === 'events'
            ? Event::with(['user', 'category'])->where('status', 'active')->latest()->limit(6)->get()
            : collect();

        $businesses = $filter === 'all' || $filter === 'businesses'
            ? Business::with(['user', 'category'])->where('status', 'active')->latest()->limit(6)->get()
            : collect();

        $matrimonials = $filter === 'all' || $filter === 'matrimonials'
            ? Matrimonial::with('user')->where('status', 'active')->latest()->limit(6)->get()
            : collect();

        return view('feed', compact('classifieds', 'jobs', 'events', 'businesses', 'matrimonials', 'filter'));
    }

    // ── Edit ──────────────────────────────────────────────────────

    public function edit(string $type, int $id)
    {
        $record     = $this->findOwned($type, $id);
        $categories = $this->getCategories();
        $provinces  = Location::activeProvinces();
        $cities     = Location::activeCities();
        return view('post.edit', compact('type', 'record', 'categories', 'provinces', 'cities'));
    }

    // ── Update ────────────────────────────────────────────────────

    public function update(Request $request, string $type, int $id)
    {
        $record = $this->findOwned($type, $id);

        match($type) {
            'classified'  => $this->updateClassified($request, $record),
            'job'         => $this->updateJob($request, $record),
            'event'       => $this->updateEvent($request, $record),
            'business'    => $this->updateBusiness($request, $record),
            'matrimonial' => $this->updateMatrimonial($request, $record),
        };

        return redirect()->route('account')->with('success', 'Your post has been updated.');
    }

    // ── Destroy ───────────────────────────────────────────────────

    public function destroy(string $type, int $id)
    {
        $record = $this->findOwned($type, $id);

        foreach (['image', 'photo', 'logo', 'company_logo'] as $field) {
            if (!empty($record->$field)) {
                Storage::disk('public')->delete($record->$field);
            }
        }
        foreach ($record->images ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $record->delete();
        return redirect()->route('account')->with('success', 'Post deleted successfully.');
    }

    // ── Store methods ─────────────────────────────────────────────

    public function storeClassified(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:150',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'nullable|string',
            'price'         => 'nullable|string|max:50',
            'price_unit'    => 'nullable|string|max:20',
            'location'      => 'nullable|string|max:150',
            'city'          => 'required|string|max:100',
            'province'      => 'required|string|max:100',
            'contact_name'  => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:150',
            'contact_phone' => 'nullable|string|max:30',
            'images'        => 'nullable|array|max:5',
            'images.*'      => 'image|max:1024',
        ]);

        unset($data['images']); // handle separately

        $data['user_id']    = Auth::id();
        $data['slug']       = $this->uniqueSlug($data['title'], 'listings');
        $data['status']     = 'active';
        $data['expires_at'] = $this->expiresAt();

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('listings', 'public');
            }
            $data['images'] = $paths;
            $data['image']  = $paths[0];
        }

        Listing::create($data);

        $msg = Auth::user()->isSubscribed()
            ? 'Your classified ad is now live!'
            : 'Your classified ad is now live! It will auto-expire in ' . Auth::user()->postDays() . ' days. Upgrade to keep it longer.';

        return redirect()->route('account')->with('success', $msg);
    }

    public function storeJob(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'category_id'  => 'nullable|exists:categories,id',
            'company'      => 'required|string|max:150',
            'description'  => 'nullable|string',
            'requirements' => 'nullable|string',
            'job_type'     => 'required|in:full-time,part-time,contract,freelance,internship',
            'work_mode'    => 'required|in:onsite,remote,hybrid',
            'salary'       => 'nullable|string|max:100',
            'experience'   => 'nullable|string|max:100',
            'location'     => 'nullable|string|max:150',
            'city'         => 'required|string|max:100',
            'province'     => 'required|string|max:100',
            'apply_email'  => 'nullable|email|max:150',
            'apply_url'    => 'nullable|url|max:255',
            'company_logo' => 'nullable|image|max:1024',
        ]);

        $data['user_id']    = Auth::id();
        $data['slug']       = $this->uniqueSlug($data['title'], 'job_listings');
        $data['status']     = 'active';
        $data['expires_at'] = $this->expiresAt();

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('jobs', 'public');
        }

        Job::create($data);

        $msg = Auth::user()->isSubscribed()
            ? 'Your job listing is now live!'
            : 'Your job listing is now live! It will auto-expire in ' . Auth::user()->postDays() . ' days.';

        return redirect()->route('account')->with('success', $msg);
    }

    public function storeEvent(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:150',
            'category_id'     => 'nullable|exists:categories,id',
            'description'     => 'nullable|string',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'venue'           => 'nullable|string|max:200',
            'city'            => 'required|string|max:100',
            'province'        => 'required|string|max:100',
            'price'           => 'nullable|string|max:50',
            'organizer'       => 'nullable|string|max:150',
            'organizer_phone' => 'nullable|string|max:30',
            'organizer_email' => 'nullable|email|max:150',
            'website'         => 'nullable|url|max:255',
            'image'           => 'nullable|image|max:1024',
        ]);

        $data['user_id'] = Auth::id();
        $data['slug']    = $this->uniqueSlug($data['title'], 'events');
        $data['status']  = 'active';
        // Events expire at event end date or post expiry, whichever is sooner
        $postExpiry  = $this->expiresAt();
        $eventEnd    = !empty($data['end_date']) ? Carbon::parse($data['end_date']) : null;
        $data['expires_at'] = null; // events don't use expires_at, they have start/end dates

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($data);
        return redirect()->route('account')->with('success', 'Your event is now live!');
    }

    public function storeBusiness(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'address'     => 'nullable|string|max:255',
            'city'        => 'required|string|max:100',
            'province'    => 'required|string|max:100',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'website'     => 'nullable|url|max:255',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|max:1024',
            'logo'        => 'nullable|image|max:1024',
        ]);

        unset($data['images']); // handle separately

        $data['user_id'] = Auth::id();
        $data['slug']    = $this->uniqueSlug($data['name'], 'businesses');
        $data['status']  = 'active';
        $data['rating']  = 0;

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('businesses', 'public');
            }
            $data['images'] = $paths;
            $data['image']  = $paths[0];
        }
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('businesses', 'public');
        }

        Business::create($data);

        $msg = Auth::user()->isSubscribed()
            ? 'Your business listing is now live!'
            : 'Your business listing is now live! Free listings appear for ' . Auth::user()->postDays() . ' days. Upgrade for permanent listing.';

        return redirect()->route('account')->with('success', $msg);
    }

    public function storeMatrimonial(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'profile_for'        => 'required|in:self,son,daughter,brother,sister,friend',
            'gender'             => 'required|in:male,female',
            'age'                => 'required|integer|min:18|max:80',
            'height'             => 'nullable|string|max:20',
            'religion'           => 'nullable|string|max:100',
            'caste'              => 'nullable|string|max:100',
            'mother_tongue'      => 'nullable|string|max:100',
            'education'          => 'nullable|string|max:150',
            'occupation'         => 'nullable|string|max:150',
            'income'             => 'nullable|string|max:100',
            'marital_status'     => 'required|in:never_married,divorced,widowed',
            'diet'               => 'nullable|in:veg,non-veg,eggetarian',
            'city'               => 'required|string|max:100',
            'province'           => 'required|string|max:100',
            'about'              => 'nullable|string|max:2000',
            'partner_preference' => 'nullable|string|max:2000',
            'contact_name'       => 'nullable|string|max:100',
            'contact_phone'      => 'nullable|string|max:30',
            'contact_email'      => 'nullable|email|max:150',
            'hide_contact'       => 'nullable|boolean',
            'photo'              => 'nullable|image|max:1024',
        ]);

        $data['user_id']      = Auth::id();
        $data['slug']         = $this->uniqueSlug($data['name'] . '-' . $data['city'], 'matrimonials');
        $data['status']       = 'active';
        $data['hide_contact'] = $request->boolean('hide_contact');
        $data['country']      = 'Canada';
        $data['expires_at']   = $this->expiresAt();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('matrimonials', 'public');
        }

        Matrimonial::create($data);

        $msg = Auth::user()->isSubscribed()
            ? 'Your matrimonial profile is now live!'
            : 'Your matrimonial profile is now live for ' . Auth::user()->postDays() . ' days. Upgrade to keep it permanently visible.';

        return redirect()->route('account')->with('success', $msg);
    }

    // ── Private update helpers ────────────────────────────────────

    private function updateClassified(Request $request, Listing $r): void
    {
        $data = $request->validate([
            'title'         => 'required|string|max:150',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'nullable|string',
            'price'         => 'nullable|string|max:50',
            'price_unit'    => 'nullable|string|max:20',
            'location'      => 'nullable|string|max:150',
            'city'          => 'required|string|max:100',
            'province'      => 'required|string|max:100',
            'contact_name'  => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:150',
            'contact_phone' => 'nullable|string|max:30',
            'images'        => 'nullable|array|max:5',
            'images.*'      => 'image|max:1024',
        ]);
        unset($data['images']);
        if ($request->hasFile('images')) {
            foreach ($r->images ?? [] as $old) Storage::disk('public')->delete($old);
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('listings', 'public');
            }
            $data['images'] = $paths;
            $data['image']  = $paths[0];
        }
        $r->update($data);
    }

    private function updateJob(Request $request, Job $r): void
    {
        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'category_id'  => 'nullable|exists:categories,id',
            'company'      => 'required|string|max:150',
            'description'  => 'nullable|string',
            'requirements' => 'nullable|string',
            'job_type'     => 'required|in:full-time,part-time,contract,freelance,internship',
            'work_mode'    => 'required|in:onsite,remote,hybrid',
            'salary'       => 'nullable|string|max:100',
            'experience'   => 'nullable|string|max:100',
            'location'     => 'nullable|string|max:150',
            'city'         => 'required|string|max:100',
            'province'     => 'required|string|max:100',
            'apply_email'  => 'nullable|email|max:150',
            'apply_url'    => 'nullable|url|max:255',
            'company_logo' => 'nullable|image|max:1024',
        ]);
        if ($request->hasFile('company_logo')) {
            if ($r->company_logo) Storage::disk('public')->delete($r->company_logo);
            $data['company_logo'] = $request->file('company_logo')->store('jobs', 'public');
        }
        $r->update($data);
    }

    private function updateEvent(Request $request, Event $r): void
    {
        $data = $request->validate([
            'title'           => 'required|string|max:150',
            'category_id'     => 'nullable|exists:categories,id',
            'description'     => 'nullable|string',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'venue'           => 'nullable|string|max:200',
            'city'            => 'required|string|max:100',
            'province'        => 'required|string|max:100',
            'price'           => 'nullable|string|max:50',
            'organizer'       => 'nullable|string|max:150',
            'organizer_phone' => 'nullable|string|max:30',
            'organizer_email' => 'nullable|email|max:150',
            'website'         => 'nullable|url|max:255',
            'image'           => 'nullable|image|max:1024',
        ]);
        if ($request->hasFile('image')) {
            if ($r->image) Storage::disk('public')->delete($r->image);
            $data['image'] = $request->file('image')->store('events', 'public');
        }
        $r->update($data);
    }

    private function updateBusiness(Request $request, Business $r): void
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'address'     => 'nullable|string|max:255',
            'city'        => 'required|string|max:100',
            'province'    => 'required|string|max:100',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:150',
            'website'     => 'nullable|url|max:255',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|max:1024',
            'logo'        => 'nullable|image|max:1024',
        ]);
        unset($data['images']);
        if ($request->hasFile('images')) {
            foreach ($r->images ?? [] as $old) Storage::disk('public')->delete($old);
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('businesses', 'public');
            }
            $data['images'] = $paths;
            $data['image']  = $paths[0];
        }
        if ($request->hasFile('logo')) {
            if ($r->logo) Storage::disk('public')->delete($r->logo);
            $data['logo'] = $request->file('logo')->store('businesses', 'public');
        }
        $r->update($data);
    }

    private function updateMatrimonial(Request $request, Matrimonial $r): void
    {
        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'profile_for'        => 'required|in:self,son,daughter,brother,sister,friend',
            'gender'             => 'required|in:male,female',
            'age'                => 'required|integer|min:18|max:80',
            'height'             => 'nullable|string|max:20',
            'religion'           => 'nullable|string|max:100',
            'caste'              => 'nullable|string|max:100',
            'mother_tongue'      => 'nullable|string|max:100',
            'education'          => 'nullable|string|max:150',
            'occupation'         => 'nullable|string|max:150',
            'income'             => 'nullable|string|max:100',
            'marital_status'     => 'required|in:never_married,divorced,widowed',
            'diet'               => 'nullable|in:veg,non-veg,eggetarian',
            'city'               => 'required|string|max:100',
            'province'           => 'required|string|max:100',
            'about'              => 'nullable|string|max:2000',
            'partner_preference' => 'nullable|string|max:2000',
            'contact_name'       => 'nullable|string|max:100',
            'contact_phone'      => 'nullable|string|max:30',
            'contact_email'      => 'nullable|email|max:150',
            'hide_contact'       => 'nullable|boolean',
            'photo'              => 'nullable|image|max:1024',
        ]);
        if ($request->hasFile('photo')) {
            if ($r->photo) Storage::disk('public')->delete($r->photo);
            $data['photo'] = $request->file('photo')->store('matrimonials', 'public');
        }
        $data['hide_contact'] = $request->boolean('hide_contact');
        $r->update($data);
    }
}
