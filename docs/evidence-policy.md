# Evidence Policy

What counts as evidence at TailWell, and what never does. This is the **policy** layer; the mechanics live in [`research/README.md`](../research/README.md) (workflow + tiers), [`research/evidence-schema.md`](../research/evidence-schema.md) (object shape), and [`docs/evidence-handoff.md`](evidence-handoff.md) (feeding evidence into the prompts). Where those files describe *how*, this file decides *what is admissible*.

**Last reviewed:** 2026-09-25.

## The single rule

> **A claim may appear on TailWell only if a real, retrievable source supports it — and AI is never that source.**

## Admissibility tiers

| Tier | Counts as evidence? | Use |
|---|---|---|
| **s1** — peer-reviewed study, regulatory document (FTC, FDA, VMD, ICO, gov.uk, legislation) | Yes | Any claim type |
| **s2** — veterinary-university resource, national veterinary organization | Yes | Health/safety/nutrition claims (minimum tier for these) |
| **s3** — manufacturer labeling, reputable commercial documentation | Yes, **narrowly** | Product specifications only (ingredients, sizes, price, warranty). **Never** "does it work" claims |
| Manufacturer marketing copy, PR, "as seen on" banners | **No** | Lead-gen for sources at best |
| Blogs, forums, social posts, Reddit, Wikipedia | **No** | Wikipedia may be used as *background* to locate primary sources — it is never cited, never tagged |
| AI/LLM output (any model, including ours) | **No, categorically** | AI is a drafting and organizing tool. An LLM's confident sentence is not a source, is not "published somewhere," and cannot back an `[ev:…]` tag |
| Our own previous articles | **No** | Circular citation; go back to the primary source |

## Rules for recording evidence

1. **Retrievability.** A URL that cannot be pulled and read is not recorded (`researcher.md` rule). `retrieved_at` is mandatory — it drives freshness review.
2. **No upgrades.** "Associated with" stays "associated with." "May" does not become "does." Predicates are transcribed, not improved.
3. **Conflicts are recorded, not resolved silently.** If two good sources disagree, both directions are logged (`supported: true/false`) and the article says the evidence is mixed.
4. **Nothing invented.** No statistics, sample sizes, dates, author credentials, or study names that the source page does not contain.
5. **Drop, don't soften.** If no admissible source exists for a planned claim, the claim is deleted from the plan — not rewritten into a vaguer sentence that still implies it.
6. **Freshness.** Evidence older than the topic demands gets re-pulled at refresh time; if the source has changed or vanished, the claim is re-verified or dropped (`docs/freshness.md`).

## How it is enforced

| Stage | Control |
|---|---|
| Planning | `automation/prompts/planner.md` lists load-bearing claims *before* writing; claims without evidence get dropped or flagged |
| Research | `automation/prompts/researcher.md` tier rules + schema (`research/evidence-schema.md`) |
| Writing | `automation/prompts/writer.md`: every health/safety/nutrition/statistical sentence carries `[ev:TWP-EVID-####]` from the supplied list; no tag = no claim |
| QA | `automation/prompts/qa.md`: untagged health claim = FAIL; tier < s2 for health = FAIL; "curves/cures/guaranteed/impossible specificity" = FAIL even with a tag |
| Product data | `scripts/validate-evidence.ps1` resolves every referenced id to a real file; `vet_verified` gate (`product-database.schema.md`) |
| Wiring | `docs/evidence-handoff.md` snippet (empty evidence ⇒ QA must fail — correct behavior) |

## Citation display

Where an article shows its sources, they link to the real source URL from the evidence object — never to a competitor's summary of it, and never to a source that only exists in a model's memory. Answer-engine/GEO practices (`docs/geo-aeo.md`) must trace to this same pool; a citable-sounding sentence with no admissible evidence is removed, not "made citable."

## Human accountability

Evidence responsibility cannot be delegated to a model or an agent. The researcher role records, the writer may only use what was recorded, QA checks the match — and a human approves publication. See `docs/ai-content-policy.md`.
