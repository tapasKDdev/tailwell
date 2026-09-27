# Prompt — Set Up TailWell WordPress Site Structure (Complete)

Copy everything below into your coding agent (Open Code / Hermes) as the task prompt. This consolidates and supersedes the earlier site-structure prompt — it's aligned with the theme, shortcodes, and n8n workflow already built for this project.

---

## Context

TailWell is a pet care wellness affiliate blog. A GeneratePress child theme (`tailwell-theme`) already exists with brand colors, typography, and 3 shortcodes: `[tw_silo_grid]`, `[tw_product name="" why="" price="" url=""]`, `[tw_disclosure]`. An n8n automation pipeline publishes draft posts via the WordPress REST API and expects specific category slugs and custom meta fields (detailed below) — **the site structure must match these exactly, or the automation will fail silently.**

## 1. WordPress Core Setup

- Install WordPress on real hosting/VPS (not a local/dev machine)
- Set permalink structure to "Post name" (required for clean silo URLs)
- Create a dedicated automation user (role: Editor or Author, not Administrator) with an Application Password — never reuse the site admin login
- Install and activate: **GeneratePress** (parent theme) → **tailwell-theme** (child theme, already built)

## 2. Required Plugins

| Plugin | Purpose |
|---|---|
| Yoast SEO | Meta title, description, focus keyword — the n8n workflow writes directly to `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw` via REST, so Yoast must be active and these fields must be exposed |
| ThirstyAffiliates | Affiliate link cloaking/management |
| Rank Math (alternative to Yoast — pick one, not both) | Same purpose as Yoast if preferred |
| WP Rocket or WP Super Cache | Performance/caching |
| UpdraftPlus (or host-level backups) | Backups |
| Wordfence or similar | Security |

## 3. Categories (must match exactly — used by the n8n workflow)

Create these 5 categories with these **exact slugs** (the automation posts using these slugs as the `categories` field):

- `wellness-health` — Wellness & Health
- `food-nutrition` — Food & Nutrition
- `gear-tech` — Gear & Tech
- `training-behavior` — Training & Behavior
- `senior-special-needs` — Senior & Special-Needs Pet Care

## 4. Pages to Create

- **Homepage** (static page, set as front page in Settings → Reading): hero section + `[tw_silo_grid]` shortcode + latest-articles block
- **5 silo archive pages** — can use the default category archive template styled via the theme, or dedicated pages linked from each category; each needs a banner at top (silo name, one-line description, icon badge) using existing `.tw-silo-card` / `.tw-icon-badge` CSS classes
- **About** — brand story, editorial standards
- **Affiliate Disclosure** — standard disclosure language; this is the page `[tw_disclosure]` links to, so confirm the slug is `/affiliate-disclosure/` or update the shortcode's link in `functions.php` to match
- **Privacy Policy** — WordPress's built-in Privacy Policy page template, customized for affiliate tracking/cookies/analytics
- **Contact** — simple contact form (use a lightweight plugin like WPForms Lite, not a heavy suite)

## 5. Navigation

- **Primary menu:** the 5 silo names (linking to their category archives) + About
- **Footer menu:** About, Affiliate Disclosure, Privacy Policy, Contact

## 6. Custom Fields the Automation Depends On

Confirm these are writable via the REST API for the automation user:
- Standard post fields: `title`, `content`, `status`, `categories`
- Yoast meta fields (listed above) — Yoast must expose these via `register_post_meta` with `show_in_rest: true`, which Yoast does by default in current versions, but verify after plugin install with a test REST call before relying on it
- Featured media — standard WP media endpoint, used by the image-upload step

## 7. Product Data Setup (outside WordPress, but required before automation can run)

Create a Google Sheet (or Airtable) with columns: `category | name | why | price | url | status | last_checked`

Valid `category` values (must match exactly, used by the n8n product-matching step): `joint-supplement`, `fresh-food`, `cbd-wellness`, `insurance`, `gear-tracker`, `gear-feeder`, `dental`, `odor-control`

Only rows with `status = approved` are ever inserted into articles — this is what prevents the automation from ever publishing an unverified or invented affiliate link.

## 8. Verification Checklist Before Handing Back

- [ ] Test post created manually via WP REST API with the automation user's Application Password — confirms auth works
- [ ] Test post created with a `categories` value of `wellness-health` — confirms slug matches
- [ ] Yoast meta fields confirmed writable via REST (test `PATCH` request, check the field actually updates in the editor)
- [ ] Media upload + attach-to-post tested via REST
- [ ] `[tw_disclosure]` link resolves to the actual Affiliate Disclosure page
- [ ] Site loads correctly at 375px mobile width on homepage, one silo page, and one article
- [ ] Core Web Vitals check (PageSpeed Insights or similar) — no red flags from unoptimized images or render-blocking scripts

## 9. What NOT to Do

- Don't rename category slugs after the automation is live — it will break silent post categorization
- Don't give the automation user Administrator role — Editor/Author is sufficient and safer
- Don't skip the REST API verification steps — a silently-failing custom field is worse than an obvious error
