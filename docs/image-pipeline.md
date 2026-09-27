# Featured-Image Pipeline (Phase 10 — design, not yet wired)

Every post gets **one** safe, generated featured image. This doc is the wiring plan
for completing the `GEMINI_IMAGE_MODEL` stage; the pipeline is deliberately a **leaf
node** today so drafts and alerts still flow while the human fills images manually.

## Safe-image rules (always)

- **Generic illustration only.** A calm happy dog in a realistic/studio style, tied
  to the topic (adult/senior dog, joint care, food bowl, harness, grooming, dental).
- **Never** depict real brands, product packaging with logos, price tags, medallions,
  or recognizable commercial goods (trademark + affiliate-compliance risk).
- **Never** show medical tools/imagery that implies a diagnosis (X-rays, syringes,
  pills on scales) — drops straight into invented-medical-claims territory.
- No on-image text, or at most a short neutral word like "Care" — Gemini images
  garble text and the failure looks unprofessional.
- Keep the model's default benign content policy untouched (family-friendly blog).

## Intended flow (what to wire when you have a working Gemini key)

```
QA passes
  └─ Generate Featured Image (Gemini REST, auth=HTTP Query Auth)
       model in URL: https://generativelanguage.googleapis.com/v1beta/models/{GEMINI_IMAGE_MODEL}:generateContent
       body: { contents: [{ parts: [{ text: prompt }] }] }
       response inlineData.base64 (image/png|jpg)
  └─ Code node "Image to Binary" (keeps images out of the prompt chain):
       decodeInlineDataToBinary(inlineData) → items[0].binary (and record mimeType)
  └─ WordPress - Upload + Attach Image (n8n node, media=upload, binaryData=true,
       dataPropertyName=binary, postId = draft id)
  └─ (media alt/caption) — the node sets alt; add a small Code node to PATCH
       /wp-json/wp/v2/media/{id} with alt_text + caption = article keyword/title
       so the "featured" alt is never empty.
```

The generated image text prompt should combine the generated topic/title with the
safe rules above, e.g. for the joint-supplement article: _"Warm studio illustration
of a happy senior golden retriever sitting beside a food bowl, soft light, web
article hero, no text, no logos, no products, family-safe."_

## Alt-text & caption rule

- `alt_text` = `{Article keyword} — healthy dogs`-style summary (never "dog", never
  the filename). Set on the media object; the theme's `the_post_thumbnail` uses it.
- `caption` = the article title (maps to `_wp_attachment_image_alt`/caption dialog).
- SEO/accessibility auditor checks alt is non-empty on every published post.

## Why it stays manual for now

- Image generation is blocked here because it requires the live Gemini key + a
  content-policy judgement call on the binary, and (more importantly) **this repo
  has no vision capability** — a blind pipeline cannot verify an image is
  on-topic/safe, so publishing an auto-drawn image is gated on human review by
  design. Wire the flow above, keep the human in the loop, and only autonomous-run
  after several manually-vetted images look right.