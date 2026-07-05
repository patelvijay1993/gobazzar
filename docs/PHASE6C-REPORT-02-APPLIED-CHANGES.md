# Phase 6C Report 02 — Applied Changes
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6C — Production Hardening Sprint

---

## Change Log

---

### CHANGE-1
**Finding ID:** DB-INT-003  
**File Changed:** `app/Models/User.php`  
**Reason:** Authorization bypass — user ID=1 had unconditional admin access  
**Lines Changed:** Line 44-46 (1 line modified)  
**Risk:** Low (3 admin users confirmed with is_admin=1 before change)  

**Before:**
```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->is_admin || $this->id === 1;
}
```

**After:**
```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->is_admin === true;
}
```

**Verification:** `canAccessPanel(id=1, is_admin=false)` → FALSE ✓  
`canAccessPanel(id=999, is_admin=true)` → TRUE ✓  
PHP syntax: PASS ✓

---

### CHANGE-2
**Finding ID:** LOG-002  
**File Changed:** `app/Http/Controllers/PricingController.php`  
**Reason:** PII (email, phone, name) logged in plaintext — PIPEDA violation  
**Lines Changed:** Lines 39-45 (3 keys removed from array)  
**Risk:** None  

**Before:**
```php
\Log::info('Upgrade request', [
    'user_id' => auth()->id(),
    'plan'    => $data['plan'],
    'name'    => $data['name'],
    'email'   => $data['email'],
    'phone'   => $data['phone'] ?? '',
]);
```

**After:**
```php
\Log::info('Upgrade request submitted', [
    'user_id' => auth()->id(),
    'plan'    => $data['plan'],
]);
```

**Verification:** No PII keys in log call ✓  
PHP syntax: PASS ✓

---

### CHANGE-3
**Finding ID:** DB-INT-001  
**File Changed:** `database/migrations/2026_07_04_175351_fix_businesses_hours_column_type.php` *(new)*  
**Reason:** businesses.hours was TEXT, model cast as array — json_decode returned null for all businesses  
**Risk:** Medium (data migration on live rows — tested safe, rollback plan included in migration)  

**Migration actions:**
1. Read all non-null `hours` rows
2. Skip rows already containing valid JSON (id=43 preserved intact)
3. JSON-encode plain-text rows as `{"note":"..."}`
4. ALTER TABLE businesses MODIFY COLUMN hours JSON NULL

**Before (DB):** `hours = "Mon–Sun: 11am–10pm"` (text) → `$business->hours` = null  
**After (DB):** `hours = {"note":"Mon–Sun: 11am–10pm"}` (JSON) → `$business->hours` = ['note' => 'Mon–Sun: 11am–10pm']  

Migration ran: 907ms ✓  
All non-null rows return is_array=true via Eloquent ✓

---

### CHANGE-4
**Finding ID:** DB-INT-001 (view fix)  
**File Changed:** `resources/views/directory/show.blade.php`  
**Reason:** View only rendered structured day-keyed hours. Legacy `{"note":"..."}` rows needed fallback display.  
**Lines Changed:** Lines 110-123 (added @if/else branch for note format)  
**Risk:** Low (additive — existing structured format unchanged)  

**Before:** Hours section only iterated `monday`, `tuesday`, etc. keys — legacy note-format rows showed nothing.  
**After:** If `$business->hours['note']` exists, renders as "Hours: [value]". Otherwise renders structured day grid.  

PHP/Blade: PASS ✓

---

### CHANGE-5
**Finding ID:** DB-INT-002  
**File Changed:** `database/migrations/2026_07_01_190917_add_flagged_status_to_content_tables.php`  
**Reason:** Partial-run workaround comment made migration unsafe on fresh DB deploy  
**Lines Changed:** Lines 10 (comment removed) + new INFORMATION_SCHEMA guard (lines 10-17)  
**Risk:** None  

**Before:**
```php
public function up(): void
{
    // listings already has 'flagged' from a partial run — skip it
    DB::statement("ALTER TABLE job_listings MODIFY COLUMN status ENUM(...)");
    ...
}
```

**After:**
```php
public function up(): void
{
    // Add 'flagged' to listings.status only if it is not already present.
    $listingsEnumType = DB::select("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS ...")[0]->COLUMN_TYPE ?? '';
    if (!str_contains($listingsEnumType, "'flagged'")) {
        DB::statement("ALTER TABLE listings MODIFY COLUMN status ENUM('pending','active','rejected','expired','flagged')...");
    }
    DB::statement("ALTER TABLE job_listings MODIFY COLUMN status ENUM(...)");
    ...
}
```

**Verification:** Idempotency guard present ✓  
PHP syntax: PASS ✓

---

### CHANGE-6
**Finding ID:** PERF-001  
**File Changed:** `database/migrations/2026_07_04_175743_add_performance_indexes_to_content_tables.php` *(new)*  
**Reason:** 24 critical filter columns with no indexes — full table scans on every content query  
**Risk:** None (pure additive)  

**Indexes added:**

| Table | Index Name | Columns |
|-------|-----------|---------|
| listings | listings_status_featured_created_idx | status, is_featured, created_at |
| listings | listings_cat_status_idx | category_id, status |
| listings | listings_province_status_idx | province, status |
| listings | listings_city_status_idx | city, status |
| businesses | businesses_status_featured_created_idx | status, is_featured, created_at |
| businesses | businesses_cat_status_idx | category_id, status |
| businesses | businesses_province_status_idx | province, status |
| businesses | businesses_city_status_idx | city, status |
| job_listings | jobs_status_featured_created_idx | status, is_featured, created_at |
| job_listings | jobs_cat_status_idx | category_id, status |
| job_listings | jobs_province_status_idx | province, status |
| job_listings | jobs_city_status_idx | city, status |
| events | events_status_featured_created_idx | status, is_featured, created_at |
| events | events_province_status_idx | province, status |
| events | events_city_status_idx | city, status |
| matrimonials | matrimonials_status_featured_created_idx | status, is_featured, created_at |
| matrimonials | matrimonials_province_status_idx | province, status |
| matrimonials | matrimonials_city_status_idx | city, status |
| blog_posts | blog_posts_status_created_idx | status, created_at |

Migration ran: 1s ✓  
All 19 indexes confirmed in INFORMATION_SCHEMA ✓

---

### CHANGE-7
**Finding ID:** STOR-004  
**File Changed:** `app/Models/BlogPost.php`  
**Reason:** BlogPost used local public disk accessor; Filament admin already saves images to S3  
**Lines Changed:** +1 use statement, 1 line changed in getImageUrlAttribute  
**Risk:** Low  

**Before:**
```php
return str_starts_with($this->image, 'http') ? $this->image : asset('storage/'.$this->image);
```

**After:**
```php
if (str_starts_with($this->image, 'http')) return $this->image;
return Storage::disk('s3')->url($this->image);
```

PHP syntax: PASS ✓

---

### CHANGE-8
**Finding ID:** STOR-002  
**File Changed:** `app/Http/Controllers/PostController.php`  
**Reason:** matrimonial gallery update uploaded new photos without deleting old S3 files  
**Lines Changed:** +4 lines in updateMatrimonial() (deletion loop before new upload)  
**Risk:** Low  

**Before:**
```php
unset($data['photos']);
if ($request->hasFile('photos')) {
    $photoPaths = [];
    foreach ($request->file('photos') as $file) {
        $photoPaths[] = $file->store('matrimonials', 's3');
    }
    $data['photos'] = $photoPaths;
}
```

**After:**
```php
unset($data['photos']);
if ($request->hasFile('photos')) {
    // Delete old gallery photos from S3 before uploading replacements
    foreach ($r->photos ?? [] as $old) {
        if ($old && !str_starts_with($old, 'http')) Storage::disk('s3')->delete($old);
    }
    $photoPaths = [];
    foreach ($request->file('photos') as $file) {
        $photoPaths[] = $file->store('matrimonials', 's3');
    }
    $data['photos'] = $photoPaths;
}
```

PHP syntax: PASS ✓

---

## Files Changed Summary

| File | Type | Change |
|------|------|--------|
| `app/Models/User.php` | Model | Admin backdoor removed |
| `app/Http/Controllers/PricingController.php` | Controller | PII removed from logs |
| `database/migrations/2026_07_04_175351_fix_businesses_hours_column_type.php` | Migration (new) | hours column fix + data migration |
| `resources/views/directory/show.blade.php` | View | Legacy hours display fallback |
| `database/migrations/2026_07_01_190917_add_flagged_status_to_content_tables.php` | Migration (edit) | Idempotency guard added |
| `database/migrations/2026_07_04_175743_add_performance_indexes_to_content_tables.php` | Migration (new) | 19 performance indexes |
| `app/Models/BlogPost.php` | Model | S3 disk for image URLs |
| `app/Http/Controllers/PostController.php` | Controller | Matrimonial gallery cleanup |
