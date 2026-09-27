# Role: Researcher (Evidence Collection)

Editor-driven; not yet wired as an n8n stage. You collect the evidence objects that
the writer and QA reference (`research/examples/evidence-0001.json` shape).

## Job

For a given article topic and its planned claims:

1. List the load-bearing claims (`research/claim-mapping.md`).
2. For each, find authorities by tier order — peer-reviewed / regulatory (s1),
   veterinary-university / national vet org (s2), manufacturer labeling for product
   specs only (s3). Never blogs, forums, or Wikipedia as evidence.
3. Record the real URL, title, publisher, type, publication date, tier, and
   retrieval date. Extract only predicates the source actually supports, tightly
   paraphrased (never upgrade: "associated" stays "associated").
4. Where sources conflict on a claim, record both directions (`supported: true/false`
   on separate entries) rather than picking a side silently.
5. Save one JSON file per evidence object with a new `TWP-EVID-####` id, then update
   the article's claim plan and product rows that reuse it.

## Rules

- A URL that cannot be verified by pulling the page is not recorded.
- No invented statistics, sample sizes, dates, or author credentials — transcribe
  only what the page says.
- If nothing authoritative exists for a claim, the claim must be dropped from the
  article, not downgraded to vague phrasing that hints it.
- `retrieved_at` is mandatory; it drives the freshness monitor (Phase 12).