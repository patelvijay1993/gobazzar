# Phase 6B Report 06 — Implementation Roadmap
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6B — Enterprise Architecture Remediation Review  
**Fix Policy:** Recommendations only. No code changes applied. All changes require developer approval and testing before implementation.

---

## Overview

This roadmap organizes all Phase 6A findings into actionable sprints based on their Phase 6B classification. Only Group A and Group B findings require pre-launch action. Group C, D, and E items are deferred.

---

## Sprint 1 — Production Blockers (Group A)
**Goal:** Clear all 6 production blockers before any production traffic  
**Total Estimated Effort:** 7.5 hours  
**Assigned to:** Developer + DevOps  

### Task 1.1 — Remove Admin Backdoor
**Finding:** DB-INT-003  
**Priority:** 1 (do this first — security breach risk)  
**Estimated Hours:** 0.1 hours (5 minutes)  
**Owner:** Developer  
**Risk:** Low  
**Regression Tests Required:** Verify admin user still has panel access after change  
**Rollback Plan:** Revert the single line change if admin access breaks  
**Deployment Steps:**
1. Confirm admin user has `is_admin=1` in `users` table
2. Edit `app/Models/User.php` — remove `|| $this->id === 1`
3. Test admin panel login with admin user
4. Test that non-admin user (ID ≠ admin ID) cannot access Filament panel
5. Deploy
**Verification:** `php artisan tinker` → `User::find(1)->canAccessPanel(new Panel)` should return `false` for a non-admin user

---

### Task 1.2 — Remove PII from Logs
**Finding:** LOG-002  
**Priority:** 2  
**Estimated Hours:** 0.2 hours (10 minutes)  
**Owner:** Developer  
**Risk:** None  
**Regression Tests Required:** None (log-only change)  
**Rollback Plan:** Revert if log entry format causes downstream issues  
**Deployment Steps:**
1. Edit `app/Http/Controllers/PricingController.php`
2. Remove `name`, `email`, `phone` from `\Log::info('Upgrade request', [...])` call
3. Keep only `user_id` and `plan` fields
4. Deploy
**Verification:** Submit an upgrade request form and verify log entry contains only user_id and plan

---

### Task 1.3 — Add Performance Indexes Migration
**Finding:** PERF-001  
**Priority:** 3  
**Estimated Hours:** 2 hours  
**Owner:** Developer  
**Risk:** None (additive migration, no data change)  
**Regression Tests Required:** Run all content index pages and verify results are correct  
**Rollback Plan:** Drop the added indexes (no data impact)  
**Deployment Steps:**
1. Create migration `add_performance_indexes_to_content_tables`
2. Add composite indexes to: listings, businesses, job_listings, events, matrimonials, blog_posts
3. Run `php artisan migrate` on test environment
4. Verify `EXPLAIN SELECT * FROM listings WHERE status='active' ORDER BY is_featured DESC, created_at DESC` shows `key: listings_status_featured_idx`
5. Deploy to production: `php artisan migrate`
**Indexes to add:**
```
listings: (status, is_featured, created_at), (category_id, status), (province, status), (city, status)
businesses: (status, is_featured, created_at), (category_id, status), (province, status), (city, status)
job_listings: (status, is_featured, created_at), (category_id, status), (province, status), (city, status)
events: (status, is_featured, created_at), (province, status), (city, status)
matrimonials: (status, is_featured, created_at), (province, status), (city, status)
blog_posts: (status, created_at)
```

---

### Task 1.4 — Fix Dirty Migration
**Finding:** DB-INT-002  
**Priority:** 4  
**Estimated Hours:** 1 hour  
**Owner:** Developer  
**Risk:** Medium (must test on clean DB before production)  
**Regression Tests Required:** Full `php artisan migrate:fresh --seed` on test environment  
**Rollback Plan:** If migration fails, fix the idempotency guard and retry  
**Deployment Steps:**
1. Run `php artisan migrate:fresh --seed` on local/test environment
2. Identify which migration fails (specifically `add_flagged_status_to_content_tables`)
3. Rewrite migration to be idempotent using `Schema::hasColumn()` guards
4. Remove the workaround comment
5. Re-run `php artisan migrate:fresh --seed` and verify clean pass
6. Deploy to production (fresh deploy will work; existing dev DB will skip already-applied migrations)
**Verification:** `php artisan migrate:fresh --seed` completes with 0 errors

---

### Task 1.5 — Fix businesses.hours Column Type Mismatch
**Finding:** DB-INT-001  
**Priority:** 5  
**Estimated Hours:** 2 hours  
**Owner:** Developer  
**Risk:** Medium (data migration on live data)  
**Regression Tests Required:** Verify all business profiles display hours correctly after migration  
**Rollback Plan:** Revert column type to TEXT and remove JSON cast (hours will be raw string but visible)  
**Deployment Steps:**
1. Write migration:
   a. Read all businesses with non-null `hours`
   b. For each: check if valid JSON; if not, wrap as `json_encode(['note' => $hours])`
   c. Update the row with the JSON-encoded value
   d. ALTER TABLE `businesses` MODIFY COLUMN `hours` JSON NULL
2. Update view templates to render `hours.note` as the fallback display value
3. Test on all 11 existing businesses — verify hours display
4. Run on test DB first; verify no data loss
5. Deploy: run migration, verify
**Verification:** All business profiles show hours content (even if as "note" format)

---

### Task 1.6 — Configure Queue Worker (Supervisor)
**Finding:** QUEUE-001 / ORPHAN-005  
**Priority:** 6  
**Estimated Hours:** 2 hours  
**Owner:** DevOps  
**Risk:** None  
**Regression Tests Required:** Send a chat message and verify real-time delivery  
**Rollback Plan:** Stop the supervisor process  
**Deployment Steps:**
1. Run `php artisan queue:clear` to discard 26 stale development jobs
2. Install Supervisor on production server
3. Create Supervisor config at `/etc/supervisor/conf.d/gobazzar-worker.conf`
4. Run `supervisorctl reread && supervisorctl update`
5. Start worker: `supervisorctl start gobazzar-worker:*`
6. Monitor: `supervisorctl status gobazzar-worker:*`
**Verification:** 
- Send a chat message between two user accounts
- Verify the message appears in real-time without page refresh
- Check `SELECT COUNT(*) FROM jobs` — should stay near 0 after worker starts processing

---

## Sprint 2 — Should Fix Before Production (Group B)
**Goal:** Complete before or very shortly after soft launch  
**Total Estimated Effort:** 10 hours  
**Assigned to:** Developer  

### Task 2.1 — Investigate User 5 Stripe Data Inconsistency
**Finding:** ORPHAN-004  
**Estimated Hours:** 1 hour  
**Owner:** Developer / Admin  
**Action:** Query: `SELECT id, email, plan, subscription_status, stripe_subscription_id FROM users WHERE id=5`. Determine if this user was manually granted power_seller, should have a Stripe subscription, or is a test account. Document the decision. If a legitimate user, create the Stripe subscription or add a `complimentary` status field.

### Task 2.2 — Clean Orphan Test Content
**Finding:** ORPHAN-001, ORPHAN-002  
**Estimated Hours:** 0.5 hours  
**Owner:** Admin  
**Action:** Delete via admin panel (or SQL): business ID=1 (`fczxzcx`), job listings 15, 16, 17 (`Test Job 1/2/3`). These are test records publicly visible on the directory and jobs index.

### Task 2.3 — Fix S3 Silent Upload Failures
**Finding:** STOR-003  
**Estimated Hours:** 2 hours  
**Owner:** Developer  
**Action:** Wrap S3 upload calls in `PostController::storeClassified` and `storeMatrimonial` in try/catch. Check return value of `$file->store(...)` — if `false`, delete already-uploaded files in the batch and return an error to the user. Consider setting `throw: env('APP_ENV') === 'production' ? true : false` in S3 config.

### Task 2.4 — Fix BlogPost Image Disk (Local vs S3)
**Finding:** STOR-004  
**Estimated Hours:** 1 hour + 30 min data migration  
**Owner:** Developer  
**Action:** Update `BlogPost::getImageUrlAttribute()` to use `Storage::disk('s3')->url(...)` instead of `asset('storage/...')`. Update the blog image upload path in the controller to use the `s3` disk. Migrate any existing blog images from local disk to S3.

### Task 2.5 — Memoize `activePlan()` Per Request
**Finding:** CACHE-003 / PERF-003  
**Estimated Hours:** 2 hours  
**Owner:** Developer  
**Action:** Add `private ?string $cachedPlan = null` property to `User` model. Cache the result of `computeActivePlan()` on first call. Move `maybeResetCredits()` from the read path to a scheduled Artisan command (monthly reset). This eliminates DB writes on every authenticated page load.

### Task 2.6 — Optimize HomeController Query Count
**Finding:** PERF-002  
**Estimated Hours:** 4 hours  
**Owner:** Developer  
**Action:** 
1. Eliminate duplicate queries (events queried twice, categories queried twice — deduplicate with variables)
2. Refactor `dirBiz()` from 8 individual calls (24 queries) to a single bulk query grouped by category
3. Combine 4 COUNT stats queries into one
4. Consider caching the full homepage data for 60 seconds

---

## Sprint 3 — Can Fix After Production (Group C)
**Goal:** Complete within 2–4 weeks of production launch  
**Total Estimated Effort:** 15 hours  

| Task | Finding | Effort | Action |
|------|---------|--------|--------|
| Add SoftDeletes to content models | DB-INT-004 | 4 hours | Add `deleted_at` column + SoftDeletes trait to Listing, Business, Job, Event, Matrimonial, BlogPost, BusinessPost |
| Fix gallery image purge in purge command | STOR-001 / SCHED-003 | 1 hour | Iterate `$r->images` array and delete each S3 path in `PurgeExpiredPosts` |
| Fix matrimonial gallery cleanup on update | STOR-002 | 30 min | Delete old `photos` from S3 before saving new ones in `updateMatrimonial()` |
| Cache Plan::active() | CACHE-002 | 30 min | Add `Cache::remember('plans_active', 600, ...)` to `Plan::active()` |
| Cache Location queries | CACHE-004 | 1 hour | Add `Cache::remember()` to `activeProvinces()` and `activeCities()` |
| Cache Category queries | CACHE-005 | 1 hour | Add `Cache::remember()` to HomeController category queries |
| Add pagination to account page | PERF-005 | 2 hours | Change `->get()` to `->paginate(20)` in `UserController::account()` |
| Purge expired business posts | SCHED-002 | 30 min | Add BusinessPost to `PurgeExpiredPosts` command |
| Build admin moderation review UI | ORPHAN-006 | 3 hours | Filament resource for reviewing pending `flagged_posts` |
| Add Sentry error monitoring | — | 1 hour | `composer require sentry/sentry-laravel` + configure |
| Configure uptime monitoring | — | 30 min | Register domain at UptimeRobot (free) |

---

## Sprint 4 — Architecture Improvements (Group D + Long-Term)
**Goal:** Complete within 1 month of launch, or as traffic grows  

| Task | Finding | Effort | When |
|------|---------|--------|------|
| Switch cache to Redis | SCALE-T3 | 2 hours | Pre-Tier 2 (1K users) |
| Switch sessions to Redis | SCALE-T3 | 1 hour | Pre-Tier 2 |
| Add FULLTEXT search indexes | PERF-004 | 4 hours | Pre-Tier 2 |
| Add CloudFront CDN for S3 images | REC-025 | 4 hours | Pre-Tier 3 |
| Migrate blog images to S3 | STOR-004 | 1 hour | Sprint 3 if not done earlier |
| Implement read replica | SCALE-T3 | 8 hours | Pre-Tier 3 |

---

## Implementation Summary

| Sprint | Group | Effort | Timeline |
|--------|-------|--------|----------|
| Sprint 1 — Blockers | A | 7.5 hours | Before launch |
| Sprint 2 — Pre-Launch | B | 10 hours | Before or at launch |
| Sprint 3 — Post-Launch | C | 15 hours | 2–4 weeks post-launch |
| Sprint 4 — Architecture | D | 20+ hours | 1+ month post-launch |
| Developer Items | E | 0.7 hours | Any sprint |

**Total pre-launch work: ~17.5 hours (Sprint 1 + Sprint 2)**  
**Total post-launch work: ~35 hours (Sprint 3 + Sprint 4)**
