# Phase 7 Report 08 — Remaining Risks
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 7 — Enterprise Regression & Performance Benchmark  
**Role:** Release Manager, Principal QA Architect  
**Policy:** Evidence only. No fixes.

---

## Risk Classification

| ID | Risk | Severity | Category | Launch Blocker? |
|----|------|----------|----------|----------------|
| RISK-L1 | APP_DEBUG=true if not changed before deploy | High | Config/Security | YES |
| RISK-L2 | Queue worker not started | High | Operations | YES (if chat/notifications are in launch scope) |
| RISK-L3 | No database backup | Critical | Operations | YES |
| RISK-L4 | Scheduler not configured | High | Operations | YES (if scheduled tasks are in scope) |
| RISK-L5 | Storage symlink missing | Medium | Deployment | CONDITIONAL (S3 is primary) |
| RISK-L6 | Config/route cache not built | Medium | Performance | Recommended |
| RISK-M1 | Chat inbox performance (correlated subquery) | High (at scale) | Performance | NO (current scale) |
| RISK-M2 | job_listings full table scan | Medium (at scale) | Performance | NO (current scale) |
| RISK-M3 | Admin listing photo preview broken | Medium | Admin UX | NO |
| RISK-M4 | planModel() N+1 queries | Low/Medium | Performance | NO |
| RISK-M5 | User account page unbounded get() | Low | Performance | NO |
| RISK-M6 | maybeResetCredits DB write on read | Low | Side Effect | NO |
| RISK-P1 | No email verification for new users | Medium | Security | Configurable |
| RISK-P2 | No rate limiting on public endpoints | Medium | Security | NO |
| RISK-P3 | listing/event image orphans on S3 | Low | Storage cost | NO |
| RISK-P4 | Orphan records (nullOnDelete) on user delete | Low | Data integrity | NO |
| RISK-P5 | No full-text search (LIKE only) | Low | UX | NO |
| RISK-P6 | Payment success — no DB transaction wrapping | Low | Data integrity | NO |
| RISK-P7 | 26 stale queue jobs (June 2026) | Medium | Operations | YES (clear before launch) |
| RISK-P8 | S3 bucket versioning unknown status | Low | Data protection | Recommended |

---

## Launch-Blocking Risks — Detail

### RISK-L1 — APP_DEBUG=true

**Severity:** High  
**Current state:** `APP_DEBUG=true` in local `.env`  
**Production action required:** Set `APP_DEBUG=false` and `APP_ENV=production` in production `.env` before any traffic reaches the server.  
**If not done:** Full PHP stack traces exposed to users on any exception.

---

### RISK-L2 — Queue Worker Not Running

**Severity:** High  
**Current state:** 26 unprocessed jobs. Worker not configured.  
**If chat real-time messaging is in launch scope:** Blocker — messages queue but never broadcast.  
**If chat is visible but polling-mode only (users reload page):** Reduced risk.  
**Action:** `php artisan queue:clear` (stale jobs) + Supervisor configuration.

---

### RISK-L3 — No Database Backup

**Severity:** Critical  
**Current state:** No automated backup strategy observed.  
**If server fails or data is corrupted:** All user data, listings, payment records permanently lost.  
**Required:** Daily automated mysqldump to S3 or offsite storage. Test restore before launch.

---

### RISK-L4 — Scheduler Not Configured

**Severity:** High  
**Current state:** No cron entry.  
**Known scheduled tasks:** None explicitly observed in `app/Console/Kernel.php` (not reviewed). Featured credit resets appear to use model-side logic rather than scheduled commands.  
**Risk:** If any scheduled tasks exist, they silently never run.  
**Action:** `php artisan schedule:list` on production; add cron if any commands registered.

---

### RISK-P7 — 26 Stale Queue Jobs

**Severity:** Medium  
**Current state:** 26 `App\Events\MessageSent` jobs from June–July 2026.  
**If worker starts without clearing:** These may process stale broadcast events (harmless but noisy), or fail and route to `failed_jobs` (also manageable).  
**Best practice:** `php artisan queue:clear` before starting worker on production.

---

## Scale Risks — Not Current Blockers

### RISK-M1 — Chat Inbox Correlated Subquery

At 100+ conversations per user, the inbox load time becomes unusable (estimated 6,900ms at 100 conversations). This is a code architecture issue that must be addressed before scaling to significant user numbers.

**Trigger threshold:** ~50 conversations per active user → visible degradation.

---

### RISK-M2 — job_listings Full Table Scan

At 100K+ job listings, the `live()` scope query has no index coverage due to `expires_at > NOW()` range condition. Listing page load times would degrade to 150–500ms.

**Trigger threshold:** ~10,000 active job listings.

---

### RISK-M3 — Admin Listing Photo Previews Broken

Admin photo preview widget uses `asset('storage/...')` for S3 keys — generates broken image URLs. No functional impact on public site. Admin usability impact: cannot visually review photos before approving/rejecting listings.

---

## Risks Closed in Phase 6B–6C (Confirmed Closed)

| Risk | Phase Closed | Evidence |
|------|-------------|---------|
| Admin authorization bypass | 6C | canAccessPanel = is_admin===true |
| PII in logs | 6C | Log::info() verified clean |
| businesses.hours null data | 6C | is_array=YES for all non-null rows |
| Dirty migration fresh-deploy risk | 6C | INFORMATION_SCHEMA guard present |
| Performance indexes missing | 6C | 19 indexes confirmed in INFORMATION_SCHEMA |
| BlogPost wrong disk | 6C | Storage::disk('s3')->url() in accessor |
| Matrimonial S3 orphan on update | 6C | Delete loop before upload present |

---

## Risk Register Count

| Category | Count |
|----------|-------|
| Launch-blocking risks | 5 (RISK-L1–L4 + RISK-P7) |
| Should fix before scale (Medium) | 3 (M1, M2, M3) |
| Post-launch / low risk | 12 |
| Closed from Phase 6C | 7 |
