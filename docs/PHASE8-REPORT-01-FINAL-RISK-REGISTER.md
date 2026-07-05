# Phase 8 Report 01 — Final Risk Register
**Project:** GoBazaar  
**Date:** 2026-07-05  
**Phase:** 8 — Enterprise Production Certification & Final Release Sign-Off  
**Role:** CTO, Head of QA, Release Manager, Enterprise Security Lead  
**Classification:** FINAL RISK REGISTER — All items classified. Nothing unclassified.

---

## Risk Classification Legend

| Class | Definition |
|-------|-----------|
| **Production Blocker** | Must be resolved before any public traffic. Application cannot go live with this risk present. |
| **Operational Requirement** | Required for safe ongoing operations. Must be in place within 24–48 hours of launch. |
| **Known Limitation** | Documented gap, accepted by stakeholders, will degrade gracefully. |
| **Future Enhancement** | Not a defect. Desired improvement for a future sprint. |
| **Technical Debt** | Functional but sub-optimal. Must be tracked and eventually remediated. |
| **Accepted Risk** | Reviewed, understood, and formally accepted. No action required at this time. |

---

## CATEGORY 1 — Production Blockers

These items PREVENT release. None may be deferred.

| ID | Risk | Evidence | Classification |
|----|------|---------|---------------|
| **PB-001** | `APP_DEBUG=true` in `.env` | `env('APP_DEBUG')=true` confirmed via probe | **Production Blocker** |
| **PB-002** | `APP_ENV=local` in `.env` | `env('APP_ENV')=local` confirmed via probe | **Production Blocker** |
| **PB-003** | No automated database backup | No backup config found anywhere in codebase or server | **Production Blocker** |

### PB-001 — APP_DEBUG=true

**Threat:** When Laravel encounters any unhandled exception in debug mode, it renders a full Whoops page containing: file paths, environment variable names, class structure, SQL query strings, and stack traces. Every end user who triggers any exception receives this information disclosure.

**OWASP:** A05:2021 — Security Misconfiguration.

**Resolution:** Set `APP_DEBUG=false` in production `.env` before first HTTP request is served.

---

### PB-002 — APP_ENV=local

**Threat:** Multiple Laravel framework behaviors differ between `local` and `production`: error page rendering, environment-specific service providers, driver behavior, and third-party package behavior. Running `local` in production is an unsupported configuration.

**Resolution:** Set `APP_ENV=production` in production `.env`.

---

### PB-003 — No Database Backup

**Threat:** Total and permanent loss of all user data, listings, payment records, and chat history on any of: server hardware failure, accidental DROP TABLE or DROP DATABASE, ransomware, unintended migration with destructive rollback, or MariaDB corruption.

**Resolution:** Automated daily `mysqldump` piped to gzip, uploaded to a separate S3 prefix or off-site storage. Restore test must be performed before launch.

---

## CATEGORY 2 — Operational Requirements

These items must be in place to operate safely. Complete within 24–48 hours of launch.

| ID | Risk | Evidence | Classification |
|----|------|---------|---------------|
| **OR-001** | Queue worker not running | 26 unprocessed jobs, no Supervisor config | **Operational Requirement** |
| **OR-002** | 26 stale queue jobs in `jobs` table | June–July 2026 `App\Events\MessageSent` jobs | **Operational Requirement** |
| **OR-003** | Scheduler cron not configured | No cron entry; `schedule:list` not verified | **Operational Requirement** |
| **OR-004** | Config cache not built | `php artisan config:cache` not run | **Operational Requirement** |
| **OR-005** | Route cache not built | `php artisan route:cache` not run | **Operational Requirement** |
| **OR-006** | View cache not built | `php artisan view:cache` not run | **Operational Requirement** |
| **OR-007** | Storage symlink missing | `public/storage` NOT FOUND | **Operational Requirement** |
| **OR-008** | S3 bucket versioning status unknown | Not verifiable from codebase; AWS Console only | **Operational Requirement** |

### OR-001 — Queue Worker Not Running

**Impact:** `App\Events\MessageSent` jobs never fire. Chat messages store correctly in DB but no real-time broadcast reaches recipients. Users must refresh manually. Any future queued operations (emails, exports) will also silently fail.

**Resolution:** Configure Supervisor-managed `queue:work` process on production server. See deployment runbook.

### OR-002 — Stale Queue Jobs

**Impact:** If queue worker starts without clearing these 26 stale jobs, they will attempt to dispatch `MessageSent` for conversations that occurred during QA testing. Likely outcome: jobs fail (conversation/user data may have changed), route to `failed_jobs`. Manageable but pollutes the failed_jobs table.

**Resolution:** `php artisan queue:clear` immediately before starting queue worker.

### OR-003 — Scheduler Not Configured

**Impact:** Unknown — `app/Console/Kernel.php` not reviewed in Phase 7. If any scheduled commands are registered (log pruning, plan enforcement, featured credit batch resets), they will silently never run.

**Resolution:** Run `php artisan schedule:list` on production. If commands appear, add cron: `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`.

---

## CATEGORY 3 — Known Limitations

Documented gaps. Accepted for launch. Will degrade gracefully.

| ID | Limitation | Impact | Classification |
|----|-----------|--------|---------------|
| **KL-001** | Chat inbox uses correlated subquery — scales O(n) | 690ms at 10 conversations; unusable at 100+ | **Known Limitation** |
| **KL-002** | `job_listings.scopeLive()` full table scan on `expires_at` | Full scan at 100K+ rows | **Known Limitation** |
| **KL-003** | Admin listing photo preview broken for S3 images | Admin sees broken images in listing edit form | **Known Limitation** |
| **KL-004** | LIKE-based search, no full-text search | Prefix-matching missing; slow at 1M rows | **Known Limitation** |
| **KL-005** | Home page fires 20–28 queries, no result caching | At 100K+ rows: 300ms+ per home page load | **Known Limitation** |
| **KL-006** | Events `start_date` has no index | Upcoming-event filter scans at scale | **Known Limitation** |
| **KL-007** | Stripe success flow not wrapped in DB transaction | Plan upgrade persists, PaymentHistory creation could fail | **Known Limitation** |
| **KL-008** | Polymorphic FKs not enforced at DB level | Orphan records possible via direct DB manipulation | **Known Limitation** |

---

## CATEGORY 4 — Future Enhancements

Not defects. Prioritize in future sprints.

| ID | Enhancement | Priority | Classification |
|----|------------|---------|---------------|
| **FE-001** | Redis cache for session, cache, and queue | High — replaces DB-based drivers at scale | **Future Enhancement** |
| **FE-002** | Full-text search (Meilisearch, Algolia) | High — replaces LIKE queries | **Future Enhancement** |
| **FE-003** | Real-time broadcast infrastructure (Pusher/Reverb) | High — required for live chat UX | **Future Enhancement** |
| **FE-004** | `User::planModel()` memoization | Medium — eliminates repeated plan queries | **Future Enhancement** |
| **FE-005** | Chat inbox `latest_message_at` denormalization | Medium — eliminates correlated subquery | **Future Enhancement** |
| **FE-006** | Composite index on `(conversation_id, created_at)` for chat_messages | Medium — partially mitigates KL-001 | **Future Enhancement** |
| **FE-007** | Composite index `(status, expires_at, is_featured, created_at)` for job_listings | Medium — eliminates full scan in KL-002 | **Future Enhancement** |
| **FE-008** | S3 bucket versioning enabled | High — protects against accidental deletion | **Future Enhancement** |
| **FE-009** | API endpoint for mobile / third-party integration | Low | **Future Enhancement** |
| **FE-010** | Two-factor authentication | Medium — enhanced account security | **Future Enhancement** |
| **FE-011** | Accessibility improvements (WCAG 2.1 AA) | Medium — not audited in full detail | **Future Enhancement** |

---

## CATEGORY 5 — Technical Debt

Functional but sub-optimal. Must be tracked.

| ID | Debt | Risk | Classification |
|----|------|------|---------------|
| **TD-001** | `User::maybeResetCredits()` fires DB UPDATE on every read of `featuredCreditsRemaining()` | Implicit write-on-read; free-plan users write on every listing page load | **Technical Debt** |
| **TD-002** | `User::account()` loads 7 collections separately (no eager batch) | At scale: slow account page | **Technical Debt** |
| **TD-003** | No global response time monitoring / APM | No baseline established for performance regression detection | **Technical Debt** |
| **TD-004** | `HomeController` uses 8 repeated `$dirBiz()` closure calls | 24 queries for directory sections; no caching | **Technical Debt** |
| **TD-005** | Orphaned S3 images on listing/event updates (old images not always deleted) | Ongoing S3 storage cost accumulation | **Technical Debt** |
| **TD-006** | No DB transaction in Stripe webhook success handler | Audit trail gap on partial failure | **Technical Debt** |
| **TD-007** | Queue driver is `database` not `redis` | Less performant at scale; acceptable at launch | **Technical Debt** |

---

## CATEGORY 6 — Accepted Risks

Reviewed, understood, and formally accepted.

| ID | Risk | Rationale | Classification |
|----|------|-----------|---------------|
| **AR-001** | Email verification disabled by default | Configurable via `Setting::bool('require_email_verification')`. Business decision to enable. | **Accepted Risk** |
| **AR-002** | No rate limiting on public content endpoints | Low threat at launch scale; implement before significant traffic | **Accepted Risk** |
| **AR-003** | Admin listing photo preview broken (S3 images) | Admin function only; no user impact. Known, documented as BUG-P7-001. | **Accepted Risk** |
| **AR-004** | hours API format changed from plain-text to JSON | All rows confirmed array-compatible. Single internal format change; no external API consumers documented. | **Accepted Risk** |
| **AR-005** | User delete cascades to listing/job/event records | Intentional FK design. Orphan-first approach (`SET NULL`) used for content tables. | **Accepted Risk** |
| **AR-006** | Chat messages viewable by conversation participants only | No encryption at rest; acceptable for a classified/marketplace application. | **Accepted Risk** |
| **AR-007** | Log rotation not configured | `storage/logs/laravel.log` will grow unbounded. Low risk at current volume. | **Accepted Risk** |

---

## Risk Register — Count Summary

| Category | Count |
|----------|-------|
| Production Blockers | **3** |
| Operational Requirements | **8** |
| Known Limitations | **8** |
| Future Enhancements | **11** |
| Technical Debt | **7** |
| Accepted Risks | **7** |
| **Total classified** | **44** |
| **Unclassified** | **0** |

---

## Disposition

**Phase 8 cannot certify READY while Production Blockers exist.**

- PB-001, PB-002 (env config): 5-minute fix on production server
- PB-003 (database backup): Requires DevOps setup, restore test verification

Once all 3 Production Blockers are resolved and Operational Requirements 001–007 are confirmed, the risk register supports conditional certification.
