# GoBazaar — Master Data Consistency Document
**Phase 1.5 — Data Consistency Discovery**  
**Generated:** 2026-07-03  
**Status:** READ-ONLY DISCOVERY — No bugs reported. No fixes applied. Single Source of Truth.  
**Stack:** Laravel 12.56 · PHP 8.2 · Filament 3.3.50 · MySQL · AWS S3 · Stripe · Reverb WebSockets

---

## TABLE OF CONTENTS

1. [DATABASE INVENTORY](#1-database-inventory)
2. [MODEL INVENTORY](#2-model-inventory)
3. [COMPLETE FIELD MAPPING](#3-complete-field-mapping)
4. [IMAGE LIFECYCLE](#4-image-lifecycle)
5. [USER VS ADMIN MATRIX](#5-user-vs-admin-matrix)
6. [BUSINESS RULE INVENTORY](#6-business-rule-inventory)
7. [CRUD MATRIX](#7-crud-matrix)
8. [VALIDATION MATRIX](#8-validation-matrix)
9. [UI INVENTORY](#9-ui-inventory)
10. [QA COVERAGE MATRIX](#10-qa-coverage-matrix)

---

## 1. DATABASE INVENTORY

### 1.1 users
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| name | varchar(255) | NO | — | |
| email | varchar(255) | NO | — | UNIQUE |
| email_verified_at | timestamp | YES | NULL | |
| password | varchar(255) | NO | — | hashed |
| remember_token | varchar(100) | YES | NULL | |
| phone | varchar(255) | YES | NULL | |
| avatar | varchar(255) | YES | NULL | S3 path |
| city | varchar(255) | YES | NULL | |
| province | varchar(255) | YES | NULL | |
| bio | text | YES | NULL | |
| is_admin | tinyint(1) | NO | 0 | boolean |
| is_active | tinyint(1) | NO | 1 | boolean |
| plan | varchar(255) | YES | 'free' | enum: free/verified/power_seller |
| plan_expires_at | timestamp | YES | NULL | |
| stripe_customer_id | varchar(255) | YES | NULL | |
| stripe_subscription_id | varchar(255) | YES | NULL | |
| subscription_status | varchar(255) | YES | NULL | |
| featured_credits_used | int | NO | 0 | |
| featured_credits_reset_at | timestamp | YES | NULL | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |
**Indexes:** UNIQUE(email)

### 1.2 listings
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| user_id | bigint unsigned | YES | NULL | FK → users |
| category_id | bigint unsigned | YES | NULL | FK → categories |
| title | varchar(255) | NO | — | |
| slug | varchar(255) | NO | — | UNIQUE |
| description | longtext | YES | NULL | rich text |
| price | varchar(50) | YES | NULL | string, not decimal |
| price_unit | varchar(20) | YES | NULL | e.g. /mo |
| location | varchar(150) | YES | NULL | free text |
| city | varchar(100) | YES | NULL | |
| province | varchar(100) | YES | NULL | |
| image | varchar(255) | YES | NULL | S3 path, first photo |
| images | json | YES | NULL | array of S3 paths |
| tags | json | YES | NULL | array |
| badges | json | YES | NULL | array: feat/ver/new/hot |
| status | enum | NO | 'pending' | pending/active/rejected/expired |
| is_featured | tinyint(1) | NO | 0 | |
| is_verified | tinyint(1) | NO | 0 | |
| expires_at | timestamp | YES | NULL | |
| views | int unsigned | NO | 0 | |
| contact_name | varchar(100) | YES | NULL | |
| contact_email | varchar(150) | YES | NULL | |
| contact_phone | varchar(30) | YES | NULL | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |
**Indexes:** UNIQUE(slug); INDEX(status); INDEX(user_id); INDEX(category_id)

### 1.3 job_listings (Job model uses $table='job_listings')
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| user_id | bigint unsigned | YES | NULL | FK → users |
| category_id | bigint unsigned | YES | NULL | FK → categories |
| title | varchar(255) | NO | — | |
| slug | varchar(255) | NO | — | UNIQUE |
| company | varchar(150) | NO | — | |
| company_logo | varchar(255) | YES | NULL | S3 path |
| description | longtext | YES | NULL | |
| requirements | longtext | YES | NULL | |
| location | varchar(150) | YES | NULL | |
| city | varchar(100) | YES | NULL | |
| province | varchar(100) | YES | NULL | |
| job_type | enum | NO | — | full-time/part-time/contract/freelance/internship |
| work_mode | enum | NO | — | onsite/remote/hybrid |
| salary | varchar(100) | YES | NULL | |
| experience | varchar(100) | YES | NULL | |
| tags | json | YES | NULL | |
| apply_email | varchar(150) | YES | NULL | |
| apply_url | varchar(255) | YES | NULL | |
| is_featured | tinyint(1) | NO | 0 | |
| status | enum | NO | 'pending' | pending/active/rejected/expired |
| expires_at | timestamp | YES | NULL | |
| views | int unsigned | NO | 0 | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |
**Indexes:** UNIQUE(slug); INDEX(status)

### 1.4 events
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| user_id | bigint unsigned | YES | NULL | FK → users |
| category_id | bigint unsigned | YES | NULL | FK → categories |
| title | varchar(255) | NO | — | |
| slug | varchar(255) | NO | — | UNIQUE |
| description | longtext | YES | NULL | |
| image | varchar(255) | YES | NULL | S3 path |
| start_date | datetime | NO | — | |
| end_date | datetime | YES | NULL | |
| venue | varchar(200) | YES | NULL | |
| city | varchar(100) | YES | NULL | |
| province | varchar(100) | YES | NULL | |
| price | varchar(50) | YES | NULL | |
| organizer | varchar(150) | YES | NULL | |
| organizer_phone | varchar(30) | YES | NULL | |
| organizer_email | varchar(150) | YES | NULL | |
| website | varchar(255) | YES | NULL | |
| tags | json | YES | NULL | |
| is_featured | tinyint(1) | NO | 0 | |
| status | enum | NO | 'pending' | pending/active/rejected/expired |
| views | int unsigned | NO | 0 | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |
**Notes:** Events do NOT have expires_at column. Expiry managed by start_date (is_past accessor).

### 1.5 businesses
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| user_id | bigint unsigned | YES | NULL | FK → users |
| category_id | bigint unsigned | YES | NULL | FK → categories |
| name | varchar(150) | NO | — | |
| slug | varchar(255) | NO | — | UNIQUE |
| description | longtext | YES | NULL | |
| image | varchar(255) | YES | NULL | S3 path, main banner |
| images | json | YES | NULL | array of S3 paths |
| logo | varchar(255) | YES | NULL | S3 path |
| address | varchar(255) | YES | NULL | |
| city | varchar(100) | YES | NULL | |
| province | varchar(100) | YES | NULL | |
| phone | varchar(30) | YES | NULL | |
| email | varchar(150) | YES | NULL | |
| website | varchar(255) | YES | NULL | |
| map_url | varchar(500) | YES | NULL | |
| tags | json | YES | NULL | |
| social | json | YES | NULL | {facebook,instagram,twitter,linkedin,youtube,whatsapp} |
| rating | decimal(3,1) | NO | 0.0 | |
| review_count | int | NO | 0 | |
| is_verified | tinyint(1) | NO | 0 | |
| is_featured | tinyint(1) | NO | 0 | |
| status | enum | NO | 'pending' | pending/active/rejected/inactive |
| hours | json | YES | NULL | {mon:{open,close,closed},tue:...} |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |
**Added later (migration 2026-07-02):** social, map_url columns

### 1.6 business_posts
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| business_id | bigint unsigned | NO | — | FK → businesses |
| category_id | bigint unsigned | YES | NULL | FK → categories |
| subcategory_id | bigint unsigned | YES | NULL | FK → categories |
| title | varchar(150) | NO | — | |
| description | longtext | YES | NULL | |
| price | varchar(50) | YES | NULL | |
| price_unit | varchar(20) | YES | NULL | |
| image | varchar(255) | YES | NULL | S3 path |
| images | json | YES | NULL | array of S3 paths |
| status | enum | NO | 'pending' | pending/active/rejected |
| views | int unsigned | NO | 0 | |
| custom_fields | json | YES | NULL | dynamic fields from CategoryFields |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.7 matrimonials
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| user_id | bigint unsigned | NO | — | FK → users |
| name | varchar(100) | NO | — | |
| slug | varchar(255) | NO | — | UNIQUE |
| profile_for | enum | NO | — | self/son/daughter/brother/sister/friend |
| gender | enum | NO | — | male/female |
| age | tinyint unsigned | NO | — | 18–80 |
| height | varchar(20) | YES | NULL | |
| marital_status | enum | NO | — | never_married/divorced/widowed |
| diet | enum | YES | NULL | veg/non-veg/eggetarian |
| religion | varchar(100) | YES | NULL | |
| caste | varchar(100) | YES | NULL | |
| mother_tongue | varchar(100) | YES | NULL | |
| education | varchar(150) | YES | NULL | |
| occupation | varchar(150) | YES | NULL | |
| income | varchar(100) | YES | NULL | |
| city | varchar(100) | NO | — | |
| province | varchar(100) | NO | — | |
| country | varchar(100) | NO | 'Canada' | |
| about | text | YES | NULL | max 2000 chars |
| partner_preference | text | YES | NULL | max 2000 chars |
| contact_name | varchar(100) | YES | NULL | |
| contact_phone | varchar(30) | YES | NULL | |
| contact_email | varchar(150) | YES | NULL | |
| hide_contact | tinyint(1) | NO | 0 | |
| photo | varchar(255) | YES | NULL | S3 path, primary photo |
| photos | json | YES | NULL | array of S3 paths |
| status | enum | NO | 'pending' | pending/active/rejected |
| is_featured | tinyint(1) | NO | 0 | |
| views | int unsigned | NO | 0 | |
| expires_at | timestamp | YES | NULL | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.8 blog_posts
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| user_id | bigint unsigned | YES | NULL | FK → users |
| title | varchar(255) | NO | — | |
| slug | varchar(255) | NO | — | UNIQUE |
| excerpt | text | YES | NULL | |
| body | longtext | NO | — | rich text (HTML) |
| image | varchar(255) | YES | NULL | S3 path (accessor still uses asset()) |
| category | varchar(100) | YES | NULL | free text string, not FK |
| tags | json | YES | NULL | |
| status | enum | NO | 'draft' | draft/published |
| is_featured | tinyint(1) | NO | 0 | |
| views | int unsigned | NO | 0 | |
| published_at | timestamp | YES | NULL | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.9 categories
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| parent_id | bigint unsigned | YES | NULL | FK → categories (self-referential) |
| name | varchar(100) | NO | — | |
| slug | varchar(100) | NO | — | UNIQUE |
| icon | varchar(50) | YES | NULL | emoji or icon class |
| type | varchar(50) | YES | NULL | classifieds/jobs/events/business/matrimonial/blog |
| is_active | tinyint(1) | NO | 1 | |
| sort_order | int | NO | 0 | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.10 category_fields
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| category_id | bigint unsigned | NO | — | FK → categories |
| label | varchar(100) | NO | — | |
| key | varchar(100) | NO | — | used as JSON key in custom_fields |
| type | varchar(50) | NO | — | text/number/select/radio/checkbox/textarea |
| options | json | YES | NULL | array of options for select/radio/checkbox |
| is_required | tinyint(1) | NO | 0 | |
| sort_order | int | NO | 0 | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.11 locations
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| province | varchar(100) | NO | — | |
| city | varchar(100) | NO | — | |
| is_active | tinyint(1) | NO | 1 | |
| sort_order | int | NO | 0 | |
| city_image | varchar(255) | YES | NULL | S3 path |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.12 plans
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| slug | varchar(50) | NO | — | UNIQUE: free/verified/power_seller |
| name | varchar(100) | NO | — | |
| icon | varchar(50) | YES | NULL | |
| icon_bg | varchar(50) | YES | NULL | |
| price | decimal(8,2) | NO | 0.00 | |
| stripe_price_id | varchar(100) | YES | NULL | |
| period | varchar(20) | YES | NULL | monthly/yearly |
| tagline | varchar(255) | YES | NULL | |
| is_popular | tinyint(1) | NO | 0 | |
| is_active | tinyint(1) | NO | 1 | |
| sort_order | int | NO | 0 | |
| features | json | YES | NULL | [{text,included,highlight}] |
| post_days | int | YES | NULL | 0=permanent |
| max_listings | int | YES | NULL | |
| max_images | int | YES | NULL | |
| biz_listings | int | YES | NULL | |
| verified_badge | tinyint(1) | NO | 0 | |
| featured_placement | tinyint(1) | NO | 0 | |
| unlimited_posts | tinyint(1) | NO | 0 | |
| priority_support | tinyint(1) | NO | 0 | |
| analytics | tinyint(1) | NO | 0 | |
| auto_renew | tinyint(1) | NO | 0 | |
| favorites | tinyint(1) | NO | 0 | |
| featured_credits | int | NO | 0 | |
| bulk_upload | tinyint(1) | NO | 0 | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.13 advertisements
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| title | varchar(255) | NO | — | |
| image | varchar(255) | NO | — | S3 path |
| url | varchar(255) | NO | — | click-through URL |
| position | enum | NO | — | hero_banner/sidebar_top/sidebar_bottom/feed_inline/footer_banner |
| scope | enum | NO | 'canada' | canada/province/city |
| province | varchar(100) | YES | NULL | if scope=province or city |
| city | varchar(100) | YES | NULL | if scope=city |
| category_type | varchar(100) | YES | NULL | filter by content type |
| is_active | tinyint(1) | NO | 1 | |
| slide_duration | int | NO | 5 | seconds per slide |
| impressions | int unsigned | NO | 0 | |
| clicks | int unsigned | NO | 0 | |
| starts_at | timestamp | YES | NULL | |
| ends_at | timestamp | YES | NULL | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.14 advertise_requests
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| business_name | varchar(255) | NO | — | |
| contact_name | varchar(255) | NO | — | |
| email | varchar(255) | NO | — | |
| phone | varchar(255) | YES | NULL | |
| ad_type | varchar(255) | NO | — | |
| message | text | YES | NULL | |
| status | enum | NO | 'pending' | pending/contacted/approved/rejected |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.15 polls
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| question | varchar(255) | NO | — | |
| options | json | NO | — | [{text, votes}] |
| scope | enum | NO | 'canada' | canada/province/city |
| province | varchar(100) | YES | NULL | |
| city | varchar(100) | YES | NULL | |
| is_active | tinyint(1) | NO | 1 | |
| expires_at | timestamp | YES | NULL | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.16 conversations
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| conversable_type | varchar(255) | NO | — | polymorphic type |
| conversable_id | bigint unsigned | NO | — | polymorphic id |
| buyer_id | bigint unsigned | NO | — | FK → users (initiator) |
| seller_id | bigint unsigned | NO | — | FK → users (listing owner) |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |
**Polymorphic types:** App\Models\Listing, App\Models\Event, App\Models\Business, App\Models\BusinessPost

### 1.17 chat_messages
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| conversation_id | bigint unsigned | NO | — | FK → conversations |
| sender_id | bigint unsigned | NO | — | FK → users |
| body | text | NO | — | |
| is_read | tinyint(1) | NO | 0 | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.18 reports
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| reportable_type | varchar(255) | NO | — | polymorphic type |
| reportable_id | bigint unsigned | NO | — | polymorphic id |
| reporter_id | bigint unsigned | YES | NULL | FK → users |
| reason | varchar(100) | NO | — | see Report::reasons() |
| details | text | YES | NULL | |
| status | enum | NO | 'pending' | pending/reviewed/dismissed |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |
**Polymorphic types:** listing/event/business/job/blog_post

### 1.19 flagged_posts
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| post_type | varchar(50) | NO | — | listing/job/event/business/business_post/matrimonial |
| post_id | bigint unsigned | NO | — | |
| user_id | bigint unsigned | YES | NULL | FK → users |
| flag_type | varchar(50) | NO | — | moderation/image/ai |
| reason | text | NO | — | |
| raw_data | json | YES | NULL | cast as array |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.20 user_favorites
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| user_id | bigint unsigned | NO | — | FK → users |
| favoriteable_type | varchar(255) | NO | — | polymorphic type |
| favoriteable_id | bigint unsigned | NO | — | polymorphic id |
**Note:** $timestamps = false on model  
**Polymorphic types:** App\Models\Listing, App\Models\Job, App\Models\Event, App\Models\Business

### 1.21 listing_views
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| listing_id | bigint unsigned | NO | — | FK → listings |
| ip | varchar(45) | YES | NULL | |
| user_agent | varchar(255) | YES | NULL | |
| referer | varchar(255) | YES | NULL | |
| device | varchar(50) | YES | NULL | desktop/mobile/tablet |
| viewed_at | timestamp | NO | — | |

### 1.22 payment_history (PaymentHistory model $table='payment_history')
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| user_id | bigint unsigned | NO | — | FK → users |
| stripe_payment_intent_id | varchar(255) | YES | NULL | |
| stripe_subscription_id | varchar(255) | YES | NULL | |
| plan | varchar(50) | NO | — | |
| amount | int | NO | — | in cents |
| currency | varchar(10) | NO | 'cad' | |
| status | varchar(50) | NO | — | succeeded/failed/pending |
| description | text | YES | NULL | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.23 featured_credit_logs
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| user_id | bigint unsigned | NO | — | FK → users |
| listing_id | bigint unsigned | NO | — | FK → listings |
| action | varchar(50) | NO | — | featured/unfeatured |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |

### 1.24 settings
| Column | Datatype | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned | NO | AI | PK |
| key | varchar(100) | NO | — | UNIQUE |
| value | text | YES | NULL | |
| created_at | timestamp | YES | NULL | |
| updated_at | timestamp | YES | NULL | |
**Known keys:** email_verification_required

### 1.25 Framework Tables (read-only reference)
- **cache** — Laravel cache driver store
- **cache_locks** — cache lock store
- **jobs** — Laravel queue jobs (NOT Job classifieds — different table)
- **job_batches** — queue batch tracking
- **failed_jobs** — failed queue records
- **sessions** — session store
- **personal_access_tokens** — Sanctum tokens

---

## 2. MODEL INVENTORY

### 2.1 User (`app/Models/User.php`)
**Table:** users  
**Implements:** FilamentUser, MustVerifyEmail  
**Traits:** HasFactory, Notifiable  
**Fillable:** name, email, password, phone, avatar, city, province, bio, is_admin, is_active, plan, plan_expires_at, stripe_customer_id, stripe_subscription_id, subscription_status, featured_credits_used, featured_credits_reset_at  
**Hidden:** password, remember_token  
**Casts:** email_verified_at(datetime), password(hashed), is_admin(boolean), is_active(boolean), plan_expires_at(datetime), featured_credits_reset_at(datetime)  
**Relationships:** listings(HasMany), userFavorites(HasMany), featuredCreditLogs(HasMany)  
**Accessors:** avatar_url (→ S3 URL)  
**Key Methods:** activePlan(), isSubscribed(), planModel(), planName(), postDays(), maxListings(), maxImages(), activeListingCount(), canPostListing(), maxBusinessListings(), activeBusinessCount(), canPostBusiness(), canPostEvent(), canPostBusinessPost(), hasVerifiedBadge(), hasAutoRenew(), hasPrioritySearch(), hasAnalytics(), hasFavorites(), featuredCredits(), featuredCreditsRemaining(), canFeatureListing(), maybeResetCredits(), hasBulkUpload()  
**Admin gate:** canAccessPanel() — true if is_admin=true OR id=1  

### 2.2 Listing (`app/Models/Listing.php`)
**Table:** listings  
**Traits:** Favoritable  
**Fillable:** user_id, category_id, title, slug, description, price, price_unit, location, city, province, image, images, tags, badges, status, is_featured, is_verified, expires_at, views, contact_name, contact_email, contact_phone  
**Casts:** tags(array), badges(array), images(array), is_featured(boolean), is_verified(boolean), expires_at(datetime)  
**Relationships:** category(BelongsTo), user(BelongsTo), listingViews(HasMany)  
**Accessors:** image_url (→ S3 URL)  
**Scopes:** live() — status=active AND (expires_at IS NULL OR expires_at > now)  
**Methods:** uniqueViewsCount(), viewsLast30Days(), isExpired()  

### 2.3 Job (`app/Models/Job.php`)
**Table:** job_listings  
**Traits:** Favoritable  
**Fillable:** user_id, category_id, title, slug, company, company_logo, description, requirements, location, city, province, job_type, work_mode, salary, experience, tags, apply_email, apply_url, is_featured, status, expires_at, views  
**Casts:** tags(array), is_featured(boolean), expires_at(datetime)  
**Relationships:** category(BelongsTo), user(BelongsTo)  
**Accessors:** job_type_label, work_mode_label, logo_url (→ S3 URL)  
**Scopes:** live()  
**Methods:** isExpired()  

### 2.4 Event (`app/Models/Event.php`)
**Table:** events  
**Traits:** Favoritable  
**Fillable:** user_id, category_id, title, slug, description, image, start_date, end_date, venue, city, province, price, organizer, organizer_phone, organizer_email, website, tags, is_featured, status, views  
**Casts:** tags(array), is_featured(boolean), start_date(datetime), end_date(datetime)  
**Relationships:** category(BelongsTo), user(BelongsTo)  
**Accessors:** location (computed: venue + city + province), is_past (start_date->isPast()), image_url (→ S3 URL)  

### 2.5 Business (`app/Models/Business.php`)
**Table:** businesses  
**Traits:** Favoritable  
**Fillable:** user_id, category_id, name, slug, description, image, images, logo, address, city, province, phone, email, website, map_url, tags, social, rating, review_count, is_verified, is_featured, status, hours  
**Casts:** tags(array), images(array), social(array), hours(array), is_verified(boolean), is_featured(boolean), rating(decimal:1)  
**Relationships:** category(BelongsTo), user(BelongsTo), posts(HasMany BusinessPost)  
**Accessors:** location (city + province), image_url (image ?: logo → S3), logo_url (→ S3)  

### 2.6 BusinessPost (`app/Models/BusinessPost.php`)
**Table:** business_posts  
**Fillable:** business_id, category_id, subcategory_id, title, description, price, price_unit, image, images, status, views, custom_fields  
**Casts:** images(array), custom_fields(array)  
**Relationships:** business(BelongsTo)  
**Accessors:** image_url (→ S3)  

### 2.7 Matrimonial (`app/Models/Matrimonial.php`)
**Table:** matrimonials  
**Fillable:** user_id, name, slug, profile_for, gender, age, height, marital_status, diet, religion, caste, mother_tongue, education, occupation, income, city, province, country, about, partner_preference, contact_name, contact_phone, contact_email, hide_contact, photo, photos, status, is_featured, views, expires_at  
**Casts:** photos(array), hide_contact(boolean), is_featured(boolean), expires_at(datetime)  
**Relationships:** user(BelongsTo)  
**Accessors:** photo_url (→ S3), marital_status_label  

### 2.8 BlogPost (`app/Models/BlogPost.php`)
**Table:** blog_posts  
**Fillable:** user_id, title, slug, excerpt, body, image, category, tags, status, is_featured, views, published_at  
**Casts:** tags(array), is_featured(boolean), published_at(datetime)  
**Relationships:** author (BelongsTo User via user_id)  
**Accessors:** read_time (word_count/200 min read), image_url — **uses asset('storage/') NOT S3**  

### 2.9 Category (`app/Models/Category.php`)
**Table:** categories  
**Fillable:** parent_id, name, slug, icon, type, is_active, sort_order  
**Relationships:** listings(HasMany), businesses(HasMany), parent(BelongsTo Category), children(HasMany Category ordered by sort_order), fields(HasMany CategoryField ordered by sort_order)  
**Scopes:** parents(), subs()  
**Methods:** applicableFields() — fields inherited from parent if child has none  

### 2.10 CategoryField (`app/Models/CategoryField.php`)
**Table:** category_fields  
**Fillable:** category_id, label, key, type, options, is_required, sort_order  
**Casts:** options(array), is_required(boolean)  
**Relationships:** category(BelongsTo)  

### 2.11 Location (`app/Models/Location.php`)
**Table:** locations  
**Fillable:** province, city, is_active, sort_order, city_image  
**Casts:** is_active(boolean)  
**Static Methods:** activeCities(province), activeProvinces()  

### 2.12 Plan (`app/Models/Plan.php`)
**Table:** plans  
**Fillable:** slug, name, icon, icon_bg, price, stripe_price_id, period, tagline, is_popular, is_active, sort_order, features, post_days, max_listings, max_images, biz_listings, verified_badge, featured_placement, unlimited_posts, priority_support, analytics, auto_renew, favorites, featured_credits, bulk_upload  
**Casts:** is_popular(boolean), is_active(boolean), verified_badge(boolean), featured_placement(boolean), unlimited_posts(boolean), priority_support(boolean), analytics(boolean), auto_renew(boolean), favorites(boolean), bulk_upload(boolean), features(array), price(decimal:2)  
**Static Methods:** active(), findBySlug(slug)  

### 2.13 Advertisement (`app/Models/Advertisement.php`)
**Table:** advertisements  
**Fillable:** title, image, url, position, scope, province, city, category_type, is_active, slide_duration, impressions, clicks, starts_at, ends_at  
**Casts:** is_active(boolean), starts_at(datetime), ends_at(datetime)  
**Accessors:** image_url (→ S3)  

### 2.14 Poll (`app/Models/Poll.php`)
**Table:** polls  
**Fillable:** question, options, scope, province, city, is_active, expires_at  
**Casts:** options(array), is_active(boolean), expires_at(datetime)  
**Accessors:** total_votes (sum of options[*].votes), is_expired (expires_at->isPast())  
**Static Methods:** current(city, province) — city > province > canada priority  

### 2.15 Conversation (`app/Models/Conversation.php`)
**Table:** conversations  
**Fillable:** conversable_type, conversable_id, buyer_id, seller_id  
**Relationships:** conversable(morphTo), messages(HasMany ChatMessage), buyer(BelongsTo User), seller(BelongsTo User)  

### 2.16 ChatMessage (`app/Models/ChatMessage.php`)
**Table:** chat_messages  
**Fillable:** conversation_id, sender_id, body, is_read  
**Casts:** is_read(boolean)  
**Relationships:** conversation(BelongsTo), sender(BelongsTo User)  

### 2.17 Report (`app/Models/Report.php`)
**Table:** reports  
**Fillable:** reportable_type, reportable_id, reporter_id, reason, details, status  
**Relationships:** reportable(morphTo)  
**Static Methods:** reasons() — array of {value, label} pairs  
**Reason values:** spam, misleading, offensive, scam, wrong_category, duplicate, other  

### 2.18 FlaggedPost (`app/Models/FlaggedPost.php`)
**Table:** flagged_posts  
**Fillable:** post_type, post_id, user_id, flag_type, reason, raw_data  
**Casts:** raw_data(array)  

### 2.19 UserFavorite (`app/Models/UserFavorite.php`)
**Table:** user_favorites  
**$timestamps = false**  
**Fillable:** user_id, favoriteable_type, favoriteable_id  
**Relationships:** favoriteable(morphTo), user(BelongsTo)  

### 2.20 PaymentHistory (`app/Models/PaymentHistory.php`)
**Table:** payment_history  
**Fillable:** user_id, stripe_payment_intent_id, stripe_subscription_id, plan, amount, currency, status, description  
**Accessors:** amount_formatted (amount/100 formatted as CAD), status_badge (color class)  

### 2.21 FeaturedCreditLog (`app/Models/FeaturedCreditLog.php`)
**Table:** featured_credit_logs  
**Fillable:** user_id, listing_id, action  

### 2.22 AdvertiseRequest (`app/Models/AdvertiseRequest.php`)
**Table:** advertise_requests  
**Fillable:** business_name, contact_name, email, phone, ad_type, message, status  

### 2.23 ListingView (`app/Models/ListingView.php`)
**Table:** listing_views  
**Fillable:** listing_id, ip, user_agent, referer, device, viewed_at  

### 2.24 Setting (`app/Models/Setting.php`)
**Table:** settings  
**Fillable:** key, value  
**Cache:** 5-minute TTL via Setting::get(key)  
**Known keys:** email_verification_required  

### 2.25 Favoritable Trait (`app/Traits/Favoritable.php`)
Applied to: Listing, Job, Event, Business  
**Methods:** isFavoritedBy(user), favoritesCount()  

---

## 3. COMPLETE FIELD MAPPING

### 3.1 CLASSIFIED (Listing) Module
| Field | DB Column | Datatype | User Create | User Edit | User View | Admin Create | Admin Edit | Admin View | Public Card | Image | Required |
|---|---|---|---|---|---|---|---|---|---|---|---|
| Title | title | varchar(150) | text input | text input | h1 | text input | text input | yes | yes | no | YES |
| Category | category_id | bigint FK | select | select | badge | select | select | yes | badge | no | YES |
| Description | description | longtext | rich editor (Quill) | rich editor | formatted | rich editor | rich editor | yes | truncated | no | NO |
| Price | price | varchar(50) | text input | text input | yes | text input | text input | yes | yes | no | NO |
| Price Unit | price_unit | varchar(20) | select (/mo,/yr,/wk,/day,each,negotiable) | select | yes | text input | text input | yes | with price | no | NO |
| Location | location | varchar(150) | text input | text input | yes | text input | text input | yes | yes | no | NO |
| City | city | varchar(100) | select (dynamic) | select | yes | text input | text input | yes | yes | no | YES |
| Province | province | varchar(100) | select | select | yes | text input | text input | yes | yes | no | YES |
| Contact Name | contact_name | varchar(100) | — | — | no | text input | text input | yes | no | no | NO |
| Contact Email | contact_email | varchar(150) | email input | email input | yes | email input | email input | yes | no | no | NO |
| Contact Phone | contact_phone | varchar(30) | text input | text input | yes | text input | text input | yes | no | no | NO |
| Images | images (json) | json | image uploader (multi) | image uploader | gallery | file upload | file upload | yes | first img | YES | NO |
| Image (main) | image | varchar(255) | derived from images[0] | derived | thumbnail | — | — | yes | card img | YES | NO |
| Tags | tags | json | — | — | no | tags input | tags input | yes | no | no | NO |
| Badges | badges | json | — | — | no | checkbox list | checkbox list | yes | badges | no | NO |
| Status | status | enum | auto=pending | — | — | select | select | yes | — | no | NO (auto) |
| Is Featured | is_featured | boolean | — | — | badge | toggle | toggle | yes | featured tag | no | NO |
| Is Verified | is_verified | boolean | — | — | badge | toggle | toggle | yes | verified tag | no | NO |
| Expires At | expires_at | datetime | auto (plan-based) | — | shown | date picker | date picker | yes | — | no | NO (auto) |
| Views | views | int | — | — | yes | — | — | yes | — | no | NO (auto) |
| Slug | slug | varchar | auto-generated | — | URL | text input | text input | yes | URL | no | YES (auto) |

### 3.2 JOB Module
| Field | DB Column | Datatype | User Create | User Edit | User View | Admin Create | Admin Edit | Admin View | Public Card | Required |
|---|---|---|---|---|---|---|---|---|---|---|
| Title | title | varchar(150) | text input | text input | h1 | text input | text input | yes | yes | YES |
| Company | company | varchar(150) | text input | text input | yes | text input | text input | yes | yes | YES |
| Company Logo | company_logo | varchar(255) | image uploader (single) | image uploader | yes | file upload | file upload | yes | logo | NO |
| Category | category_id | bigint FK | select (type=jobs) | select | badge | select | select | yes | badge | NO |
| Job Type | job_type | enum | select | select | badge | select | select | yes | badge | YES |
| Work Mode | work_mode | enum | select | select | badge | select | select | yes | badge | YES |
| Description | description | longtext | rich editor (Quill) | rich editor | formatted | text area | text area | yes | truncated | NO |
| Requirements | requirements | longtext | rich editor (Quill) | rich editor | formatted | text area | text area | yes | — | NO |
| Salary | salary | varchar(100) | text input | text input | yes | text input | text input | yes | yes | NO |
| Experience | experience | varchar(100) | text input | text input | yes | text input | text input | yes | — | NO |
| Location | location | varchar(150) | text input | text input | yes | text input | text input | yes | yes | NO |
| City | city | varchar(100) | select (dynamic) | select | yes | text input | text input | yes | yes | YES |
| Province | province | varchar(100) | select | select | yes | text input | text input | yes | yes | YES |
| Apply Email | apply_email | varchar(150) | email input | email input | — | email input | email input | yes | apply btn | NO |
| Apply URL | apply_url | varchar(255) | — | — | — | text input | text input | yes | apply btn | NO |
| Tags | tags | json | — | — | no | tags input | tags input | yes | no | NO |
| Status | status | enum | auto=pending | — | — | select | select | yes | badge | NO (auto) |
| Is Featured | is_featured | boolean | — | — | badge | toggle | toggle | yes | tag | NO |
| Expires At | expires_at | datetime | auto (plan-based) | — | shown | date picker | date picker | yes | — | NO (auto) |
| Views | views | int | — | — | yes | — | — | yes | — | NO (auto) |

### 3.3 EVENT Module
| Field | DB Column | Datatype | User Create | User Edit | User View | Admin Create | Admin Edit | Admin View | Public Card | Required |
|---|---|---|---|---|---|---|---|---|---|---|
| Title | title | varchar(150) | text input | text input | h1 | text input | text input | yes | yes | YES |
| Start Date | start_date | datetime | datetime-local | datetime-local | formatted | date picker | date picker | yes | yes | YES |
| End Date | end_date | datetime | datetime-local | datetime-local | formatted | date picker | date picker | yes | — | NO |
| Category | category_id | bigint FK | select (type=events) | select | badge | select | select | yes | badge | NO |
| Venue | venue | varchar(200) | text input | text input | yes | text input | text input | yes | yes | NO |
| City | city | varchar(100) | select (dynamic) | select | yes | text input | text input | yes | yes | YES |
| Province | province | varchar(100) | select | select | yes | text input | text input | yes | yes | YES |
| Price | price | varchar(50) | text input | text input | yes | text input | text input | yes | yes | NO |
| Image | image | varchar(255) | image uploader (single) | image uploader | banner | file upload | file upload | yes | card | NO |
| Description | description | longtext | rich editor (Quill) | rich editor | formatted | textarea | textarea | yes | truncated | NO |
| Organizer | organizer | varchar(150) | text input | text input | yes | text input | text input | yes | — | NO |
| Organizer Phone | organizer_phone | varchar(30) | text input | text input | yes | text input | text input | yes | — | NO |
| Organizer Email | organizer_email | varchar(150) | email input | email input | yes | email input | email input | yes | — | NO |
| Website | website | varchar(255) | — | — | — | text input | text input | yes | — | NO |
| Tags | tags | json | — | — | no | tags input | tags input | yes | — | NO |
| Status | status | enum | auto=pending | — | — | select | select | yes | badge | NO (auto) |
| Is Featured | is_featured | boolean | — | — | badge | toggle | toggle | yes | tag | NO |
| Views | views | int | — | — | yes | — | — | yes | — | NO (auto) |
**NOTE:** Events have NO expires_at column. Expiry derived from start_date.

### 3.4 BUSINESS Module
| Field | DB Column | Datatype | User Create | User Edit | User View | Admin Create | Admin Edit | Admin View | Public Card | Required |
|---|---|---|---|---|---|---|---|---|---|---|
| Name | name | varchar(150) | text input | text input | h1 | text input | text input | yes | yes | YES |
| Category | category_id | bigint FK | select (parent cats) | select | badge | select | select | yes | badge | YES |
| Subcategory | subcategory_id | — | select (dynamic) | select | — | — | — | — | — | NO |
| Description | description | longtext | rich editor (Quill) | rich editor | formatted | rich editor | rich editor | yes | truncated | NO |
| Address | address | varchar(255) | text input | text input | yes | text input | text input | yes | yes | NO |
| City | city | varchar(100) | select (dynamic) | select | yes | text input | text input | yes | yes | YES |
| Province | province | varchar(100) | select | select | yes | text input | text input | yes | yes | YES |
| Phone | phone | varchar(30) | text input | text input | yes | text input | text input | yes | yes | NO |
| Email | email | varchar(150) | email input | email input | yes | email input | email input | yes | — | NO |
| Website | website | varchar(255) | — | — | yes | text input | text input | yes | — | NO |
| Map URL | map_url | varchar(500) | — | — | yes (embed) | text input | text input | yes | — | NO |
| Images | images (json) | json | image uploader (multi) | image uploader | gallery | file upload | file upload | yes | first | YES | NO |
| Logo | logo | varchar(255) | image uploader (single) | image uploader | logo | file upload | file upload | yes | logo | YES | NO |
| Hours | hours | json | time inputs per day × 7 | time inputs | hours grid | text input (plain) | text input | yes | — | NO |
| Tags | tags | json | text input (comma sep) + hidden | text input | tags | text input | text input | yes | tags | NO |
| Social.facebook | social.facebook | json sub-key | text input | text input | link | text input | text input | yes | icon | NO |
| Social.instagram | social.instagram | json sub-key | text input | text input | link | text input | text input | yes | icon | NO |
| Social.twitter | social.twitter | json sub-key | text input | text input | link | text input | text input | yes | icon | NO |
| Social.linkedin | social.linkedin | json sub-key | text input | text input | link | text input | text input | yes | icon | NO |
| Social.youtube | social.youtube | json sub-key | text input | text input | link | text input | text input | yes | icon | NO |
| Social.whatsapp | social.whatsapp | json sub-key | text input | text input | link | text input | text input | yes | icon | NO |
| Rating | rating | decimal(3,1) | — | — | stars | number | number | yes | stars | NO |
| Review Count | review_count | int | — | — | count | number | number | yes | count | NO |
| Is Verified | is_verified | boolean | — | — | badge | toggle | toggle | yes | badge | NO |
| Is Featured | is_featured | boolean | — | — | badge | toggle | toggle | yes | tag | NO |
| Status | status | enum | auto=pending | — | — | select | select | yes | badge | NO (auto) |

### 3.5 BUSINESS POST Module
| Field | DB Column | Datatype | User Create | User Edit | User View | Admin Create | Admin Edit | Admin View | Public Card | Required |
|---|---|---|---|---|---|---|---|---|---|---|
| Business | business_id | bigint FK | hidden (selected) | locked | parent | select | select | yes | parent name | YES |
| Category | category_id | bigint FK | select | select | — | select | select | yes | badge | YES |
| Subcategory | subcategory_id | bigint FK | select (dynamic) | select | — | — | — | — | — | NO |
| Title | title | varchar(150) | text input | text input | h2 | text input | text input | yes | yes | YES |
| Price | price | varchar(50) | text input | text input | yes | text input | text input | yes | yes | NO |
| Price Unit | price_unit | varchar(20) | text input | text input | yes | text input | text input | yes | with price | NO |
| Description | description | longtext | rich editor (Quill) | rich editor | formatted | rich editor | rich editor | yes | truncated | NO |
| Images | images (json) | json | image uploader (multi) | image uploader | gallery | file upload | file upload | yes | first | YES | NO |
| Custom Fields | custom_fields | json | dynamic (from category) | dynamic | shown | — | read-only placeholder | yes | — | VARIES |
| Status | status | enum | auto=pending | — | — | select | select | yes | badge | NO (auto) |
| Views | views | int | — | — | yes | — | — | yes | — | NO (auto) |

### 3.6 MATRIMONIAL Module
| Field | DB Column | Datatype | User Create | User Edit | User View | Admin Create | Admin Edit | Admin View | Public Card | Required |
|---|---|---|---|---|---|---|---|---|---|---|
| Profile For | profile_for | enum | select | select | shown | select | select | yes | — | YES |
| Gender | gender | enum | select | select | shown | select | select | yes | badge | YES |
| Name | name | varchar(100) | text input | text input | h1 | text input | text input | yes | yes | YES |
| Age | age | tinyint | number (18–80) | number | yes | number | number | yes | yes | YES |
| Height | height | varchar(20) | text input | text input | yes | text input | text input | yes | — | NO |
| Marital Status | marital_status | enum | select | select | label | select | select | yes | — | YES |
| Diet | diet | enum | select | select | yes | select | select | yes | — | NO |
| Religion | religion | varchar(100) | text input | text input | yes | text input | text input | yes | yes | NO |
| Caste | caste | varchar(100) | text input | text input | yes | text input | text input | yes | — | NO |
| Mother Tongue | mother_tongue | varchar(100) | text input | text input | yes | text input | text input | yes | — | NO |
| Education | education | varchar(150) | text input | text input | yes | text input | text input | yes | — | NO |
| Occupation | occupation | varchar(150) | text input | text input | yes | text input | text input | yes | — | NO |
| Income | income | varchar(100) | text input | text input | yes | text input | text input | yes | — | NO |
| City | city | varchar(100) | select (dynamic) | select | yes | text input | text input | yes | yes | YES |
| Province | province | varchar(100) | select | select | yes | text input | text input | yes | — | YES |
| About | about | text | textarea | textarea | yes | textarea | textarea | yes | truncated | NO |
| Partner Preference | partner_preference | text | textarea | textarea | yes | textarea | textarea | yes | — | NO |
| Contact Name | contact_name | varchar(100) | — | — | — | text input | text input | yes | — | NO |
| Contact Phone | contact_phone | varchar(30) | text input | text input | if !hide | text input | text input | yes | — | NO |
| Contact Email | contact_email | varchar(150) | — | — | if !hide | email input | email input | yes | — | NO |
| Hide Contact | hide_contact | boolean | — | — | — | toggle | toggle | yes | — | NO |
| Photo | photo | varchar(255) | image uploader (single) | image uploader | avatar | file upload | file upload | yes | avatar | NO |
| Photos | photos | json | image uploader (multi) | image uploader | gallery | file upload | file upload | yes | — | NO |
| Status | status | enum | auto=pending | — | — | select | select | yes | badge | NO (auto) |
| Is Featured | is_featured | boolean | — | — | badge | toggle | toggle | yes | tag | NO |

### 3.7 BLOG Module
| Field | DB Column | Datatype | User Create | User Edit | User View | Admin Create | Admin Edit | Admin View | Public Card | Required |
|---|---|---|---|---|---|---|---|---|---|---|
| Author | user_id | bigint FK | — | — | shown | select | select | yes | yes | NO |
| Title | title | varchar(255) | — | — | h1 | text input | text input | yes | yes | YES |
| Slug | slug | varchar | auto | auto | URL | text input | text input | yes | URL | YES (auto) |
| Category | category | varchar(100) | — | — | label | text input | text input | yes | badge | NO |
| Excerpt | excerpt | text | — | — | italic intro | textarea | textarea | yes | — | NO |
| Body | body | longtext | — | — | rendered HTML | rich editor | rich editor | yes | — | YES |
| Image | image | varchar(255) | — | — | banner | file upload | file upload | yes | card | NO |
| Tags | tags | json | — | — | chips | tags input | tags input | yes | — | NO |
| Status | status | enum | — | — | — | select | select | yes | — | NO (draft default) |
| Is Featured | is_featured | boolean | — | — | — | toggle | toggle | yes | — | NO |
| Published At | published_at | datetime | — | — | date | date picker | date picker | yes | date | NO |
| Views | views | int | — | — | shown | — | — | yes | count | NO (auto) |
| Read Time | read_time | accessor | — | — | yes | — | — | computed | — | NO (computed) |
**NOTE:** Blog posts are Admin-only for create/edit. No user-facing form.

### 3.8 LOCATIONS Module (Admin only)
| Field | DB Column | Admin Create | Admin Edit | Admin View |
|---|---|---|---|---|
| Province | province | select (13 options) | select | badge |
| City | city | text input | text input | bold |
| Sort Order | sort_order | number (default 0) | number | yes |
| Is Active | is_active | toggle (default true) | toggle column | toggle column |
| City Image | city_image | file upload (S3) | file upload | image column |

### 3.9 CATEGORIES Module (Admin only)
| Field | DB Column | Admin Create | Admin Edit | Admin View |
|---|---|---|---|---|
| Parent | parent_id | select (nullable) | select | — |
| Name | name | text input | text input | yes |
| Slug | slug | auto + text input | text input (disabled after create) | yes |
| Icon | icon | text input | text input | yes |
| Type | type | select (classifieds/jobs/events/business/matrimonial/blog) | select | badge |
| Is Active | is_active | toggle | toggle column | toggle column |
| Sort Order | sort_order | number | number | yes |

### 3.10 ADVERTISEMENTS Module (Admin only)
| Field | DB Column | Admin Create | Admin Edit |
|---|---|---|---|
| Title | title | text input | text input |
| Image | image | file upload S3 (label varies by position) | file upload |
| URL | url | text input | text input |
| Position | position | select (5 options) | select |
| Scope | scope | select (canada/province/city) | select |
| Province | province | select (conditional on scope) | select |
| City | city | text input (conditional on scope=city) | text input |
| Category Type | category_type | text input | text input |
| Is Active | is_active | toggle | toggle column |
| Slide Duration | slide_duration | number (default 5) | number |
| Starts At | starts_at | date picker | date picker |
| Ends At | ends_at | date picker | date picker |

### 3.11 USERS Module (Admin only)
| Field | DB Column | Admin View | Admin Edit | Admin Actions |
|---|---|---|---|---|
| Name | name | yes | text input | — |
| Email | email | yes | email input | — |
| Avatar | avatar | image | file upload S3 | — |
| Phone | phone | — | text input | — |
| City | city | — | text input | — |
| Province | province | — | text input | — |
| Bio | bio | — | textarea | — |
| Is Admin | is_admin | badge | toggle | — |
| Is Active | is_active | toggle column | toggle | toggle_active action |
| Plan | plan | badge | — | Grant Plan action |
| Plan Expires At | plan_expires_at | — | date picker | — |
| Stripe Customer ID | stripe_customer_id | collapsible | text input | — |
| Stripe Subscription ID | stripe_subscription_id | collapsible | text input | — |
| Subscription Status | subscription_status | — | text input | Cancel Sub action |
| Featured Credits Used | featured_credits_used | — | number | — |
| Featured Credits Reset At | featured_credits_reset_at | — | date picker | — |

---

## 4. IMAGE LIFECYCLE

### 4.1 Global Image Rules (config/moderation.php)
- **Max size:** 2048 KB (2 MB) per image
- **Min dimensions:** 200 × 200 px
- **Max dimensions:** 6000 × 6000 px
- **Allowed MIME types:** jpg, jpeg, png, webp
- **Validation string:** `image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:min_width=200,min_height=200,max_width=6000,max_height=6000`
- **AI Scan (Google Vision):** Applied during content moderation flow; checks image labels against category expectations
- **AI Scan (OpenAI Moderation):** Applied to text fields, not directly to images

### 4.2 Storage Configuration
- **Disk:** AWS S3 (`FILESYSTEM_DISK=s3`)
- **Bucket:** Configured via `AWS_BUCKET` env var
- **URL generation:** `Storage::disk('s3')->url($path)` — returns CDN/S3 presigned or public URL
- **All uploads exclusively to S3 — no local disk fallback**

### 4.3 Image Fields Per Module

| Module | Field | S3 Directory | Single/Multiple | Max Count (plan-based) | Upload Method | Delete Method |
|---|---|---|---|---|---|---|
| Classified | images / image | `listings/` | Multiple | maxImages() — free=3, verified=5, power_seller=10 | PostController store() | Storage::disk('s3')->delete() |
| Job | company_logo | `jobs/` | Single | 1 | PostController storeJob() | Storage::disk('s3')->delete() |
| Event | image | `events/` | Single | 1 | PostController storeEvent() | Storage::disk('s3')->delete() |
| Business | images / image | `businesses/` | Multiple | maxImages() | PostController storeBusiness() | Storage::disk('s3')->delete() |
| Business | logo | `businesses/` | Single | 1 | PostController storeBusiness() | Storage::disk('s3')->delete() |
| BusinessPost | images / image | `business-posts/` | Multiple | maxImages() | PostController storeBusinessPost() | Storage::disk('s3')->delete() |
| Matrimonial | photo | `matrimonials/` | Single | 1 | PostController storeMatrimonial() | Storage::disk('s3')->delete() |
| Matrimonial | photos | `matrimonials/` | Multiple | varies | PostController storeMatrimonial() | Storage::disk('s3')->delete() |
| User | avatar | `avatars/` | Single | 1 | UserController updateProfile() | — (overwrites) |
| Blog | image | `blog/` | Single | 1 | Filament only | Filament FileUpload |
| Advertisement | image | `advertisements/` | Single | 1 | Filament only | Filament FileUpload |
| Location | city_image | `locations/cities/` | Single | 1 | Filament only | Filament FileUpload |
| Editor (inline) | — | `editor/` | Single per upload | unlimited | PostController editorUpload() | S3 delete via route |

### 4.4 Image Storage Pattern
- **User-facing uploads (PostController):** `$file->store('directory', 's3')` — returns relative S3 path
- **Filament admin uploads:** `->disk('s3')->directory('directory')` on FileUpload component — Filament handles store/delete
- **Main image extraction:** `images[0]` is always stored in `image` column for card display
- **Gallery updates (edit):** Merge existing images array with new uploads; remove deleted paths from S3

### 4.5 Image Deletion
- **Classified edit:** Remove individual image via `DELETE /admin/listing/{id}/remove-image` route — decodes base64 image path, calls `Storage::disk('s3')->delete($img)`
- **Content deletion:** `PurgeExpiredPosts` command — iterates expired records, calls `Storage::disk('s3')->delete()` for each image path
- **Post replacement:** Old image deleted from S3 before storing new one (EventController edit, UserController avatar update)
- **Admin bulk delete:** Filament FileUpload component handles S3 deletion automatically
- **Orphan detection:** No automated orphan detection command exists

### 4.6 Image URL Resolution
- **User model:** `avatar_url` accessor → `Storage::disk('s3')->url($this->avatar)`
- **Listing, Job, Event, BusinessPost, Business, Advertisement, Matrimonial models:** accessor → `Storage::disk('s3')->url($path)`
- **BlogPost model:** `image_url` accessor → `asset('storage/'.$this->image)` ← **uses local storage pattern, NOT S3**
- **blog/show.blade.php:** inline S3 URL generation (bypasses model accessor for related/sidebar posts)
- **Fallback for http:// paths:** All accessors check `str_starts_with($path, 'http')` and return path as-is

### 4.7 Image Hints (UI-facing)
- Classified photos: "Your [plan] plan allows [maxImages] photos per listing"
- Business photos: "First photo = main banner · [plan] plan allows [maxImages] photos"
- BusinessPost photos: "First photo will be the main image · [plan]: [maxImages] photos"
- Company logo: "Square image preferred (e.g. 200×200)"
- Event banner: "Recommended 1200×628 (16:9)"
- Business logo: "Square image preferred (200×200 minimum). Shows in search results and listings."

---

## 5. USER VS ADMIN MATRIX

### 5.1 Classified (Listing)
| Field | User Create | Admin Create | Difference |
|---|---|---|---|
| title | required text | required text | Same |
| category_id | select (all active) | select (all active) | Same |
| description | Quill rich editor | Filament RichEditor (diff toolbar) | Extra (admin has more toolbar buttons) |
| price | text | text | Same |
| price_unit | select (dropdown options) | text input | Different (user has enum dropdown; admin has free text) |
| location | text | text | Same |
| city | dynamic select from Location table | plain text input | Different (user: Location model; admin: free text) |
| province | select from Location table | plain text input | Different |
| contact_name | NOT in user form | text input | AdminOnly |
| contact_email | email input | email input | Same |
| contact_phone | text input | text input | Same |
| images | x-image-uploader component (multi) | FileUpload (s3/listings, up to 5) | Different UI, same S3 destination |
| tags | NOT in user form | TagsInput | AdminOnly |
| badges | NOT in user form | CheckboxList (feat/ver/new/hot) | AdminOnly |
| status | auto=pending (hidden) | select (pending/active/rejected/expired) | AdminOnly |
| is_featured | NOT in user form | Toggle | AdminOnly |
| is_verified | NOT in user form | Toggle | AdminOnly |
| expires_at | auto (plan-based) hidden | DateTimePicker | AdminOnly |
| slug | auto-generated hidden | TextInput (editable, unique) | AdminOnly manual edit |

### 5.2 Job
| Field | User Create | Admin Create | Difference |
|---|---|---|---|
| title | required text | required text | Same |
| company | required text | required text | Same |
| company_logo | x-image-uploader single | FileUpload | Same purpose, different component |
| category_id | select (type=jobs) | select (all active) | User: filtered; Admin: unfiltered |
| job_type | select (enum options) | select | Same |
| work_mode | select (enum options) | select | Same |
| description | Quill rich editor | Textarea (NOT rich editor) | Different (admin loses rich text) |
| requirements | Quill rich editor | Textarea (NOT rich editor) | Different |
| salary | text | text | Same |
| experience | text | text | Same |
| location | text | text | Same |
| city | dynamic select | plain text | Different |
| province | select | plain text | Different |
| apply_email | email | email | Same |
| apply_url | NOT in user form | text | AdminOnly |
| tags | NOT in user form | TagsInput | AdminOnly |
| status | auto=pending | select | AdminOnly |
| is_featured | NOT in user form | Toggle | AdminOnly |

### 5.3 Event
| Field | User Create | Admin Create | Difference |
|---|---|---|---|
| title | required text | required text | Same |
| start_date | datetime-local input | DateTimePicker | Same purpose |
| end_date | datetime-local input | DateTimePicker | Same purpose |
| category_id | select (type=events) | select (all) | User: filtered |
| venue | text | text | Same |
| city | dynamic select | plain text | Different |
| province | select | plain text | Different |
| price | text | text | Same |
| image | x-image-uploader single | FileUpload | Same purpose |
| description | Quill rich editor | Textarea (NOT rich editor) | Different |
| organizer | text | text | Same |
| organizer_phone | text | text | Same |
| organizer_email | email | email | Same |
| website | NOT in user form | TextInput | AdminOnly |
| tags | NOT in user form | TagsInput | AdminOnly |
| status | auto=pending | select | AdminOnly |

### 5.4 Business
| Field | User Create | Admin Create | Difference |
|---|---|---|---|
| name | required text | required text | Same |
| category_id | select (parent cats with subcats) | select (all) | User: parent-only select + dynamic subcat |
| subcategory_id | dynamic select (JS) | NOT in admin form | UserOnly |
| description | Quill rich editor | RichEditor | Same |
| address | text | text | Same |
| city | dynamic select | plain text | Different |
| province | select | plain text | Different |
| phone | text | text | Same |
| email | email | email | Same |
| website | NOT in user form | TextInput | AdminOnly |
| map_url | NOT in user form | TextInput | AdminOnly |
| images | x-image-uploader multi | FileUpload | Same purpose |
| logo | x-image-uploader single | FileUpload | Same purpose |
| hours | structured time inputs per day | plain TextInput (unstructured) | Different (user: structured; admin: free text) |
| tags | text + hidden JSON | TagsInput | Different input method |
| social.* | 6 text inputs | 6 TextInputs | Same |
| rating | NOT in user form | NumberInput | AdminOnly |
| review_count | NOT in user form | NumberInput | AdminOnly |
| is_verified | NOT in user form | Toggle | AdminOnly |
| is_featured | NOT in user form | Toggle | AdminOnly |
| status | auto=pending | select | AdminOnly |

### 5.5 Matrimonial
| Field | User Create | Admin Create | Difference |
|---|---|---|---|
| profile_for | select | select | Same |
| gender | select | select | Same |
| name | required text | required text | Same |
| age | number (18-80) | number | Same |
| height | text | text | Same |
| marital_status | select | select | Same |
| diet | select | select | Same |
| religion | text | text | Same |
| caste | text | text | Same |
| mother_tongue | text | text | Same |
| education | text | text | Same |
| occupation | text | text | Same |
| income | text | text | Same |
| city | dynamic select | select from Location::distinct() | Both from Location; different query |
| province | select | select from Location::distinct() | Similar |
| about | textarea | textarea | Same |
| partner_preference | textarea | textarea | Same |
| contact_name | NOT in user form | TextInput | AdminOnly |
| contact_phone | text | text | Same |
| contact_email | NOT in user form | email | AdminOnly |
| hide_contact | NOT in user form | Toggle | AdminOnly |
| photo | x-image-uploader single | FileUpload | Same purpose |
| photos | x-image-uploader multi | FileUpload | Same purpose |
| status | auto=pending | select | AdminOnly |
| is_featured | NOT in user form | Toggle | AdminOnly |
| expires_at | auto (plan-based) | DateTimePicker | AdminOnly manual override |

### 5.6 User Profile
| Field | User (account.blade.php) | Admin (UserResource) | Difference |
|---|---|---|---|
| name | required text | text | Same |
| phone | text | text | Same |
| city | text (free text) | text | Same |
| province | text (free text) | text | Same |
| bio | textarea (max 500 implied) | textarea | Same |
| avatar | file input (image/*) | FileUpload S3 | Both upload; admin explicitly to S3 |
| email | NOT editable | email input | AdminOnly |
| is_admin | NOT accessible | toggle | AdminOnly |
| is_active | NOT accessible | toggle / toggle_active action | AdminOnly |
| plan | read-only display | — / Grant Plan action | AdminOnly grant |
| stripe fields | NOT accessible | collapsible section | AdminOnly |

---

## 6. BUSINESS RULE INVENTORY

### 6.1 Plan Tiers
| Rule | Free | Verified ($4.99/mo) | Power Seller ($14.99/mo) |
|---|---|---|---|
| Max simultaneous listings + jobs (shared) | 3 | 10 | Unlimited |
| Post duration (days) | 3 | 30 | 0 (permanent) |
| Max images per post | 3 | 5 | 10 |
| Business directory listings | 0 | 1 | Unlimited |
| Verified badge on profile | No | Yes | Yes |
| Featured placement / priority search | No | Yes | Yes |
| Analytics dashboard | No | Yes | Yes |
| Favorites feature | No | Yes | Yes |
| Auto-renew listings | No | No | Yes |
| Featured credits per month | 0 | 0 | TBD (plan DB) |
| Bulk upload | No | No | Yes |
| Business directory access | blocked | 1 business | unlimited |

### 6.2 Plan Enforcement Gates
- **canPostListing():** activeListingCount() < maxListings() — classifieds + jobs combined
- **canPostBusiness():** maxBusinessListings() > 0 AND activeBusinessCount() < maxBusinessListings()
- **canPostEvent():** always true (all plans can post events)
- **canPostBusinessPost():** requires at least one active business (status=active)
- **canFeatureListing():** featuredCredits() > 0 AND featuredCreditsRemaining() > 0

### 6.3 Expiry Rules
- **Classifieds (Listing):** expires_at set at creation = now() + postDays(). postDays() = plan.post_days; 0 = permanent (null expires_at)
- **Jobs:** Same as classifieds — expires_at = now() + postDays()
- **Events:** No expires_at column. Past detection via `start_date < now()` (is_past accessor)
- **Matrimonials:** expires_at set. Same plan-based logic as classifieds
- **BusinessPosts:** No expires_at column
- **Businesses:** No expires_at column

### 6.4 Automatic Expiry Command (MarkExpiredListings)
- Runs: hourly (Laravel schedule)
- Targets: listings, job_listings, business_posts, matrimonials
- Condition: `expires_at <= now() AND status = 'active'`
- Action: Sets `status = 'expired'`

### 6.5 Automatic Purge Command (PurgeExpiredPosts)
- Runs: nightly at midnight
- Targets: listings, job_listings, matrimonials (NOT business_posts, NOT businesses, NOT events)
- Condition: status = 'expired'
- Action: 1) Delete S3 files for image/images/company_logo/photos/photo 2) Delete DB record

### 6.6 Status Flow Per Module
| Module | Initial | After Approve | After Reject | After Expire | Auto-Set |
|---|---|---|---|---|---|
| Listing | pending | active | rejected | expired | MarkExpiredListings |
| Job | pending | active | rejected | expired | MarkExpiredListings |
| Event | pending | active | rejected | — | no expiry command |
| Business | pending | active | rejected | — | no expiry command |
| BusinessPost | pending | active | rejected | — (inactive also in code) | MarkExpiredListings |
| Matrimonial | pending | active | rejected | expired | MarkExpiredListings |
| Blog | draft | published | — | — | publish action |

### 6.7 Content Moderation Rules (applies to user-submitted content at POST time)
- **Min title length:** 10 characters
- **Min description length:** 30 characters
- **Max repeat run:** 4 (e.g. "aaaa" blocks)
- **Max pattern repeat:** 3 (e.g. "asdasdasd" blocks)
- **Max URLs per field:** 2
- **Max phone numbers per field:** 2
- **Banned words list:** profanity, sexual content keywords, scam/spam keywords (see moderation.php)
- **Junk phrases:** lorem ipsum, asdf, qwerty, test data, etc.
- **Junk title words:** test, hello, hi, hey, ok, n/a, none, etc.
- **Gibberish detection:** words with vowel ratio < 0.25 AND length >= 5 are flagged; if > 50% of words gibberish → block field
- **AI Text moderation (OpenAI):** Applied to title + description before store
- **AI Image moderation (Google Vision):** Applied to uploaded images; labels matched against expected category
- **FlaggedPost record:** Created on moderation failure with flag_type=moderation/image/ai

### 6.8 Featured Credits System
- Monthly allotment defined in plan.featured_credits
- Used count tracked in users.featured_credits_used
- Reset date in users.featured_credits_reset_at
- `maybeResetCredits()` called on every `featuredCreditsRemaining()` check — resets if reset_at is past
- Log stored in featured_credit_logs per action (featured/unfeatured)
- Admin can manually edit featured_credits_used and featured_credits_reset_at in UserResource

### 6.9 Stripe Subscription Rules
- Plans with price > 0 use Stripe Checkout (stripe.checkout route)
- Webhook handles: subscription created, updated, deleted, payment_succeeded, payment_failed
- Cancel: sets subscription_status, does NOT immediately downgrade (Stripe handles end-of-period)
- Resume: re-activates cancelled subscription via Stripe API
- Payment recorded in payment_history on webhook success

### 6.10 Chat/Messaging Rules
- Polymorphic conversations: Listing, Event, Business, BusinessPost
- Only authenticated users can initiate chat
- Conversation is unique per (conversable_type, conversable_id, buyer_id, seller_id) combination
- Real-time via Laravel Reverb WebSockets (MessageSent event broadcast)
- No file attachment support in chat

### 6.11 Email Verification Gate
- Setting: `email_verification_required` (from settings table, cached 5 min)
- Env override: `REQUIRE_EMAIL_VERIFICATION=false` bypasses gate
- Middleware: `EnsureEmailVerified` aliased as `email.verified`
- Applied to post creation, profile update routes

### 6.12 Reporting Rules
- Any authenticated user can report: listing, event, business, job, blog_post
- Reason options: spam, misleading, offensive, scam, wrong_category, duplicate, other
- One report per user per content item (no enforcement code found — may allow duplicates)
- Admin reviews via ReportResource (status: pending/reviewed/dismissed)

### 6.13 Poll Display Rules
- `Poll::current(city, province)` priority: city-scoped > province-scoped > canada-scoped
- Only active and non-expired polls shown
- Votes stored in options json array; total_votes is a computed accessor

---

## 7. CRUD MATRIX

| Module | Create | Read/List | Read/Detail | Update | Delete | Restore | Bulk Delete | Approve | Reject | Feature | Expire | Renew |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| **Classified (Listing)** | User✓ Admin✓ | Public✓ User✓ Admin✓ | Public✓ User✓ Admin✓ | User✓ Admin✓ | User✓ Admin✓ | ✗ | Admin✓ | Admin✓ | Admin✓ | Admin✓ | Auto (hourly) | ✗ |
| **Job** | User✓ Admin✓ | Public✓ User✓ Admin✓ | Public✓ User✓ Admin✓ | User✓ Admin✓ | User✓ Admin✓ | ✗ | Admin✓ | Admin✓ | Admin✓ | Admin✓ | Auto (hourly) | ✗ |
| **Event** | User✓ Admin✓ | Public✓ User✓ Admin✓ | Public✓ User✓ Admin✓ | User✓ Admin✓ | User✓ Admin✓ | ✗ | Admin✓ | Admin✓ | Admin✓ | Admin✓ | No expires_at | ✗ |
| **Business** | User✓ Admin✓ | Public✓ User✓ Admin✓ | Public✓ User✓ Admin✓ | User✓ Admin✓ | User✓ Admin✓ | ✗ | Admin✓ | Admin✓ | Admin✓ | Admin✓ | No expires_at | ✗ |
| **BusinessPost** | User✓ Admin✓ | Public✓ User✓ Admin✓ | Public✓ User✓ Admin✓ | User✓ Admin✓ | User✓ Admin✓ | ✗ | Admin✓ | Admin✓ | Admin✓ | Admin✓ | Auto (hourly) | ✗ |
| **Matrimonial** | User✓ Admin✓ | Public✓ User✓ Admin✓ | Public✓ User✓ Admin✓ | User✓ Admin✓ | User✓ Admin✓ | ✗ | Admin✓ | Admin✓ | Admin✓ | Admin✓ | Auto (hourly) | ✗ |
| **Blog** | Admin✓ | Public✓ Admin✓ | Public✓ Admin✓ | Admin✓ | Admin✓ | ✗ | Admin✓ | Publish action✓ | ✗ | Admin✓ | No expires | ✗ |
| **User** | Self-register✓ Admin✓ | Admin✓ | Admin✓ | Self (profile)✓ Admin✓ | Admin✓ | ✗ | ✗ | ✗ | ✗ Toggle Active | ✗ | Grant Plan action✓ |
| **Category** | Admin✓ | Admin✓ | Admin✓ | Admin✓ | Admin✓ | ✗ | Admin✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| **Location** | Admin✓ | Admin✓ | Admin✓ | Admin✓ | Admin✓ | ✗ | Admin bulk activate/deactivate✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| **Advertisement** | Admin✓ | Admin✓ Public(view) | Admin✓ Public(view) | Admin✓ | Admin✓ | ✗ | Admin✓ | ✗ | ✗ | Toggle is_active✓ | Auto (ends_at) | ✗ |
| **Plan** | Admin✓ | Admin✓ Public(pricing) | Admin✓ Public(pricing) | Admin✓ | Admin✓ | ✗ | ✗ | ✗ | ✗ | Toggle is_popular✓ | ✗ | ✗ |
| **Poll** | Admin✓ | Admin✓ Public(widget) | Admin✓ | Admin✓ | Admin✓ | ✗ | ✗ | ✗ | ✗ | ✗ | Auto (expires_at) | ✗ |
| **Chat/Conversation** | Auth User✓ | Auth parties✓ | Auth parties✓ | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |
| **Report** | Auth User✓ | Admin✓ | Admin✓ | Admin (status only)✓ | Admin✓ | ✗ | ✗ | ✗ | Dismiss✓ | ✗ | ✗ | ✗ |
| **Favorite** | Auth User✓ (toggle) | Auth User✓ | ✗ | ✗ | Auth User✓ (toggle) | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |

---

## 8. VALIDATION MATRIX

### 8.1 Classified (Listing) — POST validation (PostController::storeClassified)
| Field | Required | Nullable | Type | Min | Max | Unique | Exists | Enum | Image | URL | Email | Custom |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| title | YES | — | string | — | 150 | — | — | — | — | — | — | min_title_length=10 (moderation) |
| category_id | YES | — | — | — | — | — | categories.id | — | — | — | — | — |
| description | — | YES | string | — | — | — | — | — | — | — | — | min_description_length=30 (moderation) |
| price | — | YES | string | — | 50 | — | — | — | — | — | — | — |
| price_unit | — | YES | string | — | 20 | — | — | — | — | — | — | — |
| location | — | YES | string | — | 150 | — | — | — | — | — | — | — |
| city | YES | — | string | — | 100 | — | — | — | — | — | — | — |
| province | YES | — | string | — | 100 | — | — | — | — | — | — | — |
| contact_name | — | YES | string | — | 100 | — | — | — | — | — | — | — |
| contact_email | — | YES | — | — | 150 | — | — | — | — | — | YES | — |
| contact_phone | — | YES | string | — | 30 | — | — | — | — | — | — | — |
| images | — | YES | array | — | maxImages | — | — | — | — | — | — | — |
| images.* | — | YES | — | — | — | — | — | — | YES (imgRules) | — | — | min:200×200, max:6000×6000, max:2048KB, mimes:jpg,jpeg,png,webp |

### 8.2 Job — POST validation (PostController::storeJob / updateJob)
| Field | Required | Nullable | Type | Max | Exists | Enum | Image | URL | Email |
|---|---|---|---|---|---|---|---|---|---|
| title | YES | — | string | 150 | — | — | — | — | — |
| category_id | — | YES | — | — | categories.id | — | — | — | — |
| company | YES | — | string | 150 | — | — | — | — | — |
| description | — | YES | string | — | — | — | — | — | — |
| requirements | — | YES | string | — | — | — | — | — | — |
| job_type | YES | — | — | — | — | full-time/part-time/contract/freelance/internship | — | — | — |
| work_mode | YES | — | — | — | — | onsite/remote/hybrid | — | — | — |
| salary | — | YES | string | 100 | — | — | — | — | — |
| experience | — | YES | string | 100 | — | — | — | — | — |
| location | — | YES | string | 150 | — | — | — | — | — |
| city | YES | — | string | 100 | — | — | — | — | — |
| province | YES | — | string | 100 | — | — | — | — | — |
| apply_email | — | YES | — | 150 | — | — | — | — | YES |
| apply_url | — | YES | — | 255 | — | — | — | YES | — |
| company_logo | — | YES | — | — | — | — | imgRules | — | — |

### 8.3 Event — POST validation (PostController::storeEvent / updateEvent)
| Field | Required | Nullable | Type | Max | Exists | Enum | Image | URL | Email | Custom |
|---|---|---|---|---|---|---|---|---|---|---|
| title | YES | — | string | 150 | — | — | — | — | — | — |
| category_id | — | YES | — | — | categories.id | — | — | — | — | — |
| description | — | YES | string | — | — | — | — | — | — | — |
| start_date | YES | — | date | — | — | — | — | — | — | — |
| end_date | — | YES | date | — | — | — | — | — | — | after_or_equal:start_date |
| venue | — | YES | string | 200 | — | — | — | — | — | — |
| city | YES | — | string | 100 | — | — | — | — | — | — |
| province | YES | — | string | 100 | — | — | — | — | — | — |
| price | — | YES | string | 50 | — | — | — | — | — | — |
| organizer | — | YES | string | 150 | — | — | — | — | — | — |
| organizer_phone | — | YES | string | 30 | — | — | — | — | — | — |
| organizer_email | — | YES | — | 150 | — | — | — | — | YES | — |
| website | — | YES | — | 255 | — | — | — | YES | — | — |
| image | — | YES | — | — | — | — | imgRules | — | — | — |

### 8.4 Business — POST validation (PostController::storeBusiness / updateBusiness)
| Field | Required | Nullable | Type | Max | Exists | Enum | Image | URL | Email | Array |
|---|---|---|---|---|---|---|---|---|---|---|
| name | YES | — | string | 150 | — | — | — | — | — | — |
| category_id | — | YES | — | — | categories.id | — | — | — | — | — |
| subcategory_id | — | YES | — | — | categories.id | — | — | — | — | — |
| description | — | YES | string | — | — | — | — | — | — | — |
| address | — | YES | string | 255 | — | — | — | — | — | — |
| city | YES | — | string | 100 | — | — | — | — | — | — |
| province | YES | — | string | 100 | — | — | — | — | — | — |
| phone | — | YES | string | 30 | — | — | — | — | — | — |
| email | — | YES | — | 150 | — | — | — | — | YES | — |
| website | — | YES | — | 255 | — | — | — | YES | — | — |
| map_url | — | YES | — | 500 | — | — | — | YES | — | — |
| tags_input | — | YES | string | 500 | — | — | — | — | — | — |
| social | — | YES | — | — | — | — | — | — | — | YES |
| social.* | — | YES | string | 255 | — | — | — | — | — | — |
| hours | — | YES | — | — | — | — | — | — | — | YES |
| images | — | YES | — | maxImages | — | — | — | — | — | YES |
| images.* | — | YES | — | — | — | — | imgRules | — | — | — |
| logo | — | YES | — | — | — | — | imgRules | — | — | — |

### 8.5 BusinessPost — POST validation (PostController::storeBusinessPost / updateBusinessPost)
| Field | Required | Nullable | Type | Max | Exists | Image |
|---|---|---|---|---|---|---|
| title | YES | — | string | 150 | — | — |
| description | — | YES | string | — | — | — |
| price | — | YES | string | 50 | — | — |
| price_unit | — | YES | string | 20 | — | — |
| images | — | YES | array | maxImages | — | — |
| images.* | — | — | — | — | — | imgRules |
| category_id | — | YES | — | — | categories.id | — |

### 8.6 Matrimonial — POST validation (PostController::storeMatrimonial / updateMatrimonial)
| Field | Required | Nullable | Type | Min/Max | Enum | Email | Image |
|---|---|---|---|---|---|---|---|
| name | YES | — | string | max:100 | — | — | — |
| profile_for | YES | — | — | — | self/son/daughter/brother/sister/friend | — | — |
| gender | YES | — | — | — | male/female | — | — |
| age | YES | — | integer | min:18 max:80 | — | — | — |
| height | — | YES | string | max:20 | — | — | — |
| religion | — | YES | string | max:100 | — | — | — |
| caste | — | YES | string | max:100 | — | — | — |
| mother_tongue | — | YES | string | max:100 | — | — | — |
| education | — | YES | string | max:150 | — | — | — |
| occupation | — | YES | string | max:150 | — | — | — |
| income | — | YES | string | max:100 | — | — | — |
| marital_status | YES | — | — | — | never_married/divorced/widowed | — | — |
| diet | — | YES | — | — | veg/non-veg/eggetarian | — | — |
| city | YES | — | string | max:100 | — | — | — |
| province | YES | — | string | max:100 | — | — | — |
| about | — | YES | string | max:2000 | — | — | — |
| partner_preference | — | YES | string | max:2000 | — | — | — |
| contact_name | — | YES | string | max:100 | — | — | — |
| contact_phone | — | YES | string | max:30 | — | — | — |
| contact_email | — | YES | — | max:150 | — | YES | — |
| hide_contact | — | YES | boolean | — | — | — | — |
| photo | — | YES | — | — | — | — | imgRules |

### 8.7 User Profile — validation (UserController::updateProfile)
| Field | Required | Nullable | Type | Max | Image |
|---|---|---|---|---|---|
| name | YES | — | string | 100 | — |
| phone | — | YES | string | 20 | — |
| city | — | YES | string | 100 | — |
| bio | — | YES | string | 500 | — |
| avatar | — | YES | — | 2048KB | image |

### 8.8 User Password Change — validation (UserController::changePassword)
| Field | Required | Type | Min | Custom |
|---|---|---|---|---|
| current_password | YES | — | — | current password verified |
| password | YES | — | 8 | — |
| password_confirmation | YES | — | — | confirmed rule |

### 8.9 Registration — validation (auth/register)
| Field | Required | Unique | Type | Min | Email |
|---|---|---|---|---|---|
| name | YES | — | string | — | — |
| email | YES | YES (users) | — | — | YES |
| password | YES | — | string | 8 | — |
| password_confirmation | YES | — | — | — | — |

---

## 9. UI INVENTORY

### 9.1 Public-Facing Pages
| Route | View | Module | Key Sections |
|---|---|---|---|
| / | home.blade.php | Home | Hero (city bg from S3), Poll widget, Ads (hero_banner), Featured Classifieds, Featured Jobs, Featured Events, Featured Businesses |
| /classifieds | classifieds/index | Listings | Filter bar (city/province/category), Card grid, Pagination, Ad (sidebar_top) |
| /classifieds/{slug} | classifieds/show | Listing Detail | Main image, Gallery, Description, Contact sidebar, Map, Related listings, Chat button |
| /jobs | jobs/index | Jobs | Filter bar, Card list, Pagination |
| /jobs/{slug} | jobs/show | Job Detail | Company logo, Description, Requirements, Apply button/email |
| /events | events/index | Events | Date filter, Card grid, Pagination |
| /events/{slug} | events/show | Event Detail | Banner, Details, Organizer, Map, Related events |
| /directory | directory/index | Businesses | Category filter, Card grid, Pagination |
| /directory/{slug} | directory/show | Business Detail | Logo, Gallery, Hours, Social, Map, Posts section |
| /matrimonial | matrimonial/index | Matrimonial | Gender/Province filter, Profile grid, Pagination |
| /matrimonial/{slug} | matrimonial/show | Matrimonial Detail | Profile banner, Info grid, Gallery, Contact sidebar |
| /blog | blog/index | Blog | Category tabs, Featured post, Grid, Sidebar (popular/categories) |
| /blog/{slug} | blog/show | Blog Post Detail | Cover, Meta bar, Body, Author card, Tags, Related, Sidebar (latest) |
| /pricing | pricing.blade.php | Plans | Plan cards, Comparison table, FAQ accordion |
| /feed | feed.blade.php | Activity Feed | Unified feed of recent classifieds, jobs, events, businesses |
| /login | auth/login | Auth | Email, Password, Remember me, Social login (Google) |
| /register | auth/register | Auth | Name, Email, Password, Confirm |

### 9.2 User Account Pages (authenticated)
| Route | View | Key Forms/Sections |
|---|---|---|
| /account | user/account.blade.php | Profile section (name/phone/city/province/bio/avatar), Password section, Listings tab, Jobs tab, Events tab, Businesses tab, Matrimonial tab, Favorites tab, Payment History tab |
| /post/create | post/create.blade.php | Multi-tab form: Classified tab, Job tab, Event tab, Business tab, BusinessPost tab, Matrimonial tab. All tabs share same view. Tab visibility gated by plan. |
| /post/{slug}/edit | post/edit.blade.php | Same structure as create; pre-filled; has remove-image buttons for existing photos |

### 9.3 Create Form — Tab Structure (post/create.blade.php)
| Tab | Form Fields | Image Component | Plan Gate |
|---|---|---|---|
| Classified | title, category_id, location, province, city, description(Quill), price, price_unit, contact_phone, contact_email, images | x-image-uploader (multiple, maxImages) | canPostListing() |
| Job | title, company, category_id, job_type, work_mode, salary, province, city, experience, description(Quill), requirements(Quill), apply_email, company_logo | x-image-uploader (single, 1) | canPostListing() |
| Event | title, start_date, end_date, category_id, price, venue, province, city, description(Quill), organizer, organizer_phone, organizer_email, image | x-image-uploader (single, 1) | canPostEvent() |
| Business | name, category_id, subcategory_id(JS), description(Quill), tags_input, tags(hidden), address, province, city, phone, email, hours×7, social×6, images, logo | x-image-uploader (multiple + single) | canPostBusiness() |
| BusinessPost | business_id(hidden), category_id, subcategory_id(JS), title, price, price_unit, description(Quill), custom_fields(dynamic JS), images | x-image-uploader (multiple, maxImages) | canPostBusinessPost() |
| Matrimonial | profile_for, gender, name, age, height, marital_status, diet, religion, caste, mother_tongue, education, occupation, income, province, city, about, partner_preference, contact_phone, photo, photos | x-image-uploader (single + multiple) | true (all plans) |

### 9.4 Filament Admin Panel — Resource Structure
| Resource | Navigation Group | Sort | Table Columns | Table Filters | Table Actions |
|---|---|---|---|---|---|
| ListingResource | Content | 1 | image, title, category, price, location, status, is_featured, is_verified, views, created_at | status, category, is_featured | approve, reject, analytics, edit, delete |
| JobResource | Content | 2 | title, company, category, job_type, work_mode, status, is_featured, created_at | status, is_featured | approve, reject, edit, delete |
| EventResource | Content | 3 | image, title, category, start_date, status, is_featured, views, created_at | status, is_featured | approve, reject, edit, delete |
| BusinessResource | Content | 4 | image(logo_url), name, category, city, status, is_featured, is_verified, created_at | status, is_featured, is_verified | approve, reject, feature, edit, delete |
| BusinessPostResource | Content | 5 | image, title, business, category, status, views, created_at | status | approve, reject, edit, delete |
| BlogPostResource | Content | 6 | image, title, category, author, status, is_featured, views, published_at | status | publish, edit, delete |
| MatrimonialResource | Content | — | name, gender, age, city, status, is_featured, created_at | status, gender | approve, edit, delete |
| UserResource | Users | 1 | avatar, name, email, plan, is_admin, is_active, created_at | is_admin, is_active, plan | toggle_active, edit, delete |
| CategoryResource | System | 1 | name, type, is_active, sort_order | type, is_active | edit, delete |
| LocationResource | System | 2 | province, city_image, city, is_active, sort_order, created_at | province, is_active | edit, delete; bulk activate/deactivate |
| PlanResource | System | 3 | icon, name, price, period, post_days, max_listings, biz_listings, is_active, is_popular, sort_order | — | edit, delete; reorderable |
| AdvertisementResource | System | 4 | image, title, position, scope, is_active, slide_duration, impressions, clicks, starts_at, ends_at | position, scope, is_active | edit, delete |
| ReportResource | Moderation | 1 | reportable_type, reportable_id, reason, status, reporter, created_at | status, reason, type | review, dismiss, edit, delete |
| FlaggedPostResource | Moderation | 2 | post_type, flag_type, reason, user, created_at | post_type, flag_type | edit, delete |
| AdvertiseRequestResource | — | — | business_name, contact_name, email, status, created_at | status | edit, delete |
| PaymentHistoryResource | — | — | user, plan, amount_formatted, status, created_at | status | edit, delete |
| PollResource | — | — | question, scope, is_active, expires_at, total_votes, created_at | scope, is_active | edit, delete |

### 9.5 Blade Components
| Component | File | Purpose |
|---|---|---|
| x-image-uploader | components/image-slider.blade.php | Multi/single image upload with preview; posts to form; plan-based maxImages |
| x-image-slider | — | Image slideshow for business/listing galleries (S3 URL based) |

### 9.6 JavaScript Interactions
| Function | Location | Purpose |
|---|---|---|
| loadCities(targetId, province) | post/create, post/edit, matrimonial/index | AJAX load cities by province from `/api/cities?province=` |
| loadSubCats(targetId, parentId) | post/create, post/edit | AJAX load subcategories by parent category |
| bpOnCategoryChange() | post/create, post/edit | Load subcategories + category fields for BusinessPost |
| bpLoadFields() | post/create, post/edit | Render dynamic custom_fields inputs |
| removeListingPhoto(url, enc, token) | Filament ListingResource | DELETE fetch to admin/listing/remove-image route |
| Quill editor init | post/create, post/edit | Rich text editor for description/requirements fields |

---

## 10. QA COVERAGE MATRIX

| Module | Fields Discovered | CRUD Ops | Validation Rules | Image Fields | Plan Rules | Permissions | Responsive | Security Rules | Performance | Regression Risk | Coverage % |
|---|---|---|---|---|---|---|---|---|---|---|---|
| Classified (Listing) | 20 | 7 (Create/Read/Update/Delete/Approve/Reject/Feature) | 13 field rules + moderation | 2 (image, images) | post_days, max_listings, max_images | User+Admin matrix | 3 breakpoints | imgRules, CSRF, auth gate | listing_views analytics | High (core module) | 85% |
| Job | 19 | 7 | 13 field rules + moderation | 1 (company_logo) | post_days, max_listings (shared) | User+Admin matrix | 3 breakpoints | imgRules, auth gate | — | High (core module) | 80% |
| Event | 17 | 6 (no expire command) | 13 field rules + moderation | 1 (image) | post_days (for events), canPostEvent=always | User+Admin matrix | 3 breakpoints | imgRules, auth gate | — | Medium | 80% |
| Business | 22 | 6 | 16 field rules + moderation | 3 (image, images, logo) | biz_listings, canPostBusiness | User+Admin matrix | 3 breakpoints | imgRules, auth gate, plan gate | — | High | 75% |
| BusinessPost | 11 | 6 | 6 field rules + moderation | 2 (image, images) | canPostBusinessPost, max_images | User+Admin matrix | 3 breakpoints | imgRules, auth gate, plan gate | — | Medium | 70% |
| Matrimonial | 25 | 6 | 17 field rules + imgRules | 2 (photo, photos) | post_days, expires_at | User+Admin matrix | 3 breakpoints | imgRules, auth gate | — | Medium | 75% |
| Blog | 12 | 4 (Admin only) | Admin Filament only | 1 (image) | none | Admin only | 3 breakpoints | S3 URL (model uses asset()) | — | Low | 60% |
| User/Auth | 18 | Registration+Login+Profile+PWChange | 8 field rules | 1 (avatar) | plan system | Self + Admin | 3 breakpoints | bcrypt, email verify gate | — | High (auth foundation) | 80% |
| Category | 7 | 5 | Filament built-in | 0 | none | Admin only | Admin panel | CSRF | — | Low | 70% |
| Location | 5 | 5 | Filament built-in | 1 (city_image) | none | Admin only | Admin panel | CSRF | — | Low | 70% |
| Advertisement | 12 | 5 | Filament built-in | 1 (image) | none | Admin only | Admin panel | CSRF | — | Low | 65% |
| Plans | 20 | 5 | Filament built-in | 0 | Stripe integration | Admin only | Admin panel | Stripe webhook verify | — | High (billing) | 70% |
| Chat | 4 | Create+Read | none (server-side) | 0 | canChat (auth) | Auth users | 3 breakpoints | Auth gate, CSRF, WebSocket | WebSocket concurrency | High | 60% |
| Reports | 6 | Create+Read+Status | reason in enum | 0 | none | Auth report, Admin review | Admin panel | auth gate | — | Low | 55% |
| Favorites | 3 | Toggle (Create+Delete) | none | 0 | hasFavorites() gate | Auth + plan check | inline | auth gate | — | Medium | 55% |
| Polls | 6 | Admin CRUD + Public vote | Filament built-in | 0 | none | Admin create, All view | widget | CSRF | — | Low | 60% |
| Payment/Stripe | 8 | Read + Stripe webhook | Stripe-side | 0 | Stripe plan | Auth + webhook | Admin panel | webhook signature verify | — | Critical | 65% |

### 10.1 Critical QA Focus Areas (by risk)

**Priority 1 — Critical:**
1. Plan enforcement gates (canPostListing, canPostBusiness, maxImages) — test all 3 plans
2. Stripe webhook handling — subscription created/cancelled/failed payment flows
3. S3 file upload and deletion — every module's image lifecycle
4. Auth gates — unauthenticated access to create/edit routes
5. Email verification gate — when REQUIRE_EMAIL_VERIFICATION=true

**Priority 2 — High:**
6. Listing + Job shared activeListingCount() budget enforcement
7. MarkExpiredListings command — correct expiry per module
8. Content moderation pipeline — banned words, gibberish, AI scan
9. Admin approve/reject workflow — status transitions
10. Chat messaging — WebSocket connection, message delivery

**Priority 3 — Medium:**
11. Image upload constraints — size/dimension/MIME validation
12. Category → Subcategory → CategoryField dynamic chain
13. City/Province dynamic loading in all module forms
14. Featured credits monthly reset and enforcement
15. Poll scope priority (city > province > canada)

**Priority 4 — Regression:**
16. BlogPost image_url accessor using asset('storage/') — blocks production image display
17. Matrimonial hidden from admin nav ($shouldRegisterNavigation = false)
18. Job model uses job_listings table — queries must target correct table
19. PaymentHistory model uses payment_history table
20. canAccessPanel() grants admin access to user id=1 regardless of is_admin flag

### 10.2 Modules with No User-Facing Create Form
- Blog (Admin only — BlogPostResource)
- Advertisements (Admin only — AdvertisementResource)
- Polls (Admin only — PollResource)
- Categories (Admin only — CategoryResource)
- Locations (Admin only — LocationResource)
- Plans (Admin only — PlanResource)
- Reports (User submits; no create form — modal/button on content pages)
- Favorites (Toggle via button; no dedicated form)

### 10.3 Modules with No Expiry Mechanism
- Events (no expires_at column; past detection via start_date only)
- Businesses (no expires_at column; no purge command)
- BusinessPosts (no expires_at in DB; MarkExpiredListings still targets them)
- Blog Posts (no expiry concept)
- Advertisements (ends_at exists but no automatic status change command found)

---

*Document Version: 1.0.0 — Generated 2026-07-03*  
*This document is the Single Source of Truth for GoBazaar QA.*  
*All future QA phases must compare against this document.*  
*Any deviation found during testing must be flagged against this reference — not fixed until QA phase 2 begins.*
