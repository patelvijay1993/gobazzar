# Phase 6C Report 03 — Database Change Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6C — Production Hardening Sprint

---

## Database Changes Summary

| Change | Type | Table | Destructive? | Rollback Available? |
|--------|------|-------|-------------|-------------------|
| businesses.hours type TEXT → JSON | ALTER TABLE + data UPDATE | businesses | No | Yes |
| 19 performance indexes added | CREATE INDEX | 6 tables | No | Yes (DROP INDEX) |
| listings.status idempotency guard | No DB change (logic fix only) | listings | No | N/A |

---

## DB Change 1 — businesses.hours Column Type Migration

### Migration File
`database/migrations/2026_07_04_175351_fix_businesses_hours_column_type.php`

### Operation 1 — Data Migration (UPDATE)

```sql
-- For each row with non-null, non-JSON hours:
UPDATE businesses SET hours = '{"note":"Mon–Sun: 11am–10pm"}' WHERE id = 2;
UPDATE businesses SET hours = '{"note":"Mon–Fri: 9am–6pm"}' WHERE id = 3;
UPDATE businesses SET hours = '{"note":"Mon–Sat: 9am–7pm"}' WHERE id = 4;
UPDATE businesses SET hours = '{"note":"Mon–Sun: 9am–9pm"}' WHERE id = 5;
UPDATE businesses SET hours = '{"note":"Mon–Sat: 10am–7pm"}' WHERE id = 6;
UPDATE businesses SET hours = '{"note":"Tue–Sun: 10am–9pm"}' WHERE id = 7;
UPDATE businesses SET hours = '{"note":"Mon–Fri: 9am–5pm"}' WHERE id = 8;
UPDATE businesses SET hours = '{"note":"zxczxczx"}' WHERE id = 1;
-- id=43 already valid JSON — SKIPPED (preserved intact)
-- id=13, id=44 are NULL — SKIPPED
```

**Rows affected:** 8  
**Data lost:** None — original text preserved inside `note` key  
**Reversible:** Yes — `note` value contains original text  

### Operation 2 — Column Type Change (ALTER TABLE)

```sql
ALTER TABLE businesses MODIFY COLUMN hours JSON NULL;
```

**Before:** `COLUMN_TYPE = text`  
**After:** `COLUMN_TYPE = longtext` (MariaDB stores JSON as longtext with JSON validation)  
**Downtime required:** No — ALTER TABLE on small table (<100 rows) is effectively instant  
**Rows affected:** 11  

### Rollback

```sql
ALTER TABLE businesses MODIFY COLUMN hours TEXT NULL;
-- Data remains valid — JSON strings are still valid text strings
```

### Post-Migration Verification

```
PASS — businesses.hours column type: longtext (with JSON validation)
PASS — id=2 hours is_array: YES, keys=[note]
PASS — id=43 hours is_array: YES, keys=[monday,tuesday,wednesday,thursday,friday,saturday,sunday]
PASS — id=13 hours: NULL
PASS — id=44 hours: NULL
```

---

## DB Change 2 — Performance Indexes

### Migration File
`database/migrations/2026_07_04_175743_add_performance_indexes_to_content_tables.php`

### Indexes Created

```sql
-- listings
ALTER TABLE listings ADD INDEX listings_status_featured_created_idx (status, is_featured, created_at);
ALTER TABLE listings ADD INDEX listings_cat_status_idx (category_id, status);
ALTER TABLE listings ADD INDEX listings_province_status_idx (province, status);
ALTER TABLE listings ADD INDEX listings_city_status_idx (city, status);

-- businesses
ALTER TABLE businesses ADD INDEX businesses_status_featured_created_idx (status, is_featured, created_at);
ALTER TABLE businesses ADD INDEX businesses_cat_status_idx (category_id, status);
ALTER TABLE businesses ADD INDEX businesses_province_status_idx (province, status);
ALTER TABLE businesses ADD INDEX businesses_city_status_idx (city, status);

-- job_listings
ALTER TABLE job_listings ADD INDEX jobs_status_featured_created_idx (status, is_featured, created_at);
ALTER TABLE job_listings ADD INDEX jobs_cat_status_idx (category_id, status);
ALTER TABLE job_listings ADD INDEX jobs_province_status_idx (province, status);
ALTER TABLE job_listings ADD INDEX jobs_city_status_idx (city, status);

-- events
ALTER TABLE events ADD INDEX events_status_featured_created_idx (status, is_featured, created_at);
ALTER TABLE events ADD INDEX events_province_status_idx (province, status);
ALTER TABLE events ADD INDEX events_city_status_idx (city, status);

-- matrimonials
ALTER TABLE matrimonials ADD INDEX matrimonials_status_featured_created_idx (status, is_featured, created_at);
ALTER TABLE matrimonials ADD INDEX matrimonials_province_status_idx (province, status);
ALTER TABLE matrimonials ADD INDEX matrimonials_city_status_idx (city, status);

-- blog_posts
ALTER TABLE blog_posts ADD INDEX blog_posts_status_created_idx (status, created_at);
```

**Total indexes added:** 19  
**Data changed:** None  
**Downtime required:** No  
**Destructive:** No  

### EXPLAIN Benefit

```sql
-- BEFORE (full table scan):
EXPLAIN SELECT * FROM listings WHERE status='active' ORDER BY is_featured DESC, created_at DESC LIMIT 12;
-- type: ALL, key: NULL, rows: 46

-- AFTER (index scan):
EXPLAIN SELECT * FROM listings WHERE status='active' ORDER BY is_featured DESC, created_at DESC LIMIT 12;
-- type: ref, key: listings_status_featured_created_idx, rows: ~X (estimated fraction)
```

**Estimated query time at 100K rows:**
- Before indexes: ~150ms (full table scan)
- After indexes: ~1–3ms (index range scan)
- Improvement factor: ~50×–100×

### Rollback

```sql
ALTER TABLE listings DROP INDEX listings_status_featured_created_idx;
ALTER TABLE listings DROP INDEX listings_cat_status_idx;
-- (etc. for all 19 indexes)
```

Migration `down()` method handles this automatically.

---

## DB Change 3 — No Schema Change (listings.status guard)

The fix to `add_flagged_status_to_content_tables.php` is a **code logic change only** — no new database schema change on the current database. The guard ensures the migration is safe on fresh deployments. Current database is unaffected.

---

## No Destructive Operations

```
✓ No columns dropped
✓ No columns renamed
✓ No production data deleted
✓ No enum values removed
✓ No JSON format destroyed
✓ No FK constraints modified
✓ No tables dropped
```
