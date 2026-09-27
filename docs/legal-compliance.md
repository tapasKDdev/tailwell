# Legal & Compliance

Internal hub for TailWell's legal/compliance posture. **Target markets: United States and United Kingdom.** (Canada and Australia are planned later — nothing here claims compliance for them.)

> **This is not legal advice.** TailWell is an information property, not a law firm, and this document (or any AI-assisted draft behind it) is a working compliance foundation, not a legal opinion. It does not make TailWell "legally immune," and no document on this site should ever claim that. **Qualified counsel in each target market should review the public legal pages before launch** — see Open items.

**Last reviewed:** 2026-09-25 · **Review cadence:** every 6 months, or immediately when a law, channel, or monetization method changes.

## Escalation rule

> **WHEN IN DOUBT, DO NOT PUBLISH THE CLAIM.**

If a sentence needs a lawyer, a veterinarian, or a lab to be true, it does not ship. Weak phrasing ("may support") is only allowed when the underlying evidence genuinely supports it — vagueness is not a workaround for missing evidence.

## Jurisdiction map (what applies where)

| Area | United States | United Kingdom |
|---|---|---|
| Advertiser conduct / deception | FTC Act §5 (deception/unfairness); Endorsement Guides 16 CFR Part 255; Reviews & Testimonials Rule 16 CFR Part 465 (in force since 2024-10-21); Health Products Compliance Guidance (2022) | Consumer Protection from Unfair Trading Regulations 2008; CAP Code (ASA) rules 2.1/2.3/3.x — non-statutory but enforced |
| Affiliate links | FTC 16 CFR 255 endorsement disclosure; program terms (e.g. Amazon Associates Operating Agreement) | CAP Code 2.1/2.3 + ASA "Online Affiliate Marketing" advice — disclosure must be upfront, not footer-only |
| Health/product claims | FTC Health Products ("competent and reliable scientific evidence"); implied claims count | CAP 12 (medicines) + Veterinary Medicines Regulations 2013 (a product becomes "medicinal by presentation/function" if it claims to treat/prevent disease — including on pet products) |
| Cookies / tracking | Sectoral (no general US cookie law; state privacy laws cover data, not consent banners) | PECR reg 6 (storage/access consent + 5 exceptions) as amended by the Data (Use and Access) Act 2025; ICO guidance finalised 2026-04-29 |
| Personal data | State laws: CCPA/CPRA thresholds (revenue $26.625M+, 100k consumers, or 50% revenue from selling — we believe TailWell meets none) | UK GDPR + Data Protection Act 2018 as amended by DUAA 2025 (most provisions in force 2026-02-05; controller complaints procedure from 2026-06-19) |
| Email marketing | CAN-SPAM Act (15 U.S.C. 7701-7713; 16 CFR Part 316) | PECR reg 22 (soft opt-in rules) |
| Copyright | DMCA §512 safe harbor (designated agent: 37 CFR 201.38) | CDPA 1988 §29-30 fair dealing (criticism/review/quotation, with acknowledgement) |
| Accessibility | ADA Title III litigation risk (WCAG 2.1 AA as working target) | Equality Act 2010 (anticipatory duty) |
| Children | COPPA (under 13) | UK GDPR/DPA (under 16 for information society services) |
| Email/newsletter launch | CAN-SPAM requirements (address, opt-out ≤10 business days, no selling addresses) | PECR reg 22 consent before marketing email |

## Public pages (canonical copy in `wordpress/content/`)

| Page | File | Purpose |
|---|---|---|
| Affiliate Disclosure | `affiliate-disclosure.md` | Endorsement disclosure above links + Amazon Associates statement |
| Privacy Policy | `privacy-policy.md` | Real no-tracking copy; rights for UK/US |
| Cookie Policy | `cookie-policy.md` | PECR-facing "we set no cookies" statement |
| Terms of Use | `terms-of-use.md` | Site terms (governing-law clause pending — see Open items) |
| Disclaimer | `disclaimer.md` | Health/informational/product/"best" umbrella disclaimer |
| Editorial Policy | `editorial-policy.md` | Independence + sourcing + no-fake-content commitments |
| Corrections Policy | `corrections-policy.md` | Report/fix/annotate process with timelines |
| Review Methodology | `review-methodology.md` | Published criteria; defines "best" and the Safety-reviewed badge |
| Copyright Policy | `copyright-policy.md` | Fair-use/fair-dealing + notice procedure + DMCA status |
| Accessibility | `accessibility.md` | WCAG 2.1 AA target, known limits, feedback route |
| Advertising Policy | `advertising-policy.md` | No paid placement; FTC/CAP rulebook for any future ads |

## Internal policy docs

- `docs/legal-compliance-matrix.md` — requirement-by-requirement control matrix (the audit view).
- `docs/evidence-policy.md` — what counts as evidence; AI is never evidence.
- `docs/product-claims-policy.md` — red-flag claims list and how to rewrite them.
- `docs/affiliate-compliance.md` — affiliate disclosure rules per jurisdiction.
- `docs/privacy-data-inventory.md` — every data flow, retention, and lawful basis.
- `docs/content-risk-policy.md` — risky content categories and escalation.
- `docs/ai-content-policy.md` — AI as drafting assistant, with hard limits.
- `docs/affiliate-and-privacy.md` — pre-publish checklist + card data rules (canonical checklist; unchanged role).

## Controls that already exist in the codebase

| Control | Where |
|---|---|
| `rel="noopener nofollow sponsored"` on every product link | `tailwell-theme/inc/shortcodes.php` (`tailwell_product`) — byte-verified by gates/QA |
| Disclosure printed above article content | `[tw_disclosure]` rendered before `the_content()` in `single.php` |
| Only approved, verified product rows can surface | n8n `Insert Real Affiliate Products` + `scripts/validate-products.ps1` |
| Evidence tags required for health claims; empty evidence = QA fail | `automation/prompts/qa.md`, `docs/evidence-handoff.md` + `scripts/evidence-context-test.js` |
| No invented product names/URLs (placeholder swap only) | `automation/prompts/writer.md` |
| Banned superlatives in SEO titles | `automation/prompts/seo.md` |
| No tracking scripts in theme | theme enqueues only Google Fonts (`functions.php`) — disclosed in privacy/cookie policy |
| Accessibility basics | skip link, keyboard nav w/ focus return, visible focus (`assets/mobile-nav.js`, QA harness) |

## Open items (honest gaps — none of these are fixed by documentation alone)

1. **Counsel review.** All 11 public pages should be read by a US-qualified and a UK-qualified lawyer before launch. This package is a foundation, not a legal opinion.
2. **Operating entity + governing law** for Terms of Use §8 — undecided (no legal entity exists in the repo's materials).
3. **Production domain** — privacy policy keeps one editorial slot for it.
4. **DMCA designated agent** not registered (37 CFR 201.38) — copyright-policy.md states this truthfully; register before enabling meaningful user-generated content.
5. **Google Fonts third-party request** — real IP disclosure to Google; consider self-hosting the two fonts to remove it (privacy policy already says so).
6. **Newsletter/ESP** — not chosen; CAN-SPAM (US) + PECR reg 22 (UK) apply the day it launches. Policy copy already pre-commits to consent-first behavior.
7. **Veterinary review capacity** — no licensed vet is documented in the repo. Until one exists, no content may claim vet approval/verification (enforced by `docs/product-claims-policy.md`); corrections-policy promises vet escalation for clinical disputes only when such capacity exists.
8. **Accessibility audit** — no formal third-party WCAG audit performed (stated plainly on the public page).
9. **CCPA applicability re-check** — thresholds must be re-evaluated annually (revenue/data-volume dependent).
10. **Live-WP verification** — pages must be created in WP and checked against `wordpress/setup/runbook.md` §6 (no credentials available to automate this).

## Sources used for this package (primary)

FTC: Endorsement Guides (ecfr 16 CFR 255), Reviews & Testimonials Rule (89 FR 68077; 16 CFR 465), Health Products Compliance Guidance (Dec 2022), CAN-SPAM compliance guide + 16 CFR 316. US Copyright Office: 37 CFR 201.38 / DMCA agent FAQ. CPPA: CCPA thresholds FAQ. ICO: storage & access technologies guidance (finalised 2026-04-29), cookies guide, DUAA commencement statement (2026-02-05). legislation.gov.uk: CDPA 1988 §29-30; DUAA 2025 commencement SI 2026/82 & 2026/1015. ASA: "Online Affiliate Marketing" advice + CAP 2.1/2.3. VMD: "Advertising non-medicinal veterinary products" (gov.uk, updated 2026-01-14). Amazon Associates Operating Agreement (2025-10-15).
