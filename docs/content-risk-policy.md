# Content Risk Policy

Which content categories carry real-world risk at TailWell, what extra scrutiny they get, and when something must stop and escalate. Complements `product-claims-policy.md` (word-level red lines) and `evidence-policy.md` (source rules).

**Last reviewed:** 2026-09-25.

## Risk tiers

### Tier 1 — could harm a pet if wrong (highest scrutiny)

| Category | Examples | Required controls before publish |
|---|---|---|
| Medical/health conditions | symptoms, pain, mobility decline, seizures, limping | s1/s2 evidence only; vet-consult framing; **no diagnosis language**; QA tier check ≥ s2; planner blocks cure-topic phrasing |
| Supplements & dosing | joint supplements, CBD, probiotics, quantity guidance | `product-claims-policy.md` red-flag pass; label-based specs only for s3 facts; dosing defers to vet/label — never "give X mg" as advice |
| Nutrition & diet changes | raw diets, allergen elimination, weight-loss plans | s1/s2 sources; explicit "not a substitute for a veterinary nutritionist" where clinical |
| Food safety & recalls | contamination, spoilage, recall notices | Date-stamp heavily; link to the official recall source (FDA/VMD/gov.uk); re-check at every refresh — a stale recall notice is a correction incident |
| Emergencies & toxins | chocolate/xylitol/grapes, bloat, heatstroke | Plain "contact a vet/emergency clinic now" instructions; zero ambiguity, zero product placement in the same block |

**Tier 1 escalation:** any Tier-1 sentence that cannot be evidenced within the session is deleted, not softened. Clinical disputes on published content get human review plus a veterinary professional *when capacity exists* — and content never *claims* that review exists before it does (`legal-compliance.md` open item #7).

### Tier 2 — money or trust at stake (standard scrutiny + checklist)

Buying guides, price comparisons, "best" lists, insurance, gear claims: full pre-publish checklist (`affiliate-and-privacy.md`), published criteria (`review-methodology.md`), no fake urgency/ratings (`product-claims-policy.md` §4), disclosure position verified.

### Tier 3 — low risk (routine)

Care routines, enrichment, training basics, product roundups without health adjacency: standard QA. Training content still avoids aversive/unsafe technique endorsement (risk of reader harm → treated as Tier 2 if a tool could injure).

## Hard stops (do not publish, ever)

1. Any sentence failing `product-claims-policy.md` §1 that cannot be truthfully replaced.
2. Any claim with no admissible source (`evidence-policy.md`) — **including if an AI produced it with confidence.**
3. Content implying veterinary endorsement, lab testing, certification, or personal experience we do not have.
4. Content written for a jurisdiction we do not serve (CA/AU) presented as locally compliant.
5. Anything requiring legal privilege or a lawyer's sign-off that has not received it (→ `legal-compliance.md` escalation rule: **when in doubt, do not publish the claim**).

## Recall / safety-event response

1. Identify affected products/articles (product rows: check `status`/`enabled` in the sheet).
2. **Deactivate first** (set `enabled=false` → pipeline stops inserting it; kill switch is designed for this), then edit the article.
3. Publish an "Updated" note with the date and the official source link (`corrections-policy.md`).
4. CHANGELOG entry; if a claim class was systemic, strengthen the prompt/QA rule the same session.

## Freshness as risk control

Outdated health/price info is a live risk: refresh cadence lives in `docs/freshness.md`, with dated "Updated" notes on articles. A stale claim that a reader relied on is handled as a correction (7-day ack / health-errors-first).

## Answer-engine (GEO/AEO) risk

Facts extracted by AI answer engines are our content with the nuance removed. Therefore: every extractable claim must stand alone with its evidence (`docs/geo-aeo.md` + `evidence-policy.md`); FAQ answers restate *our* sourced text, never new claims; if a claim only survives with hedging, it does not go into an answer box either.

## When to trigger legal review (not just editorial)

- Adding a monetization method (ads, sponsorships, paid placements).
- Adding a new jurisdiction or translating pages.
- Launching email, accounts, payments, or any user-generated feature.
- Receiving a regulator, platform, or lawyer contact — **route to counsel, answer nothing substantively first** (OP + counsel; no one responds "in character" for the brand).
- Any request to soften a disclosure for design reasons.

Log every trigger outcome in `CHANGELOG.md`.
