# Phase 7 Report 07 — Regression Bug Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 7 — Enterprise Regression & Performance Benchmark  
**Role:** Principal QA Architect, Senior Laravel Architect  
**Policy:** Evidence, Root Cause, Impact, Recommendation only. DO NOT APPLY FIXES.

---

## Regression Check: All Phase 6A–6C Fixes

| Finding ID | Fix | Regression Found? |
|-----------|-----|-----------------|
| DB-INT-003 | Admin backdoor removed | NO |
| LOG-002 | PII removed from logs | NO |
| DB-INT-001 | hours column type + migration | NO |
| DB-INT-001 | hours display view | NO |
| DB-INT-002 | Dirty migration idempotency | NO |
| PERF-001 | 19 performance indexes | NO |
| STOR-004 | BlogPost S3 disk | NO |
| STOR-002 | Matrimonial gallery cleanup | NO |

**No regressions in any Phase 6C fix. All 8 fixes remain intact.**

---

## New Bugs Found in Phase 7

---

### BUG-P7-001 — Admin Listing Photo Preview Uses Local asset() for S3 Keys
**Severity:** Medium  
**Type:** New Bug (not a regression of any Phase 6C fix)  
**Module:** Admin Panel — ListingResource  

**Evidence:** `app/Filament/Resources/ListingResource.php` line 77:
```php
$url = str_starts_with($img, 'http') ? $img : asset('storage/'.$img);
```

**Root Cause:** When an image is stored on S3 (e.g., `listings/abc123.jpg`), `str_starts_with($img, 'http')` returns false (the path is a relative S3 key, not a full URL). The code then generates `asset('storage/listings/abc123.jpg')` — a local public disk URL — which resolves to a non-existent path on the server.

**Impact:** Admin listing edit page shows broken `<img>` tags for all S3-stored listing photos in the current-photos preview widget. Admin cannot visually confirm which photos to delete.

**Not a Phase 6C regression:** Phase 6C fixed BlogPost model's accessor (STOR-004). This admin widget was always broken for S3 photos but was not in scope.

**Recommendation:**
```php
// Replace line 77 in ListingResource.php:
$url = str_starts_with($img, 'http') ? $img : \Storage::disk('s3')->url($img);
```

---

### BUG-P7-002 — Chat Inbox Performance Degradation at Scale
**Severity:** High (at scale — Medium at current)  
**Type:** Architecture finding (not a regression)  
**Module:** Chat  

**Evidence:**
```
Chat inbox user-3: 690.89ms (10 conversations, 26 messages)
```

**Root Cause:** `ChatController::inbox()` uses a correlated subquery as the ORDER BY clause:
```php
->orderByDesc(function ($query) {
    $query->select('created_at')
        ->from('chat_messages')
        ->whereColumn('conversation_id', 'conversations.id')
        ->orderByDesc('created_at')
        ->limit(1);
})
```
This generates SQL:
```sql
ORDER BY (SELECT created_at FROM chat_messages WHERE conversation_id = conversations.id ORDER BY created_at DESC LIMIT 1) DESC
```
For N conversations, this fires N subqueries. No index on `(conversation_id, created_at)` in `chat_messages`.

**Impact at scale:**
- 10 conversations: 690ms
- 100 conversations: ~6,900ms (unusable)
- 1,000 conversations: ~69,000ms (server timeout)

**Recommendation:** Add index `(conversation_id, created_at)` on `chat_messages`. Replace correlated subquery with a JOIN to `latestMessage` or use a `latest_message_at` denormalized column on `conversations`.

---

### BUG-P7-003 — User::planModel() Not Memoized — Repeated DB Queries
**Severity:** Low (current) / Medium (at scale)  
**Type:** Architecture finding  
**Module:** User Model, Listing/Business views  

**Evidence:**
```
planModel() called 10× on same user: 6.17ms, 11 queries
10 listings + access user planName/hasVerifiedBadge (no eager): 31 queries, 17.97ms
```

**Root Cause:** `User::planModel()` executes `Plan::findBySlug($this->activePlan())` on every call. No in-memory caching. On a page with 12 listing cards where each card renders the user's plan badge, this fires 12 (or 12 × 2) plan queries.

**Impact:** Adds 6–24 queries per listing index page. At current scale: ~6ms extra. At high traffic: thousands of redundant queries per minute.

**Recommendation:**
```php
private ?Plan $planModelCache = null;

public function planModel(): ?Plan
{
    return $this->planModelCache ??= Plan::findBySlug($this->activePlan());
}
```

---

### BUG-P7-004 — User::maybeResetCredits() Fires DB Write on Every featuredCreditsRemaining() Call
**Severity:** Low  
**Type:** Architecture finding / Unexpected side effect  
**Module:** User Model  

**Evidence:**
```php
public function maybeResetCredits(): void
{
    $resetAt = $this->featured_credits_reset_at;
    if (!$resetAt || $resetAt->isPast()) {
        $this->update([
            'featured_credits_used'     => 0,
            'featured_credits_reset_at' => now()->addMonth(),
        ]);
    }
}
```
This is called by `featuredCreditsRemaining()`, which is called on listing display if `canFeatureListing()` is shown.

**Impact:** If `featured_credits_reset_at` is NULL (all free-plan users, and users whose reset is due), every page load that checks `canFeatureListing()` fires a DB UPDATE on the users table. For a free-plan user: always NULL → always writes. This is an implicit write on every read.

**Recommendation:** Call `maybeResetCredits()` in a scheduled command or middleware rather than on every model read.

---

### BUG-P7-005 — APP_DEBUG=true on Local .env (Production Risk)
**Severity:** High (if deployed as-is)  
**Type:** Configuration  
**Module:** DevOps / .env  

**Evidence:**
```
APP_DEBUG: true
APP_ENV: local
```

**Impact:** If the production server copies the local `.env` without changing these values, full exception stack traces are exposed to end users. This is a known security information disclosure vulnerability.

**Recommendation:** Production `.env` must have `APP_DEBUG=false` and `APP_ENV=production`. Verify before every deploy.

---

### BUG-P7-006 — Storage Symlink Not Created
**Severity:** Medium  
**Type:** Deployment gap  
**Module:** Public storage  

**Evidence:**
```
storage link: NOT FOUND
```

**Impact:**
- Files stored on local disk (`storage/app/public/`) are inaccessible at `public/storage/`
- Admin listing photo previews using `asset('storage/...')` fail for all S3 files
- On production, if any file falls back to local disk, it would be inaccessible

**Recommendation:** `php artisan storage:link` as part of deployment runbook.

---

### BUG-P7-007 — 26 Stale Queue Jobs — No Worker
**Severity:** Medium  
**Type:** Operational  
**Module:** Chat, Queue  

**Evidence:**
```
Queue jobs: 26 (all App\Events\MessageSent, June–July 2026)
Queue worker: NOT RUNNING
```

**Impact:** Chat real-time broadcast (MessageSent event) is non-functional. All in-session messages are stored correctly in DB but no broadcast event fires. Users must manually refresh to see new chat messages. Future queued operations will also silently fail.

**Recommendation:**
1. `php artisan queue:clear` to remove stale jobs
2. Start Supervisor-managed queue worker on production

---

### BUG-P7-008 — job_listings scopeLive() Full Table Scan (No Index Used)
**Severity:** Low (current) / Medium (at scale)  
**Type:** Performance  
**Module:** Job Listings  

**Evidence:**
```
EXPLAIN job_listings WHERE status='active' AND expires_at > NOW():
type=ALL, key=NULL — Using where; Using filesort
```

**Root Cause:** The composite index `jobs_status_featured_created_idx (status, is_featured, created_at)` does not cover `expires_at`. When MySQL's optimizer evaluates `status='active' AND expires_at > NOW() ORDER BY is_featured DESC, created_at DESC`, it cannot use the composite index for both the WHERE and ORDER BY simultaneously because of the range condition on `expires_at`.

**Impact at scale:**
- At 100K job listings: full table scan ~150ms per page load
- High-traffic job board: major bottleneck

**Recommendation:** Add `(status, expires_at, is_featured, created_at)` composite index or restructure query to allow index use.

---

## Summary

| Bug ID | Severity | Type | Regression of Prior Fix? |
|--------|----------|------|--------------------------|
| BUG-P7-001 | Medium | Admin UI | No |
| BUG-P7-002 | High (at scale) | Performance | No |
| BUG-P7-003 | Low/Medium | N+1 | No |
| BUG-P7-004 | Low | Side Effect | No |
| BUG-P7-005 | High | Config | No |
| BUG-P7-006 | Medium | Deployment | No |
| BUG-P7-007 | Medium | Operations | No (known from Phase 6B) |
| BUG-P7-008 | Low/Medium | Performance | No |

**Regressions of prior Phase 6A–6C fixes: ZERO**  
**New bugs found: 8**  
**New critical bugs (app-breaking): 0**  
**New high bugs (security/production risk): 2 (BUG-P7-002 at scale, BUG-P7-005 if deployed as-is)**
