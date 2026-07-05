# Phase 6 Report 13 — Database Health Score
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Database Health Score

```
┌──────────────────────────────────────────────────────────────┐
│                                                              │
│   GOBAZAAR DATABASE HEALTH SCORE:  54 / 100                  │
│                                                              │
│   Grade:  D+                                                 │
│   Status: SIGNIFICANT REMEDIATION REQUIRED                   │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

---

## Score Breakdown

| Category | Weight | Raw Score | Weighted Score | Notes |
|----------|--------|-----------|---------------|-------|
| Schema Design & Constraints | 20% | 6/10 | 12/20 | FKs mostly correct; missing enum constraints; no soft deletes |
| Index Coverage | 20% | 1/10 | 2/20 | 24 critical indexes missing on all content tables |
| Data Integrity | 15% | 5/10 | 7.5/15 | 4 orphan records; Stripe inconsistency; partial migration |
| Query Performance | 15% | 4/10 | 6/15 | Full table scans; N+1 on homepage; no query caching |
| Caching Strategy | 10% | 2/10 | 2/10 | Only settings cached; all high-traffic data uncached |
| Operational Readiness | 10% | 3/10 | 3/10 | No backup; queue not running; scheduler not confirmed |
| Security & Privacy | 10% | 4/10 | 4/10 | Admin backdoor; PII in logs; debug mode |
| Lifecycle Management | — | — | -3 | Gallery leak; no soft deletes; dirty migration |
| Bonus: FK Map Complete | — | — | +1 | 31 FK constraints correctly mapped |
| **TOTAL** | 100% | — | **54/100** | |

---

## Detailed Category Scoring

### 1. Schema Design & Constraints (12/20)

| Criterion | Score | Evidence |
|-----------|-------|---------|
| All tables have PKs | 10/10 | All 34 tables have PKs |
| FK constraints present | 7/10 | 31 FKs; missing sessions, payment plan_slug, flagged_posts |
| Enum/type safety | 4/10 | listings.status enum; but advertise_requests, matrimonials, flagged_posts use varchar |
| Unique constraints | 8/10 | Slug unique on all content; conv_unique; user_favorites unique |
| Nullable column design | 5/10 | Nullable user_id on businesses/jobs causes orphan data |
| Soft deletes | 0/10 | No soft deletes on any content table |
| **Category Average** | **6/10** | |

---

### 2. Index Coverage (2/20)

| Criterion | Score | Evidence |
|-----------|-------|---------|
| PK indexes | 10/10 | All tables |
| FK indexes | 10/10 | All FK columns auto-indexed by Laravel |
| Status column indexes | 0/10 | Missing on ALL 6 content tables |
| Province/city indexes | 0/10 | Missing on ALL 6 content tables |
| is_featured index | 0/10 | Missing on ALL 6 content tables |
| Compound query indexes | 2/10 | Only listing_views has compound indexes |
| **Category Average** | **1/10** | |

**This is the most critical category.** Missing indexes will make the application unusable at scale.

---

### 3. Data Integrity (7.5/15)

| Criterion | Score | Evidence |
|-----------|-------|---------|
| No orphan content | 5/10 | 4 real orphans (1 business, 3 jobs) |
| FK cascade correctness | 7/10 | Cascade on listings correct; nullOnDelete creates management void |
| Payment data integrity | 5/10 | 1 user with active plan but no Stripe sub |
| Schema/code alignment | 3/10 | businesses.hours: text vs array cast mismatch |
| Migration integrity | 4/10 | Partial/dirty migration comment found |
| Queue data integrity | 3/10 | 26 unprocessed jobs (data loss risk) |
| **Category Average** | **5/10** | |

---

### 4. Query Performance (6/15)

| Criterion | Score | Evidence |
|-----------|-------|---------|
| Eager loading used | 7/10 | `with(['category','user'])` on listing/job queries |
| Pagination implemented | 7/10 | All index pages paginated; account page is not |
| N+1 queries avoided | 4/10 | HomeController fires 50+ queries; dirBiz() N+1 pattern |
| Query complexity | 5/10 | Search uses LIKE leading wildcard (full scan) |
| Write amplification | 2/10 | maybeResetCredits() DB write on every request |
| **Category Average** | **4/10** | |

---

### 5. Caching Strategy (2/10)

| Criterion | Score | Evidence |
|-----------|-------|---------|
| Setting model caching | 10/10 | Cache::remember(300) with proper invalidation |
| Plan data caching | 0/10 | No cache |
| User plan caching | 0/10 | No cache per request |
| Location caching | 0/10 | No cache |
| Category caching | 0/10 | No cache |
| Search result caching | 0/10 | No cache |
| **Category Average** | **2/10** | |

---

### 6. Operational Readiness (3/10)

| Criterion | Score | Evidence |
|-----------|-------|---------|
| Database backup | 0/10 | No backup strategy found |
| Queue worker configured | 1/10 | Not running (26 stale jobs) |
| Scheduler configured | 3/10 | Commands registered but cron not confirmed |
| S3 versioning | 3/10 | Unknown (cannot verify without AWS access) |
| Monitoring/APM | 0/10 | No Sentry, no uptime monitoring |
| Error alerting | 0/10 | No alerting configured |
| **Category Average** | **3/10** | |

---

### 7. Security & Privacy (4/10)

| Criterion | Score | Evidence |
|-----------|-------|---------|
| APP_DEBUG off in production | 5/10 | Local only — production unknown |
| No hardcoded backdoors | 0/10 | canAccessPanel() ID=1 backdoor |
| PII not in logs | 2/10 | Email + phone logged in PricingController |
| S3 throw=false removed | 3/10 | Silent upload failures |
| Admin access controlled | 5/10 | Admin panel requires is_admin; backdoor present |
| **Category Average** | **4/10** | |

---

## Post-Fix Projected Score

If all P0 and P1 recommendations are implemented:

| Category | Current | After P0/P1 Fixes | After P2/P3 Fixes |
|----------|---------|------------------|------------------|
| Schema Design | 12/20 | 14/20 | 17/20 |
| Index Coverage | 2/20 | 18/20 | 18/20 |
| Data Integrity | 7.5/15 | 11/15 | 13/15 |
| Query Performance | 6/15 | 8/15 | 13/15 |
| Caching Strategy | 2/10 | 5/10 | 9/10 |
| Operational Readiness | 3/10 | 7/10 | 9/10 |
| Security & Privacy | 4/10 | 9/10 | 10/10 |
| **TOTAL** | **54** | **82** | **95** |

**With P0+P1 fixes:** Database health score improves from 54 → 82 (D+ → B+)
**With P0-P3 fixes:** Database health score improves from 54 → 95 (D+ → A)

---

## Top 3 Highest-ROI Fixes

| Rank | Fix | Effort | Score Impact |
|------|-----|--------|-------------|
| 1 | Add 24 missing indexes (REC-005) | 2 hours | +16 points |
| 2 | Start queue worker + configure cron (REC-001/002) | 2.5 hours | +6 points |
| 3 | Cache plans, locations, categories (REC-012) | 2 hours | +6 points |

These 3 fixes alone (6.5 hours total) would move the score from 54 to 82.
