# Phase 6B Report 04 — Operational Recommendations (Group D)
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6B — Enterprise Architecture Remediation Review  
**Fix Policy:** Analysis and recommendation only. No code, database, configuration, or migration changes.

---

## Definition

**Group D — Operational Recommendation:** DevOps / Infrastructure tasks. Not a software defect. The application code is correct. The server, cloud, or deployment configuration requires setup.

---

## Operational Items (7 Total)

---

### OPS-1: QUEUE-002 — Configure Laravel Scheduler Cron

**Finding ID:** QUEUE-002  
**Severity:** High  
**Owner:** DevOps / System Administrator  

**What needs to happen:**  
The Laravel scheduler is correctly registered in `bootstrap/app.php` with two commands:
- `listings:mark-expired` — runs hourly, marks expired content as `status=expired`
- `posts:purge-expired` — runs daily at midnight, permanently deletes expired records

The code is correct. The production server cron job has not been configured.

**Impact if not done:**  
- Expired listings remain `status=active` and are publicly visible past their expiry date
- Expired content accumulates indefinitely (never purged)
- The premium listing feature (time-limited visibility) is effectively broken
- Users with paid listings that "expired" will see their listings stay visible — creates expectation management issues

**Configuration Required:**
```bash
crontab -e
# Add this line:
* * * * * cd /home/heavendw/public_html/gobazzarweb.heavendwell.com && php artisan schedule:run >> /dev/null 2>&1
```

**Verification:**
```bash
php artisan schedule:list
# Both commands should appear with their next scheduled run times
```

**Estimated Effort:** 30 minutes  
**Requires Code Change:** No  
**Requires Downtime:** No  

---

### OPS-2: LOG-003 — Configure Database Backup Strategy

**Finding ID:** LOG-003  
**Severity:** Critical (but Group D — infrastructure task)  
**Owner:** DevOps / System Administrator  

**What needs to happen:**  
No automated database backup strategy is configured. A server failure, accidental data deletion, or ransomware attack would result in complete, permanent, unrecoverable loss of all GoBazaar data (users, listings, businesses, conversations, payment history).

**Impact if not done:**  
Total business failure on any infrastructure incident. No recovery path exists.

**Recommended Configuration — Option A (Managed DB):**  
Migrate MySQL to Amazon RDS:
- Enable automated daily snapshots
- Set 7-day retention period
- Enable point-in-time recovery (PITR)
- Cost: ~$25–50/month for db.t3.micro

**Recommended Configuration — Option B (Self-Managed Cron):**
```bash
# Daily backup at 2 AM
0 2 * * * mysqldump --single-transaction -u gobazzar_user -p'$DB_PASSWORD' gobazzar_db | gzip > /backups/gobazzar_$(date +\%Y\%m\%d).sql.gz && aws s3 cp /backups/gobazzar_$(date +\%Y\%m\%d).sql.gz s3://gobazzar-backups/
```

**Test Procedure (monthly):**
```bash
# Restore test
zcat gobazzar_backup.sql.gz | mysql gobazzar_test_db
# Verify row counts match
```

**Estimated Effort:** 4 hours  
**Requires Code Change:** No  
**Requires Downtime:** No  
**Priority:** Must be done before any production traffic  

---

### OPS-3: LOG-001 — Verify APP_DEBUG=false in Production .env

**Finding ID:** LOG-001  
**Severity:** High  
**Owner:** Developer / DevOps  

**What needs to happen:**  
The development environment has `APP_DEBUG=TRUE` and `APP_ENV=local` — correct for development. The production `.env` file has not been audited. If the dev `.env` is copied to production without modification, `APP_DEBUG=true` in production exposes full stack traces (including SQL queries, file paths, environment variables) to users on any unhandled exception.

**Verification Command:**
```bash
# On production server:
php artisan about | grep -i debug
# Should show: Debug Mode = OFF
```

**Required Production .env Values:**
```
APP_ENV=production
APP_DEBUG=false
```

**Add to Deployment Checklist:**
```
[ ] Verify APP_DEBUG=false
[ ] Verify APP_ENV=production
[ ] Run: php artisan about | grep debug
```

**Estimated Effort:** 5 minutes  
**Requires Code Change:** No  

---

### OPS-4: LOG-004 — Enable S3 Versioning and Cross-Region Replication

**Finding ID:** LOG-004  
**Severity:** Low (but important for data durability)  
**Owner:** DevOps / AWS Administrator  

**What needs to happen:**  
S3 versioning status cannot be confirmed without AWS console access. Without versioning, accidental deletes or overwrites of S3 objects (profile images, listing photos, event banners) are unrecoverable.

**AWS Console Configuration:**
1. S3 → Bucket → Properties → Versioning → Enable
2. S3 → Management → Replication → Add rule → Destination bucket (secondary region)
3. Lifecycle rule: Transition objects older than 90 days to S3 Infrequent Access (cost savings)
4. Enable bucket access logging for security auditing

**Estimated Effort:** 2 hours  
**Requires Code Change:** No  

---

### OPS-5: LOG-005 — Document Recovery Runbook

**Finding ID:** LOG-005  
**Severity:** Low  
**Owner:** Developer / DevOps  

**What needs to happen:**  
No recovery procedure documentation exists. If the production server fails, whoever responds needs to know exactly what to do.

**Minimum Recovery Runbook:**
```
GoBazaar Recovery Runbook

1. Database Restore:
   zcat gobazzar_YYYYMMDD.sql.gz | mysql -u gobazzar_user -p gobazzar_db
   
2. Run Migrations (if any pending):
   php artisan migrate
   
3. Clear Cache:
   php artisan cache:clear && php artisan config:cache && php artisan route:cache
   
4. Restart Queue Worker (Supervisor):
   supervisorctl start gobazzar-worker:*
   
5. Verify Scheduler Cron:
   crontab -l | grep schedule:run
   
6. Verify S3 Connectivity:
   php artisan tinker → Storage::disk('s3')->put('test.txt', 'ok') → Storage::disk('s3')->delete('test.txt')
   
7. Verify Application:
   curl -I https://gobazzarweb.heavendwell.com/up
   # Should return: 200 OK
```

**Estimated Effort:** 2 hours to document  
**Requires Code Change:** No  

---

### OPS-6: SCALE-T3 — Switch Cache and Sessions to Redis (Tier 3 Preparation)

**Finding ID:** SCALE-T3 (Scalability Report)  
**Severity:** Low (at current launch scale)  
**Owner:** DevOps / Developer  

**What needs to happen:**  
At Tier 1 (100 users), the database cache and file session drivers are adequate. At Tier 2-3 (1,000+ users), they become bottlenecks:
- Database cache adds read load to the same MySQL instance that serves application queries
- File sessions cannot be shared across multiple app servers (prevents horizontal scaling)

**Configuration Required (when Tier 2 is targeted):**
```bash
# Install Redis
apt-get install redis-server
composer require predis/predis

# .env changes:
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

**Estimated Effort:** 4 hours  
**Requires Code Change:** No  
**When to Act:** Before reaching 1,000 concurrent users  

---

### OPS-7: SCALE-T4 — Long-Term Architecture Planning (100K+ Users)

**Finding ID:** SCALE-T4 (Scalability Report)  
**Severity:** Low (future planning)  
**Owner:** Principal Architect  

**What needs to happen:**  
At 100,000 users / 1,000,000 listings, the current single-server architecture requires significant re-architecture. This is not a current production blocker.

**Future Architecture Requirements:**
- Database read replicas (separate MySQL read replica for SELECT-heavy queries)
- Elasticsearch or Meilisearch for listing search
- CDN (CloudFront) for S3 image delivery
- Redis for queue, cache, and sessions
- Database partitioning on `listings` by province or created_at
- Horizontal app server scaling with load balancer
- Separate analytics database for `listing_views` (potentially 100M+ rows)

**Estimated Effort:** 30–60 engineering days (full re-architecture)  
**When to Act:** When Tier 3 (10,000 users) is approaching  

---

## Operational Readiness Checklist

| Item | Owner | Priority | Done? |
|------|-------|----------|-------|
| Configure scheduler cron | DevOps | Before launch | ☐ |
| Configure database backup | DevOps | Before launch | ☐ |
| Verify APP_DEBUG=false in production | Dev/DevOps | Before launch | ☐ |
| Enable S3 versioning | DevOps | Before launch | ☐ |
| Write recovery runbook | Dev/DevOps | Before launch | ☐ |
| Install Supervisor for queue worker | DevOps | Before launch (Group A) | ☐ |
| Clear stale queue jobs | DevOps | Before launch | ☐ |
| Configure uptime monitoring | DevOps | Week 1 | ☐ |
| Configure error monitoring (Sentry) | Dev | Week 1 | ☐ |
| Switch cache/sessions to Redis | DevOps | Pre-Tier 2 | ☐ |
