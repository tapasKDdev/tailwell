# Product Database — Canonical Schema

Canonical contract for TailWell's affiliate product tracking sheet. One source of
truth: `product-data/product-sheet-template.csv` in this repo, imported into Google
Sheets and shared with n8n as a **published-to-web CSV**.

No duplicate copy of this schema exists elsewhere. If you find one, archive it.

## File conventions

- UTF-8 **without BOM** (a BOM makes the first header cell read `\ufeffid` in parsers).
- Comma-separated; quote fields containing commas (`"..."`).
- The repository template contains **sample rows only** — every row is
  `status=pending`, `enabled=false`, `url=https://example.com/...`, `notes` = "SAMPLE".
  Nothing above may be treated as real product data.

## Columns

| Column | Required | Type / allowed values | Notes |
|---|---|---|---|
| `id` | yes | `TWP-###` | Unique product key; referenced by workflow alerts. |
| `name` | yes | text | Real product name. Never invented by AI. |
| `brand` | yes | text | Brand holding the affiliate program. |
| `category` | yes | one of the 8 use-case slugs below | **This is what the writer's `[tw_product_placeholder category="…"]` tag matches on.** |
| `silo` | yes | one of the 5 silo slugs below | Canonical topic area for QA/reporting; not load-bearing for matching. |
| `why` | yes | text (≤ 120 chars) | "Why we love it" endorsement copy, editorial-reviewed. |
| `price` | yes when `approved` | text like `$39.95` or `$39–45` | Must match the live listing; checked by the freshness workflow. |
| `price_updated` | yes when `approved` | `YYYY-MM-DD` | Freshness gate (Phase 12). Stale approved rows are flagged, never auto-priced. |
| `url` | yes when `approved` | `https://…` | Affiliate URL — real, from the partner program. **Never invented; never `example.com` on an approved row.** |
| `affiliate_partner` | yes | Amazon, Chewy, manufacturer, … | Which program the link runs through. |
| `vet_verified` | yes | `true` / `false` | Only `true` rows may appear in health-adjacent copy. Means the row passed TailWell's internal safety gate (label/claim check) — **not** veterinary examination or endorsement. The card badge reads "Safety-reviewed"; see `docs/product-claims-policy.md`. |
| `source_id` | no | evidence key (Phase 6) | Links the row to the research evidence object backing `why`. |
| `status` | yes | `pending` / `approved` / `rejected` / `archived` | Pipeline matches **only** `approved`. |
| `enabled` | yes | `true` / `false` | Soft kill per row; pipeline matches only `enabled=true` **and** `approved`. |
| `image_url` | no | `https://…` | Optional product image for cards. |
| `notes` | no | text | Free text (sample flags, follow-ups). |

### Use-case slugs (`category`)

`joint-supplement`, `fresh-food`, `cbd-wellness`, `insurance`, `gear-tracker`,
`gear-feeder`, `dental`, `odor-control`

### Silo slugs (`silo`)

`wellness-health`, `food-nutrition`, `gear-tech`, `training-behavior`,
`senior-special-needs`

## Validation

Run [`scripts/validate-products.ps1`](../scripts/validate-products.ps1) against the
template and against the sheet's CSV export before every pipeline test. It rejects:

- unknown/missing columns or `category`/`silo`/`status` values,
- any `approved` row with `enabled != true`, empty `price`, unparseable or stale
  `price_updated`, a non-`https` `url`, or `example.com` in the URL,
- duplicate `id`s, and non-boolean `enabled`/`vet_verified`.

## Pipeline integration (n8n)

1. `Fetch Product Database (Google Sheet CSV)` pulls the published-to-web CSV **text**.
2. `Parse Product CSV` (Code node) strips a possible BOM, parses quoted CSV into rows,
   and keeps only `status=approved` + `enabled=true`.
3. `Merge - Products + Sections` combines the parsed products with the written
   article sections (one merged item).
4. `Insert Real Affiliate Products` swaps each `[tw_product_placeholder category="X"]`
   for a `[tw_product name/why/price/url/brand/image/category/partner/vet]` shortcode
   from the first approved row in `X`;
   unmatched placeholders become a visible HTML comment and force a QA **FAIL**.

Rules that are never relaxed: products come only from `approved` rows; `url` is never
generated; a placeholder with no approved product is a failure, not a guess.