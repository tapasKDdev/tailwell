# Internal Linking & Duplicate Detection

Two goals: every page is reachable with descriptive anchor text, and no two
articles/titles compete for the same topic.

## The one-URL model (recap)

A published topic lives at exactly one permalink. Category archives (`/category/<slug>/`)
are silo hubs; articles are posts under exactly one category. There are no tags-only
pages, no duplicate category slugs, no orphaned legacy URLs (`archive/` is read-only).

## Internal link policy

Every generated article contains, by construction, the **internal-link block**
(appended by `Insert Real Affiliate Products` in the n8n workflow):

```html
<hr class="tw-internal-sep">
<p class="tw-internal-links">
  <a href="{site}/category/{silo}/">More {silo topic} guides</a> ·
  <a href="{site}/">Browse all of TailWell</a>
</p>
```

This guarantees each post links up to its silo hub and the home page. QA rule 6
(see `automation/prompts/qa.md`) fails any article missing this block.

Additional linking conventions:

- **Writers never link to products** — only the `[tw_product]` shortcode does, via
  the approved product database.
- In-body links to *other articles* are added at the human/editorial review stage
  (matching the newest article to older same-silo posts). The workflow cannot know
  future posts, so this stays manual — use descriptive anchors ("joint care for
  older dogs"), never "click here".
- The home page `tw-silo-grid` links all five silo hubs. `footer-menu` links the
  legal pages. The mobile drawer links the silo hubs.
- **Homepage intent and guide cards** (see `wordpress/content/homepage.md` for the
  source of truth and the intent→destination map): intent cards route to category
  hubs; featured-guide cards use `/?s=` search fallbacks until the real article
  permalinks exist, then must be swapped. Never invent article URLs for unpublished
  guides — broken links fail `scripts/check-links.ps1`.
- Anchor text duplicates the target heading's core term at most once per page
  (e.g. don't link ten siblings all saying "joint supplements").

## Duplicate / near-duplicate detection

Titles are the first collision surface, and AI-generated titles drift similar fast.

**Before importing a new draft**, run the title check against existing posts.

```js
// node --input-type=module -e "..."  (or paste into an n8n Code node)
// posts = [{ title, link }] from WP REST /wp-json/wp/v2/posts?per_page=100&status=any
function ld(a, b) {
  const m = Math.min(a.length, b.length);
  const d = new Array(a.length + 1).fill().map((_, i) => i);
  for (let j = 1; j <= b.length; j++) {
    let prev = d[0]; d[0] = j;
    for (let i = 1; i <= a.length; i++) {
      const tmp = d[i];
      d[i] = Math.min(d[i] + 1, d[i - 1] + 1, prev + (a[i - 1] === b[j - 1] ? 0 : 1));
      prev = tmp;
    }
  }
  return d[a.length];
}
function similar(a, b) {
  const s = Math.max(a.length, b.length);
  return s === 0 ? 1 : 1 - ld(a.toLowerCase(), b.toLowerCase()) / s;
}
// const threshold = 0.85;
```

- Threshold: **≥ 0.85 similarity to any existing title ⇒ auto-reject** the draft
  (same-topic collision) and pick a different angle or keyword.
- This check runs as a human gate before the first import; if it grows annoying,
  move it into the workflow as a Code node between QA and the WordPress node.

See also the QA rubric rule 3 (no duplicate/plagiarized content) which guards body
copy, while `docs/seo.md` covers silo vs tag URL structure.