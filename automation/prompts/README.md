# TailWell Prompt System V2

Canonical prompts for every model role in the pipeline. The n8n workflow embeds
these as system prompts in its LLM HTTP nodes; **this folder is the maintained copy
and the workflow's inline text must stay in sync with these files.** When a prompt
below changes, update the corresponding `system` content in
`n8n/tailwell-end-to-end.json` in the same commit.

## Roles

| File | Model binding | Stage |
|---|---|---|
| `planner.md` | Ollama (local) | Relevance check + section planning (`Relevance Check + Section Planner`) |
| `researcher.md` | any (editor-driven) | Evidence collection → writes evidence objects into `research/examples/` |
| `writer.md` | OpenRouter writing model | `Write Section` |
| `qa.md` | OpenRouter fact-check model | `QA / Fact Check` |
| `seo.md` | writing model (or `OPENROUTER_MODEL_FOR_SEO`) | `Generate SEO Title + Meta Description` |

## Shared principles (apply to every role)

1. **No fabrication, ever.** No invented studies, statistics, products, prices,
   URLs, vet names, or publication dates.
2. **Wikipedia is background only** — never evidence for any claim.
3. **Evidence objects** (`research/evidence-schema.md`) are the only sources for
   claims; copy carries `[ev:SOURCE_ID]` tags mapped in `claim-mapping.md`.
4. **Products come only from the product database**; writers emit placeholder tags,
   never a product name/price/URL.
5. **Structured JSON out** for anything the pipeline reads (contracted in each file).
6. Adjectives like "best", "top", "proven" must always be defensible — the QA gate
   drops headlines/claims it can't back.
7. Health-adjacent advice always carries "consult your veterinarian" framing;
   nothing reads as a diagnosis or treatment directive.

### Output contracts (all roles)

- `planner.md` → `{ "relevant": bool, "sections": [{ "heading", "prompt" }] }`
- `writer.md` → plain HTML section (headings `h2`, short paragraphs, lists, one
  `[tw_product_placeholder category="…"]` where a product fits, `[ev:…]` tags inline)
- `qa.md` → `{ "result": "PASS"|"FAIL", "reasons": [string] }`
- `seo.md` → `{ "seo_title": "…50–60 chars…", "meta_description": "…140–155 chars…" }`

Sync note: model bound to each role may change; the prompt text is model-agnostic.