# TailWell — Master SOP & Execution Plan

**This is the single source of truth for the project.** When anything feels confusing, come back to this file first. Every other file produced so far is detail/backup — this one tells you what stage you're in and exactly what to do next.

---

## 1. Project Snapshot

- **Name:** TailWell
- **What it is:** A pet care wellness affiliate blog. Buying guides, comparisons, and reviews — weighted toward high-commission wellness/senior-care categories, with puppy-to-senior coverage layered inside existing topic silos (not a fully generic pet site).
- **Stack:** n8n (orchestration) → OmniRoute (AI gateway) → Ollama (local) / OpenRouter (cloud) → WordPress (publishing) → Yoast SEO, ThirstyAffiliates, Gemini (images)

## 2. Locked Decisions (don't re-litigate these — build on them)

| Decision | Value |
|---|---|
| Niche | Pet care products & wellness |
| Site name | TailWell |
| Colors | Sage green `#5F7A52`, terracotta `#D97B4F`, cream `#F5EFE4`, charcoal `#2E2A24` |
| Fonts | Fraunces (headings), Inter (body) |
| Content silos (categories) | `wellness-health`, `food-nutrition`, `gear-tech`, `training-behavior`, `senior-special-needs` |
| Life stage layer | Separate taxonomy: `puppy`, `adult`, `senior` — layered inside the 4 general silos, not new categories |
| Theme base | GeneratePress (parent) + custom TailWell child theme |
| SEO plugin | Yoast (not Rank Math — workflow writes Yoast-specific meta keys) |
| Affiliate link plugin | ThirstyAffiliates |

## 3. The 7 Build Phases — Current Status

| Phase | What it covers | Status |
|---|---|---|
| 0 — Foundation | Niche, silos, keyword strategy, affiliate program shortlist | ✅ Done |
| 1 — Manual content proof | Hand-written/reviewed articles before automation | 🟡 Article #1 drafted, not published. Article #2 keyword validated, not drafted |
| 2 — Local infrastructure | n8n, Ollama, OmniRoute, OpenRouter, WordPress connection | 🟡 Setup guide ready; **your actual machine's install status is unconfirmed** |
| 3 — QA/Fact-check built first | The gate that blocks bad content | ✅ Designed and wired into the n8n workflow |
| 4 — Full pipeline | All workflow stages, real node-by-node | ✅ Workflow built (v6, 23 nodes), bugs fixed, diagrammed |
| 5 — Human-in-the-loop publishing | Manual review before autonomous publish | Not started — no articles live yet |
| 6 — Economics tracking | Cost per article/conversion | Not started |
| 7 — Scale & migrate | VPS migration, 2/day cap | Not started (too early) |

## 4. What's Actually Built (Inventory)

| Asset | What it is | Status |
|---|---|---|
| `wordpress/tailwell-theme/` (GeneratePress child theme) | The canonical deployable theme: templates, shortcodes, mobile nav fix, security fixes, encoding fixes, life-stage badges/pills | ✅ Built — **this is the current, correct theme to deploy** (the pre-restructure root `tailwell-theme` generation lives in `archive/legacy/`) |
| `n8n/tailwell-end-to-end.json` (v6, 23 nodes) | Full n8n workflow, research→publish | ✅ Built, both known bugs fixed (category ID resolution, SEO meta generation), life-stage term-ID resolution added |
| Article #1 draft | "Best Joint Supplements for Large Senior Dogs with Sensitive Stomachs" | 🟡 Written, needs your review + product links once approved |
| Life-stage layer | Puppy/adult/senior taxonomy plan | ✅ **Implemented and verified** — theme + workflow v6 + validation scripts; spec now lives at `docs/life-stage-layer.md` (repo gates: 17/17 theme, 14/14 workflow) |
| Product database template | CSV with required columns/categories | ✅ Template ready, all rows still `pending` (nothing approved yet) |
| Setup/deployment prompts | Local infra setup, WordPress structure, mobile nav fix, deployment | ✅ Written — pre-restructure copies live in `archive/legacy/` (e.g. `opencode-prompt-wordpress-site-structure-FINAL.md`); pipeline prompts are `automation/prompts/` |

## 5. What's Genuinely Blocking Progress (in order)

These are the actual bottlenecks — everything else is either done or waiting on these:

1. **Is the WordPress site actually live on real hosting?** Nothing else downstream works without this.
2. **Has `wordpress/tailwell-theme/` actually been installed on that live site?**
3. **Have you applied to Innovet Pet / OnePet (or similar wellness affiliate programs)?** Requires #1 and #2 done first. The product database stays empty/`pending` until this happens.
4. **Is n8n actually installed and running on your machine, with Ollama/OmniRoute/OpenRouter all connected?** (Phase 2 — status unconfirmed)

**Resolved this session:** the life-stage layer implementation is complete and repo-verified (theme `17/17`, workflow v6 `14/14` — see `docs/life-stage-layer.md`).

Nothing past this list (a real end-to-end automated run, a live article, real affiliate revenue) can happen until #1–4 are resolved — and they're the only things that are actually on you, not on more planning from me.

## 6. Standard Operating Procedures (repeatable — use these going forward)

### SOP A — Deploying a Hermes/Open Code package
1. Extract the zip, read its own README/runbook first — Hermes documents its own packages well
2. Confirm which folder is the actual deployable theme (check for a real `style.css` theme header)
3. Follow that package's runbook in order — don't skip steps
4. Run any included verification script against the live site before trusting it
5. Report back here with what passed/failed so this master doc can be updated

### SOP B — Publishing a new article (once automation is tested)
1. Validate the keyword + check competition (Phase 0 method) before anything else
2. Fill the n8n Form Trigger: keyword, Wikipedia topic, silo, article type, life stage
3. Let the workflow run — it stops itself at kill switch / relevance / QA gates if something's wrong
4. On "Draft Ready" alert: manually review before publishing, especially the product recommendations
5. Publish manually until Phase 5's 50–100 article threshold is reached

### SOP C — Adding a new approved affiliate product
1. Apply to / get accepted into the affiliate program
2. Verify the product and link manually yourself
3. Add a row to the product database sheet with `status = approved`
4. Never set `approved` without manual verification — this is the safety gate against invented/broken links

### SOP D — When something feels confusing or scattered
1. Come back to this file first
2. Check Section 5 for what's actually blocking
3. Only generate a new prompt/file if it's for one of the unresolved items in Section 5 — resist the urge to re-plan things already marked ✅ Done

## 7. Immediate Next Action (do this one thing next, not five things)

Everything currently traces back to one question: **what's the actual status of your hosting and n8n install?**

Answer that, and the next single step becomes obvious from Section 5's ordered list. Don't start a 6th parallel thread until #1–2 in Section 5 are confirmed done.
