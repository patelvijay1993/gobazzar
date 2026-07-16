<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\MatrimonialController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FeaturedCreditController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BusinessContentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/feed', [PostController::class, 'feed'])->name('feed');
Route::get('/locations/cities', fn (\Illuminate\Http\Request $r) =>
    response()->json(\App\Models\Location::activeCities($r->province))
)->name('locations.cities');

// Sub-categories for a given parent (AJAX cascade)
Route::get('/categories/subs', fn (\Illuminate\Http\Request $r) =>
    response()->json(
        \App\Models\Category::where('parent_id', $r->parent)
            ->where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')
            ->get(['id', 'name', 'icon'])
    )
)->name('categories.subs');

// Custom fields applicable to a category (parent + own), for dynamic post form
Route::get('/categories/{category}/fields', function (\App\Models\Category $category) {
    return response()->json(
        $category->applicableFields()->map(fn ($f) => [
            'label'       => $f->label,
            'key'         => $f->key,
            'type'        => $f->type,
            'options'     => $f->options ?? [],
            'placeholder' => $f->placeholder,
            'required'    => (bool) $f->is_required,
        ])->values()
    );
})->name('categories.fields');

// Businesses owned by the current user (AJAX, for post form)
Route::get('/my-businesses', function () {
    abort_unless(auth()->check(), 403);
    return response()->json(
        \App\Models\Business::where('user_id', auth()->id())
            ->orderBy('name')->get(['id', 'name', 'category_id'])
    );
})->middleware('auth')->name('my-businesses');

// Poll voting (anonymous, one vote per device token)
Route::post('/poll/{poll}/vote', function (\Illuminate\Http\Request $request, \App\Models\Poll $poll) {
    $request->validate(['option_id' => 'required|exists:poll_options,id', 'token' => 'required|string']);

    // Ensure option belongs to this poll
    $option = $poll->options()->where('id', $request->option_id)->first();
    if (!$option) return response()->json(['error' => 'Invalid option'], 422);

    // Prevent duplicate votes from same device
    $already = \App\Models\PollVote::where('poll_id', $poll->id)
        ->where('voter_token', $request->token)
        ->exists();

    if (!$already) {
        \App\Models\PollVote::create([
            'poll_id'        => $poll->id,
            'poll_option_id' => $option->id,
            'voter_token'    => $request->token,
        ]);
        $option->increment('votes');
    }

    $poll->load('options');
    return response()->json([
        'total'   => $poll->total_votes,
        'options' => $poll->options->map(fn ($o) => [
            'id'    => $o->id,
            'label' => $o->label,
            'votes' => $o->votes,
            'pct'   => $o->percentage,
        ]),
        'already' => $already,
    ]);
})->name('poll.vote');

// Sitemaps
Route::get('/sitemap.xml',          [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-static.xml',   [\App\Http\Controllers\SitemapController::class, 'static']);
Route::get('/sitemap-listings.xml', [\App\Http\Controllers\SitemapController::class, 'listings']);
Route::get('/sitemap-events.xml',   [\App\Http\Controllers\SitemapController::class, 'events']);
Route::get('/sitemap-jobs.xml',     [\App\Http\Controllers\SitemapController::class, 'jobs']);
Route::get('/sitemap-businesses.xml', [\App\Http\Controllers\SitemapController::class, 'businesses']);
Route::get('/sitemap-blog.xml',     [\App\Http\Controllers\SitemapController::class, 'blog']);

// Static pages
Route::get('/about',     [PageController::class, 'about'])->name('about');
Route::get('/advertise', [PageController::class, 'advertise'])->name('advertise');
Route::get('/contact',   [PageController::class, 'contact'])->name('contact');
Route::post('/contact',  [PageController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/privacy',   [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms',     [PageController::class, 'terms'])->name('terms');

// Stripe webhook (no auth, no CSRF)
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

// Stripe checkout (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/stripe/checkout/{plan}', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel/confirm', [StripeController::class, 'cancelConfirm'])->name('stripe.cancel.confirm');
    Route::post('/stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
    Route::post('/stripe/resume', [StripeController::class, 'resume'])->name('stripe.resume');
});

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
Route::get('/pricing/upgrade/{plan}', [PricingController::class, 'upgrade'])->name('pricing.upgrade')->middleware('auth');
Route::post('/pricing/request', [PricingController::class, 'request'])->name('pricing.request')->middleware('auth');
Route::post('/promo/apply', [PricingController::class, 'applyPromo'])->name('promo.apply')->middleware('auth');

// Advertise enquiry (public)
Route::post('/advertise/enquiry', [\App\Http\Controllers\AdvertiseController::class, 'store'])->name('advertise.store');

// Ad tracking
Route::post('/ads/{ad}/impression', [\App\Http\Controllers\AdTrackingController::class, 'impression'])->name('ads.impression');
Route::get('/ads/{ad}/click', [\App\Http\Controllers\AdTrackingController::class, 'click'])->name('ads.click');

// Content reporting (auth required)
Route::post('/report', [ReportController::class, 'store'])->name('report.store')->middleware('auth');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/password/reset/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Email verification routes
Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verificationVerify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('/email/resend', [AuthController::class, 'verificationSend'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

// AI Assistant
Route::post('/assistant/chat', [\App\Http\Controllers\AssistantController::class, 'chat'])->name('assistant.chat');

// PWA Push Notifications
Route::get('/push/vapid-key', [\App\Http\Controllers\PushController::class, 'vapidKey'])->name('push.vapid-key');
Route::get('/enable-notifications', fn() => view('push-subscribe'))->name('push.enable')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::post('/push/subscribe',   [\App\Http\Controllers\PushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushController::class, 'unsubscribe'])->name('push.unsubscribe');
});

// Chat routes (auth required)
Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'inbox'])->name('inbox');
    Route::get('/listing/{listing}', [ChatController::class, 'showListing'])->name('show');
    Route::get('/event/{event}', [ChatController::class, 'showEvent'])->name('event');
    Route::get('/business/{business:slug}', [ChatController::class, 'showBusiness'])->name('business');
    Route::get('/business/{business:slug}/post/{post:slug}', [ChatController::class, 'showBusinessPost'])->name('business.post');
    Route::get('/conversation/{conversation}', [ChatController::class, 'showConversation'])->name('conversation');
    Route::get('/conversation/{conversation}/poll', [ChatController::class, 'poll'])->name('poll');
    Route::post('/conversation/{conversation}/send', [ChatController::class, 'send'])->name('send');
    Route::post('/conversation/{conversation}/read', [ChatController::class, 'markRead'])->name('read');
    Route::get('/unread-count', [ChatController::class, 'unreadCount'])->name('unread');
    // Floating widget AJAX endpoints
    Route::post('/open/listing/{listing}',          [ChatController::class, 'openListing'])->name('open.listing');
    Route::post('/open/business/{business:slug}',   [ChatController::class, 'openBusiness'])->name('open.business');
    Route::post('/open/event/{event}',              [ChatController::class, 'openEvent'])->name('open.event');
});

// Account routes (auth required, no email verification needed)
Route::middleware('auth')->group(function () {
    Route::get('/account', [UserController::class, 'account'])->name('account');
    Route::patch('/account/profile', [UserController::class, 'updateProfile'])->name('account.profile');
    Route::patch('/account/password', [UserController::class, 'updatePassword'])->name('account.password');
    Route::patch('/account/privacy', [UserController::class, 'updatePrivacy'])->name('account.privacy');
    Route::get('/account/analytics/{listing}', [UserController::class, 'analytics'])->name('account.analytics');
    Route::get('/account/favorites', [FavoriteController::class, 'index'])->name('account.favorites');
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/featured-credits/toggle', [FeaturedCreditController::class, 'toggle'])->name('featured.toggle');
    Route::post('/business/generate-content', [BusinessContentController::class, 'generate'])->name('business.generate-content');
});

// Post submission + edit (auth required; email verification controlled by REQUIRE_EMAIL_VERIFICATION in .env)
Route::middleware(['auth', 'email.verified'])->group(function () {
    Route::get('/post/create', [PostController::class, 'create'])->name('post.create');
    Route::post('/post/classified', [PostController::class, 'storeClassified'])->name('post.classified');
    Route::post('/post/job', [PostController::class, 'storeJob'])->name('post.job');
    Route::post('/post/event', [PostController::class, 'storeEvent'])->name('post.event');
    Route::post('/post/business', [PostController::class, 'storeBusiness'])->name('post.business');
    Route::post('/post/matrimonial', [PostController::class, 'storeMatrimonial'])->name('post.matrimonial');
    Route::post('/post/business-post', [PostController::class, 'storeBusinessPost'])->name('post.business-post');
    // Rich-text editor inline image upload (returns URL)
    Route::post('/post/editor-image', [PostController::class, 'uploadEditorImage'])->name('post.editor-image');

    // Edit / Delete
    Route::get('/post/{type}/{id}/edit', [PostController::class, 'edit'])->name('post.edit');
    Route::post('/post/{type}/{id}/update', [PostController::class, 'update'])->name('post.update');
    Route::delete('/post/{type}/{id}', [PostController::class, 'destroy'])->name('post.destroy');
});

// Public routes
Route::prefix('classifieds')->name('classifieds.')->group(function () {
    Route::get('/', [ListingController::class, 'index'])->name('index');
    Route::get('/{listing:slug}', [ListingController::class, 'show'])->name('show');
});

Route::get('/seller/{user}', [ListingController::class, 'sellerProfile'])->name('seller.profile')->middleware('auth');

Route::prefix('directory')->name('directory.')->group(function () {
    Route::get('/', [BusinessController::class, 'index'])->name('index');
    Route::get('/category/{category:slug}', [BusinessController::class, 'category'])->name('category');
    Route::get('/{business:slug}', [BusinessController::class, 'show'])->name('show');
    Route::get('/{business:slug}/{post:slug}', [BusinessController::class, 'showPost'])->name('post');
});

Route::patch('/business/{business}/toggle-chat', [BusinessController::class, 'toggleChat'])
    ->name('business.toggle-chat')
    ->middleware('auth');

Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{event:slug}', [EventController::class, 'show'])->name('show');
});

Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [JobController::class, 'index'])->name('index');
    Route::get('/{job:slug}', [JobController::class, 'show'])->name('show');
});

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{post:slug}', [BlogController::class, 'show'])->name('show');
});

// Admin listing image remove (used inside Filament edit form)
Route::delete('/admin/listing/{id}/remove-image', function (\Illuminate\Http\Request $request, $id) {
    abort_unless(auth()->check() && auth()->user()->is_admin, 403);
    $listing = \App\Models\Listing::findOrFail($id);
    // Accept both JSON body and form input
    $enc = $request->input('img') ?? $request->json('img');
    $img = base64_decode($enc);
    $all = array_values(array_filter(array_merge(
        $listing->image ? [$listing->image] : [],
        (array) ($listing->images ?? [])
    )));
    $all = array_values(array_filter($all, fn($i) => $i !== $img));
    if (!str_starts_with($img, 'http')) {
        \Illuminate\Support\Facades\Storage::disk('s3')->delete($img);
    }
    $listing->image  = $all[0] ?? null;
    $listing->images = count($all) > 1 ? array_slice($all, 1) : null;
    $listing->save();
    return response()->json(['ok' => true]);
})->name('admin.listing.remove-image')->middleware('auth');

// Matrimonial hidden until v2 — routes kept for named-route references, redirect to home
Route::prefix('matrimonial')->name('matrimonial.')->group(function () {
    Route::get('/', fn() => redirect()->route('home'))->name('index');
    Route::get('/{profile:slug}', fn() => redirect()->route('home'))->name('show');
});
