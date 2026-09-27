# Privacy Data Inventory

Every data flow at TailWell — internal production **and** visitor-facing — with destination, lawful basis (UK terms), retention, and where it is disclosed publicly. Source of truth for the public `wordpress/content/privacy-policy.md` and `cookie-policy.md`; when this table and the policy disagree, fix them in the same session.

**Last reviewed:** 2026-09-25.

## A. Visitor-facing flows (what the public policy covers)

| Process | Data | Type | Destination / processor | Lawful basis (UK) | Retention | Public disclosure |
|---|---|---|---|---|---|---|
| Contact form (WPForms Lite) | Name, email, message | Personal data you submit | WordPress DB → site inbox (email) | Legitimate interests / pre-contract at your request | ≤ 12 months after conversation ends, or sooner on request | `privacy-policy.md` §1, §8 |
| Server/host logs | IP, URL, time, user agent | Technical log | Hosting provider | Legitimate interests (security, troubleshooting) | Host's normal operational period, then rotated | `privacy-policy.md` §2 |
| Google Fonts fetch | IP address (browser requests `fonts.googleapis.com` / `fonts.gstatic.com`) | Third-party request | Google | Transparency requires disclosure; consent-free but disclosed; self-hosting flagged as improvement | Google-side per its policy | `privacy-policy.md` §4; `cookie-policy.md` |
| Comments (only if enabled) | Comment text, name/email/website + WP cookies | Personal data | WordPress DB + commenter's browser cookies (1 yr / 1 yr / 2 wk) | Legitimate interests / consent for commenter cookies | Comment lifetime + cookie expiry | `privacy-policy.md` §3 |
| Affiliate click → retailer | Referral (your click) | Outbound navigation | Retailer (Amazon, Chewy, …) sets **its own** cookies | n/a — off-site | Retailer's policy | `affiliate-disclosure.md`, `cookie-policy.md` |
| Newsletter | — | **Not collected** (form is presentational) | — | — | — | `privacy-policy.md` §1 |
| Analytics / advertising / pixels | — | **None exist** | — | — | — | `cookie-policy.md` |
| Visitor data → AI/LLM | — | **Never.** No visitor personal data enters any model provider | — | — | — | `privacy-policy.md` §4 |

**Sale/sharing:** none (no ad-tech, no data brokers, no cross-context behavioural advertising) — CCPA "Do Not Sell/Share" link therefore not required; stated anyway in `privacy-policy.md` §7.

**CCPA applicability (re-check annually):** thresholds are (a) gross revenue > $26.625M, (b) buy/sell/share PI of ≥100k CA residents, (c) ≥50% revenue from selling PI. TailWell is believed to meet **none**; if any becomes true, update `privacy-policy.md` §9 and this row.

## B. Internal production flows (not visitor data)

| Process | Data | Type | Destination / processor | Notes | Retention |
|---|---|---|---|---|---|
| Topic intake | Keyword strings | Internal | n8n → LLM providers (OpenRouter / local ollama) | Editorial topics only — no user PII enters | n8n execution history |
| Wikipedia fetch | Article text | Internal | n8n (transient) | Background for planning; never cited | transient |
| Product sheet | CSV rows | Internal (product data) | Google Sheets → n8n | Sample rows in repo are placeholders; approved rows only in prod | sheet history |
| Draft creation | Title/content/meta | Internal | WordPress API (drafts) | Human approves before publish | WP revisions |
| Alerts | Keyword + status summary | Internal | Telegram | Ops notifications only; no visitor data | chat history |
| Image generation (future) | Text prompt → PNG | Internal | Gemini (when wired) | Subject to `docs/image-pipeline.md` safe-image rules; no depictions of real people/pets as "customers" | asset library |
| Evidence objects | Source URLs, claims | Internal | `research/evidence-*.json` | Public-source data only | repo history |

## C. Rights-handling process (tiny data volume, manual process)

1. Request arrives via `wordpress/content/contact.md` form (any of: access, delete, correct, restrict, object, portability, US-state equivalents).
2. Verify the requester controls the email address used (reply-to confirmation) — no more identity data collected than needed.
3. Locate data: contact entries (WPForms entries), comments (if any), email thread — nothing else exists (this inventory is exhaustive).
4. Fulfil within statutory time (UK: one month; US state laws per statute). Log the request and outcome (date, scope, response) — the log itself contains only date/decision, no message content.
5. Refusals: only where an exemption applies (e.g. required records); explain the reason in writing.

## D. Change control

Adding **any** flow (ESP, analytics, ads, chat widget, new tag) requires, in one session: a new row here → `privacy-policy.md` + `cookie-policy.md` update → consent mechanism if PECR reg 6 demands it → CHANGELOG entry → re-run gates. The homepage "Independent research"/privacy-facing chips must remain true after the change.

## E. Related documents

- Public: `wordpress/content/privacy-policy.md`, `cookie-policy.md`, `affiliate-disclosure.md`.
- Checklist + card rules: `docs/affiliate-and-privacy.md` (its historical data-flow table now lives here — see the pointer left in that file).
- Security/credentials posture: `docs/security.md`.
- Compliance mapping: `docs/legal-compliance-matrix.md`.
