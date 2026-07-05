# Phase 6 Report 12 — Production Recommendations
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Recommendation Priority Summary

| Priority | Count | Description |
|----------|-------|-------------|
| P0 — Before any users see production | 4 | Security and ops blockers |
| P1 — Before public launch | 8 | Code and infrastructure |
| P2 — Within 2 weeks of launch | 7 | Reliability and data integrity |
| P3 — Within 1 month | 6 | Performance and scalability |

---

## P0 — Before Any Users See Production

### REC-001 — Start Queue Worker (Supervisor)

**Problem:** Chat events are queued but never processed. Real-time chat is broken.

**Action:** Install Supervisor on the production server and create a config:
```ini
[program:gobazzar-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
numprocs=2
```

Run: `supervisorctl reread && supervisorctl update && supervisorctl start gobazzar-worker:*`

**Before clearing stale queue:** Run `php artisan queue:clear` to discard the 26 stale `MessageSent` events from development.

---

### REC-002 — Configure Laravel Scheduler Cron

**Problem:** Expired listings remain visible; expired content never purged.

**Action:** Add to server crontab:
```bash
crontab -e
# Add:
* * * * * cd /home/heavendw/public_html/gobazzarweb.heavendwell.com && php artisan schedule:run >> /dev/null 2>&1
```

Verify it's working: `php artisan schedule:list` shows both commands with next run time.

---

### REC-003 — Remove Admin Backdoor (canAccessPanel)

**Problem:** User with ID=1 has unconditional admin access regardless of `is_admin` flag.

**Action:** In `app/Models/User.php`, change:
```php
// Before:
return $this->is_admin || $this->id === 1;

// After:
return $this->is_admin === true;
```

Verify: Check that your actual admin user has `is_admin=1` in the database before making this change.

---

### REC-004 — Verify APP_DEBUG=false in Production .env

**Problem:** APP_DEBUG=true in development; if copied to production, stack traces visible to users.

**Action:** Production `.env` must have:
```
APP_DEBUG=false
APP_ENV=production
```

Add to deployment checklist: `php artisan about | grep -E "debug|environment"` as a pre-launch gate.

---

## P1 — Before Public Launch

### REC-005 — Add Performance Indexes Migration

**Problem:** 24 critical filter columns have no indexes. Query degradation inevitable.

**Migration to create:**
```php
Schema::table('listings', function (Blueprint $table) {
    $table->index(['status', 'is_featured', 'created_at'], 'listings_status_featured_created_idx');
    $table->index(['category_id', 'status'], 'listings_cat_status_idx');
    $table->index(['province', 'status'], 'listings_province_status_idx');
    $table->index(['city', 'status'], 'listings_city_status_idx');
});
// Repeat for businesses, job_listings, events, matrimonials
```

**Impact:** Reduces query time from 50–100ms to 1–3ms at 100,000 rows. Single highest-ROI fix.

---

### REC-006 — Fix businesses.hours Column + Data Migration

**Problem:** Column is `text`; model casts as `'array'`; existing data is plain text strings (not JSON). Hours display is broken for all businesses.

**Step 1 — Data migration:**
```php
// In migration up():
DB::table('businesses')->whereNotNull('hours')->get()
    ->each(function($biz) {
        $hours = $biz->hours;
        // If it's already JSON, skip. Otherwise encode as {note: "..."} structure
        if (json_decode($hours) === null && json_last_error() !== JSON_ERROR_NONE) {
            DB::table('businesses')->where('id', $biz->id)
                ->update(['hours' => json_encode(['note' => $hours])]);
        }
    });
```

**Step 2 — Alter column:**
```php
DB::statement('ALTER TABLE businesses MODIFY COLUMN hours JSON NULL');
```

**Note:** The existing plain-text hours ("Mon–Sun: 11am–10pm") will be stored as `{"note": "Mon–Sun: 11am–10pm"}` pending a proper hours UI redesign.

---

### REC-007 — Remove PII From Application Logs

**Problem:** PricingController logs user email + phone in plaintext.

**Action:** In `app/Http/Controllers/PricingController.php`:
```php
// Before:
\Log::info('Upgrade request', [
    'user_id' => auth()->id(),
    'plan'    => $data['plan'],
    'name'    => $data['name'],
    'email'   => $data['email'],
    'phone'   => $data['phone'] ?? '',
]);

// After:
\Log::info('Upgrade request submitted', [
    'user_id' => auth()->id(),
    'plan'    => $data['plan'],
]);
```

The full contact details are already captured in the `advertise_requests` table (if this controller saves there) or in the validated form data. The log entry needs only the plan and user for debugging.

---

### REC-008 — Set Up Automated Database Backup

**Problem:** No backup strategy — server failure = complete data loss.

**Option A (Recommended — Managed DB):**
Migrate MySQL to Amazon RDS or PlanetScale:
- RDS: Enable automated daily snapshots, 7-day retention, PITR
- Cost: ~$25–50/month for db.t3.micro RDS MySQL

**Option B (Self-managed):**
```bash
# /etc/cron.d/gobazzar-backup
0 2 * * * root mysqldump --single-transaction -u gobazzar -p$PASS gobazzar | gzip | aws s3 cp - s3://gobazzar-backups/gobazzar_$(date +\%Y\%m\%d).sql.gz
```
Test restore monthly: `zcat gobazzar_backup.sql.gz | mysql gobazzar_test`

---

### REC-009 — Fix S3 throw=false / Silent Upload Failures

**Problem:** Failed S3 uploads silently succeed — listing saved without images.

**Action:** In `config/filesystems.php`, change for production:
```php
's3' => [
    'throw' => env('APP_ENV') === 'production' ? true : false,
]
```

And wrap upload calls in PostController:
```php
try {
    $path = $file->store('listings', 's3');
    if (!$path) throw new \RuntimeException('S3 upload returned false');
} catch (\Exception $e) {
    // Delete already-uploaded files in this batch
    foreach ($uploadedPaths as $p) Storage::disk('s3')->delete($p);
    return back()->withErrors(['images' => 'Image upload failed. Please try again.']);
}
```

---

### REC-010 — Test migrate:fresh on Clean Database

**Problem:** Partial migration leaves dirty state; fresh DB deployment may fail.

**Action:** On a test environment (NOT production):
```bash
php artisan migrate:fresh --seed
```

Fix any failures in the migration sequence. Specifically:
- `add_flagged_status_to_content_tables.php` contains the comment about 'flagged' already existing — verify this runs cleanly on a fresh DB
- If it fails: rewrite the migration to use a `MODIFY COLUMN` with the complete enum list

---

### REC-011 — Configure S3 Versioning and Cross-Region Replication

**Problem:** S3 versioning status unknown — accidental deletes unrecoverable.

**Action (AWS Console):**
1. S3 → Bucket → Properties → Versioning → Enable
2. S3 → Management → Replication → Add rule → Destination: secondary region bucket
3. Lifecycle rule: transition to IA after 90 days (cost savings)

---

### REC-012 — Cache High-Traffic Read-Only Data

**Problem:** Plans, locations, categories re-fetched from DB on every request.

**Actions (in priority order):**

```php
// Plan::active() — cache for 10 minutes
return Cache::remember('plans_active', 600, fn() => ...);

// Location::activeProvinces() — cache for 1 hour
return Cache::remember('locations_provinces', 3600, fn() => ...);

// Location::activeCities($province) — cache for 1 hour
return Cache::remember('locations_cities_'.$province, 3600, fn() => ...);

// Category queries — cache for 10 minutes per type
return Cache::remember('categories_'.$type, 600, fn() => ...);
```

Add cache invalidation in Filament `saved()` hooks for Location, Category, and Plan resources.

---

## P2 — Within 2 Weeks of Launch

### REC-013 — Add SoftDeletes to Content Models

Prevents accidental unrecoverable data loss. Add `deleted_at` + `SoftDeletes` trait to:
`Listing`, `Business`, `Job` (job_listings), `Event`, `Matrimonial`, `BlogPost`, `BusinessPost`

### REC-014 — Fix Gallery Image Cleanup in Purge Command

Update `PurgeExpiredPosts` to iterate `$r->images` array and delete each S3 path.

### REC-015 — Memoize activePlan() Per Request

Add a `?string $cachedPlan` property to `User` model. Set on first call, reuse for the request lifecycle. Move `maybeResetCredits()` to a monthly scheduler command.

### REC-016 — Clean Orphan Content (Business ID=1, Jobs 15/16/17)

Remove via admin panel: business ID=1 (`fczxczx`), job listings 15, 16, 17. These are test records with no owner that are publicly visible.

### REC-017 — Clear Stale Queue Backlog Before Launch

```bash
php artisan queue:clear
```
Clear the 26 stale `MessageSent` development events before the queue worker starts processing them in production.

### REC-018 — Install Error Monitoring (Sentry)

```bash
composer require sentry/sentry-laravel
php artisan sentry:install
```

Free tier handles up to 5,000 errors/month. Add `SENTRY_LARAVEL_DSN=...` to production `.env`.

### REC-019 — Configure Uptime Monitoring

Register domain at UptimeRobot (free) or Pingdom. Monitor:
- Main URL (200 check)
- `/up` health endpoint (Laravel health route)
- Alert to developer email/SMS on downtime

---

## P3 — Within 1 Month

### REC-020 — Switch Cache and Sessions to Redis

```bash
# Production server:
apt-get install redis-server
composer require predis/predis

# .env:
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
```

Redis eliminates the overhead of using MySQL for cache and sessions, and enables horizontal scaling.

### REC-021 — Add Full-Text Search

```sql
ALTER TABLE listings ADD FULLTEXT INDEX listings_fulltext (title, description);
ALTER TABLE businesses ADD FULLTEXT INDEX businesses_fulltext (name, description);
ALTER TABLE job_listings ADD FULLTEXT INDEX jobs_fulltext (title, company, description);
```

Or integrate Meilisearch for advanced relevance scoring:
```bash
composer require laravel/scout meilisearch/meilisearch-php
php artisan scout:import "App\Models\Listing"
```

### REC-022 — Add Pagination to Account Page

`UserController::account()` loads all user records with `->get()`. Add `->paginate(20)` for each content type.

### REC-023 — Implement Admin Moderation Queue UI

28 pending flagged posts are unreviewed. Build a Filament resource or simple admin view to:
- List pending flagged posts with title/description/reason
- Approve (allow the content) or Confirm flag (block the user)

### REC-024 — Migrate Blog Images to S3

`BlogPost::getImageUrlAttribute()` uses the local public disk. Align with other models by uploading blog images to S3 and using `Storage::disk('s3')->url()`.

### REC-025 — Add CloudFront CDN for S3 Images

S3 direct access adds latency for every image request (each image hits the origin). Configure CloudFront distribution pointing to the S3 bucket. Update `AWS_URL` in `.env` to the CloudFront domain.

Estimated latency improvement: 200–500ms per image → 20–50ms (CDN edge cache).
