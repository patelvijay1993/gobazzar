# Phase 6B Report 07 — Updated Risk Register
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6B — Enterprise Architecture Remediation Review  
**Fix Policy:** Analysis and recommendation only.

---

## Risk Rating Change Log

This register inherits all 25 risks from Phase 6A Report 11 and applies Phase 6B severity reclassifications. New columns added: Phase 6B Group, Revised Rating, Status.

---

## Updated Risk Register

| ID | Risk | Likelihood | Phase 6A Rating | Phase 6B Rating | Group | Status |
|----|------|-----------|----------------|----------------|-------|--------|
| RISK-001 | APP_DEBUG=true in production .env | Possible | High | **High** | D | OPS action required: verify production .env |
| RISK-002 | Queue worker not started — chat broken day 1 | Certain | Critical | **High** | A | **PRODUCTION BLOCKER** |
| RISK-003 | Scheduler cron not configured | Likely | Critical | **High** | D | OPS action required: configure cron |
| RISK-004 | No database backup — total data loss risk | Possible | High | **Critical** | D | OPS action required: configure backup |
| RISK-005 | 24 missing indexes — severe degradation at scale | Certain | Critical | **High** | A | **PRODUCTION BLOCKER** |
| RISK-006 | User ID=1 backdoor in canAccessPanel() | Possible | High | **Critical** | A | **PRODUCTION BLOCKER** |
| RISK-007 | businesses.hours cast mismatch — hours display broken | Certain | Critical | **High** | A | **PRODUCTION BLOCKER** |
| RISK-008 | S3 throw:false — failed uploads silently succeed | Possible | High | **High** | B | Fix before launch recommended |
| RISK-009 | activePlan() DB write on every authenticated request | Certain | Critical | **High** | B | Fix before launch recommended |
| RISK-010 | Orphan business+jobs publicly visible | Certain | High | **Medium** | B | Clean before launch |
| RISK-011 | User 5: power_seller with no Stripe subscription | Certain | High | **High** | B | Investigate before launch |
| RISK-012 | PII (email, phone) logged in plaintext | Certain | High | **High** | A | **PRODUCTION BLOCKER** |
| RISK-013 | 26 stale queue jobs fire on worker start | Likely | High | **Medium** | A | Clear with queue:clear (part of blocker fix) |
| RISK-014 | Gallery images leak to S3 after expiry/update | Certain | Medium | **Low** | C | Defer — storage cost at current scale is negligible |
| RISK-015 | Dirty migration — fresh deploy may fail | Possible | High | **High** | A | **PRODUCTION BLOCKER** |
| RISK-016 | Blog images on local disk — not S3 backed up | Certain | Medium | **Medium** | B | Fix before launch |
| RISK-017 | No soft deletes — accidental deletion unrecoverable | Possible | High | **Medium** | C | Defer post-launch (2–4 weeks) |
| RISK-018 | Search LIKE leading wildcard — full scan | Certain | Critical | **Medium** | C | Defer — at launch scale (100 users, 1K listings) this is acceptable |
| RISK-019 | Database cache driver adds DB load | Certain | Medium | **Low** | C | Acceptable at launch scale; defer Redis migration |
| RISK-020 | File sessions — cannot horizontal scale | Possible | Medium | **Low** | C | Not relevant until Tier 2+ |
| RISK-021 | No APM/monitoring — production issues invisible | Certain | High | **Medium** | C | Add Sentry week 1 post-launch |
| RISK-022 | payment_history plan_slug no FK | Unlikely | Low | **Low** | E | No action — correct design |
| RISK-023 | advertise_requests.status no enum | Unlikely | Low | **Low** | C | Safe to defer |
| RISK-024 | 28 pending flagged posts unreviewed | Certain | Medium | **Low** | C | Admin UX improvement, defer |
| RISK-025 | External URLs in S3 path columns | Possible | Medium | **Low** | E | Dev seed data, no production impact |

---

## Risk Heat Map (Updated)

```
              IMPACT
              Low       Medium     High      Critical
L  C  |                 RISK-014  RISK-002  
I  e  |                 RISK-010  RISK-005  
K  r  |                 RISK-013  RISK-007  
E  t  |                 RISK-016  RISK-009  RISK-004
L  a  |                 RISK-018  RISK-011  RISK-006
I  i  |                 RISK-021  RISK-012  
H  n  |                           RISK-015  
O  ─  |  RISK-019  RISK-017  RISK-008  RISK-001
O  L  |  RISK-014           RISK-003  
D  i  |                           
   k  |                           
      |                           
   P  |  RISK-020  RISK-023       
   o  |  RISK-022                 
   s  |                           
   U  |  RISK-025  RISK-024       
```

---

## Risk Count by Phase 6B Rating

| Rating | Phase 6A Count | Phase 6B Count | Change |
|--------|---------------|---------------|--------|
| Critical | 8 | 2 | -6 |
| High | 11 | 11 | 0 |
| Medium | 5 | 6 | +1 |
| Low | 1 | 6 | +5 |

---

## Risks Confirmed as Production Blockers (Group A)

| Risk | Rating | Why Blocker |
|------|--------|-------------|
| RISK-002 | High | Chat broken — queue worker not running |
| RISK-005 | High | Missing indexes — zero-risk fix, must add before launch |
| RISK-006 | Critical | Authorization bypass — first user gets admin |
| RISK-007 | High | Business hours display broken for all businesses |
| RISK-012 | High | PII in logs — compliance violation |
| RISK-013 | Medium | Stale queue jobs — part of queue worker setup |
| RISK-015 | High | Dirty migration — deploy may fail |

---

## Risks Requiring Attention But Not Blockers (Group B)

| Risk | Rating | Recommended Action |
|------|--------|-------------------|
| RISK-008 | High | Add S3 error handling before launch |
| RISK-009 | High | Memoize activePlan() before launch |
| RISK-010 | Medium | Clean orphan test content (admin action) |
| RISK-011 | High | Investigate user 5 Stripe inconsistency |
| RISK-016 | Medium | Fix BlogPost to use S3 disk |

---

## Risks Safely Deferred Post-Launch (Group C)

| Risk | Rating | Rationale |
|------|--------|-----------|
| RISK-014 | Low | S3 cost negligible at current scale |
| RISK-017 | Medium | Soft deletes are best practice, not day-one requirement |
| RISK-018 | Medium | Search LIKE is slow at scale; at 100 users / 1K listings, acceptable |
| RISK-019 | Low | Database cache is adequate for Tier 1 |
| RISK-020 | Low | File sessions are fine for single-server launch |
| RISK-021 | Medium | Add Sentry week 1 post-launch |
| RISK-023 | Low | Application controls advertise_requests.status values |
| RISK-024 | Low | Admin moderation UI is a feature, not a blocker |

---

## Risks Reclassified as Non-Issues (Group E)

| Risk | Phase 6A Rating | Phase 6B Decision |
|------|----------------|-------------------|
| RISK-022 | Low | Correct design — payment_history intentionally decoupled from plans |
| RISK-025 | Medium | Seed data only — correct accessor handles it |
