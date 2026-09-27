# Content Freshness Workflow (Phase 12)

SEO-safe renewal of published articles under the one-URL model. Refresh in place —
**never** create a competing URL for the same topic.

## When a post needs refresh

- `price_updated` in the product database is > 90 days old for any product embedded
  in the post (industry prices move; declined "we haven't updated" is worse).
- Any `[tw_product]` URL 404s or redirects off-brand.
- An evidence source's `retrieved_at` is stale for a stat the article still cites.
- Breakage detected by `scripts/check-links.ps1`.
- A silo hub shows the post is no longer best-answer for its keyword (a newer, better
  post exists) → decide between updating the old post or retiring it (same silo,
  redirect removed).

## Refresh procedure (reuse the pipeline)

1. Feed the same keyword into the n8n form as a new run but mark it "refresh" — or
   simpler: re-run the research + writer on the existing title.
2. **Never auto-publish a refresh.** The pipeline's output is a new draft; the human
   replaces the old post's content with the new draft, keeping:
   - the same permalink (unchanged slug),
   - the same category,
   - the published date (add "Last updated: {date}" in the body near the top instead).
3. Update the product database rows (prices, `price_updated`, maybe rotate to newer
   products in the same category) and re-validate with `scripts/validate-products.ps1`.
4. Re-run the QA gate mentally — the draft will have passed it, but the human review
   is what changes publish status (pipeline only drafts).
5. Grep the new content for `[ev:...]` tags whose evidence has expired and re-run the
   researcher for those claims only (`research/README.md`).
6. Update `wp_post.modified` / Yoast doesn't need anything; keep URL stable so
   sitemap + backlinks survive.

## Freshness in the pipeline (what exists vs manual)

- The `price_updated` column exists and is validated (schema) — but the pipeline does
  **not** yet watch it. A cheap future addition: a monthly Code node that lists
  product rows with `price_updated` older than 90 days into a Telegram reminder.
  Until then, the human runs the refresh checklist quarterly.