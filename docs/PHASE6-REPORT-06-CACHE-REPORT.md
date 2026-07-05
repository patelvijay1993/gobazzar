# Phase 6 Report 06 — Cache Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Summary

| Area | Status | Finding |
|------|--------|---------|
| Cache driver | database | CONFIGURED |
| Cache entries (live) | 3 entries | Mostly stale/expired |
| Config cache | Not cached | RISK in production |
| Route cache | Not cached | RISK in production |
| View cache | Not cached | Expected for development |
| Application-level caching | Minimal | Only `Setting::get()` uses cache |
| Plan/credit caching | None | RISK — DB hit on every request |
| Search result caching | None | RISK at scale |

---

## 1. Cache Configuration

```php
// config/cache.php (default)
'default' => 'database'

// Session driver
'session.driver' => 'file'
```

**Assessment:** The `database` cache driver stores cache entries in the `cache` table. This is appropriate for a single-server deployment but adds DB load for cache reads (defeating the purpose at high volume). For production at scale, Redis is strongly recommended.

### Live Cache Entries

```
gobazzar-cache-424f74a6a7ed4d4ed4761507ebcd209a6ef  expires=2026-07-04 04:08:10 (EXPIRED)
gobazzar-cache-424f74a6a7ed4d4ed4761507ebcd209a6ef  expires=2026-07-04 04:08:10 (EXPIRED)
gobazzar-cache-setting_email_verification_required   expires=2026-07-04 04:12:10 (EXPIRED)
```

**Finding:** All 3 cache entries are expired. The duplicate key is unusual — two entries with the same key suggest a cache write race condition or a cleanup issue.

---

## 2. Application-Level Cache Usage

### CACHE-001 — `Setting::get()` Uses 5-Minute Cache (PASS)

```php
// app/Models/Setting.php
public static function get(string $key, mixed $default = null): mixed
{
    return Cache::remember("setting_{$key}", 300, function () use ($key, $default) {
        $row = static::find($key);
        return $row ? $row->value : $default;
    });
}
```

**Assessment:** Good pattern — settings are cached for 5 minutes (300 seconds). Cache is invalidated on `Setting::set()` via `Cache::forget()`. This is the only model in the application that implements proper caching.

### CACHE-002 — `Plan::active()` Has No Cache (High)

```php
// app/Models/Plan.php
public static function active()
{
    return static::where('is_active', true)->orderBy('sort_order')->get();
}
```

**Finding:** `Plan::active()` is called on every pricing page load and potentially on every plan-gated request. Plans change rarely (only when admin modifies them) but are fetched from DB on every call.

**Impact:** At 1,000 concurrent users all viewing the pricing page, this fires 1,000 identical `SELECT * FROM plans WHERE is_active=1 ORDER BY sort_order` queries. Plans have 3 rows — the query is cheap, but it's still unnecessary repeated work.

**Recommended Fix:**
```php
public static function active()
{
    return Cache::remember('plans_active', 600, fn() =>
        static::where('is_active', true)->orderBy('sort_order')->get()
    );
}
```
Invalidate on any plan update in the admin panel.

**Effort:** 30 minutes | **Breaking Change Risk:** Low

---

### CACHE-003 — `User::activePlan()` / `maybeResetCredits()` No Cache (High)

```php
// app/Models/User.php
public function activePlan(): string
{
    // DB read + potential DB write (maybeResetCredits) on every call
}
```

**Finding:** `activePlan()` is called in controllers, view helpers, and middleware on every authenticated request. It performs a DB read (check `plan_expires_at`) and potentially a DB write (`maybeResetCredits()`). There is no caching at any level.

**Impact:** An authenticated user browsing the site triggers multiple `SELECT` and `UPDATE` queries on the `users` table per page load. For a marketplace with 10,000 active users, this is 10,000+ writes per hour to the `users` table on plan checks alone.

**Recommended Fix:**
1. Cache `activePlan()` result per user for 60 seconds (short TTL acceptable since plan changes are manual)
2. Move `maybeResetCredits()` to a scheduled command rather than the request path

**Effort:** 2 hours | **Breaking Change Risk:** Low

---

### CACHE-004 — `Location::activeProvinces()` and `activeCities()` No Cache (Medium)

```php
// app/Models/Location.php
public static function activeCities(?string $province = null): Collection
{
    return static::where('is_active', true)
        ->when($province, fn ($q) => $q->where('province', $province))
        ->orderBy('sort_order')
        ->orderBy('city')
        ->pluck('city');
}
```

**Finding:** Both `activeProvinces()` and `activeCities()` are called on every page that has a location filter (Home, Classifieds, Jobs, Events, Directory — 5+ pages). Locations change extremely rarely (admin-managed) but are queried on every page load.

**Impact:** Medium — 35 location rows total. The queries are fast, but at 10,000+ concurrent users this is 20,000+ location queries per page cycle.

**Recommended Fix:**
```php
public static function activeCities(?string $province = null): Collection
{
    $key = 'locations_cities_' . ($province ?? 'all');
    return Cache::remember($key, 3600, fn() =>
        static::where('is_active', true)
            ->when($province, fn ($q) => $q->where('province', $province))
            ->orderBy('sort_order')->orderBy('city')
            ->pluck('city')
    );
}
```

**Effort:** 1 hour | **Breaking Change Risk:** Low

---

### CACHE-005 — `Category` Queries Not Cached (Medium)

```php
// HomeController
$classifiedCategories = Category::where('type', 'classifieds')
    ->where('is_active', true)->orderBy('sort_order')->get();

$directoryCategories = Category::where('type', 'directory')
    ->where('is_active', true)->orderBy('sort_order')->get();
```

**Finding:** HomeController fires 4-6 separate `SELECT * FROM categories WHERE type=X` queries on every homepage load. Categories change rarely (admin-managed), yet are re-fetched on every request.

**Recommended Fix:** Cache by type for 10 minutes. Invalidate from `CategoryResource` in Filament when categories are saved.

**Effort:** 1 hour | **Breaking Change Risk:** Low

---

## 3. Framework Cache Status

### Config Cache

```bash
# To check: php artisan config:cache
```

**Finding:** In a development environment, config caching is not expected. However, for production, `php artisan config:cache` must be run to cache the configuration. Without it, Laravel reads all `config/*.php` files on every request.

**Risk:** High in production — config reads on every request add 5-10ms per request.

**Recommended Fix:** Add `php artisan config:cache` to the deployment script.

### Route Cache

```bash
# To check: php artisan route:cache
```

**Finding:** Route caching is not active (development). In production, with 50+ named routes, route caching saves 2-5ms per request on route matching.

**Recommended Fix:** Add `php artisan route:cache` to deployment script. Note: routes using closures cannot be cached — verify all routes use controller references.

### View Cache

View caching (Blade compilation) is handled automatically by Laravel — compiled views are stored in `storage/framework/views`. This is always active.

---

## 4. Cache Invalidation Strategy

| Resource | Cache Strategy | Invalidation | Assessment |
|----------|---------------|-------------|------------|
| Settings | `Cache::remember(300)` | On `Setting::set()` | GOOD |
| Plans | No cache | N/A | MISSING |
| User plan/credits | No cache | N/A | MISSING |
| Locations | No cache | N/A | MISSING |
| Categories | No cache | N/A | MISSING |
| Listings index | No cache | N/A | MISSING |
| Search results | No cache | N/A | MISSING |

**Overall Assessment:** Cache strategy is minimal. Only the `settings` table has proper caching. All high-traffic read paths (plans, locations, categories, user plan checks) hit the database on every request.

---

## 5. Session Management

```
Session driver: file
Session lifetime: 120 minutes (2 hours)
Active sessions: 1 (development)
```

**Assessment:**
- `file` session driver is appropriate for single-server deployment
- For multi-server deployments, `database` or `redis` sessions are required
- 120-minute lifetime is standard
- File sessions are not shared across servers — sticky sessions or a centralized driver are required for horizontal scaling

---

## 6. Cache Report Verdict

| Finding | Severity |
|---------|----------|
| Database cache driver (not Redis) | Medium (acceptable for single server) |
| Plan::active() not cached | High |
| User::activePlan() not cached | High |
| Location queries not cached | Medium |
| Category queries not cached | Medium |
| Config/route cache not in deploy | High (production) |
| Setting::get() properly cached | PASS |
| Cache invalidation on setting::set | PASS |
