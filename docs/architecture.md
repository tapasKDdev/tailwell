# Architecture

TailWell = WordPress + n8n pipeline + single source-of-truth repo.

## One-URL model

- Exactly five silo **categories** (`wellness-health`, `food-nutrition`, `gear-tech`,
  `training-behavior`, `senior-special-needs`); their archives are the hub pages and
  render the `tw-silo-hero` banner.
- Every post is **exactly one category + one `life_stage` term** (`puppy` / `adult` /
  `senior`), a non-hierarchical taxonomy layered on top — additive only, no new
  categories, no nav changes (`docs/life-stage-layer.md`).
- Articles are posts assigned to exactly one category. No competing static-page URLs
  (legacy `/wellness-health/` pages were archived). One permalink per topic, forever.
- Product database → `[tw_product]` shortcode → affiliate link, never raw links in copy.

## Components

| Component | Role | Lives at |
|---|---|---|
| WordPress + GeneratePress child theme | Render site; breadcrumbs, Product JSON-LD, silo banner, life-stage badges + archive pills, shortcodes (`[tw_article_list]`/`[tw_related_articles]` filter by `life_stage`), accessible nav | `wordpress/tailwell-theme/` |
| Yoast | Titles/meta/sitemap/canonicals/WebSite schema | site runtime (plugin) |
| n8n workflow v6 | Research → write → products → QA → SEO → draft (category + life_stage + Yoast in one call) | `n8n/tailwell-end-to-end.json` |
| Prompt System V2 | Canonical role prompts the n8n nodes embed | `automation/prompts/` |
| Evidence system | Claim↔source registry, tiers s1–s3 | `research/` |
| Product database | Approved/products plus validator | `product-data/` |
| Docs + scripts | Contracts + test harness | `docs/`, `scripts/` |

## Main flow (workflow v6)

```
Form (keyword, silo, life stage)
 → Kill switch (AUTOPILOT_ENABLED)            [false ⇒ block + alert]
 → Wikipedia background (context only, never evidence)
 → Resolve Silo Category ID  +  Resolve Life Stage Term ID   (slug→numeric IDs)
 → Relevance check + Section Planner  (JSON: relevant, sections[])
 → IF relevant  → FETCH product CSV  |  Split sections
                → Parse Product CSV (BOM strip, quoted, approved+enabled only)
 → Merge - Products + Sections (single item)
 → Insert Real Affiliate Products (placeholders→shortcodes; appends internal links;
                                   unmatched ⇒ visible comment + has_unmatched)
 → QA / Fact Check (evidence rubric + product guard + internal-link rule 6)
 → IF passed → Generate SEO title/meta → WordPress draft (categories:[id] +
               life_stage:[id] + Yoast meta in one HTTP call)
             → Upload + Attach Image (open item, Phase 10 — leaf) → Telegram alert
   else      → Telegram QA-failure alert
```

Key invariants baked into the flows: the pipeline **only drafts**; products only from
approved+enabled rows; unresolved placeholders are a FAIL not a guess; health claims
carry `[ev:…]` tags with tier ≥ s2; nothing auto-publishes or auto-images; every draft
gets exactly one category and one life-stage term.

## Sync rules

- `automation/prompts/` is canonical; editing a prompt means updating the matching
  inline node prompt in the workflow **in the same commit**.
- `product-data/product-database.schema.md` + template are canonical; the validator
  guards the sheet.
- Any second copy of a canonical file gets moved to `archive/`, logged in CHANGELOG.

## See also

`docs/testing.md` (matrix), `docs/seo.md`, `docs/geo-aeo.md`, `docs/internal-linking.md`,
`docs/freshness.md`, `docs/affiliate-and-privacy.md`, `docs/image-pipeline.md`,
`docs/life-stage-layer.md`, `docs/security.md`, `docs/rollout.md`.