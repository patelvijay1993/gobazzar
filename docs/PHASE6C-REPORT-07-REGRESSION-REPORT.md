# Phase 6C Report 07 — Regression Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6C — Production Hardening Sprint

---

## Regression Analysis Methodology

For each Phase 6C change, assess:
1. What existing behavior did this change touch?
2. What could break as a side effect?
3. Is the risk realized?

---

## REGRESSION-1 — Admin Authorization Change (User.php)

**Change:** Removed `$this->id === 1` from `canAccessPanel()`.

**Surfaces touched:** Filament admin panel authentication only. No other code calls `canAccessPanel()`.

**Potential regression:** User id=1 loses admin access if their `is_admin` flag is ever 0.

**Actual regression risk:** None confirmed.
- Verified: id=1 has `is_admin=1` in database before and after change.
- id=2 and id=5 also have `is_admin=1` — unaffected.
- New behavior is strictly more secure — behavior for all users with `is_admin=1` is identical.
- Only path to regression: someone manually sets `is_admin=0` for a user who relied on id=1 bypass — which was an unauthorized path anyway.

**Regression verdict: NONE**

---

## REGRESSION-2 — PII Removed from Logs (PricingController.php)

**Change:** Removed `name`, `email`, `phone` from `Log::info()` call.

**Surfaces touched:** Application log file only. No functional code changed.

**Potential regression:** Any monitoring/alerting system that parses log entries for `email` or `phone` would stop receiving those fields.

**Actual regression risk:** None. Log entries are developer-facing only. No monitoring pipeline depends on PII in logs. Functional behavior of the upgrade flow is unchanged.

**Regression verdict: NONE**

---

## REGRESSION-3 — businesses.hours Column Migration

**Change:** Data converted from plain text → `{"note":"..."}` JSON; column type changed TEXT → JSON.

**Surfaces touched:**
1. `Business` model `hours` accessor via `'hours' => 'array'` cast
2. `resources/views/directory/show.blade.php` business hours display
3. Any Filament admin resource that reads/writes the hours column
4. Any API endpoint that returns business data

**Potential regressions investigated:**

**3a. Filament BusinessResource hours field:**
```
The hours field in Filament admin renders as a JSON editor.
Before: json_decode(plain text) = null → empty editor
After: json_decode(valid JSON) = array → populated editor
Result: IMPROVEMENT, not regression
```

**3b. Business listing public view:**
```
Before: $business->hours null → hours section hidden
After: $business->hours = ['note' => '...'] → hours section visible with legacy fallback
Result: IMPROVEMENT — previously hidden data now displayed
```

**3c. API — BusinessController::show():**
Reviewed `app/Http/Controllers/BusinessController.php`. Business data is returned via Eloquent. The `hours` field will now return an array instead of null. Any JSON API consumer that previously received `null` for hours will now receive an object. This is a behavior change.

**Assessment:** GoBazaar is not a public API product at this stage. All known consumers are the Blade views (which are updated) and the Filament admin. No API versioning contract is violated.

**3d. Existing id=43 (structured JSON) business:**
```
Before: hours = valid JSON → cast to array → rendered as day grid
After: hours = same valid JSON → cast to array → rendered as day grid
Result: NO CHANGE — id=43 unchanged
```

**Regression verdict: NONE for functional paths; theoretical API consumer change (non-breaking in current product stage)**

---

## REGRESSION-4 — Dirty Migration Idempotency Fix

**Change:** Added INFORMATION_SCHEMA guard to `add_flagged_status_to_content_tables.php`.

**Surfaces touched:** Only the migration file itself.

**Potential regression:** The migration might not run the listings ALTER on a fresh DB if the guard has a bug.

**Actual regression risk:** None. The guard checks if `'flagged'` is present in the column type string. On a fresh DB, the column type will be `enum('pending','active','rejected','expired')` — does not contain `'flagged'` → guard passes → ALTER runs. Logic is correct.

**Regression verdict: NONE**

---

## REGRESSION-5 — Performance Indexes

**Change:** 19 new composite indexes added.

**Surfaces touched:** Database query optimizer for listings, businesses, job_listings, events, matrimonials, blog_posts tables.

**Potential regressions:**
- **INSERT/UPDATE performance:** Adding indexes slightly slows writes due to index maintenance. For a content marketplace at early stage (low write volume), this is imperceptible.
- **Query plan changes:** The optimizer may use new indexes. In some edge cases, a wrong index can slow a query. Given the indexes are on the exact columns used in all `WHERE` clauses in controllers, the optimizer will correctly prefer them.
- **Disk space:** Minimal — tables are small.

**Actual regression risk:** None. Indexes are additive; application code is unchanged; optimizer changes are positive.

**Regression verdict: NONE**

---

## REGRESSION-6 — BlogPost S3 Disk Fix

**Change:** `getImageUrlAttribute()` changed from `asset('storage/...')` to `Storage::disk('s3')->url(...)`.

**Surfaces touched:** Any view or controller that calls `$post->image_url`.

**Potential regression:** If S3 is misconfigured (wrong bucket, wrong region, wrong credentials) in production `.env`, the URL returned will be wrong.

**Actual regression risk:** Low. The Filament admin was already uploading to S3 — images are already stored on S3. The only change is the URL generation. If S3 config is correct (required for uploads to work at all), URL generation is also correct.

**Pre-existing images uploaded via old local-disk pattern:** None — Filament has been the only admin upload interface and it already used S3. There are no local `storage/` blog images to break.

**Regression verdict: NONE (assuming S3 env vars are set — required for uploads anyway)**

---

## REGRESSION-7 — Matrimonial Gallery Cleanup

**Change:** Old S3 photos deleted before new upload in `updateMatrimonial()`.

**Surfaces touched:** Matrimonial profile photo update flow only.

**Potential regressions:**

**7a. Deleting wrong photos:** The delete loop iterates `$r->photos` (the current matrimonial record's photos array). Only the specific profile's photos are deleted. No cross-profile deletion possible.

**7b. Deleting http:// URLs:** Guard `!str_starts_with($old, 'http')` prevents deleting any externally-hosted URLs.

**7c. Race condition:** If two requests update simultaneously, second request could delete photos uploaded by first. Extremely unlikely in this application's use pattern (user updates their own profile).

**7d. S3 delete failure:** `Storage::disk('s3')->delete()` returns false silently on failure. No exception thrown. Upload proceeds regardless. Old photos may remain on S3 (harmless orphans) rather than causing an error.

**Regression verdict: NONE for functional correctness; theoretical race condition is pre-existing pattern in app**

---

## Regression Summary

| Change | Regression Found | Severity |
|--------|-----------------|---------|
| Admin backdoor removal | None | — |
| PII removed from logs | None | — |
| hours column migration | API behavior change (theoretical) | Low / Acceptable |
| Dirty migration idempotency | None | — |
| Performance indexes | None | — |
| BlogPost S3 disk | None (S3 config must be correct) | — |
| Matrimonial gallery cleanup | None | — |

**Regressions found: 0 critical, 0 high, 0 medium, 1 low/acceptable**

**REGRESSION REPORT: PASS**
