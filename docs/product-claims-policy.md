# Product Claims Policy

The hard red lines for anything TailWell says about a product — plus how to write the sentence instead. Backs the public promises in `wordpress/content/editorial-policy.md`, `disclaimer.md`, and `review-methodology.md`.

**Last reviewed:** 2026-09-25.

## 1. Banned words and phrases (never publish)

**Disease/condition claims about a product:**
`treats` · `cures` · `prevents` · `heals` · `reverses` · `eliminates [condition]` · `fixes` · `prescription-strength` · `detoxes` · `boosts immunity` (unevidenced) · `clinically proven` (without the actual cited study) · `guaranteed results`

**False authority:**
`veterinarian approved` · `vet-recommended` · `vet-endorsed` · `veterinarian formulated` (unless the specific product's documentation says so and is cited in that sentence) · `approved by vets` · `doctor recommended`

**Lab/certification theatre:**
`lab-tested` · `third-party tested` · `certified` · `pharmaceutical grade` · `organic` (if uncertified) — banned **unless the specific certification/test for that specific product is documented and cited**. A generic badge on a card is not documentation.

**Absolute safety:**
`safe for all dogs/cats` · `risk-free` · `no side effects` · `100% safe` · `non-toxic` · `won't hurt your pet`

**Implied claim red flags (the sneaky versions):**
`supports immunity` / `supports joints` when the evidence only shows an ingredient *association* · `soothes` · `calming` as an outcome promise · `gentle relief` · before/after framing · "owners report" used as if it were study data · `works fast`.

## 2. Writing the replacement

Three legal-safe shapes, in preference order:

1. **Spec + source (best):** "Each chew contains 400 mg glucosamine (label, updated May 2026)."
2. **Evidence-tagged observation:** "In one study, glucosamine was associated with improved mobility scores in dogs with osteoarthritis [ev:TWP-EVID-####]." — association stays association.
3. **Honest uncertainty:** "Evidence for this ingredient in dogs is limited; ask your veterinarian before starting it."

Every health-adjacent sentence also keeps the **vet-consult framing** ("talk to your veterinarian"). Nothing is a diagnosis, treatment directive, or outcome promise.

**Escalation:** when a sentence needs a lawyer, a veterinarian, or a lab to be true — **do not publish it** (see `docs/legal-compliance.md`).

## 3. The "Safety-reviewed" badge

- Shown when the CSV row has `vet_verified=true` (schema: `product-data/product-database.schema.md`).
- **Meaning (public + internal):** the row passed TailWell's internal safety gate — health-adjacent category, label/claim check against this policy, approved for health-adjacent copy.
- **Meaning it does NOT have:** veterinary examination, endorsement, certification, or lab testing of the product.
- **Label change (2026-09-25):** badge text changed from "Vet-verified" → "Safety-reviewed" in `inc/shortcodes.php`, `preview/index.html`, because the old wording implied veterinary verification that does not exist. Do not change it back without a documented, per-product veterinary review process (`docs/legal-compliance.md` open item #7).
- No other trust badge may imply vet/lab/certification status. Any future badge needs the same evidence-before-label test: **name the fact, document the fact.**

## 4. Price, urgency, and ranking claims

| Never | Instead |
|---|---|
| `lowest price guaranteed`, `best deal`, `was $99 now $49` (unverified) | Current price from the approved row + `price_updated` date; "price at time of update" |
| `Only 3 left!`, countdown timers, fake scarcity | Nothing. Ever. |
| `#1 recommended`, `top-rated`, star ratings we do not have | Our criteria-based pick language (`review-methodology.md`); no rating widget unless real, sourced ratings exist |
| `Editor's Choice`-style awards we invented | "Our pick" / "Why we picked it" |

Cards CTA wording stays `Check price` / `View product` — no urgency variants (enforced in `shortcodes.php`).

## 5. Where it is enforced

- **Prompts:** `automation/prompts/qa.md` rule 1 (auto-FAIL on cure/guarantee/impossible specificity), `writer.md` rules 1-4, `planner.md` blocks unsafe topics at plan time.
- **Data:** `scripts/validate-products.ps1` (approved rows only, real URLs, no `example.com`, price freshness); `scripts/validate-evidence.ps1` (id resolution).
- **Shortcodes:** `inc/shortcodes.php` (badge label, rel attributes, CTA wording, JSON-LD only from real rows).
- **Human:** pre-publish checklist `docs/affiliate-and-privacy.md` + editorial review; red-flag grep hygiene at release.
- **Public:** criteria and badge meaning published in `review-methodology.md` so readers can hold us to them.

## 6. If a bad claim ships

It is a corrections matter: fix fast, annotate with a dated note, log it — `wordpress/content/corrections-policy.md` (health/safety errors jump the queue). Record the miss in `CHANGELOG.md` and, if it was a prompt gap, close it in the prompt the same session.
