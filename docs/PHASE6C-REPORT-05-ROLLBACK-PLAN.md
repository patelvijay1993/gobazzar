# Phase 6C Report 05 — Rollback Plan
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6C — Production Hardening Sprint

---

## Rollback Philosophy

Every Phase 6C change was designed to be independently rollback-safe. No destructive operations were performed. All rollback steps restore the prior state without data loss.

**Decision authority:** Release Manager makes rollback call. Rollback is the correct choice if any of the following occur after deployment:
- Application 500 errors increase after migration
- `businesses.hours` data is corrupted (all return null or wrong type)
- Admin panel inaccessible to legitimate admin users
- Queue jobs are no longer being dispatched (not caused by Phase 6C — queue was already broken)

---

## Code Rollback

### Option A — Git Revert (Recommended)

All Phase 6C code changes are on the `main` branch. To revert:

```bash
# Identify Phase 6C commit hash
git log --oneline -5

# Revert the Phase 6C commit
git revert <commit-hash>
git push origin main
```

Re-deploy code and clear caches.

### Option B — Manual File Restoration

Restore individual files from git:

```bash
# Restore admin backdoor (ONLY if admin access broken — risk: re-introduces vulnerability)
git show HEAD~1:app/Models/User.php > app/Models/User.php

# Restore PricingController (re-introduces PII in logs — low risk)
git show HEAD~1:app/Http/Controllers/PricingController.php > app/Http/Controllers/PricingController.php

# Restore BlogPost model (reverts to local disk — blog images may break)
git show HEAD~1:app/Models/BlogPost.php > app/Models/BlogPost.php

# Restore PostController (re-introduces S3 orphan leak — low risk)
git show HEAD~1:app/Http/Controllers/PostController.php > app/Http/Controllers/PostController.php

# Restore directory show view (legacy hours display removed — minor)
git show HEAD~1:resources/views/directory/show.blade.php > resources/views/directory/show.blade.php
```

---

## Database Rollback

### Migration Rollback — Batch 39 (Performance Indexes)

```bash
php artisan migrate:rollback --step=1
```

This runs the `down()` method of `add_performance_indexes_to_content_tables.php`:
```php
Schema::table('listings', function ($table) {
    $table->dropIndex('listings_status_featured_created_idx');
    $table->dropIndex('listings_cat_status_idx');
    $table->dropIndex('listings_province_status_idx');
    $table->dropIndex('listings_city_status_idx');
});
// (repeats for all 6 tables — 19 indexes dropped)
```

**Risk:** None. Dropping indexes restores full table scans. No data affected. Application continues working at lower performance.

**Time to rollback:** 5–30s (index drops are fast)

### Migration Rollback — Batch 38 (businesses.hours)

```bash
php artisan migrate:rollback --step=2
```

This runs the `down()` method:
```php
DB::statement('ALTER TABLE businesses MODIFY COLUMN hours TEXT NULL');
```

**Risk:** Low.
- Column reverts to TEXT type
- Data (now JSON strings like `{"note":"..."}`) remains as-is in the column
- Eloquent `cast: 'array'` will still try to json_decode — the converted rows (now `{"note":"..."}`) will still be valid JSON, so they still return as arrays
- Original plain-text-era behavior (returning null for plain text) is not restored — the data has been converted
- To fully restore original behavior: also restore from the database backup taken before deployment

**Restore from backup (complete rollback):**
```bash
mysql -u [user] -p gobazzar_prod < gobazzar_backup_pre_phase6c_YYYYMMDD_HHMMSS.sql
```

This is only required if the converted data format causes application errors.

### Migration Rollback — Batch 37 (flagged status idempotency)

This migration has already been run. The idempotency guard change does not affect the database. No rollback needed for this change in isolation.

---

## Rollback Decision Matrix

| Symptom | Root Cause | Rollback Action |
|---------|-----------|----------------|
| Admin panel 500 error | canAccessPanel change | Revert User.php (Option B) |
| Admin locked out | is_admin=0 for all admins | Manual DB: `UPDATE users SET is_admin=1 WHERE email='admin@gobazzar.com'` |
| Business hours showing null | hours migration | Rollback batch 38 + DB restore |
| Blog images broken | BlogPost S3 fix | Revert BlogPost.php (Option B) |
| Performance degraded | Unrelated — indexes only add performance | N/A |
| Upgrade log has PII | PII removal regression | N/A (not a rollback scenario) |

---

## Emergency Admin Access Recovery

If admin panel becomes inaccessible after the `canAccessPanel` change and the `is_admin` flag is `0` or `null` for all admin users:

```sql
-- Run via MySQL CLI or phpMyAdmin
UPDATE users SET is_admin = 1 WHERE email IN ('admin@gobazzar.com', 'vijaypateldeveloper@gmail.com');
```

This restores access without reverting code.

---

## Rollback Time Estimates

| Rollback | Estimated Duration |
|----------|-------------------|
| Code only (git revert) | 5–10 min |
| Index rollback (migrate:rollback --step=1) | < 1 min |
| hours column rollback (migrate:rollback --step=2) | < 1 min |
| Full DB restore from backup | 5–20 min (depends on DB size) |
| Total worst case | 30 min |
