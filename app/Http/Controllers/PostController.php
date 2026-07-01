<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessPost;
use App\Models\Category;
use App\Models\Event;
use App\Models\Job;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Matrimonial;
use App\Services\ContentModerator;
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
            'classified'    => Listing::class,
            'job'           => Job::class,
            'event'         => Event::class,
            'business'      => Business::class,
            'matrimonial'   => Matrimonial::class,
            'business-post' => BusinessPost::class,
            default         => abort(404),
        };
    }

    private function findOwned(string $type, int $id)
    {
        $model  = $this->resolveModel($type);
        $record = $model::findOrFail($id);
        abort_if((int) $record->user_id !== (int) Auth::id(), 403);
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

    /** Calculate expires_at based on user plan. Null = permanent (power_seller auto-renew). */
    private function expiresAt(): ?Carbon
    {
        $days = Auth::user()->postDays();
        return $days ? Carbon::now()->addDays($days) : null;
    }

    /**
     * Run spam/dummy/abuse moderation on a post's title + description.
     * Throws ValidationException (→ back with errors) if content looks bad.
     */
    private function moderate(Request $request, string $titleKey = 'title'): void
    {
        $minTitle = config('moderation.min_title_length');
        $minDesc  = config('moderation.min_description_length');

        $fields = [
            $titleKey => [$request->input($titleKey), 'Title', $minTitle],
        ];
        if ($request->filled('description')) {
            $fields['description'] = [$request->input('description'), 'Description', $minDesc];
        }

        app(ContentModerator::class)->validateOrFail($fields);
    }

    /**
     * Build the validation rule string for an uploaded image based on
     * config/moderation.php — verifies it's a real image of sane dimensions.
     */
    private static function imgRules(): string
    {
        $c = config('moderation.image');
        return implode('|', [
            'image',
            'mimes:' . implode(',', $c['mimes']),
            'max:' . $c['max_kb'],
            "dimensions:min_width={$c['min_width']},min_height={$c['min_height']},max_width={$c['max_width']},max_height={$c['max_height']}",
        ]);
    }

    // ── Create ────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $type       = $request->get('type', 'classified');
        $categories = $this->getCategories();
        $user       = Auth::user();
        $provinces  = Location::activeProvinces();
        $cities     = Location::activeCities();
        $myBusiness       = Business::where('user_id', $user->id)->first();
        $myBusinesses     = $myBusiness ? collect([$myBusiness]) : collect();
        $directoryParents = Category::where('type', 'directory')->whereNull('parent_id')
            ->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $canBusiness      = $user->maxBusinessListings() > 0;
        $maxImages        = $user->maxImages();
        return view('post.create', compact(
            'type', 'categories', 'user', 'provinces', 'cities',
            'myBusiness', 'myBusinesses', 'directoryParents', 'canBusiness', 'maxImages'
        ));
    }

    // ── Rich-text editor inline image upload ─────────────────────
    public function uploadEditorImage(Request $request)
    {
        $request->validate([
            'image' => 'required|'.self::imgRules(),
        ]);

        $path = $request->file('image')->store('editor', 's3');

        return response()->json([
            'url' => Storage::disk('s3')->url($path),
        ]);
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
        // Custom fields for a business-post's category (for editing values)
        $customFields = ($type === 'business-post' && $record->category)
            ? $record->category->applicableFields()
            : collect();
        $maxImages = Auth::user()->maxImages();
        return view('post.edit', compact('type', 'record', 'categories', 'provinces', 'cities', 'customFields', 'maxImages'));
    }

    // ── Update ────────────────────────────────────────────────────

    public function update(Request $request, string $type, int $id)
    {
        $record = $this->findOwned($type, $id);

        match($type) {
            'classified'    => $this->updateClassified($request, $record),
            'job'           => $this->updateJob($request, $record),
            'event'         => $this->updateEvent($request, $record),
            'business'      => $this->updateBusiness($request, $record),
            'matrimonial'   => $this->updateMatrimonial($request, $record),
            'business-post' => $this->updateBusinessPost($request, $record),
        };

        return redirect()->route('account')->with('success', 'Your post has been updated.');
    }

    // ── Destroy ───────────────────────────────────────────────────

    public function destroy(string $type, int $id)
    {
        $record = $this->findOwned($type, $id);

        foreach (['image', 'photo', 'logo', 'company_logo'] as $field) {
            if (!empty($record->$field)) {
                Storage::disk('s3')->delete($record->$field);
            }
        }
        foreach ($record->images ?? [] as $path) {
            Storage::disk('s3')->delete($path);
        }

        $record->delete();
        return redirect()->route('account')->with('success', 'Post deleted successfully.');
    }

    // ── Store methods ─────────────────────────────────────────────

    public function storeClassified(Request $request)
    {
        // Enforce per-plan listing limit
        $user = Auth::user();
        $maxImg = $user->maxImages();

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
            'images'        => "nullable|array|max:{$maxImg}",
            'images.*'      => self::imgRules(),
        ]);
        if (!$user->canPostListing()) {
            $max = $user->maxListings();
            $plan = ucfirst(str_replace('_', ' ', $user->activePlan()));
            return back()->withInput()->with('error',
                "You've reached your limit of {$max} active listing(s) on the {$plan} plan. Delete an old listing or upgrade your plan to post more."
            );
        }

        $this->moderate($request);

        unset($data['images']); // handle separately

        $data['user_id']     = Auth::id();
        $data['slug']        = $this->uniqueSlug($data['title'], 'listings');
        $data['status']      = 'active';
        $data['expires_at']  = $this->expiresAt();
        $data['is_verified'] = Auth::user()->hasVerifiedBadge();

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('listings', 's3');
            }
            $data['images'] = $paths;
            $data['image']  = $paths[0];

            // Google Vision — safe search + category match (base64, works on localhost)
            $moderator    = app(\App\Services\ContentModerator::class);
            $categoryName = \App\Models\Category::find($data['category_id'])?->name;
            foreach ($paths as $path) {
                $imgError = $moderator->checkImageFile($path, $categoryName);
                if ($imgError) {
                    foreach ($paths as $p) \Illuminate\Support\Facades\Storage::disk('s3')->delete($p);
                    throw \Illuminate\Validation\ValidationException::withMessages(['images' => $imgError]);
                }
            }
        }

        Listing::create($data);

        $user = Auth::user();
        $msg = match($user->activePlan()) {
            'power_seller' => 'Your classified ad is now live! It will auto-renew (never expires).',
            'verified'     => 'Your classified ad is now live! Active for 30 days.',
            default        => 'Your classified ad is now live! Active for 3 days. Upgrade to Verified or Power Seller for longer visibility.',
        };

        return redirect()->route('account')->with('success', $msg);
    }

    public function storeJob(Request $request)
    {
        $user = Auth::user();

        // Jobs count against the same classified listing limit
        if (!$user->canPostListing()) {
            $max = $user->maxListings();
            return back()->withInput()->with('error',
                "You've reached your active post limit of {$max} on the {$user->planName()} plan. Delete an old listing or upgrade to post more."
            );
        }

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
            'company_logo' => 'nullable|'.self::imgRules(),
        ]);

        $this->moderate($request);

        $data['user_id']    = Auth::id();
        $data['slug']       = $this->uniqueSlug($data['title'], 'job_listings');
        $data['status']     = 'active';
        $data['expires_at'] = $this->expiresAt();

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('jobs', 's3');
        }

        Job::create($data);

        $msg = match($user->activePlan()) {
            'power_seller' => 'Your job listing is now live! It will auto-renew (never expires).',
            'verified'     => 'Your job listing is now live! Active for 30 days.',
            default        => 'Your job listing is now live! Active for 3 days. Upgrade for longer visibility.',
        };

        return redirect()->route('account')->with('success', $msg);
    }

    public function storeEvent(Request $request)
    {
        // All plans can post events — no limit enforcement needed
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
            'image'           => 'nullable|'.self::imgRules(),
        ]);

        $this->moderate($request);

        $data['user_id']    = Auth::id();
        $data['slug']       = $this->uniqueSlug($data['title'], 'events');
        $data['status']     = 'active';
        $data['expires_at'] = null; // events are governed by start/end date, not plan expiry

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 's3');
        }

        Event::create($data);
        return redirect()->route('account')->with('success', 'Your event is now live!');
    }

    public function storeBusiness(Request $request)
    {
        $user = Auth::user();

        // Free plan cannot post a business listing
        if ($user->maxBusinessListings() === 0) {
            return back()->withInput()->with('error',
                'Business listings require a Verified ($4.99/mo) or Power Seller ($14.99/mo) plan. Upgrade to list your business.'
            );
        }

        // Enforce per-plan business listing limit
        if (!$user->canPostBusiness()) {
            $max = $user->maxBusinessListings();
            return back()->withInput()->with('error',
                "You've reached your limit of {$max} active business listing(s) on the {$user->planName()} plan. Upgrade to Power Seller for unlimited business listings."
            );
        }

        $maxImg = $user->maxImages();
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'category_id'    => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'description'    => 'nullable|string',
            'address'        => 'nullable|string|max:255',
            'city'           => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:150',
            'website'        => 'nullable|url|max:255',
            'images'         => "nullable|array|max:{$maxImg}",
            'images.*'       => self::imgRules(),
            'logo'           => 'nullable|'.self::imgRules(),
        ]);

        $this->moderate($request, 'name');

        if (!empty($data['subcategory_id'])) {
            $data['category_id'] = $data['subcategory_id'];
        }
        unset($data['subcategory_id'], $data['images']);

        $data['user_id']     = Auth::id();
        $data['slug']        = $this->uniqueSlug($data['name'], 'businesses');
        $data['status']      = 'active';
        $data['rating']      = 0;
        // Verified badge automatically applied if user's plan includes it
        $data['is_verified'] = $user->hasVerifiedBadge();

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('businesses', 's3');
            }
            $data['images'] = $paths;
            $data['image']  = $paths[0];
        }
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('businesses', 's3');
        }

        Business::create($data);

        $msg = match($user->activePlan()) {
            'power_seller' => 'Your business listing is now live with Verified Badge & Priority Placement!',
            'verified'     => 'Your business listing is now live with a Verified Badge!',
            default        => 'Your business listing is now live!',
        };

        return redirect()->route('account')->with('success', $msg);
    }

    public function storeBusinessPost(Request $request)
    {
        $user = Auth::user();

        // Business posts require a plan that allows at least 1 business listing
        if ($user->maxBusinessListings() === 0) {
            return back()->withInput()->with('error',
                'Business posts require a Verified ($4.99/mo) or Power Seller ($14.99/mo) plan. Upgrade to add posts to your business.'
            );
        }

        $maxImg = $user->maxImages();
        $data = $request->validate([
            'business_id'    => 'required|exists:businesses,id',
            'category_id'    => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'title'          => 'required|string|max:150',
            'description'    => 'nullable|string',
            'price'          => 'nullable|string|max:50',
            'price_unit'     => 'nullable|string|max:20',
            'images'         => "nullable|array|max:{$maxImg}",
            'images.*'       => self::imgRules(),
        ]);

        // Ensure the business belongs to the current user
        $business = Business::where('id', $data['business_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->moderate($request);

        // Effective category = sub-category if chosen, else parent
        $categoryId = !empty($data['subcategory_id']) ? $data['subcategory_id'] : $data['category_id'];
        $category   = Category::findOrFail($categoryId);

        // Validate + collect custom fields for this category
        $data['custom_fields'] = $this->validateCustomFields($request, $category);

        unset($data['images'], $data['subcategory_id']);
        $data['category_id'] = $categoryId;
        $data['user_id']     = Auth::id();
        $data['slug']        = $this->uniqueSlug($data['title'], 'business_posts');
        $data['status']      = 'active';
        $data['expires_at']  = $this->expiresAt();

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('business-posts', 's3');
            }
            $data['images'] = $paths;
            $data['image']  = $paths[0];
        }

        BusinessPost::create($data);

        $days = $user->postDays();
        $expiry = $days ? " Active for {$days} days." : ' It will auto-renew (never expires).';
        return redirect()->route('account')
            ->with('success', 'Your post has been added to "' . $business->name . '"!' . $expiry);
    }

    /** Validate dynamic custom fields for a category and return key=>value map. */
    private function validateCustomFields(Request $request, Category $category): array
    {
        $fields = $category->applicableFields();
        $values = [];
        $input  = $request->input('cf', []);

        foreach ($fields as $field) {
            $val = $input[$field->key] ?? null;

            if ($field->is_required && ($val === null || $val === '')) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cf.' . $field->key => $field->label . ' is required.',
                ]);
            }
            if ($field->type === 'checkbox') {
                $val = $val ? 'Yes' : 'No';
            }
            if ($val !== null && $val !== '') {
                $values[$field->key] = is_string($val) ? trim($val) : $val;
            }
        }
        return $values;
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
            'photo'              => 'nullable|'.self::imgRules(),
        ]);

        $data['user_id']      = Auth::id();
        $data['slug']         = $this->uniqueSlug($data['name'] . '-' . $data['city'], 'matrimonials');
        $data['status']       = 'active';
        $data['hide_contact'] = $request->boolean('hide_contact');
        $data['country']      = 'Canada';
        $data['expires_at']   = $this->expiresAt();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('matrimonials', 's3');
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
        $maxImg = Auth::user()->maxImages();
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
            'images'        => "nullable|array|max:{$maxImg}",
            'images.*'      => self::imgRules(),
        ]);
        $this->moderate($request);
        unset($data['images']);
        if ($request->hasFile('images')) {
            foreach ($r->images ?? [] as $old) Storage::disk('s3')->delete($old);
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('listings', 's3');
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
            'company_logo' => 'nullable|'.self::imgRules(),
        ]);
        $this->moderate($request);
        if ($request->hasFile('company_logo')) {
            if ($r->company_logo) Storage::disk('s3')->delete($r->company_logo);
            $data['company_logo'] = $request->file('company_logo')->store('jobs', 's3');
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
            'image'           => 'nullable|'.self::imgRules(),
        ]);
        $this->moderate($request);
        if ($request->hasFile('image')) {
            if ($r->image) Storage::disk('s3')->delete($r->image);
            $data['image'] = $request->file('image')->store('events', 's3');
        }
        $r->update($data);
    }

    private function updateBusiness(Request $request, Business $r): void
    {
        $maxImg = Auth::user()->maxImages();
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
            'images'      => "nullable|array|max:{$maxImg}",
            'images.*'    => self::imgRules(),
            'logo'        => 'nullable|'.self::imgRules(),
        ]);
        $this->moderate($request, 'name');
        unset($data['images']);
        if ($request->hasFile('images')) {
            foreach ($r->images ?? [] as $old) Storage::disk('s3')->delete($old);
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('businesses', 's3');
            }
            $data['images'] = $paths;
            $data['image']  = $paths[0];
        }
        if ($request->hasFile('logo')) {
            if ($r->logo) Storage::disk('s3')->delete($r->logo);
            $data['logo'] = $request->file('logo')->store('businesses', 's3');
        }
        $r->update($data);
    }

    private function updateBusinessPost(Request $request, BusinessPost $r): void
    {
        $maxImg = Auth::user()->maxImages();
        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'nullable|string|max:50',
            'price_unit'  => 'nullable|string|max:20',
            'images'      => "nullable|array|max:{$maxImg}",
            'images.*'    => self::imgRules(),
        ]);

        $this->moderate($request);

        // Re-validate custom fields against the post's (unchanged) category
        if ($r->category) {
            $data['custom_fields'] = $this->validateCustomFields($request, $r->category);
        }

        unset($data['images']);
        if ($request->hasFile('images')) {
            foreach ($r->images ?? [] as $old) Storage::disk('s3')->delete($old);
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('business-posts', 's3');
            }
            $data['images'] = $paths;
            $data['image']  = $paths[0];
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
            'photo'              => 'nullable|'.self::imgRules(),
        ]);
        if ($request->hasFile('photo')) {
            if ($r->photo) Storage::disk('s3')->delete($r->photo);
            $data['photo'] = $request->file('photo')->store('matrimonials', 's3');
        }
        $data['hide_contact'] = $request->boolean('hide_contact');
        $r->update($data);
    }
}
