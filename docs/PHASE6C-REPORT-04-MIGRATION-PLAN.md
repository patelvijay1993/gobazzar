# Phase 6C Report 04 — Migration Plan
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6C — Production Hardening Sprint

---

## Overview

Two new migrations were created in Phase 6C. Both are non-destructive and safe to run on the production database.

---

## Migration Inventory

| Batch | Migration File | Status (Local) |
|-------|---------------|---------------|
| 1–36 | Pre-existing migrations | Ran |
| 37 | 2026_07_01_190917_add_flagged_status_to_content_tables.php (modified) | Ran |
| 38 | 2026_07_04_175351_fix_businesses_hours_column_type.php (new) | Ran |
| 39 | 2026_07_04_175743_add_performance_indexes_to_content_tables.php (new) | Ran |

**Total migrations in local DB:** 46 (all in "Ran" state — confirmed)

---

## Production Deployment Steps

### Pre-Deployment Checklist

- [ ] `APP_DEBUG=false` verified in production `.env`
- [ ] Full database backup taken and verified restorable
- [ ] `php artisan migrate:status` run on production — confirm batches 1–37 all "Ran"
- [ ] `php artisan config:cache` passes locally with production env values

### Step 1 — Backup Production Database

```bash
mysqldump -u [user] -p gobazzar_prod > gobazzar_backup_pre_phase6c_$(date +%Y%m%d_%H%M%S).sql
```

Verify dump file is non-zero size before proceeding.

### Step 2 — Deploy Code

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
```

### Step 3 — Clear Caches

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Step 4 — Run Migrations

```bash
php artisan migrate --force
```

Expected output:
```
Running migrations.
2026_07_04_175351_fix_businesses_hours_column_type ........ 907ms DONE
2026_07_04_175743_add_performance_indexes_to_content_tables .. 1s DONE
```

**If migration fails:** Stop immediately. Do not re-run. Proceed to rollback. See PHASE6C-REPORT-05.

### Step 5 — Rebuild Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6 — Verify

```bash
php artisan migrate:status | tail -5
# Confirm both new migrations show "Ran"

php artisan tinker --execute="echo App\Models\Business::find(1)?->hours ? 'hours OK' : 'hours NULL';"
# Expected: hours OK or hours NULL (NULL is valid if no data)
```

---

## Migration Safety Assessment

| Migration | Risk | Data Written? | Reversible? | Estimated Duration (Prod) |
|-----------|------|--------------|-------------|--------------------------|
| fix_businesses_hours | Low | Yes (8 rows UPDATE) | Yes | < 5s |
| add_performance_indexes | None | No | Yes | 10–60s (depends on table size) |

**Performance indexes duration at scale:**
- At 10K rows: ~5s per table
- At 100K rows: ~30–60s per table
- At 1M rows: consider `CREATE INDEX ... LOCK=NONE` (MySQL 8+)

For production launch, tables are expected to be small (early-stage product). Duration estimate: under 30s total.

---

## Idempotency

**fix_businesses_hours migration:** The `up()` method wraps the data migration in a JSON check — rows already in valid JSON format are skipped. Running the migration twice would be safe (column type MODIFY is idempotent; data already JSON would be skipped).

**add_performance_indexes migration:** Blueprint `->index()` will fail if the index already exists. The `down()` method uses named `dropIndex()` calls. If the migration partially ran, manually drop any created indexes before re-running.

---

## Fresh Database Deploy (New Environment)

On a completely fresh database (`php artisan migrate` from scratch):
1. All 46 migrations run in order
2. `add_flagged_status_to_content_tables` — the INFORMATION_SCHEMA guard will correctly detect `flagged` is absent and run the ALTER
3. Both new Phase 6C migrations run normally
4. Result: database in correct final state
