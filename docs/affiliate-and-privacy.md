# Affiliate & Privacy Compliance

Two layout concerns drive this: the way `[tw_product]` renders affiliate links, and
the legal pages in the footer. **The page copy lives in `wordpress/content/`
(that's canonical, not this file)** — the full legal page set (disclosure, privacy,
cookie, terms, disclaimer, editorial, corrections, methodology, copyright,
accessibility, advertising) is indexed by [`docs/legal-compliance.md`](legal-compliance.md),
which is also where jurisdiction rules and open legal items live. This doc keeps its
original role: the **pre-publish checklist** + card data rules for those pages.

Related internal policies: [`affiliate-compliance.md`](affiliate-compliance.md) (regulatory
rules per jurisdiction), [`privacy-data-inventory.md`](privacy-data-inventory.md) (every data
flow), [`product-claims-policy.md`](product-claims-policy.md) (red-flag wording rules).

## Canonical copy locations

| Page | Path | Slug |
|---|---|---|
| Affiliate Disclosure | `wordpress/content/affiliate-disclosure.md` | `/affiliate-disclosure/` |
| Privacy Policy | `wordpress/content/privacy-policy.md` | `/privacy-policy/` |

Those files include Yoast title/meta/focus suggestions. `[tw_disclosure]`
(`inc/shortcodes.php`) links to the disclosure page; its slug is overridable via the
`tailwell_disclosure_page` filter.

## Compliance checklist (run before each publish)

1. Every product link is rendered by `[tw_product]` → `rel="noopener nofollow sponsored"` exactly (already true; `docs/seo.md`).
2. No bare affiliate URLs in article copy (only the shortcode emits them).
3. No user-PII enters any affiliate URL (affiliate tokens are per-brand, fixed; never per-user).
4. Disclosure page linked in footer and visible pre-product-links.
5. Prices in Product JSON-LD come from approved rows with `price_updated` — nothing hand-typed.
6. No claims of efficacy/safety for any product; `[tw_product]` cards never say "cures".
7. New copy passes the red-flag grep in `product-claims-policy.md` §1 (no `cures`, `clinically proven`, `vet-approved`, `lab-tested`, `guaranteed`, implied-cure phrasing, etc.).
8. No badge or sentence implies veterinary endorsement, lab testing, or certification that is not documented for that specific product — the card badge reads **"Safety-reviewed"** (`product-claims-policy.md` §3).
9. Disclosure position re-verified: article-top disclosure line, footer statement, and `/affiliate-disclosure/` all present and worded per `affiliate-compliance.md`.

## Card data rules (conversion UI)

- Every card field comes from an approved CSV row (`name`, `brand`, `category`,
  `price`, `url`, `affiliate_partner`, `vet_verified`, `image_url`) or from optional
  editorial shortcode attributes (`best_for`, `limitation`) — never invented.
- CTA wording is `Check price` when a price renders, `View product` otherwise;
  buttons carry no urgency, ranking, or "buy now" language.
- `[tw_disclosure]` renders before `the_content()` in `single.php`, so the
  disclosure always sits above product links (checklist 4). Product runs are
  grouped by `tailwell_wrap_product_runs` (`inc/shortcodes.php`) into
  `.tw-product-list` — a `.tw-product-list--compare` wrapper (2+ cards) gives
  aligned comparison rows; no duplicate legal copy is introduced by the wrapper.

## Default privacy posture vs the policy copy

- **Default site behavior: no tracking.** The theme adds no cookies, no analytics,
  no external scripts beyond Google Fonts (disclosed). The privacy and cookie pages
  in `wordpress/content/` are **final copy describing that real posture** — no
  bracketed placeholders remain (only the production-domain slot in the privacy
  policy, flagged at the top of that file). If you add an analytics/ad provider,
  update `privacy-policy.md`, `cookie-policy.md`, and the row set in
  `privacy-data-inventory.md` **in the same session** — and wire consent first if
  PECR reg 6 requires it.
- WordPress core/admin/plugins may set session/security cookies and record IPs;
  the privacy policy's §2/§3 cover that.

## Data flows (for the privacy page's third-party list)

> **Moved.** The flow table now lives in [`privacy-data-inventory.md`](privacy-data-inventory.md)
> (visitor-facing + internal flows, lawful basis, retention, disclosure mapping).
> Keep that file and the public privacy policy in lockstep — do not fork a second table here.