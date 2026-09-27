# TailWell — GeneratePress Child Theme

## What's in this package

- `style.css` — all colors, typography, and component styling (buttons, cards, silo grid, product boxes, disclosure note, footer) plus the full-site structure styles (hero, silo banners, inline banner, article list, related grid, TOC, contact, accessibility)
- `functions.php` — enqueues fonts/styles, registers navigation menus, and provides 8 shortcodes (the original 3 + 5 new site-structure ones) plus lazy-loading and heading-id helpers
- `inc/patterns.php` — Gutenberg block patterns for every banner (homepage hero, 5 silo banners, silo hub pages, inline banner, article footer, contact)
- `template-parts/` — reusable hero, silo-banner, and related-articles parts
- `content/pages.md` — ready-to-paste page copy for all 8 pages + menu wiring
- `docs/setup-runbook.md` — step-by-step live-site setup (hosting, plugins, categories, pages, menus, product sheet, REST verification)
- `docs/rest-api-verification.ps1` — automated verification of every REST contract the n8n automation depends on
- `docs/product-sheet-template.csv` — starter product sheet (exact columns + valid category enum + status gate)
- `pages/` — static HTML preview prototypes (visual spec only)

> **Automation contract:** this theme ships aligned with the n8n workflow — silo page
> slugs match the category slugs, `[tw_disclosure]` links to `/affiliate-disclosure/`,
> and the Yoast meta keys / product-sheet format are documented in the runbook.
> When standing up the live site, run `docs/rest-api-verification.ps1` before letting
> the automation go live — a silently-failing custom field is worse than an obvious error.

## Installation

1. Install and activate the free **GeneratePress** parent theme from the WordPress theme directory (required — this is a child theme, it does nothing standalone).
2. Zip this `tailwell-theme` folder.
3. In WordPress admin: **Appearance → Themes → Add New → Upload Theme**, upload the zip, then **Activate**.
4. Go to **Appearance → Customize → General Settings → Layout** and set container width to match a clean, readable article width (GeneratePress default of ~1200px works well).
5. Upload your logo at **Appearance → Customize → Site Identity**.

## Shortcodes available

### Homepage silo grid
```
[tw_silo_grid]
```
Renders the 5 content-silo cards (Wellness & Health, Food & Nutrition, Gear & Tech, Training & Behavior, Senior & Special-Needs Care). Edit the `$silos` array in `functions.php` to change titles, descriptions, icons, or links.

### Affiliate product recommendation box
```
[tw_product name="Product Name" why="One-line reason this is recommended" price="$29.99" url="https://your-affiliate-link"]
```
Use this inside any article where you're recommending a specific product. It renders as a distinct card with an "Our pick" badge — not a bare text link — matching the design brief's requirement that affiliate recommendations look editorial, not like ads.

### Affiliate disclosure note
```
[tw_disclosure]
```
Place near the top of any article containing affiliate links. Points to `/affiliate-disclosure/` — create that page and update the URL in `functions.php` if your permalink differs.

## Site-structure shortcodes (new)

### Homepage hero
```
[tw_hero heading="..." sub="..." cta="..." url="..."]
```
Full-width brand hero with paw + leaf motif on the right (no photography). Rendered with `.tw-hero` / `.tw-hero__*` styles.

### Silo banner (one per category page)
```
[tw_silo_banner silo="wellness" desc="..."]
```
`silo` is one of `wellness`, `food`, `gear`, `training`, `senior`. Uses the existing `.tw-icon-badge` style with the matching icon (heart/bone/device/paw/leaf). The `desc` overrides the default copy.

### Inline article banner (mid-article internal linking)
```
[tw_inline_banner text="..." url="..." label="..."]
```
Low-key banner styled at the same visual weight as `.tw-disclosure` — deliberately not ad-like — for pointing readers at another silo.

### Article list (silo pages)
```
[tw_article_list count="6" category="wellness-health"]
```
Lists published posts from a category. Falls back to placeholder article cards on a fresh install so pages still render before real content exists.

### Related articles (article pages)
```
[tw_related_articles count="3" heading="Keep reading"]
```
Related-articles grid for the bottom of guides.

> The original shortcodes `[tw_silo_grid]`, `[tw_product]`, `[tw_disclosure]` are unchanged and still work exactly as before.

## Gutenberg block patterns

Registered in `inc/patterns.php` under the "TailWell" category. Insert from the block editor (block inserter → **Patterns → TailWell**):

| Pattern | Description |
|---|---|
| `tailwell/home-hero` | Homepage hero (`[tw_hero]`) |
| `tailwell/silo-{wellness,food,gear,training,senior}` | Single silo banner |
| `tailwell/silo-hub-{...}` | Complete silo page: banner + intro + article list |
| `tailwell/inline-banner` | Mid-article internal-linking banner |
| `tailwell/article-footer` | Disclosure + related-articles for the end of a guide |
| `tailwell/contact` | Contact section stub (drop your form plugin shortcode in) |

Patterns use the theme shortcodes rather than hardcoded markup, so a caching plugin can't strip inline styles and content stays editable without a designer.

## Menu wiring (Appearance → Menus)

Two menus are registered:
- **`primary`** — 5 silo names + About. Assign to GeneratePress **Primary Navigation**.
- **`footer`** — About, Affiliate Disclosure, Privacy Policy, Contact. Assign to a footer widget/footer-nav location.

Paste the copy in `content/pages.md` into each page (created at the slugs listed there), or edit the blueprint in the Customizer/Site Editor.

## Boost your article template (optional)
- Native `loading="lazy"` / `decoding="async"` added to attachment and content images automatically.
- Heading IDs are auto-added so a Gutenberg "Table of Contents" block (or the `.tw-toc` markup) can anchor correctly without manual IDs.
- Reduced-motion and focus-visible styles included; terracotta CTA uses dark-terracotta text to pass WCAG AA on cream.

## Static preview pages

The `pages/` folder contains static HTML prototypes of every layout in the design brief — useful as a visual spec before building in Gutenberg, or as a design reference for a designer/AI tool.

| File | Purpose |
|---|---|
| `pages/index.html` | Homepage: hero + 5 silo cards + latest-articles grid + newsletter |
| `pages/category-wellness.html` | Silo: Wellness & Health |
| `pages/category-food.html` | Silo: Food & Nutrition |
| `pages/category-gear.html` | Silo: Gear & Tech |
| `pages/category-training.html` | Silo: Training & Behavior |
| `pages/category-senior.html` | Silo: Senior & Special-Needs Care |
| `pages/article.html` | Article template: TOC, product boxes, comparison table, FAQ, author box, trust note, related |
| `pages/about.html` | About + editorial standards |
| `pages/affiliate-disclosure.html` | Required affiliate disclosure |
| `pages/contact.html` | Contact |
| `pages/privacy-policy.html` | Privacy policy |
| `pages/404.html` | 404 / soft-404 page |

Open `pages/index.html` directly in a browser, or serve the folder (e.g. `npx serve pages`). Shared styles live in `pages/assets/tailwell.css` and mirror the theme's `style.css` tokens, so the CSS you see in the prototype carries straight into the WordPress theme. The `.tw-product-box` markup matches the `[tw_product]` shortcode output. Test at 375px width as the brief requires — all layouts collapse to single-column below 600px.

### Prototype components → WordPress mapping

The prototype adds components that aren't yet in the child theme. When you build these in Gutenberg, the CSS classes are ready to reuse:

- **`.tw-compare`** — product comparison table. If you use AAWP, wrap its output in a `.tw-compare`-style card or reuse this markup; it collapses to stacked cards on mobile (≤640px).
- **`.tw-faq`** — FAQ accordion built on native `<details>`, pairs cleanly with Rank Math's FAQPage schema. A `[tw_faq]` shortcode would be a small addition to `functions.php`.
- **`.tw-author`** — author bio box for E-E-A-T; add an author box near the end of long guides.
- **`.tw-newsletter`** — calm inline email capture (no pop-ups, per the brief).
- **`.tw-error`** — the 404 layout.

Common elements are repeated per page (no templating engine), so keep header/footer consistent if you edit one.

## Recommended plugins to pair with this theme

- **ThirstyAffiliates** — link cloaking/management (already styled to inherit theme link colors)
- **Rank Math** or **Yoast SEO** — meta/schema
- **WP Rocket** or **WP Super Cache** — caching, important for Core Web Vitals
- **AAWP** — if using Amazon product data; wrap its output in a `.tw-card` div if you want it to visually match `.tw-product-box`

## Pages to create (WordPress → Pages → Add New)

| Page | Slug | How to build |
|---|---|---|
| Homepage | `/` | Pattern: `tailwell/home-hero` + `[tw_silo_grid]` + Query Loop or `[tw_related_articles count="6"]` |
| Wellness & Health | `/wellness-health/` | Pattern: `tailwell/silo-hub-wellness` |
| Food & Nutrition | `/food-nutrition/` | Pattern: `tailwell/silo-hub-food` |
| Gear & Tech | `/gear-tech/` | Pattern: `tailwell/silo-hub-gear` |
| Training & Behavior | `/training-behavior/` | Pattern: `tailwell/silo-hub-training` |
| Senior & Special-Needs | `/senior-special-needs/` | Pattern: `tailwell/silo-hub-senior` |
| About | `/about/` | See `content/pages.md` |
| Affiliate Disclosure | `/affiliate-disclosure/` | See `content/pages.md` |
| Privacy Policy | `/privacy-policy/` | See `content/pages.md` |
| Contact | `/contact/` | Pattern: `tailwell/contact` (drop your form plugin shortcode in) |

Set the Homepage page to the static front page and a blank Posts page under **Settings → Reading**. Each silo page is a static WordPress Page with the pattern pasted in.

## 404 page

GeneratePress: **Appearance → Customize → Blog → 404 Template** — or use **Appearance → Elements → Block → 404 Hook** with the copy in `content/pages.md`.

## Article template

The standard GeneratePress single-post template works out of the box. In the post editor:

1. Paste `[tw_disclosure]` near the top.
2. Write the article body.
3. Optionally use `[tw_inline_banner silo="senior" ...]` mid-article for internal linking.
4. Use `[tw_product ...]` inside product-recommendation boxes.
5. Drop `[tw_related_articles count="3"]` at the bottom — or paste the `tailwell/article-footer` pattern.

The CSS automatically styles all `[tw_product]`, `[tw_disclosure]`, `.tw-inline-banner`, and `.tw-related` elements.

## Notes for whoever builds the actual page templates

- Homepage: hero pattern (or GeneratePress Elements) + `[tw_silo_grid]` shortcode + latest posts block
- Article template: standard GeneratePress single-post template works as-is — the CSS here restyles headings, links, and any `[tw_product]` / `[tw_disclosure]` shortcodes automatically
- Silo pages: paste the corresponding `tailwell/silo-hub-*` pattern into a static Page
- No custom PHP template files were needed — everything is handled via the stylesheet, shortcodes, patterns, and template parts, which keeps this update-safe and simple to maintain
