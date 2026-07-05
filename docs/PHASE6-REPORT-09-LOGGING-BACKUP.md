# Phase 6 Report 09 — Logging & Backup Readiness
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Summary

| Area | Status | Risk |
|------|--------|------|
| APP_DEBUG in production | TRUE (local env) | Critical if left on in production |
| Log channel | stack (daily + stderr) | Acceptable |
| PII in logs | RISK — upgrade requests log email/phone | High |
| Error handling | Default Laravel 12 | Medium |
| Database backup strategy | None detected | Critical |
| S3 backup/versioning | Unknown | High |
| Recovery plan | None documented | High |
| Log rotation | Managed by daily channel | PASS |

---

## 1. Debug Mode Assessment

### LOG-001 — `APP_DEBUG=true` (Critical — Production Risk)

**Evidence:**
```
APP_ENV: local
APP_DEBUG: TRUE (value = 1)
```

**Root Cause:** The current environment is `local` with `APP_DEBUG=true`. This is expected for development but is a critical security risk if deployed to production without changing this value.

**Impact in Production (if left on):**
- Full stack traces (including file paths, SQL queries, env variable values) are displayed to users on any unhandled exception
- SQL query details visible in error pages — potential SQL injection reconnaissance aid
- Environment variable names visible in stack trace
- Laravel Ignition debug page exposed to public internet

**Current Risk:** Low (local environment only). The risk is that this value is hardcoded in `.env` and may be copied to production.

**Recommended Fix:**
1. Ensure production `.env` has `APP_DEBUG=false`
2. Add a deployment checklist item to verify `APP_DEBUG=false` before go-live
3. Implement a pre-flight check: `php artisan about | grep debug`

**Effort:** 5 minutes | **Priority:** P1 — Before production deployment

---

## 2. Logging Configuration

### Log Channel: `stack`

**Evidence:**
```
LOG_CHANNEL: stack
```

**Assessment:** The `stack` channel aggregates multiple log channels (typically `daily` + `stderr`). This is an appropriate default. Log files are written to `storage/logs/` with daily rotation.

### LOG-002 — PII Exposure in Application Logs (High)

**Evidence (`app/Http/Controllers/PricingController.php`):**
```php
\Log::info('Upgrade request', [
    'user_id' => auth()->id(),
    'plan'    => $data['plan'],
    'name'    => $data['name'],
    'email'   => $data['email'],   // PII — email address
    'phone'   => $data['phone'] ?? '', // PII — phone number
]);
```

**Root Cause:** The `PricingController::request()` method logs user's name, email address, and phone number directly to the application log in plaintext.

**Impact:**
- Log files stored in `storage/logs/` contain PII (email + phone)
- If log files are accessible (misconfigured server, log file exposed via web), user data is leaked
- Violates data minimization principles (GDPR, PIPEDA for Canadian users)
- Log files are typically included in server backups — PII propagates to backup storage

**Recommended Fix:**
1. Remove PII from log entries — log only `user_id` and `plan_slug`
2. If full details are needed for support, store in the `advertise_requests` table (which already captures this data)

```php
\Log::info('Upgrade request', [
    'user_id' => auth()->id(),
    'plan'    => $data['plan'],
]);
```

**Effort:** 10 minutes | **Breaking Change Risk:** None | **Regression Risk:** None

---

## 3. Error Handling Assessment

### Exception Handler

```php
// bootstrap/app.php
->withExceptions(function (Exceptions $exceptions): void {
    //
})->create();
```

**Finding:** The exception handler is empty — no custom error handling is configured. Laravel's default exception handling applies:
- In production (`APP_DEBUG=false`): renders generic "Server Error" page
- In development (`APP_DEBUG=true`): renders Ignition debug page
- No custom error pages for 404, 403, 500 are defined (unless in `resources/views/errors/`)

**Assessment:** Acceptable for a community portal. The custom `errors/expired.blade.php` exists for 410 Gone responses (seen in ListingController and JobController). Standard 404/500 use Laravel defaults.

### Stripe Error Handling (from Phase 4)

```php
// StripeController — try/catch added in Phase 4
try {
    // Stripe operations
} catch (\Exception $e) {
    \Log::error('Stripe error', ['message' => $e->getMessage()]);
    return back()->withErrors(['error' => 'Payment processing failed.']);
}
```

**Assessment:** Adequate. Stripe errors are caught, logged (without PII), and shown to users as a generic message.

---

## 4. Database Backup Assessment

### LOG-003 — No Database Backup Strategy Detected (Critical)

**Finding:** No evidence of:
- Automated MySQL dumps (`mysqldump` cron job)
- Cloud DB snapshot policy (RDS automated backups, or equivalent)
- Database backup verification process
- Recovery time objective (RTO) or recovery point objective (RPO) definition

**Impact:**
- A server failure, accidental table drop, or ransomware attack would result in complete data loss
- For a marketplace with user-generated content (listings, businesses, conversations), data loss means loss of the entire business
- Payment history records would be lost — compliance issue

**Recommended Fix (Production):**

Option A — Managed DB (Recommended):
- Migrate from self-managed MySQL to AWS RDS or PlanetScale
- Enable automated daily snapshots with 7-day retention
- Enable point-in-time recovery (PITR)

Option B — Self-managed:
- Add daily `mysqldump` to cron:
```bash
0 2 * * * mysqldump -u gobazzar -p$DB_PASS gobazzar | gzip > /backups/gobazzar_$(date +\%Y\%m\%d).sql.gz
```
- Upload to S3 or Backblaze B2
- Test restore procedure monthly

**Effort:** 4 hours | **Priority:** P1 — Before production launch

---

## 5. S3 Storage Backup Assessment

### LOG-004 — S3 Versioning Status Unknown (High)

**Finding:** The S3 bucket configuration is not accessible for audit (would require AWS console access). The following S3 best practices cannot be confirmed:
- Versioning enabled (allows recovery of overwritten/deleted objects)
- MFA delete protection
- Cross-region replication
- Lifecycle rules (archiving old images)
- Bucket access logging

**Recommended Fix:**
1. Enable S3 versioning on the production bucket
2. Enable cross-region replication to a secondary AWS region
3. Set lifecycle rule: move objects > 90 days to Infrequent Access storage class (cost savings)
4. Enable bucket access logging for security auditing

**Effort:** 2 hours (AWS console configuration)

---

## 6. Recovery Plan

### LOG-005 — No Recovery Procedure Documented (High)

**Finding:** No documentation found for:
- How to restore the database from a backup
- How to recover S3 images if the bucket is accidentally deleted
- How to restore the application after a server failure
- Disaster recovery runbook

**Recommended Fix:** Document a minimal recovery runbook:
1. Restore DB from latest backup: `mysql gobazzar < gobazzar_latest.sql.gz`
2. Verify S3 bucket integrity (versioning)
3. Run `php artisan migrate` to apply any new migrations
4. Restart queue worker
5. Restart cron scheduler

**Effort:** 2 hours to document

---

## 7. Monitoring Assessment

**Finding:** No application performance monitoring (APM) or error tracking is configured:
- No Sentry, Bugsnag, or Rollbar integration
- No Datadog, New Relic, or Grafana setup
- No uptime monitoring (e.g., UptimeRobot, Pingdom)
- No slow query logging in MySQL

**Impact:** When production issues occur (errors, slow queries, downtime), the development team has no visibility until users report problems.

**Recommended Fix (in priority order):**
1. Add Sentry to Laravel (`sentry/sentry-laravel`) — free tier handles small apps
2. Enable MySQL slow query log (`slow_query_log=1, long_query_time=1`)
3. Configure uptime monitoring for the main domain

**Effort:** 2 hours | **Breaking Change Risk:** None

---

## 8. Logging & Backup Verdict

```
╔══════════════════════════════════════════════════════════════╗
║                                                              ║
║   PHASE 6 — LOGGING & BACKUP READINESS                      ║
║                                                              ║
║   Critical: 2 (APP_DEBUG production risk, no DB backup)      ║
║   High: 3 (PII in logs, S3 versioning, no recovery plan)     ║
║   Medium: 1 (no APM/monitoring)                              ║
║                                                              ║
║   STATUS: NOT PRODUCTION READY — Backup strategy required    ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
```
