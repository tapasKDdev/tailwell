# Evidence Handoff — Feeding Research into the Workflow

The writer and QA prompts require `[ev:TWP-EVID-####]` tags backed by real
evidence — but the n8n workflow does not fetch evidence itself (research is a
human/agent stage, `research/README.md`). This page is the exact bridge: a
copy-paste Code-node + what to paste where.

## The snippet (verified — use verbatim)

```js
// Paste into a Code node OR use to generate the context block for your prompts.
// input: evidence = array of parsed evidence objects (one per research/evidence-*.json)
function buildEvidenceContext(evidence) {
  if (!Array.isArray(evidence) || evidence.length === 0) {
    return 'NO EVIDENCE SUPPLIED. Do NOT write health/safety/nutrition/statistical sentences. Mark every product spec unverified.';
  }
  const lines = [];
  for (const e of evidence) {
    if (!e.source_id || !e.publisher || !e.title || !e.tier || !Array.isArray(e.claims)) {
      throw new Error('Invalid evidence object: ' + (e && e.source_id));
    }
    const supported = e.claims.filter(c => c.supported === true).map(c => c.predicate);
    const refuted   = e.claims.filter(c => c.supported === false).map(c => c.predicate);
    lines.push('EVIDENCE ' + e.source_id + ' (tier ' + e.tier + ', ' + e.publisher + '): ' + e.title);
    if (supported.length) lines.push('  supports: ' + supported.join('; '));
    if (refuted.length)   lines.push('  refutes: ' + refuted.join('; '));
  }
  return 'Below are the ONLY sources you may cite with [ev:...] tags. Never invent an evidence id; never cite anything else (Wikipedia is NEVER evidence).\n' + lines.join('\n');
}
```

Fixture-tested by `scripts/evidence-context-test.js` (empty/invalid/tier-tagged cases).

## Where the evidence array comes from

- Researcher stage output: one object per `research/evidence-*.json` (schema
  `research/evidence-schema.md`), published/approved rows only.
- Any `TWP-EVID-####` referenced by the article must resolve to a file here — that
  is enforced for the product database by `scripts/validate-evidence.ps1`.

## Wiring it into n8n (two spots, no graph change)

1. **Write Section** node: append the context block to the model message. Concretely
   add a text parameter `extra_context` (or append to the node's Content field)
   containing the snippet output. The writer then emits `[ev:...]` only from these ids.
2. **QA / Fact Check** node: append the same block so the rubric can verify that
   every health claim's tag exists in the list (rule 1), is tier ≥ s2 for health
   claims (s3 = product specs only), and matches the claim predicate.

If evidence is empty, QA **fails** the draft for any health claim — that is correct
behavior until a real researcher run supplies evidence (the pipeline surfaces gaps,
it never invents them).

## Idempotence rule

Shortest-loop verification before a run: `node scripts/evidence-context-test.js`
(unit) and `scripts/validate-evidence.ps1` (schema + id resolution). Only then
trust the produced tags in a draft.