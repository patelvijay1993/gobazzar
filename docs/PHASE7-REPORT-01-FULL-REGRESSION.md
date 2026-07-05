# Phase 7 Report 01 — Full Regression Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 7 — Enterprise Regression & Performance Benchmark  
**Role:** Principal QA Architect, Senior Laravel Architect, Release Manager  
**Policy:** DO NOT APPLY FIXES. Evidence, Root Cause, Impact, Recommendation only.

---

## Regression Test Matrix

### Authentication

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Register | POST /register | PASS | Validation present, unique email enforced, bcrypt hash used |
| Login | POST /login | PASS | Auth::attempt, session regenerate, throttle:5,1 |
| Logout | POST /logout | PASS | Auth::logout, session invalidate, token regenerate |
| Forgot Password | POST /forgot-password | PASS | Password::sendResetLink, throttle:5,1, generic success message |
| Reset Password | POST /password/reset | PASS | Password::reset, token validated |
| Email Verification | GET /email/verify/{id}/{hash} | PASS | Signed URL, sha1 hash, markEmailAsVerified |
| Resend Verification | POST /email/resend | PASS | Throttle:6,1, generic success |
| Admin Login | POST /login (admin) | PASS | canAccessPanel() now uses is_admin===true exclusively |
| Admin Backdoor Removal | canAccessPanel | **VERIFIED CLOSED** | id===1 clause removed ✓ |

### User Profile & Account

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Profile Update | PATCH /account/profile | PASS | name, phone, city, province, bio, avatar upload to S3 |
| Password Change | PATCH /account/password | PASS | current_password verified via Hash::check |
| Account Dashboard | GET /account | PASS | Loads listings, jobs, events, businesses, matrimonials, paymentHistory |
| Analytics | GET /account/analytics/{listing} | PASS | Gated: auth + hasAnalytics() plan check |
| Favorites List | GET /account/favorites | PASS | Gated: hasFavorites() plan check |

### Plans & Payments

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Pricing Page | GET /pricing | PASS | Plan::active() returns 3 plans |
| Upgrade Request | POST /pricing/request | PASS | PII no longer logged (Phase 6C fix verified) |
| Stripe Checkout | GET /stripe/checkout/{plan} | PASS | Creates customer if needed, Checkout Session |
| Stripe Success | GET /stripe/success | PASS | Verifies payment_status, updates user plan |
| Cancel Confirm | GET /stripe/cancel/confirm | PASS | Retrieves period end from Stripe |
| Cancel Subscription | POST /stripe/cancel | PASS | cancel_at_period_end=true |
| Resume Subscription | POST /stripe/resume | PASS | cancel_at_period_end=false |
| Webhook | POST /stripe/webhook | PASS | Signature verified, 4 event types handled |

### Classified Listings

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Create Listing | POST /post/classified | PASS | Plan limit enforced, S3 upload, moderation |
| Edit Listing | POST /post/{type}/{id}/update | PASS | Ownership verified, old images deleted, new uploaded |
| Delete Listing | DELETE /post/{type}/{id} | PASS | Ownership check, S3 cleanup |
| Listing Index | GET /classifieds | PASS | Filtered, paginated, indexed |
| Listing Detail | GET /classifieds/{listing} | PASS | 404 if inactive, 410 if expired |
| Listing Search | GET /classifieds?search=term | PASS | LIKE query with addcslashes protection |
| Listing Filter | GET /classifieds?province=X&city=Y | PASS | Indexed province+status, city+status |

### Jobs

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Create Job | POST /post/job | PASS | Plan limit shared with listings |
| Edit Job | via /post/{type}/{id}/update | PASS | Logo replace cleans old S3 file |
| Delete Job | via /post/{type}/{id} | PASS | S3 cleanup |
| Job Index | GET /jobs | PASS | live() scope, paginate-15 |
| Job Detail | GET /jobs/{job} | PASS | 404 if inactive, 410 if expired |

### Events

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Create Event | POST /post/event | PASS | All plans allowed, status=active, no listing limit |
| Edit Event | via /post/{type}/{id}/update | PASS | Image replace cleans S3 |
| Delete Event | via /post/{type}/{id} | PASS |  |
| Event Index | GET /events | PASS | Ordered by start_date |
| Event Detail | GET /events/{event} | PASS | Views incremented |

### Business Directory

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Create Business | POST /post/business | PASS | Plan gate (free blocked), limit enforced |
| Edit Business | via update | PASS | Individual photo removal, merge upload |
| Delete Business | via destroy | PASS | S3 cleanup for image, images, logo |
| Business Index | GET /directory | PASS | Search, filter, paginate-12, featured first |
| Business Detail | GET /directory/{business} | PASS | 404 if inactive, hours display both formats |
| Business hours display | directory/show.blade | PASS | Legacy {"note":"..."} + structured formats |

### Business Posts

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Create Business Post | POST /post/business-post | PASS | Requires active business, plan gate |
| Edit Business Post | via update | PASS | Custom field revalidation, image merge |
| Delete Business Post | via destroy | PASS |  |
| Business Post Detail | GET /directory/{business}/{post} | PASS | 404 if inactive, 410 if expired |

### Blog

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Blog Index | GET /blog | PASS | Published posts, featured first |
| Blog Detail | GET /blog/{post} | PASS | 404 if draft, views incremented |
| Blog Image URL | getImageUrlAttribute | **VERIFIED FIXED** | Storage::disk('s3')->url() ✓ |

### Matrimonial

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Create Profile | POST /post/matrimonial | PASS | photo + photos[], S3 upload |
| Edit Profile | via update | **VERIFIED FIXED** | Old gallery photos deleted before upload ✓ |
| Delete Profile | via destroy | PASS |  |
| Matrimonial Index | GET /matrimonial | PASS | Active, paginate |
| Matrimonial Detail | GET /matrimonial/{profile} | PASS |  |

### Favorites

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Toggle Favorite | POST /favorites/toggle | PASS | Plan gate (hasFavorites), polymorphic |
| Favorites List | GET /account/favorites | PASS | Grouped by type, deleted items filtered |

### Reports

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Submit Report | POST /report | PASS | Auth required, 5 types, 6 reasons |
| Duplicate prevention | POST /report | PASS | 24h dedup by user_id OR IP |
| Auto-flag at threshold | POST /report | PASS | ≥3 pending reports → status=flagged |

### Chat

| Workflow | Route | Status | Notes |
|----------|-------|--------|-------|
| Inbox | GET /chat | PASS | All conversations for user |
| Start Chat | GET /chat/listing/{listing} | PASS | openChat, self-chat blocked (403) |
| Send Message | POST /chat/conversation/{id}/send | PASS | Ownership verified, broadcast queued |
| Poll Messages | GET /chat/conversation/{id}/poll | PASS | JSON messages since last ID |
| Mark Read | POST /chat/conversation/{id}/read | PASS | markReadFor() |

### Search & Filters

| Workflow | Notes | Status |
|----------|-------|--------|
| Listing search | LIKE with addcslashes | PASS |
| Business search | name OR description LIKE | PASS |
| Job search | title OR company LIKE | PASS |
| Province filter | Indexed province+status | PASS |
| City filter | Indexed city+status | PASS |
| Pagination | withQueryString() on all index pages | PASS |

### Admin Panel (Filament)

| Workflow | Status | Notes |
|----------|--------|-------|
| Admin login | PASS | canAccessPanel = is_admin===true only |
| Listings CRUD | PASS | Full Filament resource |
| Businesses CRUD | PASS | Full Filament resource |
| Jobs CRUD | PASS |  |
| Events CRUD | PASS |  |
| Categories CRUD | PASS | Sub-category support |
| Locations CRUD | PASS | City + province management |
| Blog Posts CRUD | PASS | S3 image upload (Filament disk=s3) |
| Advertise Requests | PASS | Read-only view |
| Flagged Posts | PASS | Review and action |
| Business Posts | PASS |  |
| Approve/Reject Listing | PASS | Status field in ListingResource form |
| Admin image remove | DELETE /admin/listing/{id}/remove-image | PASS | Auth + is_admin gate |

---

## Regression Verdict

### Phase 6A–6C Fixes — Re-Verification

| Fix | Expected State | Observed State | Status |
|-----|---------------|---------------|--------|
| Admin backdoor (DB-INT-003) | canAccessPanel = is_admin===true | CONFIRMED | PASS |
| PII in logs (LOG-002) | No name/email/phone in log call | CONFIRMED | PASS |
| hours column migration (DB-INT-001) | is_array=YES for all non-null hours | CONFIRMED | PASS |
| hours view display (DB-INT-001) | Both note+structured formats render | CONFIRMED | PASS |
| Dirty migration guard (DB-INT-002) | INFORMATION_SCHEMA guard present | CONFIRMED | PASS |
| 19 performance indexes (PERF-001) | All 19 indexes in INFORMATION_SCHEMA | CONFIRMED | PASS |
| BlogPost S3 disk (STOR-004) | Storage::disk('s3')->url() in accessor | CONFIRMED | PASS |
| Matrimonial gallery cleanup (STOR-002) | Delete loop before upload | CONFIRMED | PASS |

**No regressions detected in Phase 6A–6C fixes.**

---

## New Findings in Phase 7

*(See PHASE7-REPORT-07-REGRESSION-BUG-REPORT.md for full detail)*

| Finding | Severity | Type |
|---------|----------|------|
| PERF-P7-001: Listing first-load latency 359ms on XAMPP | Low | Performance/Environment |
| PERF-P7-002: Chat inbox 690ms (correlated subquery ordering) | Medium | Performance |
| PERF-P7-003: User::planModel() no caching — N+1 on listing cards | Medium | N+1 Query |
| PERF-P7-004: User::maybeResetCredits DB write on every read | Low | Side Effect |
| PERF-P7-005: job_listings: filesort on live() query despite index | Low | Query Plan |
| OPS-P7-001: APP_DEBUG=true in local .env (must be false on prod) | High | Config |
| OPS-P7-002: 26 stale queue jobs unprocessed | Medium | Operations |
| OPS-P7-003: storage symlink not created | High | Deployment |
| OPS-P7-004: Config cache and route cache not built | Medium | Performance |
| OPS-P7-005: ListingResource admin uses asset('storage/') for image preview | Low | Admin UI |
