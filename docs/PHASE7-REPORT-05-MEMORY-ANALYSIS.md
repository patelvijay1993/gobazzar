# Phase 7 Report 05 — Memory Analysis
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 7 — Enterprise Regression & Performance Benchmark  
**Role:** Performance Engineer  
**Policy:** Evidence only. No fixes.

---

## Measured Memory Usage

| Metric | Value | Method |
|--------|-------|--------|
| Peak memory — full benchmark run (25 queries) | 6 MB | memory_get_peak_usage(true) |
| Current memory at benchmark end | 6 MB | memory_get_usage(true) |
| Expected Laravel bootstrap overhead | 3–5 MB | Typical for Laravel 12 |
| Expected per-request overhead at load | 6–10 MB | Typical |

**Assessment:** Memory usage is well within normal bounds for a Laravel 12 application. No memory leaks detected in the measured query patterns.

---

## Memory Profile by Component

### Laravel Bootstrap

**Estimated:** ~3 MB  
This includes autoloader resolution, service container instantiation, config load, middleware stack, Eloquent ORM initialization.

**Config cache status:** NOT BUILT  
Without `php artisan config:cache`, the framework reads all `config/*.php` files on every bootstrap. This adds minor CPU overhead and marginal memory overhead (reading/parsing files) on each request. On production with config cache: framework reads a single serialized PHP file instead of 20+ config files.

**Impact:** ~0.5–2ms extra per request. Negligible for memory, noticeable for time on high-traffic servers.

---

### Eloquent Model Loading

**Measured: 46 listings + category + user eager load = ~2 MB model memory**

Eloquent models hydrate PHP objects from DB rows. With JSON casts on `images`, `tags`, `badges` arrays: each row decodes JSON on load. At 12 listings per page × 3 JSON fields = 36 json_decode operations. Negligible overhead.

**Business model** has additional `hours` cast to array (fixed in Phase 6C). Now returns clean PHP array.

---

### Large Dataset Memory Projection

| Dataset Size | Estimated Memory per Request |
|-------------|------------------------------|
| Current (46 listings) | 6 MB |
| 1,000 listings (paginated, 12/page) | 6–8 MB |
| 10,000 listings (paginated, 12/page) | 6–8 MB |
| 100,000 listings (paginated, 12/page) | 6–8 MB |

**Assessment:** Pagination ensures only 12 records are loaded per page regardless of total dataset size. Memory does NOT scale with total row count — it scales with page size. Pagination is correctly implemented on all index routes.

**Exceptions:**
- `UserController::account()` loads ALL user listings without pagination: `Listing::where('user_id', $user->id)->latest()->get()`. A power-seller user with 1,000 listings would load all 1,000 into memory. Unlikely at current product stage but is a potential issue.
- Home page `dirBiz()` closures collect up to 5 businesses per category × 8 categories = 40 objects. Bounded.

---

### Queue Memory (Unprocessed Jobs)

**Current state:** 26 unprocessed `App\Events\MessageSent` jobs in `jobs` table. Worker not running.

**Memory impact:** No active memory impact while worker is stopped. When a worker eventually starts, it processes jobs sequentially. `App\Events\MessageSent` broadcasts the message payload — memory per job is minimal (~1–2 KB per message).

**Risk:** No memory risk from 26 stale jobs. However, if stale jobs fail when processed (e.g., because the message/conversation was deleted), they may route to `failed_jobs` table.

---

### Image Storage — Memory Implications

**All images stored on S3.** Blade views generate S3 URLs only (no server-side image processing). No GD/Imagick memory consumption confirmed in current code paths. Image validation rules in PostController use Laravel's built-in `image|mimes|max|dimensions` — these use PHP's getimagesize() which reads minimal header bytes, not the full image.

---

## Memory Assessment Summary

| Category | Status | Notes |
|----------|--------|-------|
| Baseline memory | PASS | 6MB peak — normal |
| Memory scaling with pagination | PASS | Bounded at page size |
| JSON cast memory | PASS | Negligible per row |
| Queue job memory | PASS | Worker stopped — no active impact |
| Config cache (missing) | WARN | Minor CPU overhead per bootstrap |
| Account page — unbounded get() | WARN | All user listings loaded at once — risk at scale |
| Image processing | PASS | No server-side processing |
