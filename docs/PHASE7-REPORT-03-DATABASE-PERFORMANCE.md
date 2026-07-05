# Phase 7 Report 03 — Database Performance Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 7 — Enterprise Regression & Performance Benchmark  
**Role:** Enterprise DBA  
**Policy:** Evidence only. No fixes.

---

## Index Audit — All Content Tables

### listings (46 rows)

| Index Name | Columns | Type | Status |
|-----------|---------|------|--------|
| PRIMARY | id | PK | ✓ |
| listings_slug_unique | slug | UNIQUE | ✓ |
| listings_user_id_foreign | user_id | FK | ✓ |
| listings_status_featured_created_idx | status, is_featured, created_at | Composite | ✓ ADDED Phase 6C |
| listings_cat_status_idx | category_id, status | Composite | ✓ ADDED Phase 6C |
| listings_province_status_idx | province, status | Composite | ✓ ADDED Phase 6C |
| listings_city_status_idx | city, status | Composite | ✓ ADDED Phase 6C |

**Missing:** No index on `expires_at`. The `scopeLive()` includes `expires_at > NOW()`. Optimizer uses the status+featured+created_idx to filter `status='active'`, then post-filters on `expires_at`. At current data volume (46 rows), this is acceptable. At 1M rows with mixed expiry states, consider adding `expires_at` to the composite.

### businesses (11 rows)

| Index Name | Columns | Status |
|-----------|---------|--------|
| PRIMARY | id | ✓ |
| businesses_slug_unique | slug | ✓ |
| businesses_user_id_foreign | user_id | ✓ |
| businesses_status_featured_created_idx | status, is_featured, created_at | ✓ |
| businesses_cat_status_idx | category_id, status | ✓ |
| businesses_province_status_idx | province, status | ✓ |
| businesses_city_status_idx | city, status | ✓ |

**Complete.** Business queries are fully indexed.

### job_listings (20 rows)

| Index Name | Columns | Status |
|-----------|---------|--------|
| PRIMARY | id | ✓ |
| job_listings_slug_unique | slug | ✓ |
| job_listings_user_id_foreign | user_id | ✓ |
| jobs_status_featured_created_idx | status, is_featured, created_at | ✓ |
| jobs_cat_status_idx | category_id, status | ✓ |
| jobs_province_status_idx | province, status | ✓ |
| jobs_city_status_idx | city, status | ✓ |

**Gap:** `job_listings.expires_at` is used in `scopeLive()` (`expires_at > NOW()`). EXPLAIN shows:
```
type: ALL, key: NULL — Using where; Using filesort
```
The optimizer chose full scan over the composite index. At 20 rows this is trivial. At 100K rows, this query degrades to O(n). The `expires_at` post-filter prevents effective index range scan when combined with `status` and `is_featured`.

### events (13 rows)

| Index Name | Columns | Status |
|-----------|---------|--------|
| events_status_featured_created_idx | status, is_featured, created_at | ✓ |
| events_province_status_idx | province, status | ✓ |
| events_city_status_idx | city, status | ✓ |

**Note:** Events use `start_date` for filtering (upcoming events). No index on `start_date`. At current scale: acceptable. At scale: `start_date` index recommended.

### matrimonials (1 row)

Full composite indexes present. **Pass.**

### blog_posts (9 rows)

| Index Name | Columns | Status |
|-----------|---------|--------|
| blog_posts_status_created_idx | status, created_at | ✓ |

No province/city columns on blog_posts — correctly excluded from Phase 6C index migration.

---

## EXPLAIN Query Plans

### Listing Index (status + is_featured + ORDER)

```sql
EXPLAIN SELECT * FROM listings WHERE status='active' AND is_featured=1 ORDER BY created_at DESC LIMIT 12;
```

| Field | Value |
|-------|-------|
| type | ref |
| key | listings_status_featured_created_idx |
| key_len | 2 |
| ref | const,const |
| rows | 1 |
| Extra | Using where |

**Assessment:** PASS — Index used correctly. `type=ref` with `const,const` ref means both equality conditions hit the index. No filesort.

### Business Index (status + ORDER)

```sql
EXPLAIN SELECT * FROM businesses WHERE status='active' ORDER BY is_featured DESC, created_at DESC LIMIT 4;
```

| Field | Value |
|-------|-------|
| type | range |
| key | businesses_status_featured_created_idx |
| rows | 11 |
| Extra | Using where |

**Assessment:** PASS — Index used. `type=range` because the ORDER BY includes DESC direction on composite columns. All 11 rows scanned (small table).

### Job Listings (status + expires_at + ORDER)

```sql
EXPLAIN SELECT * FROM job_listings WHERE status='active' AND expires_at > NOW() ORDER BY is_featured DESC, created_at DESC LIMIT 15;
```

| Field | Value |
|-------|-------|
| type | **ALL** |
| key | **NULL** |
| Extra | Using where; Using filesort |

**Assessment: WARN — Full table scan with filesort.** The optimizer chose not to use `jobs_status_featured_created_idx` because the `expires_at > NOW()` range condition and the ORDER BY `is_featured DESC, created_at DESC` cannot both be satisfied by the same index pass. At 20 rows: negligible. At 100K rows: major degradation.

**Evidence:** `type=ALL, key=NULL` is definitive — no index used.

---

## Foreign Key Integrity

All foreign keys verified present and correct:

| Table | Column | References | ON DELETE | Assessment |
|-------|--------|-----------|-----------|-----------|
| listings | user_id | users.id | CASCADE | Listings deleted with user |
| listings | category_id | categories.id | CASCADE | Listings deleted with category |
| businesses | user_id | users.id | SET NULL | Businesses orphaned on user delete |
| businesses | category_id | categories.id | SET NULL | Category cleared on delete |
| job_listings | user_id | users.id | SET NULL | Jobs orphaned on user delete |
| events | user_id | users.id | SET NULL | Events orphaned on user delete |
| matrimonials | user_id | users.id | SET NULL | Profiles orphaned on user delete |
| business_posts | business_id | businesses.id | CASCADE | Posts cascade on business delete |
| business_posts | user_id | users.id | CASCADE | Posts deleted with user |
| chat_messages | conversation_id | conversations.id | CASCADE | Messages cascade |
| chat_messages | sender_id | users.id | CASCADE | Messages deleted with sender |
| conversations | buyer_id | users.id | CASCADE | Conversations deleted with buyer |
| conversations | seller_id | users.id | CASCADE | Conversations deleted with seller |
| payment_history | user_id | users.id | CASCADE | History deleted with user |
| user_favorites | user_id | users.id | CASCADE | Favorites deleted with user |

**Note:** The polymorphic tables (`user_favorites`, `reports`, `conversations`) have no DB-level FK on `favoriteable_type/id`, `reportable_type/id`, `conversable_type/id` — this is intentional (polymorphic constraint not enforceable at DB level). Application-level orphan filtering is in place.

---

## Transactions

**Evidence reviewed:** No explicit `DB::transaction()` wrappers found in payment or multi-table write paths. The Stripe success flow updates `users` table and creates `payment_history` record in two separate statements. If the second fails after the first succeeds, `users.plan` is upgraded but `payment_history` has no record.

**Severity:** Low at current volume. Not a data-loss risk (plan upgrade still applies correctly). Gap in audit trail only.

---

## Duplicate Query Analysis

**Confirmed: Plan queries not cached.**

Evidence from probe:
```
planModel() called 10×: 11 queries (1 initial User::find + 10× Plan::where)
```

Every call to `User::planModel()` fires:
```sql
SELECT * FROM plans WHERE slug = ? AND is_active = 1 LIMIT 1
```

On a listing index page with 12 cards each showing the user's plan badge, this generates 12 plan queries (or N queries for N unique users shown, since Eloquent's eager load does not cache the result of the planModel() accessor).

**Not a current production blocker** — plans table has 3 rows, query executes in ~0.6ms. However, it is an architectural pattern gap.

---

## businesses.hours Column — Final State

```
Column type: longtext (MariaDB JSON alias)
id=1: {"note":"zxczxczx"}             → is_array=YES keys=[note]
id=2: {"note":"Mon–Sun: 11am–10pm"}  → is_array=YES keys=[note]
id=43: {"monday":...,"tuesday":...}  → is_array=YES (structured)
id=13: NULL                          → NULL (correct)
id=44: NULL                          → NULL (correct)
```

**Phase 6C fix verified:** All non-null rows return as PHP array via Eloquent cast. No null returns from valid data.

---

## Database Summary

| Category | Status | Evidence |
|----------|--------|---------|
| All migrations ran | PASS | 46/46 in "Ran" state |
| Performance indexes (19) | PASS | INFORMATION_SCHEMA confirmed |
| businesses.hours fix | PASS | Eloquent returns array for all rows |
| listings.status flagged | PASS | enum includes 'flagged' |
| FK integrity | PASS | All FKs present per schema |
| Query plans (listing/business) | PASS | Index used |
| Query plan (job_listings) | WARN | Full scan on live() query |
| Transactions | WARN | No transaction wrapping Stripe success |
| Plan query caching | WARN | 1 DB query per planModel() call |
| start_date index (events) | WARN | Missing — low risk at current scale |
