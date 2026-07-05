# Phase 5 Report 07 — Root Cause UI Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 5 — Enterprise UI / UX / Responsive / Accessibility Audit

---

## Root Cause Grouping

All UI issues identified in Phase 5 grouped by their underlying design/implementation cause.

---

## Root Cause Group 1: Brand Name Inconsistency (TYPO)

**Root Cause:** The project was built under the name "GoBazzar" and partway through renamed to "GoBazaar." Find-replace was applied to most files but 7 views retained the old double-z spelling.

**Issues Caused:**
- TYPO-01: `matrimonial/index.blade.php` title
- TYPO-02: `feed.blade.php` title + body text
- TYPO-03: `blog/show.blade.php` title
- TYPO-04: `matrimonial/show.blade.php` title
- TYPO-05: `post/create.blade.php` title
- TYPO-06: `post/edit.blade.php` title

**Fix Applied:** All 6 files corrected — `GoBazzar` → `GoBazaar` in title and body.  
**Severity:** Critical (brand trust, SEO)  
**Status:** FIXED

---

## Root Cause Group 2: Missing H1 Tags (SEMANTIC HTML OMISSION)

**Root Cause:** Index pages (Classifieds, Jobs, Events, Directory, Blog) were designed with a card-grid layout that has a color hero header via CSS (`.page-hero h1` styles exist) but the actual `<h1>` HTML element was never added to the template body — only the CSS class was defined. Auth pages used `<h2>` in the auth card header without an outer `<h1>` because the page is centered around the card widget.

**Issues Caused:**
- H1-01: Classifieds index — no H1
- H1-02: Jobs index — no H1
- H1-03: Events index — no H1
- H1-04: Directory index — no H1
- H1-05: Blog index — no H1
- H1-06: Login — no H1 (H2 in card)
- H1-07: Register — no H1 (H2 in card)

**Fix Applied:** Sr-only `<h1>` added to each index page; auth card `<h2>` changed to `<h1>` with matching style.  
**Severity:** Critical (accessibility — WCAG 2.1 SC 2.4.6)  
**Status:** FIXED

---

## Root Cause Group 3: Hardcoded/Environment-Relative URLs (DEVELOPMENT ARTIFACT)

**Root Cause:** During local development on XAMPP, the app lives at `/gobazzar-app/public/` rather than at the domain root. Developers used literal path strings in a few places instead of the `url()` / `route()` helpers, which auto-resolve from `APP_URL`.

**Issues Caused:**
- LAYOUT-01: `<a href="/gobazzar-app/public/admin">Admin</a>` in footer

**Fix Applied:** Replaced with `@auth @if(auth()->user()->is_admin)<a href="{{ url('/admin') }}">Admin</a>@endif @endauth` — now admin-only, uses proper URL helper.  
**Severity:** High (would be broken in production)  
**Status:** FIXED

---

## Root Cause Group 4: Matrimonial Navigation Omission

**Root Cause:** Matrimonial was added as a feature after the initial navigation was designed. The Filament resource has `$shouldRegisterNavigation = false` (hidden from admin sidebar) and the frontend subnav was never updated to include a Matrimonial link. The mobile drawer similarly lacked it.

**Issues Caused:**
- NAV-01: Matrimonial missing from desktop subnav
- NAV-02: Matrimonial missing from mobile nav drawer

**Fix Applied:** Added `<a href="{{ route('matrimonial.index') }}">` with `fa-ring` icon to both the `.subnav-inner` div and the mobile drawer links.  
**Severity:** High (feature undiscoverable via primary navigation)  
**Status:** FIXED

---

## Root Cause Group 5: Page Title Suffix Duplication

**Root Cause:** `layouts/app.blade.php` line 6 wraps all titles with `— Indian Community in Canada`. Pages that included this text in their own `@section('title')` value got it doubled in the rendered `<title>`.

**Issues Caused:**
- TITLE-01: Jobs: `Jobs — Indian Community in Canada — Indian Community in Canada`
- TITLE-02: Events: `Events — Indian Community in Canada — Indian Community in Canada`
- TITLE-03: Blog: `Blog — GoBazaar — Indian Community in Canada — Indian Community in Canada`

**Fix Applied:** Removed the duplicate suffix from the 3 affected `@section('title')` declarations.  
**Severity:** Medium (SEO and browser tab UX)  
**Status:** FIXED

---

## Root Cause Group 6: Placeholder/Fake Features Not Replaced (FEATURE STUB)

**Root Cause:** Newsletter subscribe widget was implemented as a UI mockup/stub (JS-only) and was never backed by a real endpoint before the audit.

**Issues Caused:**
- UX-NEWS-01: Newsletter subscribe button is fake — no email stored, no confirmation sent

**Fix Applied:** None — requires backend implementation decision (email service integration).  
**Severity:** High (misleads users into thinking they've subscribed)  
**Status:** DOCUMENTED — needs business decision before fix

---

## Root Cause Group 7: Footer Social Links Not Configured (PLACEHOLDER)

**Root Cause:** Footer social media icons were added as design elements pointing to `href="#"` placeholders. No real social media accounts configured.

**Issues Caused:**
- FOOTER-01: All footer social links (Facebook, Instagram, Twitter, YouTube, WhatsApp) point to `#`

**Fix Applied:** None — requires configuration of actual social media account URLs.  
**Severity:** Medium (broken links, poor UX expectation)  
**Status:** DOCUMENTED — needs social media URLs from stakeholder

---

## Root Cause Group 8: Form Input/Label Programmatic Dissociation (ACCESSIBILITY PATTERN)

**Root Cause:** Forms throughout the app use a visual-first design pattern: `<label class="form-label">` above `<input class="form-input">`. Labels are visually adjacent and clearly associated, but the inputs don't have `id` attributes and the labels don't have `for` attributes. This is the most common accessibility pattern mistake in custom-built forms that weren't built with ARIA in mind.

**Issues Caused:**
- A11Y-LABEL-01: Form inputs across 12+ pages not programmatically linked to labels
- A11Y-ERR-01: Error messages not aria-described to their input

**Fix Applied:** None — systemic across all forms; requires a systematic pass adding `id` to inputs and `for` to labels, plus `aria-describedby` for error messages.  
**Severity:** High (WCAG 2.1 SC 1.3.1 — Info and Relationships)  
**Status:** DOCUMENTED — recommend production fix pass

---

## Root Cause Group 9: Accent Color Contrast Failure

**Root Cause:** The accent/gold color (`--accent: #e8a020`) was chosen for visual warmth but its contrast ratio against white text is only ~2.8:1 — well below WCAG AA's 4.5:1 requirement for normal text and 3:1 for large text.

**Issues Caused:**
- A11Y-CONTRAST-01: White text on accent gold buttons fails WCAG AA

**Fix Applied:** None — changing accent color would affect brand identity. Recommendation: Use darker text (`#1a1a1a`) on accent buttons, or darken the accent to `#c47800` (which yields ~4.8:1 with white).  
**Severity:** High (WCAG 2.1 SC 1.4.3)  
**Status:** DOCUMENTED

---

## Root Cause Group 10: JavaScript Dead Code / Missing Elements

**Root Cause:** `navSearch()` function and `nav-search-input` event listener were written for a search input that was planned in the navbar but never added to the HTML.

**Issues Caused:**
- JS-01: `document.getElementById('nav-search-input')?.addEventListener(...)` — no-op since element doesn't exist. Optional chaining (`?.`) prevents a runtime error but the function (`navSearch()`) would also fail silently if called.

**Fix Applied:** None required — the `?.` operator safely handles the missing element. No user-visible impact.  
**Severity:** Low (dead code — no functional impact)  
**Status:** DOCUMENTED

---

## Root Cause Summary

| # | Root Cause | Issues | Fixed? |
|---|-----------|--------|--------|
| 1 | Brand name rename not fully applied | 6 title typos | YES |
| 2 | H1 elements not added to index page templates | 7 missing H1 | YES |
| 3 | Dev-environment hardcoded URLs not cleaned up | 1 footer URL | YES |
| 4 | Matrimonial feature added after nav was built | 2 nav gaps | YES |
| 5 | Page title includes suffix already in layout | 3 doubled titles | YES |
| 6 | Feature stub never implemented | Newsletter fake | NO — needs decision |
| 7 | Social media URLs never configured | Footer # links | NO — needs config |
| 8 | Forms not built with accessibility labels | 12+ form gaps | NO — systemic |
| 9 | Accent color chosen without WCAG check | Contrast failure | NO — brand decision |
| 10 | Navbar search planned but not built | Dead JS code | NO — low priority |
