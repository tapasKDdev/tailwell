# Security & Hardening

Concerns, and the rules that keep them true.

## Secrets & credentials

- **Never commit secrets.** No API keys, app passwords, bot tokens, or admin URLs in
  this repo. Credentials live in n8n credential stores and environment variables
  (workflow uses `$env.AUTOPILOT_ENABLED`, `$env.WORDPRESS_URL`, n8n credential
  objects). The repo only stores placeholders (`YOUR_TELEGRAM_CHAT_ID`,
  `YOUR_SHEET_PUBLISHED_CSV_URL`, `OLLAMA_LOCAL_MODEL`, `OPENROUTER_MODEL_FOR_WRITING`,
  `OPENROUTER_MODEL_FOR_QA`, `GEMINI_IMAGE_MODEL`) — documented in the checklist.
- Before any `git push`-like action: grep for `key|secret|token|password` in the
  staged files; if a fresh secret shows up, revoke + fix, never commit.
- Rotate on personnel change: WordPress app passwords, n8n credentials, LLM keys,
  Telegram bot.

## WordPress

- **Automation user**: a dedicated user (not admin) with only `edit_posts`,
  `upload_files`, `edit_posts`-family capabilities needed for drafts. Application
  Password auth against the REST API; store in n8n — never in the repo.
- REST API basics: keep default discovery; Yoast exposes needed meta; check with
  `verify-tailwell.ps1` before any automation run.
- The theme adds no admin users, no eval, no file writes, no external scripts besides
  optional Google Fonts; it escapes the output at every render (`esc_html`, `esc_url`,
  `esc_attr`) and JSON-encodes schema via `wp_json_encode`.

## n8n

- Secrets in GET/POST URLs (query auth) are the risky spot — `GEMINI_IMAGE_MODEL`
  sits inside the API URL; keep the API **key** in credentials, not the URL.
- `Kill Switch` is the blast radius: `AUTOPILOT_ENABLED=false` = nothing posts. Keep
  it false except during deliberate runs.
- Never echo product-database rows containing partner tokens into alerts; the Telegram
  alert only carries keyword + pass/fail summary.

## Privacy & PII

- Theme tracks nothing (no cookies, no analytics). See
  `docs/affiliate-and-privacy.md` for the third-party list and the disclosure/privacy
  page copy. No user-PII is put into affiliate URLs.

## Content safety (the human-facing risk)

- No invented data. The evidence tier system (`research/`) is the guardrail:
  Wikipedia only background, health claims need s1/s2, product specs only s3.
- Health-adjacent copy always includes "talk to your veterinarian" framing; nothing on
  the site is a diagnosis or treatment directive.
- QA auto-fails any unresolved product placeholder and any unsourced health claim —
  the automation's job is to surface problems, not guess.

## Backup & DR

- Host snapshot + UpdraftPlus to off-host cloud storage from day one.
- Repository is the source of truth for code/content/automation (`README.md`).
- Rollback = restore snapshot OR re-export previous theme zip (`runbook.md` §4);
  bad runs are drafts, so revert = delete the draft (`docs/rollout.md`).

## Monitoring

- Draft alerts (Telegram) are the early-warning system: a run reaching QA-fail or a
  draft with no image is called out in the alert text.
- Run `scripts/check-links.ps1` + validators on a schedule (Phase 12 freshness).