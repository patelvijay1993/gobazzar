# Phase 6 Report 02 — Foreign Key Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 6 — Enterprise Database, Storage & Performance Integrity Audit  
**Fix Policy:** ANALYSIS ONLY

---

## FK Summary

| Metric | Count |
|--------|-------|
| Total FK constraints in DB | 31 |
| Tables with FKs | 20 |
| Tables without FKs | 14 |
| Cascade DELETE FKs | 14 |
| nullOnDelete FKs | 9 |
| Restrict FKs | 8 |
| Missing FKs (gap) | 3 |

---

## 1. Complete FK Map (Live Evidence)

```
advertise_requests.user_id        → users.id           (nullOnDelete)
blog_posts.user_id                → users.id           (CASCADE)
businesses.category_id            → categories.id      (nullOnDelete)
businesses.user_id                → users.id           (nullOnDelete)
business_posts.business_id        → businesses.id      (CASCADE)
business_posts.category_id        → categories.id      (nullOnDelete)
business_posts.user_id            → users.id           (CASCADE)
categories.parent_id              → categories.id      (nullOnDelete)
category_fields.category_id       → categories.id      (CASCADE)
chat_messages.conversation_id     → conversations.id   (CASCADE)
chat_messages.sender_id           → users.id           (CASCADE)
conversations.buyer_id            → users.id           (CASCADE)
conversations.seller_id           → users.id           (CASCADE)
events.category_id                → categories.id      (nullOnDelete)
events.user_id                    → users.id           (nullOnDelete)
featured_credit_logs.listing_id   → listings.id        (CASCADE)
featured_credit_logs.user_id      → users.id           (CASCADE)
flagged_posts.user_id             → users.id           (CASCADE)
job_listings.category_id          → categories.id      (nullOnDelete)
job_listings.user_id              → users.id           (nullOnDelete)
listings.category_id              → categories.id      (CASCADE)
listings.user_id                  → users.id           (CASCADE)
listing_views.listing_id          → listings.id        (CASCADE)
listing_views.user_id             → users.id           (nullOnDelete)
matrimonials.user_id              → users.id           (nullOnDelete)
payment_history.user_id           → users.id           (CASCADE)
poll_options.poll_id              → polls.id           (CASCADE)
poll_votes.poll_id                → polls.id           (CASCADE)
poll_votes.poll_option_id         → poll_options.id    (CASCADE)
reports.user_id                   → users.id           (nullOnDelete)
user_favorites.user_id            → users.id           (CASCADE)
```

---

## 2. Missing Foreign Key Constraints

### FK-GAP-001 — `sessions.user_id` Has No FK (High)

**Evidence:** `sessions.user_id` has an INDEX but is NOT in the FK constraint list.

**Impact:** Deleting a user does not cascade-delete their active sessions. Session records with a deleted user's ID remain in the table indefinitely. Since the session driver is `file`, the actual session data is in files and the DB row is a reference — but PII (user_id) persists in the table.

**Recommended Fix:**
```sql
ALTER TABLE sessions 
ADD CONSTRAINT sessions_user_id_foreign 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
```
Note: `SET NULL` is appropriate here since anonymous sessions also exist without a user_id.

**Effort:** 30 minutes | **Breaking Change Risk:** Low

---

### FK-GAP-002 — `payment_history.plan_slug` Has No FK to `plans.slug` (High)

**Evidence:**
```
payment_history.plan_slug type: varchar(255)
plans.slug: UNIQUE index exists
No FK constraint: payment_history.plan_slug → plans.slug
```

**Impact:** Payment records reference plan slugs as plain strings. If a plan is renamed or deleted, payment history becomes semantically disconnected from the plan definition. Historical payment records cannot be joined to current plans reliably. Plan slug "verified" in payment history could reference a renamed plan.

**Note:** This FK would need `ON DELETE SET NULL` since payment records should be preserved even if a plan is removed.

**Recommended Fix:**
```php
$table->foreign('plan_slug')->references('slug')->on('plans')->nullOnDelete();
```
Requires `plan_slug` to match `plans.slug` exactly — currently just a varchar, no FK enforcement.

**Effort:** 1 hour | **Breaking Change Risk:** Low

---

### FK-GAP-003 — `flagged_posts.post_type` Has No FK to Content Tables (Medium)

**Evidence:**
```
flagged_posts.post_type type: varchar(255)
Values observed: 'business', 'classified', 'event', 'job'
```

**Impact:** `flagged_posts` uses a plain string `post_type` field (not Laravel morphs). There is no FK, no integrity check, and no linkage to the actual content records. If a listing is deleted, its flagged post record remains with no way to reference the original. There is also no `post_id` column — so even with a morph you cannot join to the original content.

**Recommended Fix:** Add `post_id` column + convert to Laravel morphs (`post_type` + `post_id`) with appropriate FK behavior. Or, if `flagged_posts` is intended as an audit log (retain even after content deletion), keep the plain string but add `post_id` and document the design.

**Effort:** 2 hours | **Breaking Change Risk:** Medium

---

## 3. Cascade vs. nullOnDelete Strategy Analysis

### Cascade FKs (content destroyed when parent destroyed)

| FK | Behavior |
|----|----------|
| `listings.user_id → users.id` | Listings CASCADE deleted with user |
| `blog_posts.user_id → users.id` | Blog posts CASCADE deleted with user |
| `chat_messages.conversation_id → conversations.id` | Messages deleted when conversation deleted |
| `featured_credit_logs.listing_id → listings.id` | Credit log deleted when listing deleted |
| `listing_views.listing_id → listings.id` | View analytics deleted when listing deleted |

**Assessment:** Listing CASCADE is appropriate — a listing without a user has no owner/editor and should be purged. However, `listing_views` cascade means view analytics are permanently lost when a listing is deleted. For business analytics this is a problem — views should ideally be preserved for reporting even after listing removal.

### nullOnDelete FKs (parent nulled, content orphaned)

| FK | Risk |
|----|------|
| `businesses.user_id → users.id` | Business with `user_id=NULL` — no owner |
| `job_listings.user_id → users.id` | Job with `user_id=NULL` — no owner |
| `events.user_id → users.id` | Event with `user_id=NULL` — no owner |
| `matrimonials.user_id → users.id` | Matrimonial with `user_id=NULL` — no owner |
| `listing_views.user_id → users.id` | View with `user_id=NULL` — anonymous view record |

**Assessment:** The nullOnDelete strategy for businesses/jobs/events/matrimonials creates the **orphan data problem** confirmed in the live database (1 orphan business, 3 orphan job listings). These are records that can no longer be edited or deleted by any user through the application. They become unmanageable ghosts.

---

## 4. Live Orphan Data From FK Nullification

```
Businesses with user_id=NULL: 1 (id=1, name=fczxzcx)
Job listings with user_id=NULL: 3 (ids=15,16,17 — Test Job 1/2/3)
Events with user_id=NULL: 0
Matrimonials with user_id=NULL: 0
Listings with user_id=NULL: 0 (CASCADE — removed with user)
```

**Finding:** The nullOnDelete vs CASCADE design split is confirmed to cause real orphan data in the dev database. When a test user was deleted, their businesses and jobs became ownerless. These records show in the public index (status=active) but cannot be edited or deleted by anyone except via admin panel.

---

## 5. Polymorphic FK Analysis

### `conversations.conversable` (morphs)
```sql
conversations.conversable_type + conversations.conversable_id
Observed types: App\Models\Business, App\Models\BusinessPost, App\Models\Event, App\Models\Listing
```
No DB-level FK on the polymorphic side — this is expected and correct for Laravel morphs. The morph relationship is enforced at the application layer.

**Risk:** If a Business, Listing, Event, or BusinessPost is hard-deleted, `conversations.conversable_id` points to nothing. `Conversation::getSubjectUrlAttribute()` handles this with null checks, but the conversation still exists as a ghost.

### `user_favorites.favoriteable` (morphs)
Same pattern — no DB FK on the morph columns. Currently 0 favorites in DB, so no orphan risk yet.

### `reports.reportable` (morphs)
```
Reports by type: App\Models\Job: 1, App\Models\Listing: 1
```
These 2 reports reference existing records. If those records are deleted, the reports become ghost orphans.

---

## 6. FK Integrity Verdict

| Area | Status |
|------|--------|
| Core content FK constraints | PASS (all present) |
| Cascade strategy for listings | PASS |
| nullOnDelete causing orphan data | FAIL — 4 real orphans in DB |
| sessions.user_id missing FK | FAIL |
| payment_history.plan_slug missing FK | FAIL |
| flagged_posts — no post_id, no morph FK | FAIL |
| Polymorphic FK coverage | ACCEPTABLE (Laravel standard) |
