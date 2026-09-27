# Affiliate Compliance

Regulatory rules for TailWell's affiliate monetization, per jurisdiction. The **pre-publish checklist and card data rules stay canonical in [`affiliate-and-privacy.md`](affiliate-and-privacy.md)** — this file adds the legal "why" and the partner-side rules. Nothing here contradicts the public `wordpress/content/affiliate-disclosure.md`.

**Last reviewed:** 2026-09-25.

## The non-negotiables

1. **Disclosure before the link, not after the fact.** A footer-only or About-page-only disclosure is non-compliant in the UK (ASA: generic/bottom disclaimers insufficient; "may earn" wording explicitly criticised) and weak under US FTC "clear and conspicuous." TailWell therefore prints the disclosure **above the article body/product links** on every article (`[tw_disclosure]` before `the_content()` in `single.php`), repeats it in the footer, and publishes the full page at `/affiliate-disclosure/`.
2. **Exact affiliate hygiene.** Every product link carries `rel="noopener nofollow sponsored"` exactly; no bare affiliate URLs in prose; no user identifiers in affiliate tokens (see checklist in `affiliate-and-privacy.md`).
3. **Money ≠ placement.** Commission availability is checked only *after* a product qualifies under `review-methodology.md`. No payment for position, ever (`advertising-policy.md`).
4. **No unverifiable endorsement language.** Product copy obeys `product-claims-policy.md`; an affiliate relationship never upgrades a claim.
5. **Honest identification of non-affiliate links.** Not every link monetises; we never imply one does, and we never disguise an affiliate link as editorial navigation.

## Per-jurisdiction detail

### United States — FTC

- **Endorsement Guides, 16 CFR Part 255:** material connections disclosed "clearly and conspicuously" (255.0(f) definition); endorsements reflect honest opinions; claims in endorsements need the level of evidence the claim implies (255.2).
- **Reviews & Testimonials Rule, 16 CFR Part 465 (in force 2024-10-21):** no fake, AI-generated, or incentivized-without-disclosure reviews/testimonials; no buying positive coverage; company cannot misrepresent review authenticity. (We simply have none of these.)
- **Health-adjacent products:** Health Products Compliance Guidance (2022) — "competent and reliable scientific evidence" for any efficacy implication.
- **Program terms:** Amazon Associates Operating Agreement (version 2025-10-15) requires the identifying statement — TailWell uses *"As an Amazon Associate, TailWell earns from qualifying purchases."* — and forbids implying Amazon endorses/recommends us or that price differs for using our link. **Open check:** re-read the current OA text at each program renewal (statement wording is program-controlled).

### United Kingdom — CAP/ASA

- **CAP 2.1:** marketing communications must be obviously identifiable **as such**; **2.3:** commercial intent clear when not apparent from context.
- ASA "Online Affiliate Marketing" advice: content wholly about affiliate-linked products = the *whole* piece is advertising (identifier before engagement); partially-affiliated content must highlight the affiliated parts; disclaimers at the bottom insufficient; "may receive commission" too vague; avoid jargon labels ("aff", "afflink").
- Where content looks editorial, TailWell's upfront disclosure line is the primary control — the affiliate page reinforces, never substitutes, it.

### Amazon & other programs

- Statement present in: article disclosure line, site footer, `/affiliate-disclosure/`.
- ThirstyAffiliates cloaking (runbook) must never obscure the disclosure: disclosure sits on the *article*, independent of link format.
- Partner link redirects must remain `rel`-tagged at the article end; `target="_blank"` keeps `rel="noopener"`.

### Email (only when a newsletter exists)

- **US CAN-SPAM:** accurate headers/subject, ad identification where primary purpose is commercial, valid physical postal address, working opt-out honoured within 10 business days, no selling addresses post-opt-out (15 U.S.C. 7704; 16 CFR 316).
- **UK PECR reg 22:** consent or valid soft opt-in before marketing email.
- Newsletter form is deliberately inert today (`privacy-policy.md` §1) — wiring it is a CHANGELOG-worthy event per `legal-compliance.md` open item #6.

## Partner onboarding checklist (before adding any new program)

1. Program's identifying-statement requirement recorded in this file.
2. Disclosure page + footer updated if the program adds new wording requirements.
3. Link rendering still flows through `[tw_product]`/shortcode rules (rel, no bare URLs).
4. Product rows pass `validate-products.ps1` (real URL, approved, price fresh).
5. No placement obligation, exclusivity, or approval right accepted (rejection clause — if a partner requires any of these, decline).
6. Run the `affiliate-and-privacy.md` checklist end-to-end.

## Audit cadence

- **Per publish:** checklist in `affiliate-and-privacy.md`.
- **Quarterly:** verify disclosure positions (article top, footer, page), rel attributes, Amazon statement wording vs current OA, and that no new script/CTA language slipped in (grep hygiene).
- **On any monetization change** (ads, sponsorships, new geo): update `advertising-policy.md`, `privacy-policy.md`, `cookie-policy.md` **in the same session**, then CHANGELOG.
