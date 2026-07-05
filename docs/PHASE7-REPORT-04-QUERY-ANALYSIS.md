# Phase 7 Report 04 — Query Analysis
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 7 — Enterprise Regression & Performance Benchmark  
**Role:** Enterprise DBA, Performance Engineer  
**Policy:** Evidence only. No fixes.

---

## Query Count by Page

| Page | Query Count | Method |
|------|------------|--------|
| Home (partial simulation — 8 of ~25 sections) | 12 | DB::getQueryLog() |
| Listing index (paginate-12) | 3 (main + category eager + user eager) | Measured |
| Business index (paginate-12) | 2 (main + category eager) | Measured |
| Event index (paginate-12) | 2 (main + category eager) | Measured |
| Job index (paginate-15) | 3 (main + category eager + user eager) | Measured |
| Blog index (paginate-9) | 2 (main + author eager) | Measured |
| User account dashboard | 6 (listings/jobs/events/businesses/matrimonials/businessPosts + paymentHistory) | Code review |
| Chat inbox | 1 main + 10 × correlated subquery (1 per conversation) | Code review |

**Home page full estimate:** ~25 queries per page load (no caching). Breakdown:
- 2 blog posts queries
- 2 events queries (upcoming + community)
- 1 classifieds categories
- 1 listings (with eager: +2)
- 1 directory categories
- 2 businesses (active + featured: with eager: +2)
- 1 jobs + 1 job categories
- 8 dirBiz() closure calls (each = 2 queries: featured + non-featured)
- 1 stats (4 counts merged into 1 via collect)
- 1 advertisements
- 1 poll
- 1 provinces/cities
- 5 Category::where('slug',...)->value('id') calls

**Total: ~28–32 queries per home page load.**

At current data volume (milliseconds each), this is under 50ms total DB time. At 100K rows with province/city filtering, estimate 150–300ms.

---

## N+1 Query Evidence

### Confirmed N+1: User Plan Resolution on Listing Cards

**Trigger code path:**
```php
// In blade view — iterating listing cards:
$listing->user->planName() // calls User::planModel() each time
$listing->user->hasVerifiedBadge() // calls User::planModel() each time
```

**User::planModel():**
```php
public function planModel(): ?Plan
{
    return Plan::findBySlug($this->activePlan()); // fires SQL per call
}
```

**Plan::findBySlug():**
```sql
SELECT * FROM plans WHERE slug = ? AND is_active = 1 LIMIT 1
```

**Evidence from probe:**
```
planModel() called 10× on same user: 6.17ms, 11 queries
10 listings, access user.name/planName/hasVerifiedBadge (no eager): 31 queries, 17.97ms
```

**At scale:** 12 listing cards × 2 planModel() calls each = 24 plan queries per page load. Plans table has 3 rows — each query is fast, but the pattern does not scale cleanly and adds avoidable DB round-trips.

**Root cause:** `planModel()` has no memoization. It fires a fresh SQL query every call.

**Recommendation (no fix applied):** Add `private ?Plan $planModelCache = null;` and check before querying:
```php
public function planModel(): ?Plan
{
    return $this->planModelCache ??= Plan::findBySlug($this->activePlan());
}
```

---

### Potential N+1: User Account Dashboard

**Code:** `UserController::account()` loads 6 separate collections without joins:
```php
$listings     = Listing::where('user_id', $user->id)->latest()->get();
$jobs         = Job::where('user_id', $user->id)->latest()->get();
$events       = Event::where('user_id', $user->id)->latest()->get();
$businesses   = Business::where('user_id', $user->id)->latest()->get();
$matrimonials = Matrimonial::where('user_id', $user->id)->latest()->get();
$businessPosts = BusinessPost::with('business')->where('user_id', $user->id)->latest()->get();
$paymentHistory = PaymentHistory::where('user_id', $user->id)->latest('paid_at')->limit(20)->get();
```

**Total queries:** 7 (one per model) + 1 eager business relation = 8.  
**Assessment:** Not N+1 — each is a single aggregate query. Acceptable.

---

### Confirmed N+1: Chat Inbox — Correlated Subquery

**Code (ChatController::inbox):**
```php
$conversations = Conversation::with(['conversable', 'buyer', 'seller', 'latestMessage'])
    ->where('buyer_id', $userId)
    ->orWhere('seller_id', $userId)
    ->orderByDesc(function ($query) {
        $query->select('created_at')
            ->from('chat_messages')
            ->whereColumn('conversation_id', 'conversations.id')
            ->orderByDesc('created_at')
            ->limit(1);
    })
    ->get();
```

**Evidence:** 690ms for 10 conversations. The `orderByDesc()` closure generates a correlated subquery that runs once per conversation row in the result set. With `latestMessage` eager-loaded (separate query), the total becomes:
- 1 query: conversations WHERE buyer/seller
- 1 correlated subquery per conversation row (10 × subquery inside ORDER BY)
- 1 query: eager latestMessage
- 3 more queries: conversable, buyer, seller

**At 1,000 conversations:** The correlated subquery alone would execute 1,000 times. Estimated 5,000–10,000ms. Completely unusable.

**Root cause:** The subquery is passed as a closure to `orderByDesc()`. Laravel converts this to a correlated subquery in the SQL. Without an index on `(conversation_id, created_at)` in `chat_messages`, each subquery is a full scan of that conversation's messages.

---

### Confirmed Pattern: Home Page dirBiz() Query Fan-Out

**Code (HomeController):**
```php
$professionalServices = $dirBiz(['Professional Services', 'Immigration', 'Real Estate Agent', 'Travel Agency']);
$educationSports      = $dirBiz(['Education', 'Sports']);
$medicalDental        = $dirBiz(['Medical', 'Dental']);
$diningBusinesses     = $dirBiz(['Restaurant']);
$salonSpa             = $dirBiz(['Salon & Spa']);
$fashionBiz           = $dirBiz(['Fashion']);
$groceryStores        = $dirBiz(['Grocery']);
$jewelryBiz           = $dirBiz(['Jewelry']);
```

**Per `$dirBiz()` call:** 3 queries (Category pluck + featured businesses + non-featured businesses).  
**8 dirBiz() calls = 24 queries** for this section alone.  
**Assessment:** Functional but high query count. At current data volume: acceptable. At scale with location filtering: each query hits indexed columns (city+status, province+status) so remains fast.

---

## Duplicate Query Analysis

### Proven Duplicate: Category slugs on home page

```php
$realEstateCatId  = Category::where('slug', 'real-estate')->value('id');
$roommatesCatId   = Category::where('slug', 'roommates')->value('id');
$autosCategoryId  = Category::where('slug', 'autos')->value('id');
$diningCategoryId = Category::where('slug', 'restaurant')->value('id');
$travelAgentCatId = Category::where('slug', 'travel-agency')->value('id');
```

5 separate single-row queries to fetch category IDs by slug, when a single `whereIn('slug', [...])` would suffice.  
**At current scale:** 5 × ~0.5ms = 2.5ms — negligible.

---

## Query Plan Summary

| Query | EXPLAIN type | Key | Verdict |
|-------|-------------|-----|---------|
| listings WHERE status='active' + is_featured | ref | listings_status_featured_created_idx | PASS |
| businesses WHERE status='active' | range | businesses_status_featured_created_idx | PASS |
| job_listings WHERE status='active' AND expires_at > NOW() | **ALL** | **NULL (filesort)** | **WARN** |
| events WHERE status='active' | ref | events_status_featured_created_idx | PASS |
| matrimonials WHERE status='active' | ref | matrimonials_status_featured_created_idx | PASS |
| blog_posts WHERE status='published' | ref | blog_posts_status_created_idx | PASS |
| Plan WHERE slug=? AND is_active=1 | ref | slug index | PASS |
| Chat messages correlated subquery | ALL (per conv) | None | WARN |
