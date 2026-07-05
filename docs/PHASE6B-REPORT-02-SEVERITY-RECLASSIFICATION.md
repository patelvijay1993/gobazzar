# Phase 6B Report 02 — Severity Reclassification Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6B — Enterprise Architecture Remediation Review  
**Fix Policy:** Analysis and recommendation only. No code, database, configuration, or migration changes.

---

## Purpose

Phase 6A assigned severity levels based on standalone issue analysis. Phase 6B applies the strict enterprise severity definitions to verify, upgrade, or downgrade each assignment. This report documents every severity change with its justification.

---

## Strict Severity Definitions (Repeated for Reference)

- **Critical** = ONLY: Production crash / Data corruption / Security breach / Financial loss / Authentication bypass / Authorization bypass / Permanent data loss
- **High** = Major functionality broken / Severe performance issue / Large operational risk
- **Medium** = Feature degradation / Partial UX problem / Operational inconvenience
- **Low** = Minor issue / Documentation / Recommendation

---

## Reclassifications — Severity Downgraded

### DB-INT-001: `businesses.hours` cast mismatch
- **Phase 6A Severity:** Critical
- **Phase 6B Severity:** High
- **Change:** DOWNGRADED from Critical to High
- **Justification:** Phase 6A classified this as Critical because it breaks business hours display for all businesses. Applying strict definitions: there is no production crash, no data corruption, no security breach, no authentication bypass, and no permanent data loss. The data exists (as plain text) and can be migrated. The feature is broken (business hours show null to users), which qualifies as Major Functionality Broken → High. The fix is a data migration + column type change — no data is permanently lost.
- **Production Impact:** High. All business profiles show blank hours. This IS user-visible and embarrassing on launch.
- **Group Assignment:** A (Production Blocker) — not because it's Critical severity, but because the feature is visibly broken.

---

### QUEUE-001 / ORPHAN-005: Queue worker not running (26 stale jobs)
- **Phase 6A Severity:** Critical
- **Phase 6B Severity:** High
- **Change:** DOWNGRADED from Critical to High
- **Justification:** Real-time chat broadcast events are not being processed. Strict definition: no crash, no data loss (messages ARE saved to the database), no security breach, no authentication bypass. What IS broken: real-time notification of chat messages (WebSocket/Pusher events never fire). Users see messages on page refresh, not in real-time. This is Major Functionality Broken → High. The queue worker is an operational infrastructure item.
- **Production Impact:** High. Chat is a core feature. Real-time messaging is broken from day one without the queue worker.
- **Group Assignment:** A (Production Blocker) — operational config required, but it must be done before launch.

---

### PERF-001: 24 missing indexes
- **Phase 6A Severity:** Critical (Report 07) / High (Report 11)
- **Phase 6B Severity:** High
- **Change:** DOWNGRADED from Critical to High (where Critical was assigned)
- **Justification:** At current data volume (46 listings), there is no crash, no error, no user-visible impact. At 10,000 rows, queries take ~25ms (slow but not broken). At 100,000 rows, queries become timeout-worthy — at that point this becomes a production crash scenario. However, launching with small data volumes is safe. The risk is CERTAIN but not IMMEDIATE. Strict definition for Critical requires current production impact, not projected impact. High is correct: Severe Performance Issue.
- **Production Impact:** High. The indexes must be added — but the application functions correctly at launch scale. The risk escalates rapidly with data volume.
- **Group Assignment:** A (Production Blocker) — adding indexes is zero-risk (additive, no downtime, no data change). The ROI is enormous. There is no valid reason to defer this.

---

### LOG-001: `APP_DEBUG=true` in development
- **Phase 6A Severity:** Critical
- **Phase 6B Severity:** High
- **Change:** DOWNGRADED from Critical to High
- **Justification:** The current environment is `local` — APP_DEBUG=true here is correct and expected. The risk is that the production .env inherits this value. If it does, that IS a security breach (stack traces exposed). But as a Phase 6A finding, it is an environment-specific risk, not a confirmed production defect. The dev environment is correctly configured. The production environment cannot be audited from here. Classified as High operational risk.
- **Group Assignment:** D (Operational Recommendation) — DevOps must confirm APP_DEBUG=false in production .env before go-live.

---

### LOG-003: No database backup strategy
- **Phase 6A Severity:** Critical
- **Phase 6B Severity:** Critical
- **Change:** CONFIRMED Critical
- **Justification:** Strict definition includes "Permanent data loss." Without a backup strategy, a server failure, accidental DROP TABLE, or ransomware attack results in complete, permanent, unrecoverable data loss. This meets the strict Critical definition. A marketplace losing all its listings, user accounts, conversations, and payment history is a total business failure.
- **Group Assignment:** D (Operational Recommendation) — it is Critical severity, but it is an infrastructure task. The code is not the problem. The DevOps configuration is. Group D because it requires server/cloud configuration, not code changes.

---

### DB-INT-002: Dirty migration state
- **Phase 6A Severity:** Critical
- **Phase 6B Severity:** High
- **Change:** DOWNGRADED from Critical to High
- **Justification:** The dirty state (partial migration with a comment "listings already has 'flagged' from a partial run") is in the development environment. A fresh production deploy on a clean database MAY fail if this migration tries to add a column that already exists. This is a deployment risk, not a production crash in a running system. Not a data corruption event. Classified as High: Large Operational Risk.
- **Production Impact:** High. If the migration fails during deployment, the deployment fails and must be rolled back.
- **Group Assignment:** A (Production Blocker) — a failed deployment IS a launch blocker by definition.

---

## Reclassifications — Severity Upgraded

### LOG-002: PII in application logs
- **Phase 6A Severity:** High
- **Phase 6B Severity:** High
- **Change:** CONFIRMED High (no change in level, but Group assignment changed)
- **Justification:** Logging user email and phone in plaintext is a PIPEDA/privacy law violation. While not a security breach in the classical sense (logs are internal), it represents a compliance breach that creates legal exposure. CONFIRMED as High.
- **Group Assignment:** A (Production Blocker) — 10-minute fix. No valid reason to launch with PII in logs.

---

## Reclassifications — Severity Downgraded to Low

### DB-INT-008: `sessions.user_id` no FK constraint
- **Phase 6A Severity:** High
- **Phase 6B Severity:** Low
- **Change:** DOWNGRADED significantly
- **Justification:** Sessions can exist for unauthenticated (guest) users. Laravel's session system does not require a FK constraint on `user_id`. Adding one would break guest sessions. This is intentional framework behavior, not a defect. No production impact whatsoever.

### FK-GAP-001: `sessions.user_id` no FK
- **Phase 6A Severity:** High
- **Phase 6B Severity:** Low
- **Change:** DOWNGRADED significantly
- **Justification:** Same as DB-INT-008. Duplicate finding. Intentional Laravel design.

### FK-GAP-002: `payment_history.plan_slug` no FK to plans
- **Phase 6A Severity:** Medium
- **Phase 6B Severity:** Low
- **Change:** DOWNGRADED
- **Justification:** Historical payment records are intentionally decoupled from plans. If Plan "Verified" is renamed, historical payments must still reference "Verified." The loose string reference is the correct design for an immutable audit trail. Not a defect.

### ORPHAN-007: External URLs in S3-path columns
- **Phase 6A Severity:** Low
- **Phase 6B Severity:** Low
- **Change:** CONFIRMED Low (but Group moved to E)
- **Justification:** This is seed/dev data. The accessor handles it correctly. Production users cannot inject external URLs (form validation prevents it). Developer cleanup recommendation only.

### QUEUE-003: Mail sent synchronously
- **Phase 6A Severity:** Medium
- **Phase 6B Severity:** Low
- **Change:** DOWNGRADED
- **Justification:** No emails are currently being sent — the upgrade request only logs, does not send mail. When email IS wired up, synchronous sending is acceptable at small launch scale. Not a current defect.

### SCHED-001: Businesses not in mark-expired command
- **Phase 6A Severity:** Medium
- **Phase 6B Severity:** Pass
- **Change:** RECLASSIFIED as Pass (non-finding)
- **Justification:** Businesses have no `expires_at` column. Business visibility is controlled by user plan expiry. No bug exists here. Confirmed intentional design.

---

## Reclassification Summary

| Finding ID | Phase 6A Severity | Phase 6B Severity | Change |
|-----------|-------------------|-------------------|--------|
| DB-INT-001 | Critical | High | ↓ DOWNGRADED |
| DB-INT-002 | Critical | High | ↓ DOWNGRADED |
| DB-INT-003 | Critical | **Critical** | ✓ CONFIRMED |
| DB-INT-004 | High | Medium | ↓ DOWNGRADED |
| DB-INT-005 | Medium | Low | ↓ DOWNGRADED |
| DB-INT-006 | Medium | Low | ↓ DOWNGRADED |
| DB-INT-007 | Medium | Low | ↓ DOWNGRADED |
| DB-INT-008 | High | Low | ↓↓ DOWNGRADED |
| DB-INT-009 | Medium | Low | ↓ DOWNGRADED |
| FK-GAP-001 | High | Low | ↓↓ DOWNGRADED |
| FK-GAP-002 | Medium | Low | ↓ DOWNGRADED |
| FK-GAP-003 | Medium | Low | ↓ DOWNGRADED |
| ORPHAN-001 | Medium | Medium | ✓ CONFIRMED |
| ORPHAN-002 | Medium | Medium | ✓ CONFIRMED |
| ORPHAN-004 | High | High | ✓ CONFIRMED |
| ORPHAN-005 / QUEUE-001 | Critical | High | ↓ DOWNGRADED |
| ORPHAN-006 | Medium | Low | ↓ DOWNGRADED |
| ORPHAN-007 | Low | Low | ✓ CONFIRMED |
| STOR-001 | High | Medium | ↓ DOWNGRADED |
| STOR-002 | Medium | Low | ↓ DOWNGRADED |
| STOR-003 | High | High | ✓ CONFIRMED |
| STOR-004 | Medium | Medium | ✓ CONFIRMED |
| QUEUE-002 | High | High | ✓ CONFIRMED (Group D) |
| QUEUE-003 | Medium | Low | ↓ DOWNGRADED |
| QUEUE-004 | Medium | Low | ↓ DOWNGRADED |
| SCHED-002 | Medium | Low | ↓ DOWNGRADED |
| SCHED-003 | High | Medium | ↓ DOWNGRADED |
| SCHED-004 | Low | Low | ✓ CONFIRMED |
| CACHE-002 | High | Medium | ↓ DOWNGRADED |
| CACHE-003 / PERF-003 | High | High | ✓ CONFIRMED |
| CACHE-004 | Medium | Low | ↓ DOWNGRADED |
| CACHE-005 | Medium | Low | ↓ DOWNGRADED |
| PERF-001 | Critical/High | High | ↓ DOWNGRADED (where Critical) |
| PERF-002 | High | High | ✓ CONFIRMED |
| PERF-004 | Medium | Low | ↓ DOWNGRADED |
| PERF-005 | Medium | Low | ↓ DOWNGRADED |
| LOG-001 | Critical | High | ↓ DOWNGRADED |
| LOG-002 | High | High | ✓ CONFIRMED |
| LOG-003 | Critical | **Critical** | ✓ CONFIRMED |
| LOG-004 | High | Low | ↓ DOWNGRADED |
| LOG-005 | High | Low | ↓ DOWNGRADED |

---

## Phase 6A Severity Inflation Analysis

Phase 6A used "Critical" for 8 findings. Phase 6B confirms Critical for 2:

| Finding | Phase 6A | Phase 6B | Verdict |
|---------|---------|---------|---------|
| DB-INT-003 (admin backdoor) | Critical | **Critical** | ✓ Confirmed — Authorization bypass |
| LOG-003 (no DB backup) | Critical | **Critical** | ✓ Confirmed — Permanent data loss risk |
| DB-INT-001 (hours mismatch) | Critical | High | ↓ Feature broken, not crash/breach |
| DB-INT-002 (dirty migration) | Critical | High | ↓ Deployment risk, not data corruption |
| QUEUE-001 (worker down) | Critical | High | ↓ Feature broken, not crash |
| PERF-001 (missing indexes) | Critical | High | ↓ Performance risk, current impact low |
| LOG-001 (APP_DEBUG local) | Critical | High | ↓ Environment-specific risk, not confirmed prod defect |
| SCALE-T4 (100K users) | Critical | Low | ↓ Future architecture concern, not launch issue |

**Conclusion:** Phase 6A inflated severity for 6 of 8 Critical findings. The 2 true Criticals (admin backdoor, no DB backup) remain confirmed. The remaining 6 are correctly classified as High.
