# Evidence Object — Schema

A single JSON object records one retrieved source and the claim predicates it
supports. `source_id` values are referenced from article copy (`[ev:…]`) and the
product database (`source_id` column).

```json
{
  "$schema": "https://json-schema.org/draft-07/schema#",
  "$id": "https://tailwell.example.com/research/evidence-schema",
  "title": "TailWell evidence object",
  "type": "object",
  "required": ["source_id", "publisher", "title", "url", "type", "tier", "retrieved_at", "claims"],
  "additionalProperties": false,
  "properties": {
    "source_id":  { "type": "string", "pattern": "^TWP-EVID-\\d{4}$" },
    "title":      { "type": "string" },
    "url":        { "type": "string", "format": "uri", "pattern": "^https://" },
    "publisher":  { "type": "string" },
    "type":       { "enum": ["peer-reviewed study", "regulatory document", "veterinary-university resource", "national veterinary organization", "manufacturer labeling", "news article"] },
    "publication_date": { "type": "string", "format": "date" },
    "retrieved_at":     { "type": "string", "format": "date" },
    "tier":       { "enum": ["s1", "s2", "s3"] },
    "notes":      { "type": "string" },
    "claims": {
      "type": "array",
      "items": {
        "type": "object",
        "required": ["predicate", "supported"],
        "properties": {
          "predicate": { "type": "string", "description": "The exact claim this source is evidence for (tight paraphrase)." },
          "supported": { "type": "boolean", "description": "False = this source REFUTES the predicate (still recorded, e.g. for balance)." },
          "quote_hint": { "type": "string" }
        }
      }
    }
  }
}
```

## Tier meaning (from `research/README.md`)

| Tier | Source | Can support |
|---|---|---|
| `s1` | Peer-reviewed / regulatory (FDA-CVM, EPA, AAFCO) | Medical, safety, statistical claims |
| `s2` | Veterinary-university / national vet org (AVMA, AAHA) | Medical and practice advice |
| `s3` | Manufacturer labeling/claims | Product specs & features only — never medical benefits |
| — | Blogs, forums, Wikipedia, influencer content | **Never evidence** |

## Validation

`research/` is validated by `scripts/validate-evidence.ps1` (added in Phase 13): every
`source_id` pattern, URL scheme, tier enum, claim array shape, and date format, and
cross-checks that every `TWP-EVID-####` referenced by the product database exists.