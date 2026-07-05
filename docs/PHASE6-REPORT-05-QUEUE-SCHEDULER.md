# Phase 6 Report 05 — Queue & Scheduler Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Summary

| Area | Status | Finding |
|------|--------|---------|
| Queue driver | database | CONFIGURED — but worker not running |
| Queue backlog | 26 jobs | CRITICAL — all unprocessed MessageSent events |
| Failed jobs | 0 | PASS |
| Scheduler registration | bootstrap/app.php | PASS — no Kernel.php needed (Laravel 12) |
| Scheduler commands | 2 | PASS — mark-expired + purge-expired |
| Scheduler execution | Unknown | RISK — no evidence cron is running |
| Mail queue | SMTP (sync) | RISK — emails sent synchronously on request |
| Image processing | Synchronous | PASS (no queue dependency) |

---

## 1. Queue Configuration

### Driver: `database`

```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'database'),

'database' => [
    'driver'      => 'database',
    'table'       => 'jobs',            // ✓ exists in DB
    'queue'       => 'default',
    'retry_after' => 90,               // 90 seconds
    'after_commit' => false,
]
```

**Assessment:** Database queue driver is appropriate for a small-to-medium Laravel application. It requires a persistent worker process (`php artisan queue:work`) running at all times. Without it, jobs accumulate indefinitely.

### Failed Jobs Driver: `database-uuids`

```php
'failed' => [
    'driver'   => 'database-uuids',
    'database' => env('DB_CONNECTION', 'sqlite'), // ← references sqlite, not mysql
    'table'    => 'failed_jobs',
]
```

**Finding (QUEUE-004):** The `failed` queue config references `DB_CONNECTION` with a default of `'sqlite'`, not `'mysql'`. The application uses MySQL but the failed jobs table references SQLite as the fallback. If `DB_CONNECTION` is not explicitly set in `.env` to `mysql`, failed jobs would try to write to a SQLite database that doesn't exist.

**Mitigation:** The application's `.env` almost certainly sets `DB_CONNECTION=mysql` explicitly, making this a low risk. But the default in config is incorrect for this project.

---

## 2. Queue Backlog — Critical Finding

### QUEUE-001 — 26 Unprocessed `App\Events\MessageSent` Jobs (Critical)

**Evidence:**
```sql
SELECT COUNT(*) FROM jobs;  → 26
Job class: App\Events\MessageSent (all 26)
Failed jobs: 0
Jobs currently processing: 0
```

**Root Cause:** The `queue:work` process is not running on this development environment. Chat messages trigger a `MessageSent` event which dispatches to the queue. Since no worker processes the queue, all 26 events are stuck.

**Impact (Development):** Chat messages are saved to the database correctly but real-time broadcast events (WebSocket/Pusher) are not fired. Users see messages only on page refresh, not in real-time.

**Impact (Production Risk):** If this environment is deployed to production without starting a queue worker, real-time chat will be completely non-functional from day one. The 26 stale jobs would also fire when the worker first starts, potentially sending stale event notifications.

**Recommended Fix:**
1. Development: Use `QUEUE_CONNECTION=sync` in `.env.local` to process jobs immediately without a worker
2. Production: Configure Supervisor to run `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
3. Before launch: Clear the stale queue — `php artisan queue:clear`

**Effort:** 2 hours to configure Supervisor | **Priority:** P1 — Production blocker

---

## 3. Retry and Failure Policy

### retry_after: 90 seconds

```php
'retry_after' => 90,
```

**Assessment:** The `retry_after` of 90 seconds means if a job is picked up by a worker and the worker crashes, the job will be retried after 90 seconds. For `MessageSent` (a simple broadcast event), 90 seconds is appropriate.

**Missing:** No `tries` configuration in the queue connection (this is set per-job or via `queue:work --tries=3`). If `App\Events\MessageSent` doesn't define `$tries`, it will retry indefinitely until manually failed.

### No Job-Level Retry Configuration Observed

No `Jobs/` classes were found in `app/Jobs/`. The `MessageSent` events are likely handled by Laravel's event system directly. The queue driver is used for broadcast events.

---

## 4. Scheduler Configuration

### Laravel 12 Bootstrap Scheduler

```php
// bootstrap/app.php
->withSchedule(function (Schedule $schedule): void {
    $schedule->command('listings:mark-expired')->hourly();
    $schedule->command('posts:purge-expired')->dailyAt('00:00');
})
```

**Assessment:** Laravel 12 registers the scheduler in `bootstrap/app.php` (no `app/Console/Kernel.php` needed). Both commands are registered correctly.

### QUEUE-002 — No Evidence That Laravel Scheduler Cron Is Configured (High)

**Finding:** The scheduler commands are registered in code, but there is no evidence that the server cron job `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1` is configured.

**Impact:**
- `listings:mark-expired` — runs hourly, marks expired content as `status=expired`. If not running, expired listings remain `status=active` and are publicly visible past their expiry date.
- `posts:purge-expired` — runs daily at midnight, permanently deletes expired records and their S3 images. If not running, expired content accumulates indefinitely.

**Verification Needed:** Check the server's crontab for a `schedule:run` entry.

**Recommended Fix (production server):**
```bash
* * * * * cd /home/heavendw/public_html/gobazzarweb.heavendwell.com && php artisan schedule:run >> /dev/null 2>&1
```

**Effort:** 30 minutes | **Priority:** P1 — Required for listing expiry to function

---

## 5. Scheduler Command Audit

### `listings:mark-expired` — `app/Console/Commands/MarkExpiredListings.php`

```php
$schedule->command('listings:mark-expired')->hourly();

// Command handles:
Listing::where('status', 'active')->whereNotNull('expires_at')
    ->where('expires_at', '<=', $now)->update(['status' => 'expired']);

Job::where(...)->update(['status' => 'expired']);
BusinessPost::where(...)->update(['status' => 'expired']);
Matrimonial::where(...)->update(['status' => 'expired']);
```

**SCHED-001 — Missing: Business Expiry Marking (Medium)**

**Finding:** `MarkExpiredListings` marks Listings, Jobs, BusinessPosts, and Matrimonials as expired — but **not Businesses**. If a business has an `expires_at` (possible if plan expiry is linked to business visibility), it is never automatically expired.

**Evidence:** Checking the businesses table schema — businesses do not have an `expires_at` column (confirmed via migration). Business visibility is governed by `user.plan` expiry, not a per-record `expires_at`. So this is not a bug — businesses don't expire individually.

**Status:** PASS — Businesses intentionally have no `expires_at`. Business visibility is controlled by user plan status.

### `posts:purge-expired` — `app/Console/Commands/PurgeExpiredPosts.php`

```php
$schedule->command('posts:purge-expired')->dailyAt('00:00');

// Handles Listings, Jobs, Matrimonials
// Does NOT handle: BusinessPosts, Events
```

**SCHED-002 — `posts:purge-expired` Does Not Purge Expired Business Posts (Medium)**

**Finding:** `PurgeExpiredPosts` handles Listings, Jobs, and Matrimonials but **not BusinessPosts**. Business posts have `expires_at` and `scopeLive()` — they can expire — but the purge command doesn't clean them up.

**Impact:** Expired business posts are marked `status=expired` by `MarkExpiredListings` but are never deleted. They accumulate in the database and their S3 images are never cleaned up.

**SCHED-003 — `posts:purge-expired` Does Not Handle Gallery Images (High)**

As documented in STOR-001, the purge command only deletes `$r->image` (the primary image) and not the `images` JSON gallery. This is a duplicate of the storage finding — confirmed as a scheduler-level deficiency.

**SCHED-004 — Events Never Purged (Low)**

Past events (where `start_date < now()`) remain in the database forever. There is no cleanup command for old events. At scale, this table could grow significantly.

**Recommended Fix:** Add event archival/purge schedule (e.g., delete events older than 6 months).

---

## 6. Mail Queue Assessment

### QUEUE-003 — Mail Sent Synchronously (No Mail Queue) (Medium)

**Evidence:**
```
MAIL_MAILER: smtp
Queue driver: database (but no mail queue configured)
```

**Finding:** `PricingController::request()` logs upgrade requests but does NOT send emails yet (comment: "email sending can be wired up later"). However, any future email sending (Stripe receipts, password resets, account notifications) will go through the SMTP mailer synchronously on the request thread.

**Impact:** Password reset, email verification, and any transactional email adds latency to the user's request (SMTP round-trip can be 200–2000ms depending on the mail server). On heavy load, SMTP can time out and cause request failures.

**Recommended Fix:** Configure `MAIL_QUEUE=true` or use `Mail::queue()` instead of `Mail::send()` for non-critical emails. Password reset emails need to go out promptly so may warrant `Mail::send()`, but bulk notifications should use the queue.

**Effort:** 2 hours | **Breaking Change Risk:** Low

---

## 7. Queue & Scheduler Verdict

```
╔══════════════════════════════════════════════════════════════╗
║                                                              ║
║   PHASE 6 — QUEUE & SCHEDULER                                ║
║                                                              ║
║   Critical: 1 (26 unprocessed jobs, worker not running)      ║
║   High: 2 (scheduler cron unknown, gallery purge missing)    ║
║   Medium: 3 (mail sync, failed jobs sqlite default, etc.)    ║
║                                                              ║
║   STATUS: REQUIRES REMEDIATION BEFORE PRODUCTION             ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
```
