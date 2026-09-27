# n8n Workflows

Canonical n8n workflows for the TailWell pipeline. One workflow per file, imported
in n8n (Workflow → Import from file).

| File | Trigger | Purpose |
|---|---|---|
| `tailwell-end-to-end.json` | Form (manual topic + life stage) | Research → relevance → per-section writing → CSV product parse → explicit merge → approved-product insertion → QA gate → SEO title/meta → WordPress draft (category + life_stage + Yoast meta in one call) → featured-image stage → Telegram alert |

Planned (see `docs/` phase plan):

- `tailwell-freshness-monitor.json` — scheduled review of article/product/source
  freshness, broken links, and stale prices (flags, never auto-rewrites).

## How to import

1. n8n → **Workflows → ⋯ → Import from JSON**, select the file.
2. Create the credentials named in the checklist and the runbook:
   - **OmniRoute (HTTP Header Auth)** — local LLM router key.
   - **WordPress** (built-in node) — site URL + automation user app password.
   - **WordPress Basic Auth** (generic HTTP Basic Auth) — raw REST calls.
   - **Telegram** — bot token.
   - **Gemini (HTTP Query Auth)** — *not needed for v6*: no Gemini node exists in
     this export (image stage is an intentional leaf — see `docs/image-pipeline.md`);
     add the credential only when Phase 10 is wired.
3. Set environment variables: `AUTOPILOT_ENABLED`, `WORDPRESS_URL`
   (and optionally `OPENROUTER_MODEL_FOR_SEO`).
4. Replace the literal model placeholders inside node bodies
   (`OLLAMA_LOCAL_MODEL`, `OPENROUTER_MODEL_FOR_WRITING`,
   `OPENROUTER_MODEL_FOR_QA`) and
   `YOUR_TELEGRAM_CHAT_ID`, set `https://YOUR_SHEET_PUBLISHED_CSV_URL` to your
   product sheet's published-to-web CSV, and keep the CSV columns in line with
   [`product-data/product-database.schema.md`](../product-data/product-database.schema.md)
   (validate with [`scripts/validate-products.ps1`](../scripts/validate-products.ps1)).

Full step-by-step: [`docs/tailwell-final-setup-checklist.md`](../docs/tailwell-final-setup-checklist.md).

## Design rules (non-negotiable)

- The pipeline only ever creates WordPress **drafts**. Publishing is a separate,
  human action.
- Products come only from `status = approved` rows of the product database.
- Category and life_stage resolution stop the run (with a Telegram alert) if a slug
  has no term.
- AI output is validated (JSON schema, QA gate) before anything is created.
- Every draft gets one silo category **and** one `life_stage` term (`docs/life-stage-layer.md`).