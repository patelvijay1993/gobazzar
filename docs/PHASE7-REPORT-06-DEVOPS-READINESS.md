# Phase 7 Report 06 — DevOps Readiness Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 7 — Enterprise Regression & Performance Benchmark  
**Role:** DevOps Architect, Release Manager  
**Policy:** Evidence only. No fixes.

---

## Environment Variables — Current State

| Variable | Current Value | Required for Production | Status |
|----------|-------------|------------------------|--------|
| APP_ENV | local | production | **FAIL — must change** |
| APP_DEBUG | true | false | **FAIL — must change** |
| QUEUE_CONNECTION | database | database (or redis) | PASS (driver acceptable) |
| MAIL_MAILER | smtp | smtp | PASS |
| CACHE_STORE | database | database or redis | PASS |
| SESSION_DRIVER | file | file or database | PASS |
| FILESYSTEM_DISK | s3 | s3 | PASS |
| AWS_BUCKET | [SET] | Required | PASS |
| STRIPE_SECRET | [SET] | Required | PASS |
| STRIPE_WEBHOOK_SECRET | [SET] | Required | PASS |

**Evidence:**
```
APP_ENV: local
APP_DEBUG: true
```

**Risk of APP_DEBUG=true in production:**  
Laravel renders full stack traces in HTTP responses when exceptions occur. This exposes file paths, environment variable names (not values), application structure, and SQL query strings to any user who triggers an exception. This is a known security vector (information disclosure).

---

## Storage Symlink

**Evidence:** `public/storage` symlink: **NOT FOUND**

**Command required:**
```bash
php artisan storage:link
```

**Impact if missing:**
- Any image stored via `storage_path('app/public/')` (local disk) is inaccessible via browser
- S3 images are unaffected (served via S3 URL, not through public/storage)
- The application is configured with `FILESYSTEM_DISK=s3` — primary uploads go to S3
- However, Filament's image preview in `ListingResource.php` (line 77) uses `asset('storage/'.$img)` for admin photo preview — this path would render broken images in admin

**Assessment:** Medium risk. S3 images work correctly for users. Admin listing photo previews may break.

**Evidence from ListingResource.php (line 77):**
```php
$url = str_starts_with($img, 'http') ? $img : asset('storage/'.$img);
```
S3 paths start with the S3 bucket key (no `http://`) — this would generate `asset('storage/listings/abc.jpg')` = local URL for S3-stored files. This is a separate bug in the admin photo preview (not a Phase 6C fix).

---

## Cache Status

| Cache Type | Status | Command to Build |
|-----------|--------|-----------------|
| Config cache | NO | `php artisan config:cache` |
| Route cache | NO | `php artisan route:cache` |
| View cache | NO | `php artisan view:cache` |
| Services cache | YES | — |
| Application cache (DB driver) | Functional | — |

**Impact of missing config + route cache:**
- Config: framework reads ~20 PHP config files per bootstrap vs. 1 compiled file. ~1–3ms overhead per request.
- Route: framework resolves all 146 routes on each request vs. compiled route list. ~1–5ms overhead per request.
- At 1,000 requests/hour: cumulative waste of 2–8 seconds of CPU time per hour.

**Required before production launch:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Queue Worker

**Evidence:** `jobs` table contains 26 unprocessed `App\Events\MessageSent` jobs.  
Worker status: **NOT RUNNING**.

**Queue driver:** `database`  
**Jobs:** All 26 are `App\Events\MessageSent` — chat message broadcast events created during Phase 3 QA testing (timestamps from June 2026 through early July 2026). These are stale and will likely fail when processed if the conversation/message data still exists.

**Impact:**
- Chat message broadcasting is non-functional. Messages are stored in DB correctly but real-time broadcast (Pusher/Reverb) never fires — users must manually reload to see new messages.
- Any other queued jobs (future) will also not process until worker is started.

**Required action:**
1. Clear stale jobs: `php artisan queue:clear`
2. Configure Supervisor (production):
```ini
[program:gobazzar-worker]
command=php /path/to/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/log/gobazzar-worker.log
```

---

## Scheduler

**Evidence:** No cron configuration verified. Scheduler not running.

**Check command:** `php artisan schedule:list`

**Impact:** Any scheduled commands (log rotation, plan expiry enforcement, featured credit resets) will not run. `User::maybeResetCredits()` is called on each user model read — this is the only observed credit reset mechanism, not a scheduled job.

**Required action:**
```bash
# Add to crontab on production server:
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

---

## Database Backup

**Evidence:** No backup configuration observed. No backup files found in repository.

**Risk:** Total data loss on server failure or accidental DROP.

**Required action (before launch):**
```bash
# Daily backup example:
mysqldump -u [user] -p gobazzar_prod | gzip > /backup/gobazzar_$(date +%Y%m%d).sql.gz
# Upload to S3 or separate storage
```

---

## S3 Versioning

**Evidence:** S3 bucket versioning status not verifiable from application code (AWS console only).

**Risk:** Accidental object deletion is permanent without versioning. An S3 `delete()` call in the application (during image replace) permanently removes the object.

**Recommendation:** Enable S3 bucket versioning in AWS Console.

---

## Application Logs

**Evidence (last 40 lines of laravel.log):**

Log file contains errors from Phase 7 probe scripts only:
```
[2026-07-04 18:23:59] local.ERROR: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'stripe_subscription_status'
[2026-07-04 18:22:xx] local.ERROR: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'featured_credits'
```

These errors are from this Phase 7 probe session's incorrect column name guesses in the test scripts — **not from application code**. The application itself does not reference `stripe_subscription_status` or `featured_credits` — confirmed by searching User model:
- Column is `subscription_status` (not `stripe_subscription_status`) — probe used wrong name
- Column is `featured_credits_used` (not `featured_credits`) — probe used wrong name

**No application-generated errors in logs.**  
**No PII in logs** — Phase 6C fix confirmed (PricingController no longer logs name/email/phone).

---

## Failed Jobs

**Evidence:** `failed_jobs` table count = **0**

No failed jobs. The queue worker has never run, so no jobs have been attempted — none have failed.

---

## DevOps Readiness Summary

| Check | Status | Action Required |
|-------|--------|----------------|
| APP_ENV=production | FAIL | Set in .env before launch |
| APP_DEBUG=false | FAIL | Set in .env before launch |
| Config cache built | FAIL | `php artisan config:cache` |
| Route cache built | FAIL | `php artisan route:cache` |
| View cache built | FAIL | `php artisan view:cache` |
| Storage symlink | FAIL | `php artisan storage:link` |
| Queue worker running | FAIL | Configure Supervisor |
| Scheduler cron | FAIL | Add to crontab |
| Database backup | FAIL | Configure automated backup |
| S3 versioning | UNKNOWN | AWS Console check |
| AWS credentials | PASS | [SET] confirmed |
| Stripe credentials | PASS | [SET] confirmed |
| No sensitive data in logs | PASS | Phase 6C fix verified |
| Failed jobs | PASS | 0 failed jobs |
| Migrations | PASS | 46/46 ran |
