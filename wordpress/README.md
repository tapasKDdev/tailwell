# TailWell — WordPress Workspace

WordPress site-structure deliverables for the TailWell pet wellness affiliate blog.
This is one workspace within the repo — see the root [`README.md`](../README.md) for
the source-of-truth map.

## Layout

| Path | Contents |
|---|---|
| `tailwell-theme/` | GeneratePress child theme, launch-ready: `header.php`/`footer.php` (brand header + Primary/Footer menus), `index.php`, `front-page.php`, `page.php`, `single.php` (auto disclosure note), `archive.php`, `search.php`, `searchform.php`, `404.php`, `comments.php`, brand CSS, and the `[tw_silo_grid]`, `[tw_product]`, `[tw_disclosure]`, `[tw_latest_posts]` shortcodes + silo category banners |
| `content/` | Paste-ready page content: homepage, silo banners, about, contact, plus the full legal/policy set (affiliate disclosure, privacy, cookie, terms, disclaimer, editorial, corrections, review methodology, copyright, accessibility, advertising) |
| `preview/index.html` | Static visual preview of the theme (uses the real `tailwell-theme/style.css`) |
| `run.bat` | Opens the preview in Firefox — double-click to see the design locally |
| `setup/runbook.md` | Step-by-step hosting setup: WP core, plugins, categories, pages, menus, REST checks, n8n notes |
| `setup/verify-tailwell.ps1` | REST API verification script (auth, category slugs, Yoast meta, media, disclosure page) |

The product database is shared at the repo level: [`product-data/`](../product-data/)
(see [`product-data/product-sheet-template.csv`](../product-data/product-sheet-template.csv)).

## Quick start

1. Read `setup/runbook.md` and follow it in order.
2. Zip the contents of `tailwell-theme/` → upload as a child theme after GeneratePress.
3. Create the 5 categories with the exact slugs from runbook Section 5.
4. Build the pages from `content/`.
5. Run `setup/verify-tailwell.ps1` against the live site — it must fully pass before the n8n automation is trusted.
6. Import the n8n workflows from [`n8n/`](../n8n/) and follow
   [`docs/tailwell-final-setup-checklist.md`](../docs/tailwell-final-setup-checklist.md).

## Hard constraints (from the n8n workflow)

- Category slugs: `wellness-health`, `food-nutrition`, `gear-tech`, `training-behavior`, `senior-special-needs` — never rename.
- Yoast SEO is the SEO plugin (no Rank Math) because the workflow writes its meta keys.
- Round-trip `_yoast_wpseo_title` / `_yoast_wpseo_metadesc` / `_yoast_wpseo_focuskw` via REST before publishing anything.
- Product sheet `category` values: `joint-supplement`, `fresh-food`, `cbd-wellness`, `insurance`, `gear-tracker`, `gear-feeder`, `dental`, `odor-control`; only `status = approved` rows are inserted.