# Phase 6C Report 08 — Remaining Risks
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6C — Production Hardening Sprint

---

## Status of All Phase 6B Risks

### Group A — Production Blockers (all fixed in Phase 6C)

| Finding | Risk | Phase 6C Status |
|---------|------|----------------|
| DB-INT-003 — Admin backdoor | Authorization bypass | CLOSED — canAccessPanel fixed |
| LOG-002 — PII in logs | PIPEDA/privacy violation | CLOSED — keys removed |
| DB-INT-001 — hours type mismatch | Data corruption (null returns) | CLOSED — migration ran |
| DB-INT-002 — dirty migration | Fresh-deploy failure | CLOSED — idempotency guard |
| PERF-001 — missing indexes | Full table scans at scale | CLOSED — 19 indexes added |
| QUEUE-001 — no queue worker | Silent job failures | OPEN — DevOps action required |

**Group A remaining: 1 (DevOps only — no code fix possible)**

---

### Group B — Should Fix Before Production

| Finding | Risk | Phase 6C Status |
|---------|------|----------------|
| STOR-004 — BlogPost wrong disk | Broken blog images | CLOSED — S3 disk fixed |
| STOR-002 — matrimonial gallery leak | S3 storage growth | CLOSED — delete loop added |
| QUEUE-002 — scheduler not running | Missing scheduled jobs | OPEN — DevOps action required |
| STOR-001 — listing image orphan | S3 storage growth | OPEN — Group B, not fixed in 6C |
| STOR-003 — event image orphan | S3 storage growth | OPEN — Group B, not fixed in 6C |
| LOG-001 — APP_DEBUG true | Debug info exposure | OPEN — DevOps .env check |

**Group B remaining: 4 (2 DevOps, 2 code Group B not in approved Phase 6C scope)**

---

### Group C — Can Fix After Production (all deferred)

| Finding | Status |
|---------|--------|
| ORPHAN-001 — listing orphan records | Deferred to post-launch |
| ORPHAN-002 — business orphan records | Deferred to post-launch |
| ORPHAN-003 — event orphan records | Deferred to post-launch |
| ORPHAN-004 — matrimonial orphan records | Deferred to post-launch |
| VAL-001 — input validation gaps | Deferred to post-launch |
| UI-001 — missing loading states | Deferred to post-launch |
| AUTH-001 — no email verification | Deferred to post-launch |
| RATE-001 — no rate limiting | Deferred to post-launch |
| CACHE-001 — no query caching | Deferred to post-launch |
| SESSION-001 — no remember-me | Deferred to post-launch |
| FEAT-001 — no notifications | Deferred to post-launch |
| SEARCH-001 — basic LIKE search | Deferred to post-launch |
| PLAN-001 — plan enforcement gaps | Deferred to post-launch |

---

## Remaining Risks — Detail

### RISK-R1 — Queue Worker Not Running (HIGH)

**Finding:** QUEUE-001  
**Type:** Operational / Infrastructure  
**Severity:** High  
**Risk:** All queued jobs (email notifications, event-driven processing for `App\Events\MessageSent`) fail silently. Users may not receive messages or notifications.

**Current state:** Queue driver is `database`. Worker process is not configured. 26 stale jobs observed in `jobs` table. New jobs dispatched will accumulate without processing.

**Required action:**
```bash
# On production server — add via Supervisor
[program:gobazzar-worker]
command=php /home/heavendw/public_html/gobazzarweb.heavendwell.com/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/log/gobazzar-worker.log
```

**Owner:** DevOps / Server Admin  
**Blocking production launch:** Yes, if job-based functionality is user-facing

---

### RISK-R2 — Scheduler Not Running (HIGH)

**Finding:** QUEUE-002  
**Type:** Operational / Infrastructure  
**Risk:** No scheduled jobs execute (log rotation, cache warming, subscription expiry checks, etc.)

**Required action:**
```bash
# Add to server crontab
* * * * * cd /home/heavendw/public_html/gobazzarweb.heavendwell.com && php artisan schedule:run >> /dev/null 2>&1
```

**Owner:** DevOps / Server Admin

---

### RISK-R3 — APP_DEBUG May Be True in Production (MEDIUM)

**Finding:** LOG-001  
**Type:** Configuration / Security  
**Risk:** Full stack traces exposed in HTTP responses if exceptions occur. Reveals file paths, environment vars, and application internals.

**Required action:** Verify `/home/heavendw/public_html/gobazzarweb.heavendwell.com/.env` contains `APP_DEBUG=false` before launch.

**Owner:** DevOps

---

### RISK-R4 — No Database Backup Strategy (CRITICAL — operational)

**Finding:** LOG-003  
**Type:** Operational  
**Risk:** Any data corruption, accidental deletion, or server failure results in permanent data loss.

**Required action:**
- Configure automated daily mysqldump
- Store backups in S3 or separate server
- Test restore procedure before launch

**Owner:** DevOps / Server Admin

---

### RISK-R5 — No S3 Versioning (LOW)

**Finding:** LOG-004  
**Type:** Operational  
**Risk:** Accidental overwrite or deletion of S3 objects is permanent.

**Required action:** Enable S3 bucket versioning in AWS Console.

**Owner:** DevOps / AWS

---

### RISK-R6 — listing/event Image Orphans on Update (LOW)

**Findings:** STOR-001, STOR-003  
**Type:** Code — Group B not applied in Phase 6C  
**Risk:** S3 storage grows unbounded as image replacements do not delete old files. No user-facing impact; only cost impact at scale.

**Required action (post-launch):** Add `Storage::disk('s3')->delete($old_image)` in `updateListing()` and `updateEvent()` in `PostController.php`. Mirror the pattern applied to matrimonials in Phase 6C.

**Owner:** Developer — Phase 7

---

### RISK-R7 — Nulled FK Orphan Records (LOW)

**Findings:** ORPHAN-001 through ORPHAN-004  
**Type:** Database design — intentional nullOnDelete  
**Risk:** When a user is deleted, their listings/businesses/events/matrimonials have `user_id = NULL`. Records remain visible but show no owner. Not data corruption — orphans are recoverable.

**Required action (post-launch):** Implement user soft-delete and decide whether to archive or delete associated content.

**Owner:** Developer — Phase 7

---

## Risk Register — Final Counts

| Group | Total | Closed in 6C | Remaining |
|-------|-------|-------------|-----------|
| A (Production Blocker) | 6 | 5 | 1 (DevOps) |
| B (Should Fix) | 6 | 2 | 4 |
| C (Can Fix After) | 13 | 0 | 13 |
| D (Operational) | 7 | 0 | 7 |
| E (Developer) | 5 | 0 | 5 |
| **Total** | **37** | **7** | **30** |

---

## Phase 6C Risk Closure

Phase 6C closed all code-fixable production blockers and 2 approved Group B items. The 1 remaining Group A item (queue worker) is an operational infrastructure task with no code to change. The 30 remaining open items are classified Group B–E and are appropriate for post-launch or operational sprint work.
