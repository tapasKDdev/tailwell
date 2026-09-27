# Rollout

Controlled path from empty WP install to a recurring, human-reviewed publishing
loop. Everything here is the "how", the "what" lives in
`docs/tailwell-final-setup-checklist.md` (credential + sanity list) and
`wordpress/setup/runbook.md` (host setup).

## Stage 0 — Build (done in this repo)

- Restructure + theme hardening + accessible nav + SEO/schema layer,
  prompts, evidence, product DB tooling, validators, and test harness are all in the
  repo and green (`docs/testing.md`).

## Stage 1 — Prove the guardrails (before any real content)

1. Import workflow **with** `AUTOPILOT_ENABLED=false`.
2. Run with the sample topic: kill switch must block + alert. (FAIL = stop here.)
3. Import the **template-only** product sheet (every row `pending`); set
   `AUTOPILOT_ENABLED=true`, run: the run must reach QA and **FAIL** on the
   no-approved-product comment. This is the pipeline's crown-jewel test — prove it
   before trusting any output.
4. Approve one real product row (run `scripts/validate-products.ps1` on the export)
   and re-run: expect a WordPress **draft** with real `[tw_product]` shortcode, no
   leftover placeholders, correct silo category, Yoast meta populated, internal-link
   block present.
5. Manually review that draft against the QA rubric (`automation/prompts/qa.md`).
   Featured image is a known open item (`docs/image-pipeline.md`) — add it manually.

## Stage 2 — Graduated autonomy

- Run weekly, all runs create drafts; a human publishes every post after review.
- Gate rules during this stage: never `enabled=true` on the sheet without a review;
  never let the writer's emergency "no approved product" comment go to production.

## Stage 3 — Post-publish hygiene (continuous)

- Freshness quarterly (`docs/freshness.md`): prices, evidence `retrieved_at`, links.
- Link + data checks on schedule (`scripts/check-links.ps1`, validators).
- Title-similarity gate before each import (`docs/internal-linking.md`, ≥ 0.85).

## Rollback

- Bad draft → delete it in WP (nothing was published).
- Bad theme/plugin → restore the snapshot or re-upload the last theme zip
  (`runbook.md` §4).
- Bad automation config → flip `AUTOPILOT_ENABLED=false` (instant kill switch), then
  revert the workflow JSON from git.

## Definition of done (a stage only advances when…)

- Stage 1: both guardrail tests pass AND one real draft reviewed by a human.
- Stage 2: N clean weekly runs (N ≥ 2) with zero manual fixes to product markup.
- Stage 3: checks green once per quarter, and freshest-publish evidence is linked.