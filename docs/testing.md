# Test Matrix

What to run, when, and pass criteria. Report card for the whole repo. Scripts live
in `scripts/` unless noted.

## Full-repo gates (run before any commit)

| # | Check | Command / target | Pass = |
|---|---|---|---|
| 1 | PHP syntax | `scripts/php-lint.ps1` (falls back to `php -l` per file if PHP installed; skips gracefully if not) | every `.php` file parses |
| 2 | Internal links | `scripts/check-links.ps1` | every href/src in the static prototype + theme preview resolves (12 `pages/*.html` + `preview/index.html`, 13 files) |
| 3 | Product sheet | `scripts/validate-products.ps1` on `product-data/product-sheet-template.csv` (and real sheet export before pipeline tests) | headers/enums/approved-row/duplicate/BOM rules pass; template has no approved rows |
| 4 | Evidence files | `scripts/validate-evidence.ps1` (add `-IncludeExamples` to validate the illustrative placeholder too) | schema rules per file; every product `source_id` exists |
| 5 | Product/insert logic | `node scripts/product-pipeline-test.js` | 4/4 cases PASS (BOM+quotes+approved-only; unmatched→comment; swap→real shortcode; internal-links block) |
| 5b | Evidence handoff mapping | `node scripts/evidence-context-test.js` | 3/3 PASS (no-evidence suppression; ids/tier/supported/refuted surfaced; invalid input throws) |
| 6 | n8n graph integrity | `scripts/validate-workflow.ps1` | valid JSON, unique node names, all connection targets resolve, both Code bodies syntax-check |
| 6a | Life Stage theme structure | `scripts/validate-life-stage.ps1` | 17/17 PASS (taxonomy flags, terms, shortcode attrs + tax_query, single badge, pill filter excl. senior silo, badge CSS) |
| 6b | Life Stage workflow invariants | `node scripts/life-stage-workflow-check.js` | 14/14 PASS (v6, form dropdown, resolver node, life_stage term-ID in create body, edges intact) |

## Browser verification (uses headless Chrome via CDP)

Run against `wordpress/preview/index.html` — the preview mirrors the real header.
Need Node ≥ 22 (global `WebSocket`). Harness: launch
`chrome --headless=new --remote-debugging-port=<P> --user-data-dir=<temp>` passing
the preview URL as an argument, then drive `Runtime.evaluate` from the script. For
layout measures use `Emulation.setDeviceMetricsOverride` **then** `Page.navigate`
(don't measure a mid-session override).

| # | Check | Script | Pass = |
|---|---|---|---|
| 8 | Mobile nav open/close + no-JS fallback | `scripts/nav-mobile-browser-test.js` | button `aria-expanded` toggles, outside-click closes, panel spans viewport |
| 9 | Escape + focus return | `scripts/nav-escape-focus-test.js` | Escape closes and returns focus to the toggle |
| 10 | Layout metrics | `scripts/nav-layout-metrics.js`, `scripts/nav-layout-diag.js` | hScroll = 0 at 375/320/900 px; one-row brand at 320 px |

## Live-site contract (after deploy)

| # | Check | Command | Pass = |
|---|---|---|---|
| 12 | REST contract | `wordpress/setup/verify-tailwell.ps1` vs the live host | theme assets, category slugs, shortcodes, Yoast postmeta all present |
| 13 | Draft contents | n8n test run per `docs/tailwell-final-setup-checklist.md` steps 1–4 | kill switch blocks; QA fails on no-approved-product; real draft has sections + shortcode + Yoast meta + internal links + correct life-stage term |
| 14 | Life Stage matrix | `wordpress/setup/verify-life-stage.ps1` (-Cleanup removes them) | 13/13 drafts created; every one round-trips both `categories` and `life_stage` (puppy/adult/senior × 4 general silos + senior × senior silo) |

## Manual / by-design (no automation)

- Visual review of the running site in the browser.
- Life-stage shortcode render check: `[tw_article_list category="X" life_stage="Y"]` on a test page + the silo-archive pills (`docs/life-stage-layer.md`).
- Title-similarity gate ≥ 0.85 before import (`docs/internal-linking.md`).
- Featured-image human review (`docs/image-pipeline.md`) until wired.
- Freshness quarterly (`docs/freshness.md`).