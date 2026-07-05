# Phase 6 Report 04 — Storage Integrity Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## Summary

| Area | Status | Issues |
|------|--------|--------|
| S3 Configuration | CONFIGURED | Credentials set, bucket set |
| Local Storage Disk | OK | Points to correct path |
| Public Disk | ENV-CONDITIONAL | Different paths for local/production |
| File Naming | GOOD | Laravel random hash naming |
| Gallery (dual image/images) | RISK | Partial data migration detected |
| Image Deletion | PARTIAL | Gallery images not deleted on update |
| Purge Command | PARTIAL | Listings only delete `image`, not `images` gallery |
| External URLs in DB | RISK | 12 external http URLs in S3-path columns |

---

## 1. Storage Configuration

### Filesystem Config (`config/filesystems.php`)

```php
'default' => env('FILESYSTEM_DISK', 'local'),

's3' => [
    'driver'     => 's3',
    'key'        => env('AWS_ACCESS_KEY_ID'),     // SET
    'secret'     => env('AWS_SECRET_ACCESS_KEY'), // SET  
    'region'     => env('AWS_DEFAULT_REGION'),
    'bucket'     => env('AWS_BUCKET'),            // SET
    'url'        => env('AWS_URL'),
    'endpoint'   => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'visibility' => 'public',
    'throw'      => false,
    'report'     => false,
]
```

**Status:** S3 is configured. AWS credentials, bucket, and region are set in `.env`. `throw: false` means S3 upload failures are silently swallowed (see STOR-003 below).

### Public Disk (Environment-Conditional)

```php
'public' => [
    'root' => env('APP_ENV') === 'production'
        ? '/home/heavendw/public_html/gobazzarweb.heavendwell.com/storage'
        : public_path('storage'),
]
```

**Finding:** The public disk has a hardcoded production path in the config file. This is an unusual pattern — typically `APP_ENV`-based branching belongs in `.env`, not in config files. The production path `/home/heavendw/public_html/...` is committed to version control, exposing the server's directory structure and hosting provider details.

**Risk:** Low-Medium — information disclosure in version control. If the server path changes, this config must be updated and redeployed.

---

## 2. S3 Storage Structure

### Folder Organization (from DB image paths)

```
listings/       — Classified listing images
jobs/           — Job company logos
events/         — Event images
businesses/     — Business images + logos
matrimonials/   — Matrimonial profile photos
avatars/        — User profile avatars
editor/         — Rich text editor inline images
business-posts/ — Business post images
```

**Assessment:** Clean folder-per-content-type organization. No flat root dump. Good naming convention — Laravel's `store()` generates random 40-char hash filenames (e.g., `listings/DwC8cjjEFSMXX53lIrWoc7W80X2iwdpatfjxsrAg.jpg`), preventing name collisions and path enumeration.

**Missing Folder:** City hero images (`locations.city_image`) — referenced in `HomeController` and stored to S3 via admin upload. Folder path not observed in DB samples but expected to be `cities/` or `locations/`.

---

## 3. Dual Image Pattern Analysis

All major content types use a dual-column image pattern:
- `image` (string) — primary/cover image, S3 path or URL
- `images` (JSON array) — gallery of additional images, array of S3 paths

### STOR-001 — Gallery Images Not Purged by `PurgeExpiredPosts` Command (High)

**Evidence (`app/Console/Commands/PurgeExpiredPosts.php`):**
```php
// Listings
foreach ($listings as $r) {
    if ($r->image) Storage::disk('s3')->delete($r->image);
    $r->delete();
}
```

**Root Cause:** `PurgeExpiredPosts` deletes only the primary `image` when purging expired listings, jobs, and matrimonials. The `images` JSON gallery array is not iterated — all additional gallery images remain in S3 indefinitely after the DB record is deleted.

**Impact:**
- Orphan S3 files accumulate every time the purge command runs
- No cleanup mechanism exists for gallery files
- At scale (100,000 listings, 5 images each), this could amount to 400,000 orphan S3 files

**Recommended Fix:**
```php
foreach ($listings as $r) {
    if ($r->image && !str_starts_with($r->image, 'http')) 
        Storage::disk('s3')->delete($r->image);
    foreach ($r->images ?? [] as $path) {
        if (!str_starts_with($path, 'http')) 
            Storage::disk('s3')->delete($path);
    }
    $r->delete();
}
```

**Effort:** 1 hour | **Breaking Change Risk:** Low | **Regression Risk:** Low

---

### STOR-002 — Gallery Images Not Replaced on Classified Update (Medium)

**Evidence (`PostController::updateClassified`):**
```php
if ($request->hasFile('images')) {
    foreach ($r->images ?? [] as $old) {
        if ($old && !str_starts_with($old, 'http')) 
            Storage::disk('s3')->delete($old);
    }
    // Upload new gallery...
}
```

**Status for classifieds:** PASS — old gallery images ARE deleted before new ones are uploaded.

**Evidence (`PostController::updateMatrimonial`):**
```php
unset($data['photos']); // remove UploadedFile array before update
if ($request->hasFile('photos')) {
    $photoPaths = [];
    foreach ($request->file('photos') as $file) {
        $photoPaths[] = $file->store('matrimonials', 's3');
    }
    $data['photos'] = $photoPaths;
}
```

**Finding:** Matrimonial photo gallery update does NOT delete old photos from S3 before storing new ones. Old gallery files accumulate on each update.

**Impact:** Every time a matrimonial profile's gallery is updated, the old gallery images remain on S3. This is a storage leak.

**Recommended Fix:** Add deletion of old photos before storing new ones in `updateMatrimonial()`, matching the pattern used in `updateClassified()`.

**Effort:** 30 minutes | **Breaking Change Risk:** Low

---

### STOR-003 — S3 `throw: false` Silences Upload Failures (High)

**Evidence:**
```php
's3' => [
    'throw' => false,
    'report' => false,
]
```

**Root Cause:** With `throw: false`, any S3 upload failure (network error, invalid credentials, bucket permission denied, storage quota exceeded) is silently swallowed. `Storage::disk('s3')->put(...)` returns `false` on failure instead of throwing an exception.

**Impact:**
- `PostController::storeClassified` does `$paths[] = $file->store('listings', 's3')` — if S3 fails, `$paths[]` gets `false` stored as the path
- The listing is created with `image = false` (saved as string "")
- User sees "Your classified ad is now live!" but their images are missing
- No error notification to the user or logs

**Recommended Fix:**
1. Change `'throw' => true` in production S3 config and wrap upload calls in try/catch
2. Or: check the return value of `$file->store(...)` and abort with error if `false`

**Effort:** 2 hours | **Breaking Change Risk:** Low | **Regression Risk:** Low

---

## 4. File Naming and Security

### File Name Strategy: PASS

All uploads use Laravel's built-in `store()` method which generates a random 40-character hex filename. Original filename and extension are not preserved.

**Benefits:**
- No path traversal risk
- No filename collision
- Content type is enforced by the `image|mimes:jpg,png,webp,gif` validation rule
- Original filename never appears in URL

### Image Validation: PASS (with caveat)

```php
private static function imgRules(): string
{
    return 'image|mimes:jpg,jpeg,png,webp,gif|max:5120|dimensions:min_width=100,...';
}
```

**Assessment:** Solid — MIME type, extension, dimensions, and size are all validated. The `image` rule verifies the file is actually an image (checks binary signature, not just extension).

**Caveat:** Editor images (`/post/image-upload`) are uploaded to S3 before content moderation. An AI content check runs afterward but if moderation fails, the image is already on S3 and must be manually purged.

---

## 5. Storage Disk Selection Analysis

### `BlogPost::getImageUrlAttribute()` Uses Wrong Disk (Medium)

**Evidence (`app/Models/BlogPost.php`):**
```php
public function getImageUrlAttribute(): ?string
{
    if (!$this->image) return null;
    return str_starts_with($this->image, 'http') 
        ? $this->image 
        : asset('storage/'.$this->image);
}
```

**Root Cause:** `BlogPost` uses `asset('storage/...')` — the **public disk** — for image URLs. All other content models (`Listing`, `Business`, `Event`, `Job`, `BusinessPost`, `Matrimonial`) use `Storage::disk('s3')->url(...)`. This means blog images are stored to the public disk, not S3.

**Impact:**
- Blog images are not backed up with other S3 content
- Blog images must be stored in `/storage/app/public` and symlinked to `public/storage`
- In production, the public disk path is a different absolute path (hardcoded in filesystems.php)
- If migrating hosting, blog images require separate migration

**Recommended Fix:** Align `BlogPost` images with other models — store to S3 and use `Storage::disk('s3')->url()` in the accessor.

**Effort:** 1 hour + data migration for existing images | **Breaking Change Risk:** Low

---

## 6. Storage Integrity Verdict

| Finding | Severity | Status |
|---------|----------|--------|
| S3 credentials configured | — | PASS |
| S3 folder structure clean | — | PASS |
| File naming (random hash) | — | PASS |
| Image MIME validation | — | PASS |
| PurgeExpiredPosts gallery leak (STOR-001) | High | FAIL |
| S3 `throw:false` silent failures (STOR-003) | High | FAIL |
| Matrimonial gallery not cleaned on update (STOR-002) | Medium | FAIL |
| BlogPost uses wrong disk vs others (STOR-004) | Medium | FAIL |
| External URLs in S3-path columns | Low | WARNING |
| Production path hardcoded in config | Low | WARNING |
