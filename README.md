# TailWell — Production Source of Truth

TailWell is a pet-information and affiliate content platform: a WordPress site
driven by an n8n content pipeline. This repository is the **single authoritative
implementation** — legacy and obsolete material lives in [`archive/`](archive/) and
is not to be edited.

- **Business purpose:** publishing buying guides, comparisons and wellness
  articles that earn affiliate commissions (transparently disclosed) on pet
  care products, weighted toward high-commission wellness/senior-care categories.
- **Target audience:** pet owners researching health, nutrition, gear, training
  and senior-care products for dogs and cats.
- **Target markets:** **United States and United Kingdom only.** Canada,
  Australia and other jurisdictions are explicitly out of scope
  (see [`docs/legal-compliance.md`](docs/legal-compliance.md)).

> **Edit rule:** every change made in this repo is logged in [`CHANGELOG.md`](CHANGELOG.md)
> in the same session it is made (newest first; buckets Added / Changed / Fixed /
> Removed / Docs).

## Repository map (canonical locations)

| Concern | Authoritative path | Notes |
|---|---|---|
| Production WordPress child theme | `wordpress/tailwell-theme/` | GeneratePress child; templates, `functions.php`, `style.css`, `assets/` |
| Page content copy | `wordpress/content/` | Paste-ready page bodies (home, about, disclosure, privacy, contact, silo banners) |
| WordPress setup runbook | `wordpress/setup/runbook.md` | Step-by-step live-site setup, in order |
| REST API verifier | `wordpress/setup/verify-tailwell.ps1` | Live-site contract check; must fully pass before automation runs |
| WordPress workspace notes | `wordpress/README.md` | Theme/content/setup overview |
| n8n workflows | `n8n/` | `tailwell-end-to-end.json` (research→publish); a freshness monitor is planned, not yet present |
| n8n setup checklist | `docs/tailwell-final-setup-checklist.md` | Credentials, env vars, model placeholders, sanity checks, test order |
| Automation prompts | `automation/prompts/` | The prompt system that the n8n nodes reference |
| Research/evidence system | `research/` | Evidence schema, claim→source mapping, methodology |
| Product database | `product-data/` | Canonical schema, template CSV, validation tooling |
| Static design prototype | `pages/` | Visual spec (12 HTML pages) — design reference, not the live URL set |
| Theme (real CSS) preview | `wordpress/preview/index.html` | Live render check against the actual theme stylesheet |
| Architecture / security / testing docs | `docs/` | Setup checklist, SEO, GEO/AEO, internal linking, freshness, affiliate/privacy, image pipeline, architecture, security, rollout, testing matrix, legal-compliance hub + US/UK control matrix, evidence / product-claims / affiliate-compliance / privacy-inventory / content-risk / AI-content policies |
| Changelog | `CHANGELOG.md` | Every change, dated |
| Legacy / obsolete | `archive/` | Old theme generation, stale ZIP, previous prompts/docs — read-only reference |

## Production architecture (one URL model)

- **Content silos = native WordPress categories**, exactly five slugs:
  `wellness-health`, `food-nutrition`, `gear-tech`, `training-behavior`,
  `senior-special-needs`. These slugs are never renamed.
- Category archives render the silo banner automatically (`tw-silo-hero`) in the
  theme. There are **no competing static-page silo URLs** (`/wellness-health/` as a
  page was the legacy architecture — archived).
- The n8n pipeline resolves each slug → numeric term ID once per run
  (`GET /wp-json/wp/v2/categories?slug=…`) and posts drafts with `categories:[id]`.
- SEO plugin: **Yoast** (`_yoast_wpseo_title`, `_yoast_wpseo_metadesc`,
  `_yoast_wpseo_focuskw`). Do not combine with Rank Math.
- Affiliate products are inserted **only** from approved rows of the product
  database — never invented by the writer; unresolved placeholders fail QA.

## Repository structure

```
TailWell/
├── README.md · CHANGELOG.md · TAILWELL-MASTER-SOP.md   governance
├── .env.example · .gitignore                            config template + guards
├── wordpress/
│   ├── tailwell-theme/      production child theme (GeneratePress) — deploy this
│   ├── preview/index.html   static render of the theme (QA harness target)
│   ├── content/             paste-ready page copy (13 pages + legal set)
│   └── setup/               runbook + live-site verify scripts
├── n8n/                     tailwell-end-to-end.json (v6 workflow) + checklist ptr
├── automation/prompts/      prompt system (planner/writer/qa/seo/researcher)
├── research/                evidence schema, claim mapping, examples/
├── product-data/            product schema + starter CSV (validate-* gate)
├── docs/                    architecture, legal/compliance, policies, testing
├── scripts/                 validation gates + browser test harness
├── pages/                   FROZEN design prototype (reference only, never deploy)
└── archive/                 historical material (protected, read-only)
```

## System roles

| Piece | Role |
|---|---|
| **WordPress** | Publishing target only. Content silos = the five native categories (`wellness-health`, `food-nutrition`, `gear-tech`, `training-behavior`, `senior-special-needs`, never renamed) + a separate `life_stage` taxonomy (puppy/adult/senior). Yoast SEO is the only SEO plugin (workflow writes `_yoast_wpseo_*` keys). The pipeline only ever creates **drafts** — a human publishes. |
| **n8n** | Orchestration: form trigger → kill switch (`AUTOPILOT_ENABLED`) → Wikipedia background → relevance gate → section planning → evidence-tagged writing → product CSV parse → approved-product insert + internal links → QA gate → SEO title/meta → WordPress draft → Telegram alert. Import from [`n8n/tailwell-end-to-end.json`](n8n/tailwell-end-to-end.json); setup per [`docs/tailwell-final-setup-checklist.md`](docs/tailwell-final-setup-checklist.md). |
| **Research/evidence** | Health/safety/nutrition claims require `[ev:TWP-EVID-####]` tags backed by `research/evidence-*.json` objects (schema in [`research/evidence-schema.md`](research/evidence-schema.md)); Wikipedia is background, never evidence; AI output is never evidence. Workflow does not fetch evidence — hand it in per [`docs/evidence-handoff.md`](docs/evidence-handoff.md). |
| **Product/affiliate system** | Products are inserted only from `status=approved AND enabled=true` rows of the Google-Sheet CSV built from [`product-data/product-sheet-template.csv`](product-data/product-sheet-template.csv); the writer never invents products or URLs; every product link renders `rel="noopener nofollow sponsored"`; CTAs are "Check price"/"View product" only. Approving a row is a manual, verified step (SOP C). |
| **Legal/compliance** | 11 public legal pages in [`wordpress/content/`](wordpress/content/) (privacy, affiliate disclosure, terms, disclaimer, editorial/corrections/review-methodology/copyright/accessibility/advertising, cookie) + internal policies in [`docs/legal-compliance.md`](docs/legal-compliance.md) (jurisdiction map, escalation rule, open items) and [`docs/legal-compliance-matrix.md`](docs/legal-compliance-matrix.md). Red lines live in [`docs/product-claims-policy.md`](docs/product-claims-policy.md). **Counsel review before launch is an open item.** |

## Quick start

1. Read [`wordpress/setup/runbook.md`](wordpress/setup/runbook.md) and follow it on
   the live host (WP core, permalinks, automation user, plugins, categories, pages,
   menus).
2. Build the product sheet from [`product-data/product-sheet-template.csv`](product-data/product-sheet-template.csv),
   then run [`wordpress/setup/verify-tailwell.ps1`](wordpress/setup/verify-tailwell.ps1).
3. Import the workflow(s) from [`n8n/`](n8n/) and configure credentials/env per
   [`docs/tailwell-final-setup-checklist.md`](docs/tailwell-final-setup-checklist.md).

## Validation (run before any push)

All gates are green as of the current release candidate; re-run after changes
(PowerShell, from the repo root; add `D:\xampp\php` to `PATH` for the PHP lint):

```powershell
powershell -File scripts/php-lint.ps1            # theme PHP syntax (13 files)
powershell -File scripts/check-links.ps1         # static href/src resolution
powershell -File scripts/validate-products.ps1   # product CSV schema/gates
powershell -File scripts/validate-evidence.ps1 -IncludeExamples
node scripts/product-pipeline-test.js            # product insert logic (4/4)
node scripts/evidence-context-test.js            # evidence handoff (3/3)
powershell -File scripts/validate-workflow.ps1   # n8n graph integrity (v6, 23 nodes)
node scripts/life-stage-workflow-check.js        # workflow life-stage wiring (14/14)
powershell -File scripts/validate-life-stage.ps1 # theme life-stage wiring (17/17)
# + UTF-8 BOM policy: .css/.html/.js/.md must carry BOM; .php/.json must not
# + browser QA: start Chrome headless on :9333, open the preview, then
node scripts/nav-mobile-browser-test.js 9333
node scripts/nav-escape-focus-test.js 9333
```

Live-site contract (after WordPress exists): `wordpress/setup/verify-tailwell.ps1`
then `verify-life-stage.ps1` — see [`docs/testing.md`](docs/testing.md) for the
full matrix.

## Environment variables & secrets

- Template: [`.env.example`](.env.example) — **names only**. `.env` is gitignored.
- Real values live only in **n8n → Credentials** and **n8n → Settings →
  Environment** (`AUTOPILOT_ENABLED`, `WORDPRESS_URL`), plus your local shell.
- Model names and `YOUR_TELEGRAM_CHAT_ID` / `YOUR_SHEET_PUBLISHED_CSV_URL` are
  literal placeholders **inside** the workflow JSON — replace before import.
- Rules: never commit keys, never paste real values into docs/prompts/JSON,
  rotate any credential that ever lands in Git history. The repo has been
  content-audited for secret shapes (clean as of the GitHub-prep pass).

## Deployment architecture (Cloudflare Pages)

**Public deployment root: `wordpress/`** — the only directory where the validated
preview actually renders: `preview/index.html` loads `../tailwell-theme/style.css`
(relative), so the preview must be uploaded **together with** `tailwell-theme/`.
Serve the site from `/preview/index.html` (or map it as the Pages root via a
staging copy).

Recommended deploy step (produces a `dist/` staging folder — gitignored, never committed):

```powershell
robocopy wordpress\preview dist /E
robocopy wordpress\tailwell-theme dist\tailwell-theme /E
# upload dist/ to Cloudflare Pages (direct upload or wrangler pages deploy dist)
```

**Never exposed publicly:** `docs/`, `research/`, `product-data/`, `automation/`,
`n8n/`, `scripts/`, `archive/`, `pages/`, root governance files (README /
CHANGELOG / SOP), and `wordpress/setup/` + `wordpress/content/` source files
(editor notes are internal; the *published pages* are what goes live).

## Current production status & known blockers

Status: **release candidate — repo-side complete, environment not provisioned.**

- ✅ Theme, workflow v6, prompts, product/evidence systems, 11 legal pages,
  docs, all validation gates and browser QA (same-session, green).
- ⛔ No domain/hosting → no WordPress installation yet.
- ⛔ n8n and the OmniRoute gateway are not installed (Ollama is running locally).
- ⛔ No credentials anywhere: OpenRouter, WP app password, Telegram, product
  sheet URL, affiliate programs (all placeholders).
- ⛔ Product database has zero approved rows; evidence files for real topics
  don't exist yet — both are manual, verified steps by design.
- 📄 Counsel review of the public legal pages is an open launch item.

Full detail: production-readiness table in CHANGELOG (2026-09-25) and
[`docs/legal-compliance.md`](docs/legal-compliance.md) §Open items. The first
end-to-end run must stay human-reviewed — the pipeline only creates drafts.

## Backup & rollback

- **Backup:** host-level snapshot **and** UpdraftPlus to cloud storage from day
  one; the repository itself is the source of truth for code/content/automation.
- **Rollback:** restore the previous theme zip you exported in
  [`wordpress/setup/runbook.md`](wordpress/setup/runbook.md) (§4), or revert this
  repo (`archive/` keeps the prior generation). n8n drafts, never publishes, so a
  bad run is deleted, not corrected.

## Where the production files live — final word

| File | Edition | Purpose |
|---|---|---|
| `wordpress/tailwell-theme/` | one | the only theme to deploy |
| `n8n/tailwell-end-to-end.json` | one | the only end-to-end workflow |
| `automation/prompts/` | one | the only prompt system |
| `product-data/` | one | the only product database schema/template |
| `research/` | one | the only evidence/claim architecture |
| `docs/`, `wordpress/setup/` | one each | the only runbooks and contracts |

There is exactly one authoritative file per production concern. If you find a
second copy of anything, move the obsolete one to `archive/` and log it in
`CHANGELOG.md`.