# Phase 5 Report 03 — Accessibility Report
**Project:** GoBazaar  
**Date:** 2026-07-04  
**Phase:** 5 — Enterprise UI / UX / Responsive / Accessibility Audit  
**Standard:** WCAG 2.1 Level AA

---

## Heading Hierarchy Audit

| Page | H1 | H2 | H3 | Hierarchy Correct? | Status (Before Fix) | Status (After Fix) |
|------|----|----|----|--------------------|---------------------|-------------------|
| Homepage | 1 | 0 | varies | YES | PASS | PASS |
| Classifieds | 0 | 0 | — | NO — missing H1 | FAIL | **FIXED** |
| Jobs | 0 | 0 | — | NO — missing H1 | FAIL | **FIXED** |
| Events | 0 | 0 | — | NO — missing H1 | FAIL | **FIXED** |
| Directory | 0 | 0 | — | NO — missing H1 | FAIL | **FIXED** |
| Matrimonial | 1 | 0 | varies | YES | PASS | PASS |
| Blog | 0 | 1 | — | NO — missing H1 | FAIL | **FIXED** |
| Pricing | 1 | 2 | — | YES | PASS | PASS |
| Feed | 1 | 4 | — | YES | PASS | PASS |
| Login | 0 | 1 | — | NO — H2 without H1 | FAIL | **FIXED** |
| Register | 0 | 1 | — | NO — H2 without H1 | FAIL | **FIXED** |
| About | 1 | 2 | — | YES | PASS | PASS |
| Contact | 1 | 1 | — | YES | PASS | PASS |
| Privacy | 1 | 13 | — | YES | PASS | PASS |
| Terms | 1 | 13 | — | YES | PASS | PASS |
| Post Create | 1 (layout title) | — | — | YES | PASS | PASS |
| Account | 1 (sr-only) | panels | — | OK | PASS | PASS |

**7 H1 violations fixed.** All pages now have exactly one H1.

---

## Form Label Audit

### Method
- Each `<input type="text|email|password|number|tel">` should have an associated `<label for="id">` or `aria-label`
- Audit checks: does the input have an `id` attribute (required for `for=` label linkage)?

| Page | Inputs | With ID | Coverage | Status |
|------|--------|---------|----------|--------|
| Homepage | 7 | 3 | 43% | WARN — hero search inputs missing explicit label-for |
| Classifieds | 2 | 1 | 50% | WARN — search input |
| Jobs | 2 | 1 | 50% | WARN |
| Events | 2 | 1 | 50% | WARN |
| Directory | 2 | 1 | 50% | WARN |
| Matrimonial | 4 | 1 | 25% | WARN |
| Blog | 2 | 1 | 50% | WARN |
| Pricing | 1 | 1 | 100% | PASS |
| Login | 3 | 1 | 33% | WARN — email/password inputs lack id |
| Register | 7 | 1 | 14% | WARN |
| About | 1 | 1 | 100% | PASS |
| Contact | 2 | 1 | 50% | WARN |

**Root cause (common to all):** Inputs use `.form-input` class with `name` attribute but no `id`, and labels use `class="form-label"` without `for=` attribute. Labels are adjacent in the DOM so visual association is clear, but screen readers cannot programmatically link them.

**Severity: High (WCAG 2.1 SC 1.3.1 — Info and Relationships)**

**Note:** These are systemic across all forms — fixing all inputs would touch every form across 20+ views. The fix is straightforward but extensive. See Report 08 (Auto Fix Report) for the approach.

---

## ARIA Audit

| Element | Expected ARIA | Actual | Status |
|---------|--------------|--------|--------|
| Hamburger menu button | `aria-label="Menu"` | `aria-label="Menu"` ✓ | PASS |
| Nav chat badge | `aria-live` region | None | WARN — badge updates silently |
| Location modal | `role="dialog"` | None | WARN |
| Report modal | `role="dialog"` | None | WARN |
| Image slider | `role="list/listitem"` | None | WARN |
| Alert flash messages | `role="alert"` | None | WARN |
| Favorite heart button | `aria-label` | None | WARN |
| Mobile drawer | `aria-expanded` | None | WARN |

---

## Keyboard Navigation Audit

| Page / Element | Tab order | Enter activates | Escape closes | Status |
|---------------|-----------|-----------------|---------------|--------|
| Login form | Sequential ✓ | Submit ✓ | — | PASS |
| Register form | Sequential ✓ | Submit ✓ | — | PASS |
| Location modal | ~ (focus not trapped) | — | JS `closeLocationModal()` ✓ | WARN |
| Report modal | ~ (focus not trapped) | — | JS listener ✓ | WARN |
| Mobile drawer | ~ (focus not trapped) | — | ✓ overlay click | WARN |
| Subnav links | ✓ | ✓ | N/A | PASS |
| Filter sidebar links | ✓ | ✓ | N/A | PASS |
| Favorite heart | ~ (no tabindex, no button role) | ✗ | N/A | FAIL |

**Issue A11Y-FAV-01 (High):** Favorite heart icons (`.ad-fav`) are `<div>` elements inside anchor cards. They are not separately focusable or activatable by keyboard. A keyboard user cannot add/remove favorites without clicking.

---

## Focus Visibility Audit

All pages include `:focus` styles in their CSS (verified via HTTP audit). The global CSS in `layouts/app.blade.php` includes:
```css
.form-input:focus{border-color:var(--primary);outline:none;...}
```

However, `outline:none` is set on form inputs — this removes the native browser focus ring and replaces it with a border-color change. The border change provides a visible indicator, satisfying WCAG 2.1 SC 2.4.7, but may be insufficient for users with severe contrast sensitivity.

---

## Image Alt Text Audit

| Page | Total `<img>` | Missing `alt` | Status |
|------|--------------|--------------|--------|
| Homepage | 25 | 0 | PASS |
| Classifieds | 3 | 0 | PASS |
| Events | 9 | 0 | PASS |
| Directory | 12 | 0 | PASS |
| Blog | 15 | 0 | PASS |

All images that have `alt` attributes set. Images use `alt="{{ $listing->title }}"` pattern throughout — correct. Placeholder/decorative images use CSS backgrounds instead of `<img>` — also correct.

---

## Color Contrast Audit (Estimated)

| Element | Foreground | Background | Estimated Ratio | WCAG AA (4.5:1) |
|---------|-----------|-----------|-----------------|-----------------|
| Body text | `#1a1a1a` | `#fff` | ~19:1 | ✓ PASS |
| Muted text | `#666` | `#fff` | ~5.7:1 | ✓ PASS |
| Primary buttons text | `#fff` | `#1a3a8f` | ~9.5:1 | ✓ PASS |
| Accent button text | `#fff` | `#e8a020` | ~2.8:1 | ✗ FAIL |
| Badge small text | `#92400e` | `#fef9c3` | ~5.2:1 | ✓ PASS |
| Matrimonial purple btn | `#fff` | `#7c3aed` | ~7.1:1 | ✓ PASS |
| Muted text on primary bg | `rgba(255,255,255,.65)` | `#1a3a8f` | ~4.1:1 | ~ BORDERLINE |

**Issue A11Y-CONTRAST-01 (High):** White text on accent/gold background (`#e8a020`) yields ~2.8:1 contrast ratio — fails WCAG AA (4.5:1 required for normal text). Affected buttons: "Post Free Ad" hero button, all accent CTA buttons.

---

## Button Size / Tap Target Audit

| Element | Approximate Size | WCAG 2.5.5 (44×44px) | Status |
|---------|-----------------|----------------------|--------|
| Navbar post button | ~34×38px | ~ | BORDERLINE |
| Mobile tab bar icons | ~48×48px | ✓ | PASS |
| Filter sidebar links | ~38px height | ~ | BORDERLINE |
| Favorite heart | ~26×26px | ✗ | FAIL |
| Form submit buttons | ~44px+ height | ✓ | PASS |
| Auth form submit | ~46px height | ✓ | PASS |

**Issue A11Y-TAP-01 (Medium):** Favorite heart buttons are 26×26px — below the recommended 44×44px minimum tap target.

---

## Error Message Association Audit

| Form | Error Display Method | Programmatically Associated? |
|------|---------------------|------------------------------|
| Login | `@error('email')...div.error-msg` — adjacent DOM | NO — no `aria-describedby` |
| Register | Same pattern | NO |
| Post Create | Server validation `@error()` blocks | NO |
| Account forms | Session flash — no field association | NO |

**Issue A11Y-ERR-01 (High):** Validation error messages are rendered adjacent to inputs in the DOM but not programmatically linked via `aria-describedby`. Screen readers may not announce errors when fields are focussed.

---

## Accessibility Score

| Category | Score | WCAG Compliance |
|----------|-------|-----------------|
| Heading hierarchy | 10/10 (after fix) | AA |
| Form labels | 5/10 | Partial — visual OK, programmatic NO |
| ARIA attributes | 6/10 | Partial |
| Keyboard navigation | 7/10 | Partial |
| Focus visibility | 8/10 | AA (border substitute) |
| Image alt text | 10/10 | AA |
| Color contrast | 7/10 | Accent button FAIL |
| Button/tap sizes | 7/10 | Favorites too small |
| Error association | 5/10 | Not linked |
| **Overall** | **72/100** | **Partial WCAG 2.1 AA** |

---

## Accessibility Issues Summary

| ID | Severity | Issue | Fixed? |
|----|----------|-------|--------|
| A11Y-H1-01 through 07 | Critical | Missing H1 on 7 pages | YES — FIXED |
| A11Y-CONTRAST-01 | High | White text on accent gold fails WCAG AA | No — documented |
| A11Y-LABEL-01 | High | Form inputs not linked to labels programmatically | No — systemic |
| A11Y-ERR-01 | High | Error messages not aria-described | No — documented |
| A11Y-FAV-01 | High | Favorite button not keyboard accessible | No — documented |
| A11Y-MODAL-01 | Medium | Modals missing role="dialog" and focus trap | No — documented |
| A11Y-TAP-01 | Medium | Favorite heart 26×26px (below 44px min) | No — documented |
| A11Y-ARIA-01 | Medium | Chat badge, drawer, alerts missing ARIA | No — documented |
