# Phase 6 Report 03 — Orphan Data Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Executive Summary

Orphan data was found in 3 areas. All orphans result from the `nullOnDelete` FK strategy applied to businesses and job_listings. No orphan data was found in listings (CASCADE), events, matrimonials, or payment_history.

| Category | Orphan Count | Risk |
|----------|-------------|------|
| Businesses with null user_id | 1 | Medium |
| Job listings with null user_id | 3 | Medium |
| Stripe data inconsistency | 1 | High |
| Queue job backlog (unprocessed) | 26 | High |
| Flagged posts (disconnected) | 28 | Medium |
| Mixed storage URLs in listings | 5 external URLs in s3-path column | Low |

---

## 1. Content Table Orphans

### ORPHAN-001 — 1 Business With No Owner

```sql
SELECT id, name, user_id FROM businesses 
WHERE user_id IS NULL OR user_id NOT IN (SELECT id FROM users);

Result:
  id=1  name=fczxzcx  user_id=NULL
```

**Root Cause:** A test user was deleted. The FK `businesses.user_id → users.id` is `nullOnDelete`, so the business record persisted with `user_id=NULL`.

**Impact:**
- Business ID=1 is currently `status=active` — it appears on the public directory index
- No user can edit or delete it through the application (`PostController::findOwned()` checks `user_id == Auth::id()`)
- Only admin panel or direct SQL can remove it
- The business has `name=fczxzcx` — clearly test data but publicly visible

**Recommended Fix:**
1. Immediate: Delete business ID=1 via admin panel or SQL
2. Long-term: Change business `user_id` FK to `CASCADE` or implement a scheduled cleanup job for null-owner content

**Effort:** 30 minutes to clean, 2 hours for strategy change

---

### ORPHAN-002 — 3 Job Listings With No Owner

```sql
SELECT id, title, user_id FROM job_listings 
WHERE user_id IS NULL OR user_id NOT IN (SELECT id FROM users);

Result:
  id=15  title=Test Job 1  user_id=NULL
  id=16  title=Test Job 2  user_id=NULL
  id=17  title=Test Job 3  user_id=NULL
```

**Root Cause:** Same pattern — test user deleted, job listings persisted via `nullOnDelete`.

**Impact:**
- All 3 are `status=active` and visible in the public jobs index
- Cannot be managed by any authenticated user
- Job listings with `user_id=NULL` appear in the public index and display without an owner profile

**Recommended Fix:** Same as ORPHAN-001 — immediate cleanup + strategy review.

**Effort:** 15 minutes to clean

---

### ORPHAN-003 — No Listing Orphans (Cascade Working)

```sql
Listings with orphan user_id: 0
```

**Status:** PASS — `listings.user_id` uses `CASCADE DELETE`, so all listings are removed when the user is deleted. This is the correct behavior for user-owned content.

---

## 2. Stripe Data Orphans

### ORPHAN-004 — User With `subscription_status=active` But No Stripe Subscription ID (High)

```sql
SELECT id, plan, subscription_status, stripe_subscription_id 
FROM users WHERE subscription_status='active' AND stripe_subscription_id IS NULL;

Result:
  user_id=5  plan=power_seller  subscription_status=active  stripe_subscription_id=NULL
```

**Root Cause:** User 5 has `plan=power_seller` and `subscription_status=active` but no `stripe_subscription_id` or `stripe_customer_id`. This is a data inconsistency — the user appears to have been manually promoted to `power_seller` plan without going through the Stripe checkout flow.

**Impact:**
- User 5 has access to Power Seller features (10 images, 9999 listings, unlimited posts) without a valid Stripe subscription
- No recurring billing is active for this user
- Revenue loss — user gets paid plan benefits for free
- If Stripe subscription tracking is used for renewals, this user will never be auto-downgraded

**Recommended Fix:**
1. Verify whether user 5 should be on Power Seller plan (was it a test/admin grant?)
2. If legitimate: create a Stripe customer/subscription or mark as `complimentary`
3. Add application validation: when setting `subscription_status=active`, require `stripe_subscription_id` to be non-null

**Effort:** 1 hour | **Breaking Change Risk:** Low

---

## 3. Queue Job Orphans (Critical)

### ORPHAN-005 — 26 Unprocessed `MessageSent` Events in Queue (High)

```sql
SELECT COUNT(*) FROM jobs;
Result: 26

Job class: App\Events\MessageSent (all 26)
Queue driver: database
Failed jobs: 0
```

**Root Cause:** The application uses a `database` queue driver. There are 26 `App\Events\MessageSent` jobs sitting in the `jobs` table that have never been processed. This means the `queue:work` worker is not running.

**Impact:**
- Real-time chat message events are not being broadcast
- Any WebSocket/Pusher integration for live chat is not functioning
- Jobs accumulate without being processed
- If a queue worker is started, 26 old chat events will fire simultaneously — potentially confusing to recipients
- No failed jobs (0) suggests the worker has simply never run, not that jobs are failing

**Recommended Fix:**
1. Investigate whether `queue:work` is configured to run as a background process on the server
2. For production: use Supervisor or a similar process manager to keep `queue:work` running
3. Clear the stale queue jobs before launching: `php artisan queue:clear`
4. Consider using `QUEUE_CONNECTION=sync` in development to avoid this accumulation

**Effort:** 2 hours to configure properly | **Breaking Change Risk:** Low

---

## 4. Flagged Posts Disconnection

### ORPHAN-006 — 28 Flagged Posts With No Post ID Reference

```sql
SELECT status, post_type, COUNT(*) FROM flagged_posts GROUP BY status, post_type;

Result:
  pending/business: 11
  pending/classified: 15
  pending/event: 2
  pending/job: 2
```

**Root Cause:** The `flagged_posts` table stores moderation audit records for rejected post attempts. It has `post_type` (string) but **no `post_id` column**. Once a post is flagged and blocked, there is no way to join the flagged_post record back to any original listing, business, or job record.

**Impact:**
- Admins can see flagged content (`title`, `description`, `raw_data`) but cannot navigate to the original post
- If a business was blocked and then the user tries again under a different title, there's no cross-reference
- 28 pending flagged posts have never been reviewed (all `status=pending`)
- The moderation queue is backed up

**Recommended Fix:**
1. Add `post_id` nullable integer column to `flagged_posts`
2. Convert `post_type` to match Eloquent morph convention (`App\Models\Business`, etc.)
3. Alternatively: keep as audit log but add admin interface to review pending flagged posts

**Effort:** 2 hours | **Breaking Change Risk:** Low

---

## 5. Mixed Storage URL Orphans

### ORPHAN-007 — External URLs Mixed Into S3 Path Columns

```sql
listings.image column:
  s3_paths: 6
  external_http: 5  (Unsplash URLs)
  null: 35

businesses.image column:
  s3_paths: 1
  external_http: 7  (external URLs)
```

**Root Cause:** Seed data (or manual data entry) used external URLs directly in the `image` column. The `image_url` accessor handles this correctly (`str_starts_with($this->image, 'http')` returns the URL as-is). However, in production, external URLs are not under the application's control — they can change, go offline, or be DMCA-removed.

**Impact:**
- Low impact now (dev data)
- In production: broken images if external URLs become unavailable
- S3 lifecycle rules cannot manage external URLs
- Image deletion (`PostController::destroy`) skips `http` URLs — correct, but means cleaning up later requires a data migration

**Recommended Fix:**
1. For production data: run a migration script to download external images → upload to S3 → update the column
2. Add validation to reject `http://` or `https://` values in the `image` field during form submission

**Effort:** 2 hours | **Breaking Change Risk:** Low

---

## 6. Orphan Summary Table

| Finding | Table | Count | Risk | Status |
|---------|-------|-------|------|--------|
| ORPHAN-001 | businesses | 1 | Medium | Action required |
| ORPHAN-002 | job_listings | 3 | Medium | Action required |
| ORPHAN-003 | listings | 0 | — | PASS |
| ORPHAN-004 | users (Stripe) | 1 | High | Action required |
| ORPHAN-005 | jobs (queue) | 26 | High | Action required |
| ORPHAN-006 | flagged_posts | 28 | Medium | Structural fix needed |
| ORPHAN-007 | listings/businesses | 12 | Low | Dev data only |

---

## 7. Cleanup SQL (For Approval Before Execution)

```sql
-- Remove orphan business (test data)
DELETE FROM businesses WHERE id = 1;

-- Remove orphan job listings (test data)  
DELETE FROM job_listings WHERE id IN (15, 16, 17);

-- Clear queue backlog before production launch
-- (run via artisan, not SQL)
-- php artisan queue:clear
```

**DO NOT EXECUTE — awaiting approval.**
