# Life Stage Layer (Puppy → Adult → Senior)

Added 2026-09-23. This is an **additive layer on top of the 5 silos** — it does
not touch the category structure, slugs, navigation, product database, or the
automation contract.

- Every published post gets **exactly one** of the 5 existing categories **plus one**
  `life_stage` term (`puppy`, `adult`, or `senior`).
- The 5 silos stay topic-based. Life stage is a separate, non-hierarchical
  taxonomy (Tag-style) layered on top.
- `senior-special-needs` is inherently life-stage-specific: posts there should use
  `life_stage = senior`, and the archive page does **not** render the filter pills.

## 1. Taxonomy contract (WordPress)

Registered in `wordpress/tailwell-theme/functions.php`:

- Name: `life_stage`, on `post`, **non-hierarchical** (`hierarchical => false`).
- REST-exposed with `show_in_rest => true` and `rest_base => 'life_stage'`, so the
  REST routes are `/wp-json/wp/v2/life_stage`, exactly like `/wp-json/wp/v2/tags`.
- Terms: `puppy`, `adult`, `senior` (labels Puppy / Adult / Senior).
- Terms are seeded automatically on theme switch (`tailwell_seed_life_stage_terms`)
  and can also be created via REST/WP-CLI (runbook §5.5).

To attach the term when creating a post via REST, send a numeric term-ID array —
same rule as categories:

```json
{
  "title": "…",
  "status": "draft",
  "categories": [123],
  "life_stage": [456]
}
```

## 2. Theme

### Shortcodes

`[tw_article_list]` and `[tw_related_articles]` (in `inc/shortcodes.php`) accept an
optional `life_stage` attribute that filters by term slug:

```text
[tw_article_list category="wellness-health" life_stage="puppy"]
[tw_related_articles count="3" heading="Puppy picks" life_stage="adult"]
```

`life_stage` is validated against the three term slugs and combined with the
category filter via a `tax_query` (AND). Both shortcodes render through
`tailwell_article_card`, so cards look identical to `[tw_latest_posts]`.
`[tw_related_articles]` also accepts `exclude="<post_id>"` (defaults to the current
post when inside the loop).

### Life-stage badge on articles

`single.php` renders the post's life-stage term as a small `.tw-badge` pill next to
the "Filed under" category, in the `.entry-meta` row (`tw-badge--life-stage`).

### Archive filter pills

On the 4 general silo archives the theme renders a row of pill links —
**All · Puppy · Adult · Senior** — via `generate_before_main_content` (priority 25,
below the silo hero). Pills are `.tw-badge` links; the active one gets `.is-active`.

- `All` links to the plain category archive.
- Each pill links to `?life_stage=<slug>` on that archive.
- `tailwell_register_life_stage_query_var` whitelists the query var; the
  `pre_get_posts` hook adds the `life_stage` tax_query to the main query (merged
  with the category filter — the category still applies).
- `senior-special-needs` archives never render the pills.

### Homepage

No change: `[tw_latest_posts]`/related-article queries are life-stage-agnostic, so
puppy/adult posts appear automatically once they exist.

## 3. n8n workflow (v6)

Workflow: `n8n/tailwell-end-to-end.json` (now 23 nodes).

- **Form Trigger** gained a `Life Stage` dropdown (`puppy` / `adult` / `senior`).
- **`Resolve Life Stage Term ID`** (HTTP GET, WordPress Basic Auth) resolves the
  term slug → numeric term ID against `/wp-json/wp/v2/life_stage?slug=…`, in
  parallel with the silo lookup. WordPress REST needs term IDs, not slugs — the
  same bug class as the original category fix.
- **Create Draft** is now an HTTP Request node (`POST …/wp-json/wp/v2/posts`) —
  the built-in WordPress node cannot express custom taxonomies. The body sends
  `categories`, `life_stage`, and the Yoast `meta` block in one call (mirrors
  runbook §11):

```json
{
  "title": "…keyword…",
  "content": "…final_html…",
  "status": "draft",
  "categories": [123],
  "life_stage": [456],
  "meta": {
    "_yoast_wpseo_title": "…",
    "_yoast_wpseo_metadesc": "…",
    "_yoast_wpseo_focuskw": "…"
  }
}
```

- Unchanged: QA rubric, product-matching, kill switch, SEO generation, evidence
  handoff, internal-linking block, featured-image stage.

## 4. Product database

Unchanged. `joint-supplement`, `fresh-food`, `cbd-wellness`, `insurance`,
`gear-tracker`, `gear-feeder`, `dental`, `odor-control` stay topic-based. A "puppy
version" and a "senior version" of the same product type both use the *same*
category (and different `[tw_product]` entries where they're genuinely different
products).

## 5. Verification

Run against the repo (no live site needed):

```powershell
node scripts/life-stage-workflow-check.js   # workflow v6 invariants (14 checks)
scripts/validate-life-stage.ps1             # theme structural checks (17 checks)
scripts/validate-workflow.ps1               # graph integrity (23 nodes)
```

Run against the live site:

```powershell
# extended REST contract (now also verifies life_stage terms + round-trip)
.\wordpress\setup\verify-tailwell.ps1 -BaseUrl "https://<domain>" -Username tw-automation -AppPassword "<pw>"

# 13-post coverage matrix (puppy/adult/senior x 4 general silos + senior x senior silo), -Cleanup to delete
.\wordpress\setup\verify-life-stage.ps1 -BaseUrl "https://<domain>" -Username tw-automation -AppPassword "<pw>" -Cleanup
```

Manual render check (production behavior the scripts can't reach): drop
`[tw_article_list category="wellness-health" life_stage="puppy"]` into a test page
and confirm only puppy-themed wellness posts render; click through the pills on a
silo archive and confirm the post list narrows and links are crawlable.

## 6. Content guidance (kept deliberately light)

Life stage is metadata, not a content-format change: writers still follow the
evidence rules (`docs/evidence-handoff.md`) and can never invent claims. Stage just
tunes emphasis — puppy (growth, house-training, first gear), adult (maintenance,
active gear), senior (mobility, joint, cognitive, comfort). Claims still need
evidence tags; vet-consultation framing for anything health-adjacent.