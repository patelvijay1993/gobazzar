# Phase 8 Report 04 — Deployment Package
**Project:** GoBazaar  
**Date:** 2026-07-05  
**Phase:** 8 — Enterprise Production Certification & Final Release Sign-Off  
**Role:** Release Manager, Production Support Manager  
**Classification:** Operational Runbook — Production Deployment

---

## Environment Reference

| Item | Value |
|------|-------|
| Production host | heavendwell.com shared cPanel |
| Production path | `/home/heavendw/public_html/gobazzarweb.heavendwell.com/` |
| PHP version required | 8.2+ |
| Database | MariaDB / MySQL |
| Queue driver | database |
| Storage | AWS S3 |
| Payments | Stripe |

---

## A. PRE-DEPLOYMENT CHECKLIST

Complete ALL items before uploading any files to production.

### Code Readiness
```
□ A-01  Git status clean — no uncommitted changes on main branch
□ A-02  php artisan test (if test suite exists) — all pass
□ A-03  php -l on all modified PHP files — no syntax errors
□ A-04  composer install --no-dev --optimize-autoloader verified locally
□ A-05  All Phase 7 Production Blockers documented and planned for resolution
□ A-06  Code tag / release commit created: git tag v1.0.0
```

### Server Readiness
```
□ A-07  PHP 8.2+ confirmed on production server (php -v)
□ A-08  Required PHP extensions confirmed: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, gd
□ A-09  Production .env file prepared with correct values (NOT copied from local)
□ A-10  APP_ENV=production verified in production .env
□ A-11  APP_DEBUG=false verified in production .env
□ A-12  APP_KEY set (run: php artisan key:generate if new install)
□ A-13  DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD set correctly
□ A-14  AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, AWS_BUCKET set
□ A-15  STRIPE_KEY, STRIPE_SECRET, STRIPE_WEBHOOK_SECRET set
□ A-16  MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS set
□ A-17  APP_URL set to https://gobazzarweb.heavendwell.com
```

### Database Readiness
```
□ A-18  Production database created and credentials tested
□ A-19  Database backup strategy confirmed (see Section F, Step 1)
□ A-20  Full backup of any existing production data taken BEFORE deployment
□ A-21  Backup restore test completed — confirmed a restore works
```

### Third-Party Configuration
```
□ A-22  Stripe webhook endpoint updated to: https://gobazzarweb.heavendwell.com/stripe/webhook
□ A-23  Stripe webhook events confirmed: checkout.session.completed, customer.subscription.updated, customer.subscription.deleted, invoice.payment_succeeded
□ A-24  AWS S3 bucket CORS policy allows requests from production domain
□ A-25  AWS S3 bucket policy allows public read on required prefixes (or pre-signed URLs configured)
```

---

## B. DEPLOYMENT CHECKLIST

Execute in this exact order. Do not skip steps. Do not proceed if a step fails.

### Step 1 — Backup Current State (if upgrading)
```bash
# If this is an upgrade (not a fresh install):
mysqldump -u [prod_user] -p gobazzar_prod > backup_pre_deploy_$(date +%Y%m%d_%H%M).sql
gzip backup_pre_deploy_*.sql
# Upload to S3 or copy to safe location
```

### Step 2 — Upload Code
```bash
# Via Git (preferred):
cd /home/heavendw/public_html/gobazzarweb.heavendwell.com
git pull origin main

# Via SFTP/FTP (alternative):
# Upload all files except: .env, vendor/, node_modules/, storage/
# Never overwrite production .env with local .env
```

### Step 3 — Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### Step 4 — Clear All Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Step 5 — Run Migrations
```bash
# Verify current state first:
php artisan migrate:status

# Run migrations:
php artisan migrate --force

# Verify all ran:
php artisan migrate:status
# Expected: all 46 migrations show "Ran"
```

### Step 6 — Build Production Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Note:** Run these AFTER migrations. Config cache captures env values — must rebuild if .env changes.

### Step 7 — Create Storage Symlink
```bash
php artisan storage:link
# Verify: ls -la public/storage → should show symlink to ../storage/app/public
```

### Step 8 — Set File Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Step 9 — Clear Stale Queue Jobs
```bash
php artisan queue:clear
# Expected output: 26 jobs deleted (or 0 if already cleared)
```

### Step 10 — Configure Queue Worker (Supervisor)
```ini
# /etc/supervisor/conf.d/gobazzar-worker.conf
[program:gobazzar-worker]
command=php /home/heavendw/public_html/gobazzarweb.heavendwell.com/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
directory=/home/heavendw/public_html/gobazzarweb.heavendwell.com
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=heavendw
numprocs=1
redirect_stderr=true
stdout_logfile=/home/heavendw/logs/gobazzar-worker.log
stopwaitsecs=3600
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start gobazzar-worker
supervisorctl status gobazzar-worker
# Expected: gobazzar-worker RUNNING
```

### Step 11 — Configure Scheduler Cron
```bash
# Add via crontab -e:
* * * * * cd /home/heavendw/public_html/gobazzarweb.heavendwell.com && php artisan schedule:run >> /dev/null 2>&1

# Verify no scheduled commands exist first:
php artisan schedule:list
```

### Step 12 — Configure Database Backup Cron
```bash
# Add via crontab -e:
0 2 * * * mysqldump -u [prod_user] -p[prod_pass] gobazzar_prod | gzip > /home/heavendw/backups/gobazzar_$(date +\%Y\%m\%d).sql.gz && aws s3 cp /home/heavendw/backups/gobazzar_$(date +\%Y\%m\%d).sql.gz s3://[backup-bucket]/gobazzar/
```

### Step 13 — Verify HTTPS
```bash
# Confirm SSL certificate is active:
curl -I https://gobazzarweb.heavendwell.com
# Expected: HTTP/2 200 (or 301/302 redirect from http to https)
```

---

## C. POST-DEPLOYMENT CHECKLIST

Complete within 1 hour of deployment. These verify the deployment succeeded.

```
□ C-01  Homepage loads without error: https://gobazzarweb.heavendwell.com
□ C-02  User registration completes (create test@test.com)
□ C-03  User login works
□ C-04  Create a classified listing — S3 upload succeeds, image renders
□ C-05  Listing appears on /classifieds with correct data
□ C-06  Admin panel loads: https://gobazzarweb.heavendwell.com/admin
□ C-07  Admin can see and approve/reject listings
□ C-08  Pricing page loads (/pricing), plan details correct
□ C-09  No errors in storage/logs/laravel.log: tail -f storage/logs/laravel.log
□ C-10  Queue worker running: supervisorctl status gobazzar-worker
□ C-11  Config cache active: php artisan config:show app | grep debug → false
□ C-12  APP_DEBUG=false confirmed: if laravel.log is clean — no debug output
□ C-13  Storage symlink works: ls -la public/storage
□ C-14  Stripe test: initiate checkout flow (use Stripe test card 4242 4242 4242 4242)
□ C-15  Stripe webhook received: check laravel.log for webhook success entry
□ C-16  S3 image URL resolves: right-click uploaded image → URL starts with s3.amazonaws.com or cloudfront
```

---

## D. SMOKE TEST CHECKLIST

Execute after post-deployment checklist passes. This is the go/no-go gate.

| Test | Steps | Expected Result |
|------|-------|----------------|
| **Home page** | Visit / | Loads with listings, events, businesses, blog visible |
| **Registration** | /register with new email | Account created, redirected to dashboard |
| **Login** | /login with test account | Dashboard accessible |
| **Create listing** | POST /post/classified | Listing created, appears in /classifieds |
| **View listing** | Click listing | Detail page loads with image from S3 |
| **Search** | /classifieds?search=test | Results or empty state displayed without error |
| **Business directory** | /directory | Directory loads with filter |
| **Job listings** | /jobs | Jobs index loads |
| **Events** | /events | Events index loads |
| **Blog** | /blog | Blog index loads with post image from S3 |
| **Matrimonial** | /matrimonial | Index loads |
| **Chat** | Start conversation with different user | Message sent, stored in DB |
| **Favorites** | Toggle favorite on listing | Toggle succeeds (plan gated if free) |
| **Pricing** | /pricing | Three plans displayed with correct pricing |
| **Admin login** | /admin with admin user | Admin panel accessible |
| **Admin listings** | /admin/listings | Listings table visible |
| **Admin approve** | Approve a listing | Status changes to active |
| **Plan upgrade** | Initiate Stripe checkout | Redirected to Stripe, payment form loads |
| **Logout** | POST /logout | Session terminated, redirected to home |

**PASS criteria:** All 19 smoke tests pass with no errors in laravel.log.

---

## E. ROLLBACK CHECKLIST

Execute if smoke tests fail or critical errors are observed post-deployment.

### Immediate Assessment (first 5 minutes)
```
□ E-01  Review laravel.log: tail -50 storage/logs/laravel.log
□ E-02  Identify whether failure is: code bug | migration failure | config error | missing dependency
□ E-03  If config error (env variable): fix .env, rebuild cache, retry — no rollback needed
□ E-04  If migration failure: php artisan migrate:rollback --step=1; fix; re-attempt
```

### Code Rollback — Option A (Git, preferred)
```bash
# Identify the previous working commit:
git log --oneline -10

# Roll back to prior commit:
git checkout [previous-commit-hash] -- .

# Rebuild caches:
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify: smoke test homepage
```

### Migration Rollback (if Phase 6C migrations are the issue)
```bash
# Step 1: Rollback index migration (most recent)
php artisan migrate:rollback --step=1

# Step 2: Rollback hours migration (if needed)
php artisan migrate:rollback --step=2
```

### Emergency Admin Access Recovery
```sql
-- If admin access is lost:
UPDATE users SET is_admin = 1 WHERE email IN ('your-admin@email.com');
```

### Database Restore (only if data is corrupted)
```bash
# Stop queue worker first:
supervisorctl stop gobazzar-worker

# Drop and recreate (if fresh restore needed):
mysql -u prod_user -p gobazzar_prod < /path/to/backup_pre_deploy.sql

# Restart worker after restore:
supervisorctl start gobazzar-worker
```

### Rollback Verification
```
□ E-05  Homepage loads after rollback
□ E-06  Admin panel accessible
□ E-07  No new errors in laravel.log
□ E-08  Queue worker status: running
□ E-09  Incident documented with timeline and root cause
```

---

## F. PRODUCTION MONITORING CHECKLIST

### Daily Checks (Automated — set up alerts)
```
□ F-01  Application uptime — configure UptimeRobot or similar to ping / every 5 minutes
□ F-02  Database backup completed — check S3 for today's backup file
□ F-03  Failed jobs — query: SELECT COUNT(*) FROM failed_jobs WHERE created_at > NOW() - INTERVAL 1 DAY
□ F-04  Error rate — check laravel.log for ERROR entries
```

### Weekly Checks (Manual)
```
□ F-05  Review laravel.log for unexpected patterns
□ F-06  Check failed_jobs table: SELECT * FROM failed_jobs ORDER BY id DESC LIMIT 20
□ F-07  Check jobs table (stale jobs): SELECT COUNT(*) FROM jobs WHERE created_at < NOW() - INTERVAL 1 HOUR
□ F-08  Review user registration rate and listing creation rate (growth health)
□ F-09  Verify backup restore: restore latest backup to staging environment
□ F-10  Check storage/logs/ disk usage on server
```

### Monthly Checks
```
□ F-11  Review S3 storage costs — confirm no unexpected object accumulation
□ F-12  Stripe payment reconciliation — confirm payment_history matches Stripe Dashboard
□ F-13  Review application logs for patterns indicating abuse (report spam, auth brute force)
□ F-14  Rotate Laravel application key if security incident occurred
□ F-15  Review PHP and Laravel versions for security patches
□ F-16  Review Composer dependencies: composer audit
```

### Post-Incident
```
□ F-17  Document incident: what failed, when, impact, how resolved
□ F-18  Review database backup: was it sufficient for recovery?
□ F-19  Update runbook with lessons learned
□ F-20  Assess whether monitoring gaps allowed incident to grow before detection
```

---

## G. MONITORING SETUP (Recommended — Not Confirmed Active)

| Tool | Purpose | Recommended |
|------|---------|------------|
| UptimeRobot (free) | Availability monitoring, alerts | Yes — set up before launch |
| Laravel Telescope | Request/query/job debugging | Yes — install in staging only |
| Sentry (or Bugsnag) | Exception tracking with stack traces | Yes — captures errors without APP_DEBUG=true |
| AWS CloudWatch | S3 access logs, billing alerts | Yes |
| Stripe Dashboard | Payment monitoring | Already available |

---

## H. HEALTH CHECK ENDPOINTS (Recommended Implementation)

Not currently implemented. Recommended for Phase 8+ or post-launch sprint:

```php
// Route::get('/health', function () {
//     return response()->json([
//         'status' => 'ok',
//         'db' => DB::connection()->getPdo() ? 'connected' : 'failed',
//         'queue' => (int) DB::table('jobs')->count() . ' pending',
//         'timestamp' => now()->toISOString(),
//     ]);
// });
```
