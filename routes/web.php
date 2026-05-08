<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\MatrimonialController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/feed', [PostController::class, 'feed'])->name('feed');
Route::get('/locations/cities', fn (\Illuminate\Http\Request $r) =>
    response()->json(\App\Models\Location::activeCities($r->province))
)->name('locations.cities');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
Route::get('/pricing/upgrade/{plan}', [PricingController::class, 'upgrade'])->name('pricing.upgrade')->middleware('auth');
Route::post('/pricing/request', [PricingController::class, 'request'])->name('pricing.request')->middleware('auth');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Account routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/account', [UserController::class, 'account'])->name('account');
    Route::patch('/account/profile', [UserController::class, 'updateProfile'])->name('account.profile');
    Route::patch('/account/password', [UserController::class, 'updatePassword'])->name('account.password');

    // Post submission
    Route::get('/post/create', [PostController::class, 'create'])->name('post.create');
    Route::post('/post/classified', [PostController::class, 'storeClassified'])->name('post.classified');
    Route::post('/post/job', [PostController::class, 'storeJob'])->name('post.job');
    Route::post('/post/event', [PostController::class, 'storeEvent'])->name('post.event');
    Route::post('/post/business', [PostController::class, 'storeBusiness'])->name('post.business');
    Route::post('/post/matrimonial', [PostController::class, 'storeMatrimonial'])->name('post.matrimonial');

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

Route::prefix('directory')->name('directory.')->group(function () {
    Route::get('/', [BusinessController::class, 'index'])->name('index');
    Route::get('/{business:slug}', [BusinessController::class, 'show'])->name('show');
});

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

Route::prefix('matrimonial')->name('matrimonial.')->group(function () {
    Route::get('/', [MatrimonialController::class, 'index'])->name('index');
    Route::get('/{profile:slug}', [MatrimonialController::class, 'show'])->name('show');
});
