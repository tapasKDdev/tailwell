# SEO — Canonical Setup

How TailWell's on-page SEO is configured. The theme covers what Yoast doesn't; Yoast
covers the rest.

## Division of labor

| Concern | Owner | Where |
|---|---|---|
| Title tag + meta description | n8n → Yoast keys (`_yoast_wpseo_title`, `_yoast_wpseo_metadesc`) | created with the draft |
| Focus keyword | n8n (`_yoast_wpseo_focuskw` = the input keyword) | created with the draft |
| XML sitemap | Yoast (Settings → Sitemaps) | site runtime |
| Canonical + robots meta | Yoast | site runtime |
| WebSite / Organization schema | Yoast (Site representation) | site runtime |
| **Product schema** (`[tw_product]` cards) | theme (`inc/shortcodes.php`) | each product card emits inline `Product` JSON-LD with name/url/brand/offers |
| **Breadcrumb trail + BreadcrumbList JSON-LD** | theme (`functions.php` `tailwell_breadcrumbs`) | visual on single/category; JSON-LD only when Yoast is absent (avoids duplicate breadcrumb schema with Yoast) |
| Category silo banner/copy | theme + page content | slug-driven banner on `/category/<slug>/` |

## Yoast settings

- Titles & Metas: default templates fine; the workflow writes per-post keys.
- Breadcrumbs: **leave Yoast breadcrumbs OFF** — the theme renders its own visual
  trail (single + category) so you never get two breadcrumbs.
- Site representation: Organization, matching the footer/About copy.
- Sitemaps: category archives included; articles included when published.
- `noindex`: keep tags/author archives `noindex`; leave categories and articles public.

## Silo structure = SEO structure

- Five categories are the silos; their archives are the money pages. WooCommerce-free
  and duplicate-free by design (`README.md` "one URL model").
- New articles target one silo each via the dropdown in the n8n form; the drafted
  post is assigned exactly one category.
- Internal links between silo hubs and articles are handled by
  [`docs/internal-linking.md`](internal-linking.md) (Phase 9).

## Product pages

- Products render through the `[tw_product]` shortcode only (never raw affiliate
  links in copy). Each card carries `rel="noopener nofollow sponsored"` and Product
  JSON-LD. Prices come only from approved product-database rows.