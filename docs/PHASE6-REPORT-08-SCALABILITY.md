# Phase 6 Report 08 — Scalability Assessment
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Overview

This report models GoBazaar's behavior at four user scales: 100, 1,000, 10,000, and 100,000 concurrent users / 1,000,000 listings. Estimates are based on: measured query times (dev data), known architecture patterns, and the identified missing indexes and caching gaps.

---

## Scale Tier 1: 100 Active Users / 1,000 Listings

### Status: PRODUCTION READY (with known gaps)

| Component | Assessment | Risk |
|-----------|-----------|------|
| Database queries | 1–5ms per query (dev: confirmed) | Low |
| Homepage load | ~50–60 queries, ~200ms total | Low |
| Missing indexes | Negligible at 1,000 rows | Low |
| Queue worker | Must be running | Medium |
| Cache (database) | Adequate | Low |
| S3 images | No issues | None |
| Session (file) | Adequate for single server | Low |

**Verdict:** GoBazaar can comfortably serve 100 concurrent users with current architecture **if** the queue worker is running and the scheduler cron is configured. No code changes required at this scale.

**Estimated capacity:** 100 concurrent users / 1,000 listings — **READY** (with operational fixes).

---

## Scale Tier 2: 1,000 Active Users / 10,000 Listings

### Status: MARGINAL — Performance Work Required

| Component | Assessment | Risk |
|-----------|-----------|------|
| Database queries (no indexes) | ~10–20ms at 10K rows | Medium |
| Homepage (50+ queries × 1,000 users) | 50,000+ queries/min | High |
| `activePlan()` per request | 3–5 DB ops × 1,000 users = 3–5K ops/min | High |
| `maybeResetCredits()` writes | Up to 1K writes/min on users table | High |
| Queue backlog | 26 jobs already pending | Medium |
| Missing indexes | Noticeable degradation | High |
| Cache (database driver) | Cache queries add to DB load | Medium |
| Session (file) | File I/O bottleneck | Medium |

**Bottlenecks at this scale:**
1. Homepage generates 50,000+ DB queries/minute (50 queries × 1,000 req/min)
2. `activePlan()` adds 5,000 DB operations per minute on the `users` table
3. Missing indexes cause full table scans on 10,000-row tables (~10–20ms each)
4. Database queue driver adds cache/queue load to the same MySQL instance

**Required before reaching 1,000 users:**
1. Add composite indexes to all content tables (PERF-001)
2. Cache Plan::active(), Location queries, Category queries (CACHE-002 to CACHE-005)
3. Cache or memoize activePlan() per request (PERF-003)
4. Configure Redis for cache and session drivers
5. Refactor HomeController to reduce query count

**Estimated capacity (current):** ~500 concurrent users before noticeable slowdown.
**Estimated capacity (after fixes):** ~2,000–3,000 concurrent users.

---

## Scale Tier 3: 10,000 Active Users / 100,000 Listings

### Status: REQUIRES ARCHITECTURAL CHANGES

| Component | Estimated Query Time | Issue |
|-----------|---------------------|-------|
| `SELECT * FROM listings WHERE status='active'` | ~50–100ms (no index) | Critical |
| `SELECT * FROM listings WHERE status='active' AND province='ON'` | ~50–100ms | Critical |
| Homepage (50 queries) | ~2.5–5 seconds | Critical |
| Full-text search (`LIKE '%...%'`) | ~100–200ms | High |
| `activePlan()` per request | ~5–10 DB ops × 10K = 100K/min | Critical |
| File sessions | Not usable | Must change to Redis |
| Database queue | Overloaded by 10K users | Must use Redis |

**With indexes added (PERF-001 fix):**
| Query | Time (estimate) |
|-------|---------------|
| `WHERE status='active'` (indexed) | 1–3ms |
| `WHERE status='active' AND province='ON'` (composite) | 1–2ms |
| `WHERE status='active' AND province='ON' AND city='Toronto'` | 1–2ms |
| Homepage load (after caching) | ~50ms |

**Required architectural changes:**
1. **Redis** — for cache, sessions, and queue
2. **Full-text search** — Algolia, Meilisearch, or MySQL FULLTEXT indexes
3. **Read replica** — separate MySQL read replica for SELECT-heavy queries
4. **CDN** — serve S3 images through CloudFront (avoid per-image S3 origin latency)
5. **Homepage caching** — cache rendered homepage data for 60 seconds
6. **Horizontal scaling** — load balancer + multiple app servers (requires Redis sessions)
7. **Database connection pooling** — PgBouncer or ProxySQL

**Estimated capacity (current):** ~1,000 concurrent users before major degradation.
**Estimated capacity (with all fixes):** 10,000–15,000 concurrent users on a single MySQL instance with Redis.

---

## Scale Tier 4: 100,000 Active Users / 1,000,000 Listings

### Status: REQUIRES SIGNIFICANT RE-ARCHITECTURE

At 1 million listings, the current single-table structure (`listings`) becomes challenging:

| Challenge | Impact |
|-----------|--------|
| Table scan on `listings` (no index) | Minutes, not seconds |
| With composite index on (status, province, city) | ~5ms — viable |
| `images` JSON column (up to 10 paths per listing) | 1M listings × 10 images = 10M S3 paths in DB |
| `listing_views` table | Could reach 100M+ rows (one per unique IP per day) |
| `chat_messages` table | Could reach 1B+ rows in an active marketplace |

**Required for 100K users / 1M listings:**

1. **Sharding or partitioning** — partition `listings` by `province` or `created_at` range
2. **Elasticsearch/OpenSearch** — for listing search and filtering at this scale
3. **Separate analytics database** — move `listing_views` to ClickHouse or BigQuery
4. **Message queue (SQS or RabbitMQ)** — replace database queue
5. **Microservices split** — chat service, listing service, and auth service as separate deployments
6. **Read replicas per region** — Canadian geography suggests BC, ON, QC replicas
7. **Image CDN** — CloudFront with intelligent tiering for S3
8. **Database indexes** — all content tables need composite indexes (see PERF-001)

**Estimated capacity (with re-architecture):** 100,000 concurrent users, 1M listings — achievable on AWS with proper setup.

---

## Scalability Summary Table

| Tier | Users | Listings | Ready? | Key Blockers |
|------|-------|----------|--------|-------------|
| Tier 1 | 100 | 1,000 | YES* | Queue worker, scheduler cron |
| Tier 2 | 1,000 | 10,000 | NO | Missing indexes, no caching, queue config |
| Tier 3 | 10,000 | 100,000 | NO | Redis required, full-text search, read replica |
| Tier 4 | 100,000 | 1,000,000 | NO | Major re-architecture required |

*Tier 1 ready with operational fixes only (no code changes)

---

## Immediate Pre-Launch Scalability Actions (Priority Order)

| Priority | Action | Effort | Impact |
|----------|--------|--------|--------|
| P1 | Add composite indexes to all content tables | 2h | Critical — prevents query degradation |
| P1 | Start queue worker (Supervisor) | 2h | Critical — chat broken without it |
| P1 | Configure scheduler cron | 30min | Critical — listing expiry broken without it |
| P2 | Cache Plan::active(), Locations, Categories | 2h | High — eliminates redundant queries |
| P2 | Memoize activePlan() per request | 2h | High — eliminates write amplification |
| P2 | Fix homepage duplicate queries | 4h | High — major query reduction |
| P3 | Switch cache/session to Redis | 4h | Medium — required for Tier 2+ |
| P3 | Add full-text search indexes | 4h | Medium — improves search quality |
| P4 | Homepage data caching | 2h | Medium — major performance gain |
