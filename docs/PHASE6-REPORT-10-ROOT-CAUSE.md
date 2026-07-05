# Phase 6 Report 10 — Root Cause Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Root Cause Groups

All Phase 6 findings trace back to 6 systemic root causes. Addressing these causes eliminates the entire class of issues they produce.

---

## Root Cause Group 1 — Schema Created Without Performance Planning

**Manifestation:**
- PERF-001: 24 missing indexes on status, province, city, is_featured columns
- PERF-004: No full-text index for search
- PERF-007: Account page no pagination (User::account loads all records)

**Root Cause:** The database schema was designed for correctness and feature completeness but not for query performance. Indexes were added only where FK constraints require them (Laravel automatically indexes FK columns). The most common WHERE conditions (`status`, `province`, `city`) were never indexed.

**Why It Happened:** During rapid MVP development, index planning is often deferred ("optimize later when needed"). The small dev dataset (46 listings, 11 businesses) never triggered visible slowness, so the gap went undetected.

**Fix Pattern:** Add a performance migration that adds composite indexes to all content tables. This is a non-breaking, additive change.

**Files Affected:** All content table migrations, requiring one new `add_performance_indexes` migration.

---

## Root Cause Group 2 — No Application-Level Caching Strategy

**Manifestation:**
- CACHE-002: Plan::active() not cached
- CACHE-003: User::activePlan() not cached
- CACHE-004: Location queries not cached  
- CACHE-005: Category queries not cached
- PERF-002: HomeController fires 50+ queries per page load
- PERF-003: maybeResetCredits() DB write on every authenticated request

**Root Cause:** Caching was not included in the application design. The only cached resource is `Setting::get()` (a good pattern), but it was not applied consistently to other frequently-read, rarely-changing data (plans, locations, categories, user plans).

**Why It Happened:** Single-developer development where performance is validated against a small dataset. With 3 plans, 35 locations, and 32 categories, queries return instantly and caching seems unnecessary. The problem only manifests under load.

**Fix Pattern:** Apply `Cache::remember(key, ttl, fn)` consistently to all read-only, slowly-changing data. Use short TTLs (60–300 seconds) for plan-related data, longer TTLs (3600 seconds) for locations and categories. Implement cache invalidation in Filament admin observers.

---

## Root Cause Group 3 — Incomplete Schema Design for Content Lifecycle

**Manifestation:**
- DB-INT-004: No soft deletes on any content table
- DB-INT-001: businesses.hours type mismatch (text vs array)
- STOR-001: Gallery images not purged by purge command
- STOR-002: Matrimonial gallery not cleaned on update
- SCHED-002: Business posts not purged by expiry command
- FK-GAP-003: flagged_posts has no post_id reference

**Root Cause:** Content lifecycle (create, update, expire, delete, recover) was not fully designed upfront. Soft deletes, image lifecycle, and expiry cleanup were added incrementally as features were built, but the full lifecycle path was not audited end-to-end.

**Specific Sub-Causes:**
1. The `hours` column was designed as `text` before the structured hours array format was decided
2. The `PurgeExpiredPosts` command was written when only `image` existed; `images` gallery was added later without updating the purge command
3. The `flagged_posts` table was created to capture moderation events but without a forward reference to enable admin navigation

**Fix Pattern:** Complete the lifecycle:
1. Add `SoftDeletes` to all content models
2. Fix `businesses.hours` type + migrate data
3. Update purge command to include gallery images
4. Add `post_id` to `flagged_posts`

---

## Root Cause Group 4 — nullOnDelete FK Strategy Creates Management Void

**Manifestation:**
- ORPHAN-001: 1 business with user_id=NULL (publicly visible, cannot be managed)
- ORPHAN-002: 3 job listings with user_id=NULL (publicly visible, cannot be managed)
- FK-GAP-001: sessions.user_id has no FK constraint

**Root Cause:** The team chose `nullOnDelete` for businesses, jobs, events, and matrimonials (instead of CASCADE) to preserve content when a user account is deleted. The intention was: "if a business owner deletes their account, the business listing should remain visible to the community."

**Why It's a Problem:** The design preserves the content but removes the ability for anyone to manage it. A null-owner business cannot be edited or deleted through the application. The only way to manage it is via the admin panel — which requires the admin to proactively find and clean up orphans. In practice, this never happens and orphan content accumulates.

**Fix Pattern:** Choose one of:
a. **CASCADE**: Delete all content when user is deleted (simplest, loses data)
b. **Transfer**: Transfer content to an "anonymous" or "deleted_user" placeholder user before nullifying
c. **Admin cleanup**: Keep nullOnDelete but add a scheduled command to auto-deactivate (`status=inactive`) any null-owner content, preventing orphan content from being publicly visible
d. **Soft-delete users**: With user soft-delete, user deletion doesn't cascade to content but the user record is preserved

**Option C** is the lowest-risk change: add a query `->where('user_id', '!=', null)` to all content `scopeLive()` methods, and auto-inactive null-owner content via scheduler.

---

## Root Cause Group 5 — Operational Infrastructure Not Configured

**Manifestation:**
- QUEUE-001: 26 unprocessed jobs (queue worker not running)
- QUEUE-002: Scheduler cron not confirmed as running
- LOG-003: No database backup strategy
- LOG-004: S3 versioning status unknown
- LOG-005: No recovery runbook
- LOG-006: No APM or monitoring

**Root Cause:** The application code is functionally complete, but the operational infrastructure (process management, backups, monitoring) has not been configured. This is the typical gap between "it works on my machine" and "it runs in production."

**Why It Happened:** The application is in active development on XAMPP (local). Operational concerns (Supervisor, cron, backup, monitoring) are deployment-time tasks, not development tasks. They have simply not been addressed yet.

**Fix Pattern:** Pre-launch infrastructure checklist:
1. Supervisor config for queue worker
2. Cron job for `schedule:run`
3. Database backup automation (RDS or mysqldump cron)
4. S3 versioning and cross-region replication
5. Sentry for error tracking
6. Uptime monitoring

**This is the highest-priority class of fix** — it doesn't require any code changes, only server configuration.

---

## Root Cause Group 6 — Development Shortcuts Left in Production Code

**Manifestation:**
- DB-INT-003: `canAccessPanel()` ID=1 backdoor
- DB-INT-002: Dirty migration from partial run
- LOG-002: PII (email, phone) logged in upgrade requests
- STOR-003: S3 `throw:false` silences upload failures
- ORPHAN-004: User has subscription_status=active with no Stripe subscription
- DB-INT-009: Stale migration comment

**Root Cause:** Individual shortcuts added during development were not tracked and cleaned up before production readiness. Each shortcut was rational at the time (backdoor for quick admin access, throw:false to avoid crashes during S3 testing) but represents technical debt that becomes a security or reliability risk in production.

**Fix Pattern:** Pre-launch security/cleanliness audit:
1. Remove `$this->id === 1` backdoor from `canAccessPanel()`
2. Set `S3::throw = true` and add try/catch around upload code
3. Remove PII from log calls
4. Fix the dirty migration (test `migrate:fresh` on clean DB)
5. Fix user 5 Stripe data inconsistency
6. Update stale migration comment

These are all small, targeted fixes with minimal risk.

---

## Root Cause Impact Matrix

| Root Cause | Issues Caused | Severity | Fix Effort |
|------------|--------------|---------|-----------|
| 1. No performance planning | 24 missing indexes, no full-text | Critical | 2h |
| 2. No caching strategy | 8 cache gaps, 50+ queries/page | High | 6h |
| 3. Incomplete content lifecycle | 6 schema/cleanup issues | High | 6h |
| 4. nullOnDelete management void | 4 orphan records publicly visible | Medium | 2h |
| 5. Operational infra not configured | 6 deployment blockers | Critical | 8h |
| 6. Development shortcuts in code | 6 security/reliability issues | High | 2h |

**Total estimated remediation effort: ~26 hours**
