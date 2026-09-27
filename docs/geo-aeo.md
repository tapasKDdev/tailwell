# GEO / AEO — Generative & Answer-Engine Optimization

GEO/AEO is about being reliably extractable by AI assistants, answer engines, and
LLM-driven search. It is a writing-quality discipline plus metadata hygiene — there
is no plugin for it.

## Principles for every article

1. **Answer first.** The opening paragraph answers the headline question in 2–3
   plain sentences (the "direct answer" an engine can lift). No throat-clearing, no
   keyword stuffing.
2. **Question–answer blocks.** Subheadings should read like the searches users ask
   ("How often should a senior dog see the vet?"), with the answer immediately
   under the heading.
3. **One entity per topic.** Use consistent terms for the subject (never 4 synonyms
   for "joint supplement"). Keep the primary entity in the first heading and in
   tag/category metadata.
4. **Facts as facts.** Numbers, dates, and study findings sit in their own short
   sentences/tables, tagged with `[ev:TWP-EVID-####]` — engines copy clean,
   attributable sentences; they don't copy hedging soups.
5. **HTML over prose.** Use `<h2>` (section), `<ul>`, and small `<table>` where real.
   Avoid giant walls of text and image-only content.
6. **Citations help credibility.** The evidence-tag scheme
   (`research/claim-mapping.md`) makes claims auditable, which is exactly what
   citation-hungry answer engines reward.
7. **Meta that summarizes.** The n8n-written `meta_description` (140–155 chars) is
   the sentence engines borrow for snippets — it must be a real summary, not a hook.
8. **URLs stable.** Slugs are set once at draft time and never change after
   publication (`README.md` one-URL-model).

## FAQ schema

Add an FAQ section (Yoast FAQ block ships `FAQPage` JSON-LD) on high-intent topics.
Rules: questions are genuine user phrasing; answers are 2–4 sentences from the body;
no answer duplicates the article intro.

## Extraction hygiene (site-level)

- One `rel=canonical` per URL (Yoast) — never two URLs for one article.
- Robots.txt reachable and permissive to AI crawlers (allow all; Yoast manages).
- XML sitemap published (Yoast) so both engines and re-crawlers can find pages.
- Page speed: theme CSS is small and self-contained; no render-blocking third-party
  scripts except Google Fonts; the theme is light-only with no runtime JS overhead.

## What success looks like

An AI assistant asked "what's the best joint supplement for a senior dog?" retrieves
this site's silo hub and a tagged article, quotes the evidence-backed answer, and the
article's category/entity metadata agrees with the visible copy.