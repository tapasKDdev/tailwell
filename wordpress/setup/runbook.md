# TailWell WordPress Setup Runbook

Hands-on, step-by-step setup guide. Follow it in order on your hosting account.

## 0. Prerequisites

- A domain pointed at the hosting account with HTTPS available (Let's Encrypt is fine).
- Hosting that supports WordPress (any reputable shared host or VPS; PHP 8.0+ recommended, MySQL/MariaDB).
- An npm/zip tool to package the child theme (see Step 4).
- The n8n instance ready to receive the site URL, automation credentials, and the product sheet link (Section 11).

Local deliverables referenced here:
- `tailwell-theme/` — the child theme to upload
- `content/*.md` — paste-ready page content
- `../product-data/product-sheet-template.csv` — the Google Sheet template (repo root `product-data/`)
- `setup/verify-tailwell.ps1` — REST API verification script

## 1. WordPress core + permalinks

1. Install WordPress on the real hosting account (use the host's one-click installer, or manual install; **not** a local/dev machine).
2. **Settings → Permalinks → Post name** → Save. This is required for clean silo URLs.
3. Confirm the **Category base** under Settings → Permalinks is left as `category` (default). The theme's silo links assume `/category/<slug>/`.
4. Log in, then set a static front page: **Settings → Reading → Your homepage displays → A static page** (page created in Step 6).

## 2. Automation user + Application Password

1. **Users → Add New**: Username `tw-automation`, email a real inbox, **Role: Editor** (never Administrator).
2. Open the new user profile. Under **Application Passwords**, create one named `n8n`. Copy it — it is shown only once.
3. Keep the site admin login strictly for manual admin; the automation only ever uses `tw-automation`.

Credentials needed by n8n: site URL, username `tw-automation`, the application password, and the REST base `https://<domain>/wp-json/`.

## 3. Plugins

Install and activate, in this order:

| Plugin | Why | Notes |
|---|---|---|
| **Yoast SEO** | The n8n workflow writes `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw` via REST. **Do not use Rank Math alongside or instead** unless you rewrite the n8n meta steps — Rank Math uses different meta keys. Pick Yoast. | After activation, run through the setup wizard (no paid upgrade needed). |
| **ThirstyAffiliates** | Affiliate link cloaking/management. | Configure link prefix under a reserved slug (e.g. `/go/`). The n8n flow can pass final cloaked URLs, or raw URLs — ThirstyAffiliates will redirect raw affiliate URLs. |
| **WP Super Cache** or **WP Rocket** | Performance/caching. | WP Rocket is premium; WP Super Cache is free and sufficient at launch. |
| **UpdraftPlus** | Backups. | Configure scheduled backups to cloud storage (S3/Drive/Dropbox) on day one. Or rely on host-level backups, not both. |
| **Wordfence** | Security. | Recommended default config: scan schedule, login security, firewall. Whitelist the automation user's IP range if static. |
| **WPForms Lite** | Contact form only. | Used by the Contact page; nothing heavier. |

After Yoast is active, finish the check in Step 8.

## 4. Themes

1. **Appearance → Themes → Add New** → upload the **GeneratePress** parent theme → Activate.
2. Package the child theme:
   - Zip the **contents** of `tailwell-theme/` into `tailwell-theme.zip`. Important: the zip must contain `style.css` and `functions.php` at its root (not a wrapping folder).
   - On Windows PowerShell (adjust the source path to your clone):
     ```powershell
     Compress-Archive -Path "D:\PROJECTS_x\TailWell\wordpress\tailwell-theme\*" -DestinationPath "$env:TEMP\tailwell-theme.zip"
     ```
3. **Appearance → Themes → Add New → Upload Theme** → upload `tailwell-theme.zip` → Activate.

If you ever need to change the disclosure-page slug, update the `tailwell_disclosure_page` filter in `tailwell-theme/inc/shortcodes.php`.

## 5. Categories (exact slugs — do not rename later)

Create these 5 categories under **Posts → Categories**. Use the exact names and slugs; paste the descriptions from `content/silo-banners.md` into each Description field.

| Name | Slug (must match) |
|---|---|
| Wellness & Health | `wellness-health` |
| Food & Nutrition | `food-nutrition` |
| Gear & Tech | `gear-tech` |
| Training & Behavior | `training-behavior` |
| Senior & Special-Needs Pet Care | `senior-special-needs` |

WP-CLI alternative (same result):
```bash
wp term create category "Wellness & Health" --slug=wellness-health --description="..."
# repeat for the other four
```

The n8n workflow sends category **slugs**; the REST API `categories` field needs **term IDs**. The automation must resolve slug → ID once per run via `GET /wp-json/wp/v2/categories?slug=<slug>` (see Section 11).

## 5.5 Life stage taxonomy + terms

Activating the TailWell child theme (Step 4) registers a **`life_stage`**
taxonomy on posts (non-hierarchical, `show_in_rest`, REST base `life_stage`). The
three terms are seeded automatically on theme switch, but create them explicitly
so the setup is reproducible without re-activating the theme:

| Name | Slug (must match) |
|---|---|
| Puppy | `puppy` |
| Adult | `adult` |
| Senior | `senior` |

WP-CLI alternative:
```bash
wp term create life_stage "Puppy" --slug=puppy
wp term create life_stage "Adult"  --slug=adult
wp term create life_stage "Senior" --slug=senior
```

Life stage mirrors the category rule: every post gets **exactly one** of the 5
categories **plus one** life-stage term, and the REST API `life_stage` field needs
**term IDs**, not slugs. Senior & Special-Needs posts use `life_stage = senior`;
that archive shows no life-stage filter pills.

## 6. Pages

Create the pages below using `content/*.md`. For pages with shortcodes, add a **Shortcode block** in the editor. Remember to set the homepage as static front page (Step 1).

| Page | Slug (Path) | Source | Notes |
|---|---|---|---|
| Home | `home` (`/`) | `content/homepage.md` | Conversion layout: hero → `#help` → `#guides` → `[tw_silo_grid]` → `[tw_latest_posts count="3"]` → trust/method → newsletter; set as front page |
| About | `about` | `content/about.md` | |
| Affiliate Disclosure | `affiliate-disclosure` | `content/affiliate-disclosure.md` | Slug must match the shortcode target exactly (`/affiliate-disclosure/`) |
| Privacy Policy | `privacy-policy` | `content/privacy-policy.md` | Assign in **Settings → Privacy**; copy is final (only the production domain line is pending — see the editor note at the top of the file) |
| Cookie Policy | `cookie-policy` | `content/cookie-policy.md` | Describes the site's real no-cookie posture; update together with the privacy policy if tracking is ever added |
| Terms of Use | `terms-of-use` | `content/terms-of-use.md` | Governing-law section is intentionally pending the operating entity — see `docs/legal-compliance.md` open items |
| Disclaimer | `disclaimer` | `content/disclaimer.md` | Umbrella health/product/"best" disclaimer — linked from affiliate disclosure and articles |
| Editorial Policy | `editorial-policy` | `content/editorial-policy.md` | Full version of About's four bullets — link, don't duplicate |
| Corrections Policy | `corrections-policy` | `content/corrections-policy.md` | |
| Review Methodology | `review-methodology` | `content/review-methodology.md` | Canonical "how we pick" + what the Safety-reviewed badge means; footer "How we research" links here |
| Copyright Policy | `copyright-policy` | `content/copyright-policy.md` | DMCA agent not yet registered — section 4 says so; update when registered |
| Accessibility | `accessibility` | `content/accessibility.md` | |
| Advertising Policy | `advertising-policy` | `content/advertising-policy.md` | Public commitment behind the homepage "No sponsored rankings" chip |
| Contact | `contact` | `content/contact.md` | Needs WPForms Lite + the generated form shortcode |

The 5 silo archives need no pages — the theme renders a banner automatically on each category archive (Step 5 + `tw-silo-hero` CSS). Optional dedicated landing pages are documented in `content/silo-banners.md`.

**Silo banner sanity check:** open `/category/wellness-health/` and confirm the hero banner with the icon badge renders above the post list.

## 7. Navigation

Under **Appearance → Menus** create, then assign:

- **Primary menu** (set as Primary in menu settings) — short labels (full silo names are long for mobile nav; URLs unchanged):
  - Wellness → `/category/wellness-health/`
  - Food → `/category/food-nutrition/`
  - Gear → `/category/gear-tech/`
  - Training → `/category/training-behavior/`
  - Senior Care → `/category/senior-special-needs/`
  - About → `/about/`
- **Footer menu** (assign via GeneratePress Footer or a footer widget) — matches `tailwell_footer_menu_fallback()` in the theme:
  - About (`/about/`), How we research (`/review-methodology/`), Affiliate Disclosure (`/affiliate-disclosure/`), Privacy Policy (`/privacy-policy/`), Cookie Policy (`/cookie-policy/`), Terms of Use (`/terms-of-use/`), Contact (`/contact/`)
  - Remaining legal pages (Disclaimer, Editorial/Corrections/Advertising/Copyright policies, Accessibility) are cross-linked from the pages above; add them to the footer too if you want everything one click away.

## 8. REST API verification (custom fields + auth)

Run the verification script against the live site. Prerequisite: Yoast active, `tw-automation` user + application password ready.

```powershell
.\setup\verify-tailwell.ps1 -BaseUrl "https://<domain>" -Username "tw-automation" -AppPassword "<app-password>"
```

The script verifies, in order:
1. Authentication works (Basic auth with the application password).
2. All 5 category slugs exist and returns their term IDs (capture these for n8n).
3. The `life_stage` taxonomy is exposed and the 3 terms (`puppy`, `adult`, `senior`) exist, and returns their term IDs.
4. A draft post can be created in `wellness-health` with both `categories` and `life_stage` attached, and both round-trip.
5. **Yoast meta fields** `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw` can be written via `PATCH /wp-json/wp/v2/posts/<id>` and read back. If this step warns, Yoast is not exposing the fields — do not proceed with the automation until it passes.
6. Media upload to the library works, and a featured image can be attached to a post.
7. The `/affiliate-disclosure/` page resolves (HTTP 200).

The script leaves its test draft and test image in place so you can inspect them first; add `-Cleanup` on a later run to delete them automatically.

After it passes, run the Life Stage coverage matrix — 13 drafts (puppy/adult/senior × 4 general silos + senior × senior silo), each round-trip checked:
```powershell
.\setup\verify-life-stage.ps1 -BaseUrl "https://<domain>" -Username "tw-automation" -AppPassword "<app-password>" -Cleanup
```

## 9. Product data sheet (outside WordPress)

1. Copy `../product-data/product-sheet-template.csv` (repo root `product-data/`)
   into Google Sheets (File → Import; or create columns manually).
2. Column headers **exactly**: `category | name | why | price | url | status | last_checked`.
3. Valid `category` values (must match n8n's product matcher exactly):
   `joint-supplement`, `fresh-food`, `cbd-wellness`, `insurance`, `gear-tracker`, `gear-feeder`, `dental`, `odor-control`.
4. `status` gate: only rows where `status = approved` are ever inserted into articles. Leave sample rows as `pending`; flip to `approved` only after you manually verify the product + link.
5. Grant the n8n service account (or Google Sheet API key) read access to this sheet.

## 10. Post-launch checks

- 375px mobile width on homepage, one silo archive, and one published article (DevTools responsive mode). The theme grid collapses to a single column under 700px.
- Core Web Vitals via PageSpeed Insights. Common first fixes: serve images in next-gen format with `srcset`, enable caching plugin page cache, defer third-party scripts, and prefer WebP featured images.

## 11. n8n integration notes (from the existing workflow's expectations)

- **Categories:** the workflow sends slugs from the sheet/category step. The REST `categories` field is an array of term IDs — resolve each slug once per run:
  ```
  GET /wp-json/wp/v2/categories?slug=wellness-health  →  [0].id
  ```
  POST to posts with `"categories":[<id>]`.
- **Life stage:** same rule for the custom `life_stage` taxonomy (non-hierarchical, REST base `life_stage`):
  ```
  GET /wp-json/wp/v2/life_stage?slug=senior  →  [0].id
  ```
  POST to posts with `"life_stage":[<id>]` alongside `categories`.
- **Create draft + Yoast meta:** the workflow v6 POSTs the whole body in one call (HTTP Request node — the built-in WordPress node can't send custom taxonomies):
  ```json
  {
    "title": "...",
    "content": "...",
    "status": "draft",
    "categories": [123],
    "life_stage": [456],
    "meta": {
      "_yoast_wpseo_title": "...",
      "_yoast_wpseo_metadesc": "...",
      "_yoast_wpseo_focuskw": "..."
    }
  }
  ```
  `status: "draft"` throughout — human reviews, then publishes. If Yoast fields silently no-op, the verify script's step 5 catches it before the pipeline trusts them.
- **Featured image:** `POST /wp-json/wp/v2/media` (multipart/form-data binary) → returns `id` → `PATCH /wp-json/wp/v2/posts/<id>` with `{"featured_media":<id>}`.
- **Auth header:** `Authorization: Basic base64(username:app_password)` for every call. Never reuse the site admin's password.
- Only insert products where sheet row `status = approved`. No exceptions.

## 12. What NOT to do (recap)

- Never rename category slugs after the automation is live — posts will silently land in the wrong (or no) category.
- Never give the automation user Administrator — Editor is sufficient and safer.
- Never skip the REST verification steps — a silently-failing custom field is worse than an obvious error.
- Don't activate both Yoast and Rank Math — pick Yoast, because the n8n workflow writes Yoast meta keys.
- Don't create puppy/adult/senior **categories** — life stage is a separate, non-hierarchical taxonomy layered on top of the 5 silos.
- Don't send `life_stage` as a slug — WordPress REST needs the resolved term ID, same as categories.
- Don't rename or delete `life_stage` terms after automation is live — the resolve node returns `[]` and the draft fails.