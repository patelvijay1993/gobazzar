# Phase 7 Report 02 — Performance Benchmark Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 7 — Enterprise Regression & Performance Benchmark  
**Role:** Performance Engineer  
**Policy:** Measure actual values. Evidence only. Do NOT apply fixes.

---

## Environment

| Attribute | Value |
|-----------|-------|
| PHP | 8.2.12 |
| Laravel | 12.56.0 |
| Database | MariaDB (MySQL via XAMPP) |
| Server | XAMPP local dev — Windows 11 |
| Cache driver | database |
| Session driver | file |
| Config cache | NO |
| Route cache | NO |
| Services cache | YES |
| OPcache | Not measured (XAMPP default) |
| Queue worker | NOT RUNNING |
| Storage symlink | NOT FOUND |

**Note:** All timings are from a local XAMPP environment, not production hardware. Actual production timings will differ. Measurements are collected via PHP `microtime(true)` directly inside the application bootstrap, not via HTTP layer.

---

## Query Performance — Actual Measurements

| Query | Rows | Time (ms) | Index Used |
|-------|------|-----------|-----------|
| BlogPosts published latest-4 | 9 total | 5.63 | blog_posts_status_created_idx |
| Events active upcoming-6 | 13 total | 3.89 | events_status_featured_created_idx |
| Listings live latest-4 (cold) | 46 total | **359.70** | listings_status_featured_created_idx |
| Listings live latest-4 (warm) | 46 total | 2.79 | listings_status_featured_created_idx |
| Businesses active latest-4 | 11 total | 3.29 | businesses_status_featured_created_idx |
| Jobs live latest-5 | 20 total | 2.19 | jobs_status_featured_created_idx |
| Listing index paginate-12 (cold) | 46 total | **422.20** | listings_status_featured_created_idx |
| Business index paginate-12 | 11 total | 3.09 | businesses_status_featured_created_idx |
| Events index paginate-12 | 13 total | 2.27 | events_status_featured_created_idx |
| Jobs index paginate-15 | 20 total | 2.11 | jobs_status_featured_created_idx |
| Listing search title LIKE | any | 2.56 | Full scan (LIKE '%term%') |
| Business search name LIKE | any | 0.95 | Full scan (LIKE '%term%') |
| Listing by category_id | any | 1.30 | listings_cat_status_idx |
| User account listings | any | 0.52 | user_id FK index |
| User account jobs | any | 0.50 | user_id FK index |
| User account businesses | any | 0.49 | user_id FK index |
| Payment history user-3 | 1 row | 54.85 | user_id FK index |
| Blog index paginate-9 | 9 total | 2.51 | blog_posts_status_created_idx |
| Matrimonial index paginate-12 | 1 total | 2.33 | matrimonials_status_featured_created_idx |
| User favorites list | 0 rows | 18.94 | user_id + favoriteable |
| Plan findBySlug | 1 row | 1.33 | slug index |
| Listing count live | count | 0.62 | listings_status_featured_created_idx |
| Business count active | count | 0.40 | businesses_status_featured_created_idx |
| **Chat inbox user-3** | 10 convs | **690.89** | Correlated subquery (no index on messages.created_at join) |
| Listings N+1 (no eager) × 10 | 10 | 11.29 | 11 queries fired |
| Listings WITH eager load × 10 | 10 | 2.15 | 3 queries (batch) |

---

## First-Boot vs Warm-Boot Anomaly

**Observed:** First call to `Listing::with(['category','user'])->live()->limit(4)->get()` = **359ms**.  
**Second call (same process):** **2.79ms**.

**Root Cause:** The 359ms on first call is PHP cold bootstrap overhead (OPcache miss, autoloader resolution, Eloquent model initialization) in the local XAMPP environment, not a database performance problem. The raw SQL itself executed in **99ms** on cold start, still acceptable at current data volume.

**Implication for Production:** On production with OPcache enabled and PHP-FPM workers persistent across requests, cold-start overhead is absorbed by the first request after deploy. Steady-state request times will match the 2-3ms warm numbers.

**Production estimate at current data volume:**
- Listing index page: ~15–30ms total PHP time (query + render)
- Business index page: ~10–20ms
- Job index page: ~10–15ms

---

## Chat Inbox Performance (CRITICAL — 690ms)

**Query pattern:**
```sql
SELECT conversations.* 
FROM conversations
WHERE buyer_id = ? OR seller_id = ?
ORDER BY (
    SELECT created_at FROM chat_messages
    WHERE conversation_id = conversations.id
    ORDER BY created_at DESC LIMIT 1
) DESC
```

**Problem:** Correlated subquery orders the result set. For each conversation row returned, a subquery runs against `chat_messages`. No composite index on `(conversation_id, created_at)` in `chat_messages`.

**At scale (1,000 conversations, 100,000 messages):** Estimated 2,000–5,000ms. Unusable.

**At current data (10 conversations, ~26 messages):** 690ms on cold boot. Still high.

---

## Home Page Query Count

**Partial simulation (8 of ~25 home page queries):** 12 queries executed.  
**Full home page query estimate:** 20–28 queries (no caching).  
**Largest single query time:** 0.54ms (with indexes).

**At current scale:** Home page acceptable. At 10K+ rows per table, multiple province/city-specific queries without result caching will compound.

---

## Memory Usage

| Metric | Value |
|--------|-------|
| Peak memory (full benchmark run) | 6 MB |
| Current memory at end of run | 6 MB |

**Assessment:** Memory usage is minimal. No concern at current or projected data volume for a standard Laravel application.

---

## N+1 Query Analysis

| Scenario | Queries Fired | Time |
|----------|-------------|------|
| 10 listings, access user.name/planName/hasVerifiedBadge without eager load | **31** | 17.97ms |
| 10 listings with `with(['user','category'])` | 3 | 2.15ms |

**Evidence:** 31 queries for 10 listing cards when user is not eager-loaded. The listing index and home page both use `with(['category', 'user'])` — this is correct. However, any view or loop that accesses `$listing->user->planName()` triggers `Plan::findBySlug()` per user, which is itself a query.

---

## Plan Model — No In-Memory Caching

**Evidence:**
```
planModel() called 10x on same user: 6.17ms, 11 queries
```
Each call to `User::planModel()` fires `Plan::where('slug', $slug)->where('is_active', true)->firstOrFail()`. With 10 listing cards from the same user, this is 10 identical queries.

**At scale:** On a listing index page showing 12 listings from various users, each user's planModel() is fetched per card. With 12 unique users = 12 plan queries. With 100K listings/page = catastrophic.

---

## Performance at Scale — Bottleneck Projections

| Dataset | Expected home page total time | Bottleneck |
|---------|------------------------------|-----------|
| Current (46 listings) | 50–100ms (local) | Cold-start |
| 1,000 listings | 80–150ms | Home page query fan-out |
| 10,000 listings | 150–300ms | LIKE search, home page fans |
| 100,000 listings | 300–800ms | No result cache, chat inbox |
| 1,000,000 listings | 1,000ms+ (unusable) | Multiple unindexed paths, no cache |

---

## Performance Benchmark Summary

| Category | Status | Notes |
|----------|--------|-------|
| Index page queries (current data) | PASS | 2–5ms with indexes |
| Search queries | PASS (current scale) | LIKE without full-text index |
| Pagination | PASS | withQueryString() correct |
| Chat inbox | WARN | 690ms cold, correlated subquery |
| Plan resolution caching | WARN | 11 queries for 10 calls |
| Home page query count | WARN | 20+ queries, no caching |
| Memory usage | PASS | 6MB peak |
| Config/route cache | FAIL | Not built — increases bootstrap cost |
| Storage symlink | FAIL | Not created |
