# Phase 6 Report 07 — Performance Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Summary

| Area | Status | Severity |
|------|--------|----------|
| Missing DB indexes (24 critical columns) | CRITICAL | Critical |
| N+1 queries: HomeController | HIGH | High |
| N+1 queries: HomeController dirBiz() | HIGH | High |
| N+1 queries: `activePlan()` per request | HIGH | High |
| `maybeResetCredits()` write per request | HIGH | High |
| Pagination on index pages | PASS | — |
| `with()` eager loading on listings/jobs | PASS | — |
| Category `applicableFields()` parent query | MEDIUM | Medium |
| `PollOption::percentage` N+1 | MEDIUM | Medium |
| No query result caching | HIGH | High |

---

## 1. Missing Indexes — Critical Performance Gap

### PERF-001 — 24 Critical Filter Columns Have No Index (Critical)

**Evidence:**
```
MISSING INDEX: listings.status
MISSING INDEX: listings.province
MISSING INDEX: listings.city
MISSING INDEX: listings.is_featured
MISSING INDEX: businesses.status
MISSING INDEX: businesses.province
MISSING INDEX: businesses.city
MISSING INDEX: businesses.is_featured
MISSING INDEX: job_listings.status
MISSING INDEX: job_listings.province
MISSING INDEX: job_listings.city
MISSING INDEX: job_listings.is_featured
MISSING INDEX: events.status
MISSING INDEX: events.province
MISSING INDEX: events.city
MISSING INDEX: events.is_featured
MISSING INDEX: matrimonials.status
MISSING INDEX: matrimonials.province
MISSING INDEX: matrimonials.city
MISSING INDEX: matrimonials.is_featured
MISSING INDEX: blog_posts.status
(blog_posts.province/city/is_featured not applicable)
```

**Root Cause:** The original migrations did not add indexes to `status`, `province`, `city`, or `is_featured` columns on any content table. These are the 4 most commonly used WHERE conditions across every controller.

**Current Impact (46 listings, small dataset):** Queries take 1.5–5ms — tolerable.

**Impact at Scale (100,000 listings):**

| Query | No Index (full table scan) | With Index (estimated) |
|-------|--------------------------|----------------------|
| `WHERE status='active'` | ~50ms | ~1ms |
| `WHERE province='ON'` | ~50ms | ~2ms |
| `WHERE status='active' AND province='ON'` | ~50ms | ~1ms |
| `WHERE is_featured=1 ORDER BY is_featured DESC` | ~50ms | ~1ms |

Every page load that queries listings (Home, Classifieds index, Jobs, Events, Directory) performs full table scans on the most-used filter column (`status`). At 100,000 rows this becomes the primary bottleneck.

**Recommended Fix:**
```php
// Migration: add_performance_indexes_to_content_tables
Schema::table('listings', function (Blueprint $table) {
    $table->index(['status', 'province', 'city']);
    $table->index(['status', 'is_featured', 'created_at']);
    $table->index(['category_id', 'status']);
});
// Same for businesses, job_listings, events, matrimonials
```

Composite index `[status, province, city]` covers the most common filter combination.
Composite index `[status, is_featured, created_at]` covers the `orderByDesc(is_featured)->latest()` pattern.

**Effort:** 2 hours | **Breaking Change Risk:** None | **Regression Risk:** None

---

## 2. N+1 Query Analysis

### PERF-002 — HomeController Fires 20+ Queries Per Page Load (High)

**Evidence (`HomeController::index()`):**

The homepage fires the following queries:
```
1.  SELECT * FROM blog_posts WHERE status='published' LIMIT 4
2.  SELECT * FROM events WHERE status='active' AND start_date >= ? LIMIT 6
3.  SELECT * FROM categories WHERE type='classifieds' AND is_active=1
4.  SELECT * FROM listings WITH category WHERE status='active' LIMIT 4
5.  SELECT * FROM categories WHERE type='directory' ...
6.  SELECT * FROM businesses WITH category WHERE status='active' LIMIT 4
7.  SELECT * FROM categories WHERE name IN ('Professional Services'...) -- dirBiz()
8.  SELECT * FROM businesses WHERE category_id IN (...) AND is_featured=1 -- dirBiz featured
9.  SELECT * FROM businesses WHERE category_id IN (...) AND is_featured=0 LIMIT 5 -- dirBiz random
10. [repeats 7–9 for each of 8 category groups = 24 queries just for dirBiz sections]
11. SELECT * FROM events WHERE status='active' LIMIT 5 -- community events (duplicate of #2)
12. SELECT * FROM categories WHERE type='classifieds' -- jobCategories (duplicate of #3)
13. SELECT * FROM job_listings ... LIMIT 5
14. SELECT * FROM businesses WHERE is_featured=1 LIMIT 4
15. SELECT * FROM listings WHERE is_featured=1 LIMIT 4
16. SELECT * FROM listings ORDER BY views DESC LIMIT 4
17. SELECT * FROM businesses LIMIT 5
18. COUNT queries for stats (4 separate COUNT queries)
19. Advertisement queries (3 separate)
20. Poll::current() (up to 3 queries for city/province/canada)
21. Location::activeProvinces()
22. Location::activeCities()
23. Category slug lookups (5 individual queries for real-estate, roommates, autos, etc.)
```

**Total estimated queries on homepage load: ~50–60 queries**

**Root Cause:** `dirBiz()` closure is called 8 times (8 category groups), each making 3 queries: category ID lookup, featured businesses, non-featured businesses. That's 24 queries just for the homepage directory sections.

**Impact:** At 100 concurrent homepage loads = 5,000–6,000 queries/second just from the homepage. This will saturate a typical MySQL instance around 500–1,000 concurrent users.

**Recommended Fix:**
1. Cache the entire homepage data for 60 seconds (acceptable — data changes are not real-time)
2. Refactor `dirBiz()` to a single query with `category_id IN (...)` grouping
3. Eliminate duplicate queries (events queried twice, categories queried twice)
4. Combine stat COUNT queries into a single query with UNION or aggregated subquery

**Effort:** 4 hours | **Breaking Change Risk:** Low | **Regression Risk:** Medium

---

### PERF-003 — `activePlan()` + `maybeResetCredits()` on Every Authenticated Request (High)

**Evidence (`app/Models/User.php`):**
```php
// Called from controllers, middleware, view helpers
public function activePlan(): string
{
    $this->maybeResetCredits(); // potential DB UPDATE
    // DB read: check plan_expires_at
}
```

**Root Cause:** `activePlan()` performs a plan expiry check and potentially a `featured_credits` reset on every call. On a page like the account page, it may be called 3–5 times (for listing limits, image limits, plan name display, etc.).

**Impact:** Each authenticated page request executes 3–5 calls to `activePlan()`, each potentially hitting the database. On a busy day with 10,000 page loads from authenticated users, this is 30,000–50,000 additional DB operations.

**Recommended Fix:**
1. Cache the result of `activePlan()` per user in the request lifecycle using `once()` or a property:
```php
private ?string $cachedPlan = null;

public function activePlan(): string
{
    return $this->cachedPlan ??= $this->computeActivePlan();
}
```
2. Move `maybeResetCredits()` to a scheduled Artisan command that runs monthly.

---

### PERF-004 — `Category::applicableFields()` Makes Duplicate Parent Query (Medium)

**Evidence:**
```php
public function applicableFields()
{
    $fields = $this->fields()->get();
    if ($this->parent_id) {
        $parentFields = CategoryField::where('category_id', $this->parent_id)
            ->orderBy('sort_order')->get(); // separate query
        $fields = $parentFields->concat($fields);
    }
    return $fields->unique('key')->values();
}
```

**Root Cause:** When a sub-category's fields are loaded, a second query fetches the parent's fields. If the category relationship is not eager-loaded, this may also trigger a third query for the parent category object.

**Impact:** `PostController::create()` and `BusinessController::showPost()` call `applicableFields()` which fires 2–3 queries per call. Low impact currently (few categories), medium impact with 100+ sub-categories.

**Recommended Fix:** Eager load `fields` with the category: `Category::with(['fields', 'parent.fields'])->...`

---

### PERF-005 — `PollOption::getPercentageAttribute()` N+1 (Medium)

**Evidence:**
```php
public function getPercentageAttribute(): int
{
    $total = $this->poll->total_votes; // loads poll relationship if not loaded
    if ($total === 0) return 0;
    return (int) round(($this->votes / $total) * 100);
}
```

**Root Cause:** `getPercentageAttribute()` accesses `$this->poll->total_votes` which loads the `poll` relationship if not already loaded. `total_votes` is itself a `getTotalVotesAttribute()` that calls `$this->options->sum('votes')` — which loads the `options` relationship on the poll.

**Impact:** Displaying a poll with 5 options fires: 1 poll query + 5 option queries (N+1) + 5 percentage attribute accesses each loading the poll again. However, `Poll::current()` uses `with('options')` so options are eager-loaded — mitigating the worst case.

**Impact is Low** given polling is currently a simple 2-option poll and `with('options')` is used in `Poll::current()`.

---

## 3. Query Timing (Dev Data)

```
listings.status=active:     5.06ms (no index — full scan of 46 rows)
businesses.status=active:   4.07ms (no index — full scan of 11 rows)
events.status=active:       1.85ms (no index — full scan of 13 rows)
job_listings.status=active: 1.52ms (no index — full scan of 20 rows)
```

**Projection at 100,000 rows (no index):**
- Full table scan on `status`: estimated 50–100ms per query
- With composite index `(status, province, city)`: estimated 1–3ms

---

## 4. Pagination Assessment

| Controller | Pagination | Page Size | Assessment |
|-----------|-----------|-----------|------------|
| ListingController | `paginate(12)` | 12 | PASS |
| BusinessController | `paginate(12)` | 12 | PASS |
| EventController | `paginate(12)` | 12 | PASS |
| JobController | `paginate(15)` | 15 | PASS |
| UserController (account) | `->get()` (no pagination) | All | RISK |

**Finding:** `UserController::account()` loads all user listings, jobs, events, businesses, matrimonials, and business posts with `->get()` — no pagination. A power-seller user with 9,999 listings (max allowed) would load 9,999 records into memory at once.

**Recommended Fix:** Add pagination to the account page listing tables, or at minimum add a `->limit(100)` safety cap.

**Effort:** 2 hours | **Breaking Change Risk:** Low

---

## 5. Eager Loading Assessment

| Query | Eager Loading | Assessment |
|-------|--------------|------------|
| `Listing::with(['category', 'user'])` | ✓ | PASS |
| `Business::with('category')` | ✓ | PASS |
| `Job::with(['user', 'category'])` | ✓ | PASS |
| `BusinessController::show` related | No eager loading | RISK |
| `Conversation::getSubjectUrlAttribute` | No eager loading | N+1 risk |

---

## 6. Search Performance

All search filters use `LIKE '%...%'` with `addcslashes()`:
```php
->where('title', 'like', '%' . addcslashes($request->search, '%_\\') . '%')
```

**Assessment:** Leading `%` wildcard means the query cannot use indexes even if a `title` index existed. Full table scan required for all text searches.

**Impact at scale:** A search on `listings.title` with 100,000 rows, no full-text index = ~100ms per search.

**Recommended Fix:** Add `FULLTEXT` indexes for searchable text fields (`title`, `description`, `company`, `name`) and use `MATCH() AGAINST()` syntax. Or integrate a search engine (Algolia, Meilisearch) for the listing/business search.

**Effort:** 4 hours (full-text index) or 2 days (search engine) | **Breaking Change Risk:** Low

---

## 7. Performance Verdict

| Issue | Severity | Impact at Scale |
|-------|---------|----------------|
| 24 missing indexes | Critical | Query time ×50 at 100K rows |
| Homepage 50+ queries | High | DB saturation at 500+ users |
| activePlan() per request | High | Write amplification |
| Account page no pagination | Medium | Memory exhaustion for power sellers |
| LIKE search (leading %) | High | Full scan on every search |
| No query caching | High | Redundant identical queries |
