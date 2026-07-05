# Phase 6 Report 11 — Risk Register
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Risk Rating Matrix

| Likelihood × Impact | Low Impact | Medium Impact | High Impact | Critical Impact |
|--------------------|-----------|--------------|-------------|----------------|
| Certain | Low | Medium | High | Critical |
| Likely | Low | Medium | High | Critical |
| Possible | Low | Low | Medium | High |
| Unlikely | Negligible | Low | Low | Medium |

---

## Risk Register

| ID | Risk | Likelihood | Impact | Rating | Category |
|----|------|-----------|--------|--------|----------|
| RISK-001 | Production deploy with APP_DEBUG=true — full stack traces visible | Possible | Critical | **High** | Security |
| RISK-002 | Queue worker not started — real-time chat non-functional from day 1 | Certain | High | **Critical** | Operations |
| RISK-003 | Scheduler cron not configured — expired listings stay visible indefinitely | Likely | High | **Critical** | Operations |
| RISK-004 | No database backup — server failure = total data loss | Possible | Critical | **High** | Business |
| RISK-005 | 24 missing indexes — database degrades severely beyond 10K listings | Certain | Critical | **Critical** | Performance |
| RISK-006 | User ID=1 backdoor — first registered user has admin access | Possible | Critical | **High** | Security |
| RISK-007 | businesses.hours cast mismatch — hours display broken for all businesses | Certain | High | **Critical** | Data |
| RISK-008 | S3 throw:false — failed image uploads silently succeed without images | Possible | High | **High** | Reliability |
| RISK-009 | Plan/credits checked via DB write every request — write amplification | Certain | High | **Critical** | Performance |
| RISK-010 | Orphan business+jobs publicly visible — no owner to manage or remove | Certain | Medium | **High** | Data |
| RISK-011 | User 5: power_seller plan with no Stripe subscription — revenue loss | Certain | Medium | **High** | Business |
| RISK-012 | PII (email, phone) logged in plaintext — privacy violation | Certain | Medium | **High** | Privacy |
| RISK-013 | 26 stale queue jobs fire on worker start — mass stale notifications | Likely | Medium | **High** | Operations |
| RISK-014 | Gallery images leak to S3 after expiry/update — storage cost creep | Certain | Low | **Medium** | Storage |
| RISK-015 | Dirty migration state — deploy to fresh DB may fail | Possible | High | **High** | Deployment |
| RISK-016 | Blog images on wrong disk — not backed up with S3 content | Certain | Medium | **Medium** | Storage |
| RISK-017 | No soft deletes — accidental deletion unrecoverable | Possible | High | **High** | Data |
| RISK-018 | Search LIKE leading wildcard — no index possible, slow at scale | Certain | High | **Critical** | Performance |
| RISK-019 | Database cache driver — cache operations add to DB load | Certain | Medium | **Medium** | Performance |
| RISK-020 | File sessions — cannot horizontally scale | Possible | Medium | **Medium** | Scalability |
| RISK-021 | No monitoring/APM — production issues invisible until user reports | Certain | Medium | **High** | Operations |
| RISK-022 | Payment history plan_slug no FK — orphan payment records on plan rename | Unlikely | Low | **Low** | Data |
| RISK-023 | advertise_requests.status no enum — invalid status values possible | Unlikely | Low | **Low** | Data |
| RISK-024 | 28 pending flagged posts unreviewed — moderation queue backlogged | Certain | Medium | **Medium** | Compliance |
| RISK-025 | External URLs in S3 path columns — broken images if external URLs die | Possible | Medium | **Medium** | Data |

---

## Critical Risk Detail

### RISK-002 — Queue Worker Not Running (Critical)

- **Likelihood:** Certain (confirmed: 26 jobs pending, 0 being processed)
- **Impact:** Real-time chat events not broadcast → chat appears broken to users
- **Detection:** User complains that chat messages only appear after page refresh
- **Mitigation:** Start `php artisan queue:work` via Supervisor before launch

---

### RISK-003 — Scheduler Cron Not Configured (Critical)

- **Likelihood:** Likely (no evidence of cron setup)
- **Impact:** Expired listings remain `status=active` and publicly visible past their expiry date
- **Detection:** Users report that 3-day free listings are still visible after a week
- **Mitigation:** Add `* * * * * php artisan schedule:run` to server crontab

---

### RISK-005 — 24 Missing Indexes (Critical)

- **Likelihood:** Certain (confirmed missing via INFORMATION_SCHEMA)
- **Impact:** Query time grows linearly with dataset size; full table scans on every listing filter
- **Detection:** Slow pages at ~10,000 listings; timeout errors at ~100,000 listings
- **Mitigation:** Add composite indexes migration

---

### RISK-007 — `businesses.hours` Cast Mismatch (Critical)

- **Likelihood:** Certain (confirmed: 5/5 businesses have plain text hours, not JSON)
- **Impact:** All business hours display as blank/null to users
- **Detection:** Business profile pages show empty hours section for all businesses
- **Mitigation:** Data migration to JSON-encode existing hours + alter column type

---

### RISK-009 — Plan/Credits DB Write Per Request (Critical)

- **Likelihood:** Certain (confirmed in User.php code)
- **Impact:** Write amplification on `users` table; degrades under concurrent load
- **Detection:** Slow authenticated page loads; `users` table lock contention
- **Mitigation:** Memoize `activePlan()` per request; move credits reset to scheduler

---

### RISK-018 — Search LIKE Leading Wildcard (Critical)

- **Likelihood:** Certain (confirmed in all search implementations)
- **Impact:** Every search triggers full table scan regardless of indexes
- **Detection:** Slow search results at ~10,000 listings
- **Mitigation:** Add FULLTEXT indexes or integrate a search engine

---

## Risk Heat Map

```
         IMPACT
         Low     Medium   High    Critical
L  C  |         RISK-014  RISK-002  RISK-005
I  e  |         RISK-016  RISK-003  RISK-007
K  r  |         RISK-019  RISK-009  RISK-009
E  t  |         RISK-020  RISK-012  RISK-018
L  a  |         RISK-024  RISK-013  
I  i  |         RISK-025  RISK-021  
H  n  |                             
O  ─  |                   RISK-008  RISK-001
O  L  |                   RISK-015  RISK-004
D  i  |                   RISK-010  RISK-006
   k  |                   RISK-011  RISK-017
      |                   RISK-018       
   P  |                               
   o  |  RISK-022  RISK-023           
   s  |                               
```

---

## Risk Priority Action Plan

### P0 — Immediate (Before Any User Touches Production)

| Risk | Action | Owner |
|------|--------|-------|
| RISK-002 | Start queue worker (Supervisor) | DevOps |
| RISK-003 | Configure scheduler cron | DevOps |
| RISK-006 | Remove ID=1 backdoor from canAccessPanel() | Dev |
| RISK-012 | Remove PII from PricingController logs | Dev |

### P1 — Before Launch (Code + Migration Changes)

| Risk | Action | Owner |
|------|--------|-------|
| RISK-005 | Add composite indexes migration | Dev |
| RISK-007 | Fix businesses.hours type + data migration | Dev |
| RISK-009 | Memoize activePlan() + move credits reset | Dev |
| RISK-015 | Test migrate:fresh on clean DB | Dev |
| RISK-004 | Set up automated database backup | DevOps |
| RISK-001 | Confirm APP_DEBUG=false in production .env | Dev |

### P2 — Within 2 Weeks of Launch

| Risk | Action | Owner |
|------|--------|-------|
| RISK-017 | Add SoftDeletes to content models | Dev |
| RISK-008 | Set S3 throw=true + try/catch | Dev |
| RISK-013 | Clear stale queue before launch | DevOps |
| RISK-018 | Add full-text search indexes | Dev |
| RISK-021 | Configure Sentry + uptime monitoring | Dev |
| RISK-010 | Clean orphan businesses/jobs | Admin |

### P3 — Within 1 Month

| Risk | Action | Owner |
|------|--------|-------|
| RISK-014 | Fix gallery purge in purge command | Dev |
| RISK-016 | Migrate blog images to S3 | Dev |
| RISK-019 | Switch cache to Redis | DevOps |
| RISK-020 | Switch sessions to Redis | DevOps |
| RISK-024 | Build admin moderation review UI | Dev |
| RISK-011 | Audit and fix user 5 Stripe data | Admin |
