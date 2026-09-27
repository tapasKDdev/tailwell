# TailWell — Live Site Setup Runbook

This runbook takes a fresh WordPress install on real hosting through every step the
n8n automation depends on. It assumes the `tailwell-theme` child theme in this repo
is already built (it is — shortcodes, patterns, template parts, and content copy
all ship here).

**Role split:** steps 1–6 require hosting/operator access. Step 7 is automated by
`docs/rest-api-verification.ps1`. Steps 8–9 are final QA.

**Non-negotiables (the automation contract):** category slugs, Yoast meta keys, and
the product-sheet columns below are read by n8n exactly as written. If any of these
drift, the workflow fails silently — no post, no category, no meta, no error you'd
notice. Verifying them is the whole point of step 7.

---

## 0. The contract the automation expects

| Thing | Exact value(s) | Where it's enforced |
|---|---|---|
| Category slugs | `wellness-health`, `food-nutrition`, `gear-tech`, `training-behavior`, `senior-special-needs` | n8n `categories` field |
| Yoast meta keys | `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw` | n8n meta PATCH step |
| Post status | `draft` (workflow drafts first, human publishes) | n8n status field |
| Disclosure URL | `/affiliate-disclosure/` | already hardcoded in `functions.php` (`[tw_disclosure]`) — verified present |
| Product sheet columns | `category, name, why, price, url, status, last_checked` | n8n product-matching step |
| Product `category` enum | `joint-supplement`, `fresh-food`, `cbd-wellness`, `insurance`, `gear-tracker`, `gear-feeder`, `dental`, `odor-control` | n8n product-matching step |
| Product gate | only rows with `status = approved` are inserted into articles | n8n filter step |

---

## 1. WordPress core

1. Install WordPress on the host. Minimum: PHP 8.0+, HTTPS enabled.
   - **HTTPS is required** — WordPress Application Passwords are disabled on
     non-HTTPS (non-localhost) connections. Buy/configure the TLS cert first.
2. **Settings → Permalinks → Post name.** Required so silo URLs match the slugs the
   automation and the theme's shortcodes use.
3. **Create the automation user**, not-reusing admin:
   - Users → Add New: username `tailwell-bot`, **role: Editor** (not Administrator).
   - Users → Profile (as that user) → **Application Passwords → Add New**.
   - Copy the generated password (format `xxxx xxxx xxxx xxxx xxxx xxxx`). Store it
     in the n8n WordPress credential. You can't see it again after this screen.
4. **Themes**: install + activate **GeneratePress**, then upload `tailwell-theme.zip`
   (this repo) and activate it.

---

## 2. Required plugins (Yoast path)

| Plugin | Action | Why |
|---|---|---|
| **Yoast SEO** | Install + activate | n8n writes `_yoast_wpseo_*` meta via REST. Do **not** install Rank Math — they conflict. |
| **ThirstyAffiliates** | Install + activate | Link cloaking; inherits theme link colors (already styled). |
| **WP Rocket** or WP Super Cache | Install + configure | Caching for Core Web Vitals. |
| **UpdraftPlus** | Install + schedule | Backups (or host-level backups). |
| **Wordfence** | Install + configure | Security. **Optional later:** whitelist your n8n server IP / REST routes so it doesn't block automation. |

**After Yoast activates**, don't trust it — verify. Yoast registers its meta with
`show_in_rest: true` by default in current versions, but behavior varies by version,
block editor, and caching. Step 7 tests it live against your site.

---

## 3. Categories (must match exactly)

Create these in **Posts → Categories**. Slugs are what the automation posts with,
not names.

| Slug (exact) | Name |
|---|---|
| `wellness-health` | Wellness & Health |
| `food-nutrition` | Food & Nutrition |
| `gear-tech` | Gear & Tech |
| `training-behavior` | Training & Behavior |
| `senior-special-needs` | Senior & Special-Needs Pet Care |

> **Do not** rename or delete these after the automation is live. Silent breakage.

---

## 4. Pages

The theme ships block patterns (block inserter → **Patterns → TailWell**) and full
body copy in `content/pages.md`. Paste copy from there; few pages need more than a
pattern + pasted text.

| Page | Slug (exact) | How to build |
|---|---|---|
| Homepage | `/` | Pattern `tailwell/home-hero` → `[tw_silo_grid]` → latest-articles block |
| Wellness & Health | `/wellness-health/` | Pattern `tailwell/silo-hub-wellness` |
| Food & Nutrition | `/food-nutrition/` | Pattern `tailwell/silo-hub-food` |
| Gear & Tech | `/gear-tech/` | Pattern `tailwell/silo-hub-gear` |
| Training & Behavior | `/training-behavior/` | Pattern `tailwell/silo-hub-training` |
| Senior & Special-Needs | `/senior-special-needs/` | Pattern `tailwell/silo-hub-senior` |
| About | `/about/` | Copy from `content/pages.md` §3 |
| Affiliate Disclosure | `/affiliate-disclosure/` | Copy §4 — **slug is non-negotiable**, `[tw_disclosure]` links here |
| Privacy Policy | `/privacy-policy/` | WordPress **Settings → Privacy** page assignment, then paste copy §5 |
| Contact | `/contact/` | Pattern `tailwell/contact`, then replace stub with **WPForms Lite** (lightweight) shortcode |
| Blog index | `/blog/` | Empty page; set as Posts page (see below) |

**Settings → Reading:** *A static page* → Homepage = Homepage, Posts page = Blog.
This is what makes silo pages single-category hubs rather than post streams.

---

## 5. Navigation (Appearance → Menus)

| Menu | Assigned location | Items (in order) |
|---|---|---|
| **Primary** | `primary` | Wellness & Health, Food & Nutrition, Gear & Tech, Training & Behavior, Senior & Special-Needs, About |
| **Footer** | `footer` | About, Affiliate Disclosure, Privacy Policy, Contact |

Locations `primary` and `footer` are registered by the theme (see `functions.php`).

---

## 6. Product data (outside WordPress)

Build a Google Sheet (or Airtable) with **exactly** these columns:

```
category | name | why | price | url | status | last_checked
```

- `category` must be one of: `joint-supplement`, `fresh-food`, `cbd-wellness`,
  `insurance`, `gear-tracker`, `gear-feeder`, `dental`, `odor-control`.
- `status` drives the gate: **only `approved` rows are ever inserted into articles.**
  Keep every new product `draft` until reviewed; flip to `approved` only when the
  affiliate link is verified current.
- `last_checked` = date of last link/price verification; re-check on a schedule so
  the automation never publishes a dead or wrong URL.
- Starter CSV: `docs/product-sheet-template.csv`.

---

## 7. Run the verification script

From a machine with network access to the site (not the hosting box necessarily):

```powershell
powershell -ExecutionPolicy Bypass -File docs/rest-api-verification.ps1 `
  -BaseUrl "https://tailwell.example.com" `
  -User "tailwell-bot" `
  -Password "xxxx xxxx xxxx xxxx xxxx xxxx" `
  -TestCategory "wellness-health"
```

The script runs each checklist item as an independent call and prints PASS/FAIL:

1. **Auth** — GET `/wp-json/wp/v2/users/me?context=edit` → confirms the Application
   Password and role (Editor/Author).
2. **Category slug** — GET `/categories?slug=wellness-health` → confirms the exact
   slug resolves (TAP the categories field n8n will send).
3. **Post creation** — POST `/posts` with `categories: [that id]`, `status: draft` →
   confirms the drafted post actually lands in the right category.
4. **Yoast meta write** — PATCH `_yoast_wpseo_title` / `_yoast_wpseo_metadesc` /
   `_yoast_wpseo_focuskw`, then read back → confirms the meta is registered for REST
   and persisted. **This is the most common silent failure.**
5. **Disclosure page** — GET `/pages?slug=affiliate-disclosure` → confirms
   `[tw_disclosure]`'s target exists.
6. **Media upload + attach** — POST raw bytes to `/media`, then PATCH
   `featured_media` on the test post → confirms the image-upload step works.
7. **Cleanup** — deletes the test post and uploaded media (keeps the site clean).

Expect the script to exit `0` only when **all** checks pass. If step 4 fails,
yoast's REST registration is off for your version — that's a plugin-config fix, not
a theme fix.

Manual fallback (same checks, curl):

```bash
curl -u "tailwell-bot:xxxx xxxx xxxx xxxx xxxx xxxx" \
  https://tailwell.example.com/wp-json/wp/v2/users/me?context=edit
curl -u "tailwell-bot:xxxx xxxx xxxx xxxx xxxx xxxx" \
  "https://tailwell.example.com/wp-json/wp/v2/categories?slug=wellness-health"
```

---

## 8. Final QA (human)

- [ ] Contact page form submits and email arrives (test once).
- [ ] `[tw_disclosure]` link navigates to `/affiliate-disclosure/` from a real post.
- [ ] Homepage, one silo page, and one article render correctly at **375px** width
      (DevTools responsive mode — all layouts collapse to single column ≤600px).
- [ ] **PageSpeed Insights** on the same 3 URLs: no red flags for unused/unoptimized
      images or render-blocking scripts. Theme adds native `loading="lazy"` +
      `width`/`height` automatically.
- [ ] Yoast meta written by the script is visible in the post editor (Document
      sidebar → SEO), not just in the DB.

---

## 9. What NOT to do

- Don't rename category slugs once automation is live.
- Don't install Rank Math alongside Yoast (or vice versa). Pick one; the workflow
  writes Yoast keys.
- Don't give the automation user Administrator. Editor is sufficient and safer; meta
  writing needs *edit_posts*, which Editor has.
- Don't skip step 7. A silently-failing custom field wastes a day of "it should have
  worked" — the script is ten minutes.
- Don't let Wordfence or a WAF block the n8n server's REST traffic without you
  noticing — whitelist its IP if security fast-blocking triggers.
- Don't forget HTTPS **before** creating Application Passwords — they won't work
  over plain HTTP.