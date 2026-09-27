# Research & Evidence System

Canonical methodology for how TailWell sources, records, and re-uses factual claims
in published content. Everything here feeds the QA gate in the n8n pipeline and the
`source_id` column of the product database.

## Hard rules

1. **Never invent a source.** No fabricated studies, statistics, vet labels,
   publisher names, URLs, or publication dates — anywhere, including research notes.
2. **Wikipedia is background only.** Wikipedia may not be cited as evidence for any
   medical, safety, legal, statistical, or product claim in production copy.
3. **Preferred primary/secondary sources** (in order): peer-reviewed veterinary and
   nutrition research; official bodies (AVMA, AAHA, FDA-CVM, EPA, AAFCO, Pet Food
   Institute); veterinary-university resources; and — for product-specific facts only —
   the manufacturer's labeling/claims page. Blogs and forums are never evidence.
4. **Every health-adjacent statement is claim=tagged.** Any sentence making a
   health/safety/nutrition/statistical claim carries an evidence reference
   (`[ev:SOURCE_ID]`), and the QA gate must be able to map the claim back to the
   evidence object.
5. **Freshness.** Each evidence object records `retrieved_at`. The freshness monitor
   (Phase 12) re-flags items older than the study's own provenance rules (see the
   schema). Stale evidence degrades claims, never silently.
6. **Product specs vs medical claims.** Manufacturer pages may support a product
   spec (ingredients, size, features) but never a medical benefit claim ("supports
   joints" still needs a study).

## Files

| File | Purpose |
|---|---|
| `evidence-schema.md` | The evidence object contract (JSON Schema). |
| `claim-mapping.md` | The `[ev:ID]` tag syntax and how planner/writer/QA consume it. |
| `examples/evidence-0001.json` | An **illustrative** evidence object — placeholder, not real. |

## Research workflow

1. **Define the claim set first.** From the keyword + section plan, the researcher
   lists the factual/health/statistical claims the article might make (not every
   sentence — the load-bearing ones).
2. **Search by claim.** For each, pull the 2–3 most authoritative sources from the
   tier list above. Capture the exact URL, title, publisher, and date at retrieval.
3. **Extract defensible predicates.** Record only what the source actually says —
   paraphrased tightly, no extrapolation ("may be associated with" must not become
   "cures").
4. **Write the evidence object** (`examples/evidence-0001.json` shape) and give it a
   `TWP-EVID-####` id.
5. **Link it to consumers:** the writer's section prompt references
   `[ev:SOURCE_ID]`; the product database row `source_id` points at the same id; QA
   reruns the mapping as a checklist item.
6. **Never copy text.** Evidence informs paraphrase; quote-with-citation only where
   genuinely necessary and short.
7. **Hand it to the writer + QA.** The workflow's writer and QA prompts require
   `[ev:...]` tags but do not fetch evidence themselves — paste the researcher's
   output through the handoff described in
   [`docs/evidence-handoff.md`](../docs/evidence-handoff.md) (contains the verified
   context-block snippet + `scripts/evidence-context-test.js` fixture).

Quality tiers (QA rubric): `s1` peer-reviewed/regulatory, `s2` veterinary-university
or national veterinary org, `s3` reputable commercial/manufacturer (product facts
only). Below `s3` is not citable evidence.