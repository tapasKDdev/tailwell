'use strict';
// Fixture test for the evidence-context snippet used in docs/evidence-handoff.md.
// Keep in sync with the n8n Code-node snippet body.
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

const results = [];

// Case 1: no evidence -> must suppress health claims.
const ctx0 = buildEvidenceContext([]);
results.push({
  name: 'no evidence -> suppression block',
  pass: ctx0.includes('NO EVIDENCE SUPPLIED') && ctx0.includes('Do NOT write'),
});

// Case 2: valid evidence -> ids, tier, and supported/refuted predicates present.
const ev = [{
  source_id: 'TWP-EVID-0001',
  publisher: 'Example Pubs',
  title: 'Joint care basics',
  tier: 's1',
  claims: [
    { predicate: 'Exercise helps older joints.', supported: true },
    { predicate: 'Supplements cure hip dysplasia.', supported: false },
  ],
}];
const ctx1 = buildEvidenceContext(ev);
results.push({
  name: 'valid evidence -> ids/tier/supported/refuted all surfaced',
  pass: ctx1.includes('[ev:...]')
    && ctx1.includes('TWP-EVID-0001')
    && ctx1.includes('tier s1')
    && ctx1.includes('supports: Exercise helps older joints.')
    && ctx1.includes('refutes: Supplements cure hip dysplasia.'),
});

// Case 3: invalid object shape -> throws (never silently cites garbage).
let threw = false;
try { buildEvidenceContext([{ source_id: 'TWP-EVID-0002' }]); } catch (e) { threw = true; }
results.push({
  name: 'invalid evidence throws',
  pass: threw,
});

let ok = true;
for (const r of results) { ok = ok && r.pass; console.log((r.pass ? 'PASS' : 'FAIL') + ' - ' + r.name); }
console.log(ok ? '\nALL TESTS PASS' : '\nTEST FAILURES');
process.exit(ok ? 0 : 1);