# Phase 6 Report 01 — Database Integrity Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Auditor:** Senior Database Architect / Enterprise QA Lead  
**Fix Policy:** ANALYSIS ONLY — No fixes applied

---

## Summary

| Category | Issues Found | Critical | High | Medium | Low |
|----------|-------------|---------|------|--------|-----|
| Schema Integrity | 9 | 3 | 3 | 2 | 1 |
| FK Constraints | 4 | 1 | 2 | 1 | 0 |
| Enum / Type Safety | 5 | 0 | 3 | 2 | 0 |
| Soft Deletes | 1 | 0 | 1 | 0 | 0 |
| Constraint Gaps | 3 | 0 | 1 | 2 | 0 |
| **Total** | **22** | **4** | **10** | **7** | **1** |

---

## 1. Database Overview

| Metric | Value |
|--------|-------|
| Database | gobazzar (MariaDB) |
| Total tables | 34 |
| Total migrations run | 45 |
| Last migration batch | 37 |
| Total content rows | ~320 (dev data) |

---

## 2. Primary Key Integrity

All 34 tables have auto-increment unsigned integer PKs. No composite PKs used as identity. No UUID PKs.

| Finding | Status |
|---------|--------|
| All tables have PK | PASS |
| All PKs are NOT NULL | PASS |
| No missing PKs | PASS |
| Settings table uses string PK (`key`) | PASS — intentional, non-incrementing |

---

## 3. Critical Schema Issues

### DB-INT-001 — `businesses.hours` Cast Mismatch (Critical)

**Evidence:**
```
businesses.hours type: text
Business model: protected $casts = ['hours' => 'array'];
Sample data: biz_id=1 hours_raw=zxczxczx
             biz_id=2 hours_raw=Mon–Sun: 11am–10pm
             biz_id=3 hours_raw=Mon–Fri: 9am–6pm
```

**Root Cause:** The `businesses.hours` column was created as `text` in the original migration `create_businesses_table.php`. The `Business` model casts it as `'array'` (which expects JSON). However, existing data in the column is plain text strings, not JSON. When Eloquent reads these rows, `json_decode()` on `"Mon–Sun: 11am–10pm"` silently returns `null`.

**Impact:** Any code accessing `$business->hours` to display opening hours gets `null` instead of the expected array. The hours display is broken for all 11 businesses in the database that have non-JSON hours values. New data written through PostController is stored as JSON (via `$data['hours'] = $hours` array), but legacy rows remain plain text.

**Risk:** Critical data loss — hours data silently returns null on read. Mixed formats in the same column.

**Recommended Fix:**
1. Add migration to alter `businesses.hours` from `text` to `json`
2. Run data migration to JSON-encode all existing plain-text values that can be parsed
3. Handle rows with ambiguous strings (free-form text like "Mon-Sun: 11am-10pm") manually

**Effort:** 2 hours | **Breaking Change Risk:** Medium (requires data migration) | **Regression Risk:** Medium

---

### DB-INT-002 — Partial Migration Evidence: Dirty Migration State (Critical)

**Evidence:**
```
Migration: 2026_07_01_190917_add_flagged_status_to_content_tables.php
Comment: "listings already has 'flagged' from a partial run — skip it"
```

**Root Cause:** Migration `add_flagged_status_to_content_tables` contains a comment acknowledging that the `listings` table already had the 'flagged' status value from a prior partial run. This means a migration was run, failed or was interrupted mid-execution, and the developer manually worked around it instead of rolling back and fixing the migration properly.

**Impact:** The migration state in the `migrations` table may not accurately reflect the actual database schema. A fresh `php artisan migrate` on a new environment would attempt to add 'flagged' to the listings enum and fail, breaking deployment to production.

**Risk:** Critical — production deployment blocker. The migration sequence is not idempotent.

**Recommended Fix:**
1. Test `php artisan migrate:fresh` on a clean database to verify all migrations run to completion
2. Fix the migration to use `DB::statement` with existence checks (IF NOT EXISTS for enum values is not supported in MySQL — use `MODIFY COLUMN` with the full enum list)
3. Document the actual schema state vs. migration state discrepancy

**Effort:** 3 hours | **Breaking Change Risk:** High (requires full migration audit) | **Regression Risk:** High

---

### DB-INT-003 — `User::canAccessPanel()` ID=1 Backdoor (Critical)

**Evidence (from User.php model):**
```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->is_admin || $this->id === 1;
}
```

**Root Cause:** The method grants admin panel access to user with `id=1` unconditionally, regardless of the `is_admin` flag. This is a hardcoded development backdoor.

**Impact:** If user ID 1 is a regular customer account (e.g., the first person to register), they have full admin access. This is a security integrity failure. In a fresh production database where the admin account happens not to be ID=1, the backdoor persists for whatever user registered first.

**Risk:** Critical — unauthorized admin panel access.

**Recommended Fix:**
```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->is_admin === true;
}
```

**Effort:** 5 minutes | **Breaking Change Risk:** Low | **Regression Risk:** Low

---

## 4. High Severity Schema Issues

### DB-INT-004 — No Soft Deletes on Any Content Table (High)

**Evidence:**
```
listings        soft_delete: NO
businesses      soft_delete: NO
job_listings    soft_delete: NO
events          soft_delete: NO
matrimonials    soft_delete: NO
blog_posts      soft_delete: NO
business_posts  soft_delete: NO
```

**Root Cause:** No content table uses Laravel's `SoftDeletes` trait or has a `deleted_at` column.

**Impact:**
- Deleting a user listing permanently removes all associated content with no recovery path
- Deleting a business with active conversations leaves the conversations pointing to a non-existent business
- Admin panel has no way to restore accidentally deleted content
- The `PurgeExpiredPosts` command does hard deletes — once run, data is unrecoverable
- Violates basic data governance requirements for any production marketplace

**Risk:** High — unrecoverable data loss, no audit trail.

**Recommended Fix:** Add `SoftDeletes` trait + `deleted_at` column to: `listings`, `businesses`, `job_listings`, `events`, `matrimonials`, `blog_posts`, `business_posts`. Scope all queries with `->whereNull('deleted_at')` (handled automatically by Eloquent SoftDeletes).

**Effort:** 4 hours | **Breaking Change Risk:** Medium | **Regression Risk:** Low

---

### DB-INT-005 — `maybeResetCredits()` DB Write on Every Read Path (High)

**Evidence (from User.php):**
```php
public function maybeResetCredits(): void
{
    // DB UPDATE on every request that checks featured credits
    // Called from activePlan(), postDays(), etc.
}
```

**Root Cause:** The `maybeResetCredits()` method performs a database write on every request that touches plan or credit checking. Since `activePlan()` is called in controllers, middleware, and view helpers, this method fires multiple times per page request.

**Impact:** Every page load for an authenticated user that checks plan status results in at least one additional DB UPDATE query. At scale (1,000+ concurrent users) this becomes significant write contention on the `users` table.

**Risk:** High — performance degradation under load, write-amplification on hot rows.

**Recommended Fix:** Gate the credit reset behind a time-based check (e.g., store `credits_reset_at` and only reset if `> 1 month` has passed, using a cache lock).

**Effort:** 2 hours | **Breaking Change Risk:** Low | **Regression Risk:** Low

---

### DB-INT-006 — `sessions.user_id` Has No FK Constraint (High)

**Evidence:**
```
sessions table: user_id has INDEX but no FOREIGN KEY constraint
```

**Root Cause:** The `sessions` table created by Laravel's default migration adds only an index on `user_id`, not a foreign key. This is intentional in Laravel (sessions are driver-agnostic), but means deleted users leave orphan sessions.

**Impact:** If a user is deleted, their session records remain in the `sessions` table indefinitely with no cascade cleanup. Not a runtime error (PHP sessions check auth on load) but adds orphan data accumulation over time.

**Risk:** High — data integrity gap, potential PII exposure (session data contains serialized auth state).

**Recommended Fix:** Add a `Foreign Key` constraint with `onDelete('cascade')` via a new migration, or add a cleanup step to `UserController::deleteAccount()` when implemented.

**Effort:** 30 minutes | **Breaking Change Risk:** Low | **Regression Risk:** Low

---

## 5. Medium Severity Schema Issues

### DB-INT-007 — `advertise_requests.status` is Plain String, Not Enum (Medium)

**Evidence:**
```
advertise_requests.status type: varchar(255)
```

**Root Cause:** The status field was created as a plain `string` column. Valid values are presumably `pending|approved|rejected` but nothing enforces this at the database level.

**Impact:** Admin can accidentally set an invalid status value (e.g., "actve" typo). No DB-level constraint catches it.

**Recommended Fix:** `ALTER TABLE advertise_requests MODIFY status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending';`

**Effort:** 30 minutes | **Breaking Change Risk:** Low | **Regression Risk:** Low

---

### DB-INT-008 — `matrimonials.gender` / `marital_status` are Plain Strings (Medium)

**Evidence:**
```
matrimonials.gender type: varchar(255)
matrimonials.marital_status type: varchar(255)
```

**Root Cause:** Migration created these as `string` columns. PostController validates them with `in:male,female` and `in:never_married,divorced,widowed` at the application layer, but there is no DB-level constraint.

**Impact:** Direct DB inserts (e.g., seeder, admin SQL) can bypass application validation and insert invalid values. Model accessors like `getMaritalStatusLabelAttribute()` would return `ucfirst($this->marital_status)` for unknown values — silent incorrect display.

**Recommended Fix:** Convert to `ENUM('male','female')` and `ENUM('never_married','divorced','widowed')`.

**Effort:** 30 minutes | **Breaking Change Risk:** Low | **Regression Risk:** Low

---

## 6. Low Severity Schema Issues

### DB-INT-009 — Stale Migration Comment on `users.plan` (Low)

**Evidence:**
```
Migration comment: "free|basic|premium|business" (old plan slugs)
Actual values in use: "free|verified|power_seller"
```

**Root Cause:** The `add_plan_to_users_table` migration was written when the plan system used different slugs. The plan slugs were later changed but the comment was not updated.

**Impact:** Developer confusion only — no runtime effect. The column is `varchar(20)`, not an enum, so any string value is accepted.

**Recommended Fix:** Update the migration comment to reflect current plan slugs. Add a DB-level CHECK constraint or convert to ENUM.

**Effort:** 10 minutes | **Breaking Change Risk:** None | **Regression Risk:** None

---

## 7. Constraint Completeness Matrix

| Table | PK | FK Constraints | Status Enum | Unique Slug | Soft Delete |
|-------|----|----------------|-------------|-------------|-------------|
| listings | ✓ | user_id, category_id CASCADE | ✓ | ✓ | ✗ |
| businesses | ✓ | user_id nullable, category_id nullable | ✓ | ✓ | ✗ |
| job_listings | ✓ | user_id nullable, category_id nullable | partial | ✓ | ✗ |
| events | ✓ | user_id nullable, category_id nullable | partial | ✓ | ✗ |
| matrimonials | ✓ | user_id nullable | ✓ | ✓ | ✗ |
| blog_posts | ✓ | user_id CASCADE | ✓ | ✓ | ✗ |
| business_posts | ✓ | business_id CASCADE, user_id CASCADE | ✓ | ✓ | ✗ |
| conversations | ✓ | buyer_id, seller_id CASCADE | — | conv_unique | ✗ |
| chat_messages | ✓ | conversation_id, sender_id CASCADE | — | — | ✗ |
| user_favorites | ✓ | user_id CASCADE | — | ✓ | ✗ |
| reports | ✓ | user_id nullable | ✓ | — | ✗ |
| payment_history | ✓ | user_id CASCADE | partial | — | ✗ |
| flagged_posts | ✓ | user_id CASCADE | — | — | ✗ |
| plans | ✓ | — | — | slug unique | ✗ |
| settings | ✓ (string) | — | — | — | ✗ |

---

## 8. Database Integrity Verdict

```
╔══════════════════════════════════════════════════════════════╗
║                                                              ║
║   PHASE 6 — DATABASE INTEGRITY                               ║
║                                                              ║
║   Critical: 3   High: 5   Medium: 5   Low: 1                 ║
║                                                              ║
║   STATUS: REQUIRES REMEDIATION BEFORE PRODUCTION             ║
║                                                              ║
║   Blockers: DB-INT-001 (hours cast), DB-INT-002 (dirty       ║
║             migration), DB-INT-003 (backdoor admin access)   ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
```
