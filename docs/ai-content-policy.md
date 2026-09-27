# AI Content Policy

How AI may be used at TailWell — and the list of things it may never do. The public-facing commitment is the "no fake voices / AI is not a source" paragraph in `wordpress/content/editorial-policy.md`; this file is the internal rulebook for `automation/prompts/*` and any future agent work.

**Last reviewed:** 2026-09-25.

## Position

AI is a **drafting and organizing assistant**. A human owns every published word. Models used: `OPENROUTER_MODEL_FOR_WRITING` / `OPENROUTER_MODEL_FOR_QA` (OpenRouter), a local ollama model for planning — see `automation/prompts/README.md`.

## The five rules

1. **AI is never evidence.** Model output cannot back a fact, a statistic, a quote, a study, a price, or a product spec. Only admissible sources from `evidence-policy.md` can. If the model "knows" a study, it does not exist until a human retrieves it.
2. **AI never speaks as a person.** No invented authors, vets, experts, reviewers, or testimonial writers; no bylines implying a human wrote text they never saw *and* no bylines hiding that a human approved it — the editorial team is accountable either way. No fabricated quotes of any real person or organization.
3. **AI never publishes.** The pipeline produces drafts; a human reviews, and only a human moves content to published (rollout stage rules in `docs/rollout.md`).
4. **PII boundary.** Prompts receive editorial topics, drafts, and evidence objects — **never visitor personal data** (see `docs/privacy-data-inventory.md` §B; public promise in `privacy-policy.md` §4).
5. **Claims stay inside the fences.** AI output is passed through `product-claims-policy.md` and the QA rubric unchanged; an AI's opinion that a claim is "fine" changes nothing.

## Role-by-role guardrails (`automation/prompts/`)

| Role | Binding limits |
|---|---|
| `planner.md` | Blocks unsafe/unclear topics (`relevant:false`); section prompts "instruct what to cover, never what to invent"; Wikipedia = background only, never cited |
| `researcher.md` | Tier rules; no invented stats/credentials; unverifiable URL ⇒ not recorded; unsourceable claim ⇒ dropped |
| `writer.md` | Evidence tags mandatory for health/safety/nutrition/statistical sentences; product placeholders only (never real names/URLs/prices); no recycling; vet-consult framing |
| `qa.md` | Pass/fail rubric: untagged health claim, invented spec, leftover placeholder, duplicated content, missing vet framing ⇒ FAIL; affiliate rel check |
| `seo.md` | Accurate titles; no unsupported "best/top" superlatives (superlatives must be earned by the body per `review-methodology.md`) |

Prompt edits are **change-controlled**: after any edit, run `node scripts/evidence-context-test.js`, `scripts/product-pipeline-test.js`, and `scripts/life-stage-workflow-check.js`, then CHANGELOG the edit. (These prompts are Markdown, not n8n nodes — no workflow graph changes are implied by editing them.)

## Images

`docs/image-pipeline.md` governs generation. Additional hard rules: no synthetic "photo" presented as documentary evidence of a real event; no generated lab/vet/clinic imagery implying a real facility examined a product; alt text describes what is actually shown.

## Prohibited uses (exhaustive enough to enforce)

- Asking a model for facts to insert without retrieval ("what does the research say?" → stop, go retrieve).
- Generating reviews, ratings, testimonials, Q&A "customer" comments, or expert quotes.
- Bypassing or hand-waving a QA FAIL ("it's probably fine — pass").
- Letting a model edit `product-claims-policy.md` / `evidence-policy.md` red lines without the same human review as any other policy change.
- Presenting model-summarized law as advice (see `legal-compliance.md` header: not legal advice, counsel review required).

## Disclosure posture

We do not label individual articles "AI-written." We do commit, publicly, that no content is invented and no source is fabricated (`editorial-policy.md`) — which is the promise that matters. If guidance in a target market changes to require labeling, the editorial policy and this file update together.

## When AI assistance grows (future agents, autonomy stages)

Rollout stages (`docs/rollout.md`) may increase autonomy only within these rules: evidence requirements, red-flag list, and human-publish gate **do not relax at any stage**. A stage may never authorize a model to publish unreviewed health claims or to be cited as a source.
