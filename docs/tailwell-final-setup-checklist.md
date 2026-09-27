# TailWell Final Workflow (v6) — Setup Checklist

File: `n8n/tailwell-end-to-end.json`

Complete pipeline: **Kill switch → Wikipedia background → relevance check → section planning → per-section writing (evidence-tagged) → CSV product parse → explicit merge → real affiliate product insertion (+ internal links) → QA gate (evidence + product + framing) → SEO title/meta → WordPress draft (category + life stage + Yoast meta in one call) → featured-image stage → Telegram alert.**

## What's new/fixed (v6 layer)

0. **Life Stage layer (puppy/adult/senior).** The Form Trigger now collects a
   `Life Stage` dropdown; a new `Resolve Life Stage Term ID` node resolves
   `?slug=` → numeric term ID against `/wp-json/wp/v2/life_stage` (same
   slug→ID rule as categories); and Create Draft sends both `categories:[id]`
   and `life_stage:[id]`. Create Draft is now an **HTTP Request** node because
   the built-in WordPress node can't express custom taxonomies — the body
   mirrors runbook §11 (`title/content/status/categories/life_stage/meta`).
   The theme (taxonomy + pills + shortcode filters + badges) is Doc #:
   `docs/life-stage-layer.md`. Checkpoints: `node scripts/life-stage-workflow-check.js`,
   `scripts/validate-life-stage.ps1`, `wordpress/setup/verify-life-stage.ps1`
   (13-post matrix), extended `wordpress/setup/verify-tailwell.ps1`.

## What's new/fixed (v5 layer)

0. **Internal linking + duplicate detection (Phase 9).** `Insert Real Affiliate Products`
   now appends the silo-hub + home internal-link block to every article and reports
   `internal_links`; QA rule 6 fails drafts missing it. Run the title-similarity gate
   (≥ 0.85) before import — see `docs/internal-linking.md`.

## What's new/fixed (v4 layer)

0. **Prompt system V2 is now embedded.** The planner/writer/QA system prompts
   mirror `automation/prompts/*.md`: evidence tags (`[ev:TWP-EVID-####]`) are
   required inline for every health/safety/nutrition/statistical sentence, health
   claims need tier ≥ s2 evidence, and the writer only ever emits placeholder tags
   for products. Keep the workflow's inline prompts in sync with `automation/prompts/`.
   The writer/QA prompts require evidence but the workflow doesn't fetch it — feed
   the researcher stage output via **`docs/evidence-handoff.md`** (verified snippet,
   `scripts/evidence-context-test.js`); without it, QA correctly fails any health claim.

## What's new/fixed in v3

1. **The product sheet is parsed, not relied on as an array.** The published-to-web Google Sheet CSV comes back from `Fetch Product Database (Google Sheet CSV)` as raw *text*. The new `Parse Product CSV` Code node strips a possible UTF-8 BOM, parses quoted CSV rows, and keeps **only rows with `status=approved` AND `enabled=true`**. Products come only from approved rows, and never invented.
2. **Explicit branch merge.** `Merge - Products + Sections` (combine/multiplex) joins the parsed product rows with the written sections so `Insert Real Affiliate Products` reads one item carrying both `section_html` and `products` — no implicit cross-branch references.
3. **Affiliate products are matched from the real database, never guessed.** Writers insert `[tw_product_placeholder category="…"]`; the insert node swaps in a real `[tw_product]` shortcode (name/why/price/url from the DB). No approved product for that category → a visible HTML comment flag is left and QA auto-fails on any leftover placeholder.
4. **Kill switch gate runs first.** If `AUTOPILOT_ENABLED` isn't `"true"`, nothing runs — you get a Telegram alert instead. Flip to `"false"` to pause everything without touching the workflow.
5. **Silo slug → numeric category ID.** `Resolve Silo Category ID` (HTTP GET `…/wp-json/wp/v2/categories?slug=…`) runs in parallel with research; the draft node sends `categories: [id]`.
6. **SEO title + meta written as Yoast keys.** `Generate SEO Title + Meta Description` runs only after QA passes and produces `seo_title` (50–60 chars) / `meta_description` (140–155 chars). The WordPress create call sends `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw` in the same request via `metaJson`.
7. **Known open item — featured image.** `WordPress - Upload + Attach Image` is intentionally a leaf for now (Phase 10): the Gemini image-generation step is **not yet wired in** this repo (see `docs/image-pipeline.md` for the full wiring plan and safe-image prompt rules). Drafts are created without an image and the Telegram alert says so. **Do not** treat a draft with no featured image as production-ready.

## What you must set up before running it

### 1. Credentials (n8n → Credentials)
- **OmniRoute (HTTP Header Auth)** — API key used by the 4 HTTP Request nodes calling `localhost:20128` (planner, writer, QA, SEO).
- **WordPress (n8n built-in WordPress node)** — site URL + your dedicated automation user's Application Password. Used by `WordPress - Upload + Attach Image` (postId comes from the HTTP create call).
- **WordPress Basic Auth (generic HTTP Basic Auth)** — separate credential for the raw REST calls: `Resolve Silo Category ID`, `Resolve Life Stage Term ID`, and **`WordPress - Create Draft (category + life_stage + Yoast meta)`** (now an HTTP Request node). If you're on an older workflow export where Create Draft was the built-in node, re-import v6.
- **Telegram** — bot token. Get `YOUR_TELEGRAM_CHAT_ID` by messaging your bot once and checking `getUpdates`, then replace every `YOUR_TELEGRAM_CHAT_ID` in the 4 Telegram nodes.
- **Gemini (HTTP Query Auth)** — API key for image generation — **not needed for v6**: no Gemini node exists in this export (the `WordPress - Upload + Attach Image` node is a bare upload leaf on purpose). Add this credential only when Phase 10 is wired per `docs/image-pipeline.md`; until then humans attach images manually.

### 2. Environment variables (n8n → Settings → Environment)
The workflow references these as `$env.AUTOPILOT_ENABLED` and `$env.WORDPRESS_URL`:
- `AUTOPILOT_ENABLED` = `"true"` or `"false"` — your kill switch
- `WORDPRESS_URL` = your site's base URL, e.g. `https://tailwell.com`

### 3. Model names — literal placeholders to replace inside the JSON before import
These are plain strings in the node bodies (not `$env`), so replace them:
- `OLLAMA_LOCAL_MODEL` → your installed Ollama model (Relevance Check + Section Planner)
- `OPENROUTER_MODEL_FOR_WRITING` → your OpenRouter writing model (Write Section **and** Generate SEO)
- `OPENROUTER_MODEL_FOR_QA` → your OpenRouter fact-check model (QA / Fact Check)

There is no `GEMINI_IMAGE_MODEL` placeholder in the v6 JSON — the image stage is unwired by design (see `docs/image-pipeline.md`); ignore that name until Phase 10 lands.

Optional: if you want a dedicated SEO model, add an environment variable `OPENROUTER_MODEL_FOR_SEO` and change the `model` line in `Generate SEO Title + Meta Description` to `={{ $env.OPENROUTER_MODEL_FOR_SEO }}`. Otherwise it reuses the writing model.

### 4. Product database (the affiliate-safety piece — don't skip this)
The canonical contract is `product-data/product-database.schema.md`; the import-ready starter is `product-data/product-sheet-template.csv` (validate with `scripts/validate-products.ps1`). Build a Google Sheet with those columns; the load-bearing ones are `category | status | enabled | name | why | price | url`.

- `category` must exactly match a value from the writer prompt: `joint-supplement`, `fresh-food`, `cbd-wellness`, `insurance`, `gear-tracker`, `gear-feeder`, `dental`, `odor-control`
- Matching requires `status=approved` **and** `enabled=true`
- CSV must be UTF-8 **without BOM** (the parser strips one if present, but validation rejects it)
- File → Share → Publish to web → CSV, then paste that URL into `Fetch Product Database (Google Sheet CSV)` (replace `YOUR_SHEET_PUBLISHED_CSV_URL`)
- No approved rows yet → categories show the "no approved product" comment and QA correctly fails those articles. That is the intended guard, not a bug.

### 5. Yoast options (WordPress plugin)
Yoast fields are sent in the create body's `meta` block on the HTTP Create Draft
node (runbook §11 body). If your installed WordPress node version doesn't expose a raw 'meta' field — no longer relevant on v6, which uses an HTTP Request node for the draft. The Yoast write itself is verified by `setup/verify-tailwell.ps1` step 4.

### 5.5 Life stage terms (WordPress, runbook §5.5)
The `life_stage` taxonomy is registered by the TailWell child theme. Its three
terms (`puppy`, `adult`, `senior`) are seeded on theme switch and must exist
before the workflow runs — `verify-tailwell.ps1` now asserts all three via REST.
If the taxonomy isn't exposed, activate the theme (it registers `life_stage` on
`init`) and re-run verification.

### 6. Sanity checks before importing
- No duplicate node names; **all 23 node references resolve** (the leaf nodes are intentional: the 3 Telegram alerts, `Resolve Silo Category ID`, `Resolve Life Stage Term ID` and `Fetch Product Database (Google Sheet CSV)` are consumed by expression/edge from downstream nodes).
- `Resolve Silo Category ID` expects the categories GET to return an array; if the slug is wrong this returns `[]` and the draft would be created without a category — verify one slug by hand with your Basic Auth credential first.
- `Resolve Life Stage Term ID` has the same shape: `GET /wp-json/wp/v2/life_stage?slug=puppy` must return the term; an empty array means the term doesn't exist yet (seed it) and the draft would fail on `life_stage:[undefined]`. Run `wordpress/setup/verify-tailwell.ps1` first — it asserts all three terms.
- `Merge Sections` must produce `section_html` as an array; with the `combine/multiplex` merge, `Insert Real Affiliate Products` reads `section_html` + `products` off the single merged item.

## Test run order

1. Set `AUTOPILOT_ENABLED=false` first, manually trigger with a known-good topic (e.g. "Glucosamine" / "best joint supplements for large senior dogs sensitive stomach"), confirm the kill switch correctly blocks it.
2. Import the template product sheet (all rows `pending`), set `AUTOPILOT_ENABLED=true`, run again: the run must reach QA and **fail** with the "no approved product" comment — proving the parse + guard work before any real product rows exist.
3. Approve at least one real product row (validate with `scripts/validate-products.ps1` first), re-run, and confirm the run reaches a WordPress draft with a real `[tw_product]` shortcode and no leftover placeholders.
4. Check the draft manually for: correct sections, no leftover `[tw_product_placeholder]` tags, correct silo category **and** life-stage term, Yoast fields populated, internal-link block (`tw-internal-links`) present. (Featured image is the known open item from Step 7 above.)
5. Only after several clean runs — consider the human-review-then-autonomous-publish transition from the build plan.

## Still deliberately not automated (per the build plan)

- Keyword Discovery/Scoring — you're still hand-picking validated keywords
- Approving new affiliate products into the database — that stays a manual, verified step by design
- Publishing — the pipeline only ever creates **drafts**
- Featured-image generation — Gemini stage not wired (see `docs/image-pipeline.md`); humans add images until then