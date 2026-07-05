# Phase 6B Report 01 — Finding Review Matrix
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6B — Enterprise Architecture Remediation Review  
**Role:** Principal Software Architect / Senior Laravel Architect / Enterprise DBA / DevOps Architect / Release Manager  
**Fix Policy:** DO NOT CHANGE ANY CODE. DO NOT CHANGE DATABASE. DO NOT CHANGE CONFIGURATION. DO NOT APPLY MIGRATIONS. Only recommend.  
**Source:** Phase 6A Reports 01–15 only. No new findings introduced.

---

## Severity Definitions (Strict Application)

| Severity | Definition |
|----------|-----------|
| **Critical** | ONLY: Production crash / Data corruption / Security breach / Financial loss / Authentication bypass / Authorization bypass / Permanent data loss |
| **High** | Major functionality broken / Severe performance issue / Large operational risk |
| **Medium** | Feature degradation / Partial UX problem / Operational inconvenience |
| **Low** | Minor issue / Documentation / Recommendation |

## Reclassification Groups

| Group | Definition |
|-------|-----------|
| **A** | Production Blocker — Must be fixed before production |
| **B** | Should Fix Before Production — Not critical but highly recommended |
| **C** | Can Fix After Production — Safe to defer |
| **D** | Operational Recommendation — DevOps / Infrastructure. Not a software defect |
| **E** | Developer Recommendation — Developer experience improvement. No production impact |

---

## Finding Review Matrix — All Phase 6A Findings

### Report 01 — Database Integrity (DB-INT-001 through DB-INT-009)

| Finding ID | Title | Phase 6A Severity | Phase 6B Severity | Phase 6B Group | Rationale |
|-----------|-------|-------------------|-------------------|----------------|-----------|
| DB-INT-001 | `businesses.hours` column text vs array cast mismatch | Critical | **High** | **A** | Real user-facing break — business hours display blank for all businesses. Not a crash, not data corruption, but major feature broken. Reclassified from Critical: no data is permanently lost; a migration can fix it. IS a production blocker because the feature is visibly broken on launch. |
| DB-INT-002 | Dirty migration (partial run comment) | Critical | **High** | **A** | A fresh DB deploy MAY fail. Not guaranteed. The dirty state is in dev, but production deploy on clean DB is the real risk. Production blocker because a failed migration halts deployment. |
| DB-INT-003 | `canAccessPanel()` ID=1 admin backdoor | Critical | **Critical** | **A** | Authorization bypass confirmed. Any user who registers first gets unconditional admin access. Strict definition met: authorization bypass. This is a true Critical. |
| DB-INT-004 | No soft deletes on content tables | High | **Medium** | **C** | Accidental deletion is irreversible — true. But this is not a current defect; it is an architectural risk. Soft deletes are a best practice, not a launch requirement. Safe to defer post-launch. |
| DB-INT-005 | `advertise_requests.status` no enum constraint | Medium | **Low** | **C** | The application code controls what values are written; no user can inject arbitrary status values through the application. No current breakage. Database-level enum is a recommendation, not a blocker. |
| DB-INT-006 | `matrimonials.gender` / `marital_status` no enum | Medium | **Low** | **C** | Same reasoning as DB-INT-005. Application layer validates inputs. Database constraint is defensive depth. Safe to defer. |
| DB-INT-007 | `flagged_posts` has no `post_id` column | Medium | **Low** | **C** | The table is an audit log — it captures rejected post attempts. Without `post_id`, admins cannot navigate to originating content. This is an admin UX limitation, not a user-facing break. Safe to defer. |
| DB-INT-008 | Sessions table has no FK to users | High | **Low** | **E** | Polymorphic and session FK omission is intentional in Laravel — sessions can exist for unauthenticated users. Laravel's session system does not require this FK. Not a defect. Developer recommendation only. |
| DB-INT-009 | Stale migration comment (partial run note) | Medium | **Low** | **E** | A code comment. No runtime impact. Developer cleanliness recommendation. |

---

### Report 02 — Foreign Key Report (FK-GAP-001 through FK-GAP-003)

| Finding ID | Title | Phase 6A Severity | Phase 6B Severity | Phase 6B Group | Rationale |
|-----------|-------|-------------------|-------------------|----------------|-----------|
| FK-GAP-001 | `sessions.user_id` has no FK constraint | High | **Low** | **E** | Sessions can exist for guests (unauthenticated). Adding an FK here would break guest sessions. Intentional Laravel design. Not a defect. |
| FK-GAP-002 | `payment_history.plan_slug` has no FK to `plans.slug` | Medium | **Low** | **C** | Historical payment records intentionally decouple from plans — if a plan is renamed or retired, historical records must remain. The loose FK is the correct design choice here. No current defect. |
| FK-GAP-003 | `flagged_posts` has no post_id | Medium | **Low** | **C** | Same as DB-INT-007 — audit log design limitation. No production impact; admin UX improvement. |

---

### Report 03 — Orphan Data (ORPHAN-001 through ORPHAN-007)

| Finding ID | Title | Phase 6A Severity | Phase 6B Severity | Phase 6B Group | Rationale |
|-----------|-------|-------------------|-------------------|----------------|-----------|
| ORPHAN-001 | 1 business with `user_id=NULL` publicly visible | Medium | **Medium** | **B** | Test data (`fczxzcx`) is publicly visible on the directory. No one can manage it. Should be cleaned before launch but does not block the application from functioning. |
| ORPHAN-002 | 3 job listings with `user_id=NULL` publicly visible | Medium | **Medium** | **B** | Same as ORPHAN-001. Test data ("Test Job 1/2/3") visible in public jobs index. Clean before launch. |
| ORPHAN-003 | No listing orphans (CASCADE working) | Pass | **Pass** | — | No action needed. |
| ORPHAN-004 | User 5: `subscription_status=active` but no Stripe subscription | High | **High** | **B** | Financial risk — user has paid plan benefits without valid payment. Not a crash, but revenue integrity concern. Needs investigation before launch. |
| ORPHAN-005 | 26 unprocessed `MessageSent` jobs in queue | High | **High** | **A** | The queue worker is not running. This is confirmed evidence that chat broadcast events are not being processed. Real-time chat is broken. Production blocker because a core feature (live chat) is non-functional. The stale jobs themselves should be cleared. |
| ORPHAN-006 | 28 flagged posts never reviewed (no admin UI) | Medium | **Low** | **C** | Moderation backlog is a compliance concern, not a technical production blocker. Admin moderation UI is a feature enhancement. |
| ORPHAN-007 | External URLs in S3 path columns (dev seed data) | Low | **Low** | **E** | This is dev/seed data. The accessor handles it correctly. No production users can inject external URLs through forms (validation prevents it). |

---

### Report 04 — Storage Integrity (STOR-001 through STOR-004)

| Finding ID | Title | Phase 6A Severity | Phase 6B Severity | Phase 6B Group | Rationale |
|-----------|-------|-------------------|-------------------|----------------|-----------|
| STOR-001 | Gallery images not purged by `PurgeExpiredPosts` | High | **Medium** | **C** | S3 storage cost creep. Not a user-visible defect. The primary image IS deleted; gallery images accumulate on S3. At current scale (46 listings), this is negligible. Real impact begins at thousands of listings. Safe to fix post-launch. |
| STOR-002 | Matrimonial gallery not cleaned on update | Medium | **Low** | **C** | Same class of issue as STOR-001. Storage leak on matrimonial gallery update. Not user-visible. Low cost impact at current scale. Safe to defer. |
| STOR-003 | S3 `throw:false` silences upload failures | High | **High** | **B** | Silent S3 failures cause listings to be saved without images. The user sees "listing created" but images are missing. This is a UX defect that would confuse real users. Should be fixed before launch but does not cause data corruption or crash. Group B: not a hard blocker but significant reliability risk. |
| STOR-004 | `BlogPost` uses wrong disk (local vs S3) | Medium | **Medium** | **B** | Blog images not backed up with S3 content. In production, blog images go to local disk — which may not be persistent on cloud hosting. Should fix before launch to ensure blog images survive server restarts. |

---

### Report 05 — Queue & Scheduler (QUEUE-001 through QUEUE-004, SCHED-001 through SCHED-004)

| Finding ID | Title | Phase 6A Severity | Phase 6B Severity | Phase 6B Group | Rationale |
|-----------|-------|-------------------|-------------------|----------------|-----------|
| QUEUE-001 | 26 unprocessed jobs — queue worker not running | Critical | **High** | **A** | Real-time chat is broken. Confirmed with live evidence. Core feature non-functional. Production blocker. Phase 6A called it Critical; per strict definitions it is High (major functionality broken), not Critical (no crash/data loss). Group A because it blocks production functionality. |
| QUEUE-002 | Scheduler cron not confirmed running | High | **High** | **D** | Not a software defect — the code is correct. The cron setup is a DevOps/infrastructure task. The scheduler commands ARE registered correctly in bootstrap/app.php. This is purely an operational configuration item. Group D. |
| QUEUE-003 | Mail sent synchronously (no mail queue) | Medium | **Low** | **C** | Currently no emails are being sent (PricingController logs only, email not wired). When email IS added, synchronous sending is acceptable at small scale. Can address when email feature is wired. |
| QUEUE-004 | `failed` queue config references sqlite default | Medium | **Low** | **E** | Low risk because production .env sets `DB_CONNECTION=mysql` explicitly. The default in config is wrong but overridden by .env. Developer cleanliness item. |
| SCHED-001 | `MarkExpiredListings` does not handle Businesses | Medium | **Pass** | — | Businesses intentionally have no `expires_at` column. Business visibility is governed by user plan. Not a defect. |
| SCHED-002 | `PurgeExpiredPosts` does not purge BusinessPosts | Medium | **Low** | **C** | Expired business posts are correctly marked `status=expired` by the mark command. They are not deleted by purge. This causes DB accumulation over time, not a user-visible defect. Safe to defer. |
| SCHED-003 | `PurgeExpiredPosts` does not delete gallery images | High | **Medium** | **C** | Same root cause as STOR-001. S3 storage leak. Not user-visible. Safe to defer. |
| SCHED-004 | Past events never purged | Low | **Low** | **C** | Archive/purge of past events is an operational hygiene task. At current scale (13 events), irrelevant. |

---

### Report 06 — Cache Report (CACHE-001 through CACHE-005)

| Finding ID | Title | Phase 6A Severity | Phase 6B Severity | Phase 6B Group | Rationale |
|-----------|-------|-------------------|-------------------|----------------|-----------|
| CACHE-001 | `Setting::get()` properly cached | Pass | **Pass** | — | No action needed. |
| CACHE-002 | `Plan::active()` not cached | High | **Medium** | **C** | At current launch scale (~100 users), uncached plan query is a ~3-row query, negligible. Impact is real at 1,000+ users. Safe to fix post-launch. |
| CACHE-003 | `User::activePlan()` / `maybeResetCredits()` not cached | High | **High** | **B** | Write amplification on every authenticated request. At even 100 concurrent authenticated users, this creates significant DB write load. Should fix before launch to avoid user table contention. However, it does not cause a production crash — it causes performance degradation. Group B. |
| CACHE-004 | `Location` queries not cached | Medium | **Low** | **C** | 35-row table queried per page. Fast at any scale under 1,000 users. Safe to defer. |
| CACHE-005 | `Category` queries not cached | Medium | **Low** | **C** | Same reasoning as CACHE-004. Negligible at launch scale. |

---

### Report 07 — Performance (PERF-001 through PERF-005)

| Finding ID | Title | Phase 6A Severity | Phase 6B Severity | Phase 6B Group | Rationale |
|-----------|-------|-------------------|-------------------|----------------|-----------|
| PERF-001 | 24 missing indexes on content tables | Critical | **High** | **A** | At current dev scale (46–46 rows) this is invisible. But this is certain to become a production blocker once real users generate data. Adding indexes is zero-risk (no data change, no downtime, additive migration). At 10K rows and no indexes, all listing queries take 25–50ms — unacceptable. Production blocker to launch without this fix. Group A. |
| PERF-002 | Homepage fires 50+ DB queries per load | High | **High** | **B** | At 100 users, this is manageable (~5,000 queries/min, well within MySQL capacity). At 500+ concurrent users, it becomes saturating. Not an immediate day-one blocker for a soft launch, but must be addressed quickly. Group B. |
| PERF-003 | `activePlan()` DB write on every authenticated request | High | **High** | **B** | Same as CACHE-003 analysis. Write amplification is real. Should fix before launch. Group B. |
| PERF-004 | `Category::applicableFields()` parent query N+1 | Medium | **Low** | **C** | Low-traffic path (only on post creation/edit forms). Current category count is small. No user-visible impact at launch scale. Safe to defer. |
| PERF-005 | `PollOption::percentage` N+1 risk | Medium | **Low** | **C** | Poll is eager-loaded with `with('options')` so N+1 is mitigated. Low impact even without fix. Safe to defer. |

---

### Report 08 — Scalability Assessment

| Finding ID | Title | Phase 6A Severity | Phase 6B Severity | Phase 6B Group | Rationale |
|-----------|-------|-------------------|-------------------|----------------|-----------|
| SCALE-T1 | Tier 1 (100 users): READY with ops fixes | — | **Pass (conditional)** | **D** | Operational fixes only (queue worker, scheduler cron). No code changes required. |
| SCALE-T2 | Tier 2 (1,000 users): NOT READY | High | **Medium** | **C** | Post-launch goal. With index migration and basic caching, Tier 2 is achievable without architectural change. Defer. |
| SCALE-T3 | Tier 3 (10,000 users): Requires Redis | High | **Low** | **D** | DevOps/infrastructure recommendation. Not a launch blocker for initial launch. |
| SCALE-T4 | Tier 4 (100,000 users): Re-architecture | Critical | **Low** | **D** | Future architectural planning. No production blocker for initial launch. |

---

### Report 09 — Logging & Backup (LOG-001 through LOG-005)

| Finding ID | Title | Phase 6A Severity | Phase 6B Severity | Phase 6B Group | Rationale |
|-----------|-------|-------------------|-------------------|----------------|-----------|
| LOG-001 | `APP_DEBUG=true` (development env) | Critical | **High** | **D** | The local env has APP_DEBUG=true — this is expected and correct for development. The RISK is that the production .env might copy this value. This is a deployment configuration item, not a code defect. Group D. Cannot be verified without production access. |
| LOG-002 | PII (email + phone) in logs | High | **High** | **A** | Privacy violation. PIPEDA (Canada's privacy law) applies. Even at launch with one user, logging PII in plaintext is a compliance violation. 10-minute fix. Group A. |
| LOG-003 | No database backup strategy | Critical | **Critical** | **D** | Complete data loss risk on server failure. Per strict definition: risk of **permanent data loss**. Strict definition IS met. However, this is an infrastructure/operational task, not a software defect. Group D: DevOps must configure before production, but it's not a code fix. |
| LOG-004 | S3 versioning status unknown | High | **Low** | **D** | Cannot be assessed without AWS console access. Infrastructure task. Group D. |
| LOG-005 | No recovery runbook documented | High | **Low** | **D** | Documentation task. Group D. |

---

### Report 11 — Risk Register (RISK-001 through RISK-025)

The risk register is a consolidated view of findings already covered above. Risks map directly to findings and are reclassified within those finding entries. No standalone re-entries for RISK items.

---

### Report 12 — Production Recommendations (REC-001 through REC-025)

Recommendations are implementations of the above findings. They are tracked in the Implementation Roadmap (Report 07) not re-entered here.

---

## Summary Count by Group

| Group | Count | Description |
|-------|-------|-------------|
| **A — Production Blocker** | 6 | DB-INT-003, DB-INT-002*, DB-INT-001, ORPHAN-005/QUEUE-001, PERF-001, LOG-002 |
| **B — Should Fix Before Production** | 7 | ORPHAN-004, STOR-003, STOR-004, CACHE-003/PERF-003, PERF-002, ORPHAN-001, ORPHAN-002 |
| **C — Can Fix After Production** | 13 | DB-INT-004/005/006/007, FK-GAP-002/003, ORPHAN-006/007, STOR-001/002, QUEUE-003/SCHED-002/003/004, CACHE-002/004/005, PERF-004/005, SCALE-T2 |
| **D — Operational Recommendation** | 7 | QUEUE-002, LOG-001, LOG-003, LOG-004, LOG-005, SCALE-T3, SCALE-T4 |
| **E — Developer Recommendation** | 5 | DB-INT-008/009, FK-GAP-001, ORPHAN-007, QUEUE-004 |
| **Pass** | 4 | ORPHAN-003, SCHED-001, CACHE-001, SCALE-T1 (conditional) |

*DB-INT-002 (dirty migration) is Group A because a failed migration halts production deployment, making it a deployment blocker by definition.
