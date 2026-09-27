# Claim → Evidence Mapping

How a claim in a TailWell article is tied to the evidence object that supports it.

## Syntax

Inline in section HTML, immediately after the sentence that needs support:

```html
<p>Omega-3 supplementation from fish oil has been associated with improved
mobility scores in one veterinary trial [ev:TWP-EVID-0042].</p>
```

- The tag is `[ev:SOURCE_ID]`, where `SOURCE_ID` is `TWP-EVID-####`.
- One tag per load-bearing claim (health/safety/nutrition/statistical).
- Product-spec sentences may cite the product's evidence object (`source_id` from the
  product sheet), but **medical-benefit claims never cite a manufacturer `s3` source**.
- Background/definitional copy needs no tag.

## Who uses it

| Stage | Role |
|---|---|
| Researcher | Produces evidence objects; hands the claim→source_id mapping to the writer. |
| Writer | Places `[ev:…]` tags; never adds a claim that has no evidence object. |
| QA gate | Rule: every health/safety/nutrition/statistical sentence must carry `[ev:…]` referencing an evidence object of tier ≥ s2 (s3 only for product facts). Any claim with no tag, a fabricated source_id, or a Wikipedia-sourced claim → **FAIL**. Unmatched `[ev:…]` ids → **FAIL**. |
| Editor | Reviews tags as part of the human gate before publishing. |

## QA checklist items (mirrors the n8n QA prompt)

1. Extract all `[ev:…]` ids, resolve each to an evidence object in `research/`.
2. Is the tier appropriate for the claim type? (health ⇒ ≥ s2; product specs ⇒ s3 ok)
3. Does each tagged sentence faithfully reflect the evidence object's predicate?
   (No upgrading: "associated" must not become "cures".)
4. Any claim without a tag? Any tag invented for the occasion? → FAIL.

## Anti-patterns (auto-fail)

- `[ev:]` with an empty or made-up id
- A medical claim citing only a manufacturer page or Wikipedia
- Two different claims sharing one tag where only one is supported
- Stale evidence (a `retrieved_at` older than the freshness window) carrying a
  time-sensitive claim like pricing or "latest study"