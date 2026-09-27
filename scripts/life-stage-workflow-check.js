'use strict';
// Life Stage layer check against the canonical n8n workflow (v6).
// Asserts, without any live dependencies:
//   1. The Form Trigger has a "Life Stage" dropdown with exactly puppy/adult/senior.
//   2. A "Resolve Life Stage Term ID" node resolves the taxonomy via the REST base.
//   3. Create Draft sends "life_stage" as a numeric term-ID array alongside categories.
//   4. The kill-switch fan-out reaches the resolver and the create/upload chain is intact.
const fs = require('fs');
const path = require('path');
const wfPath = path.join(__dirname, '..', 'n8n', 'tailwell-end-to-end.json');
const wf = JSON.parse(fs.readFileSync(wfPath, 'utf8'));

const results = [];
let failed = false;
const check = (name, ok, detail) => {
  results.push({ name, ok, detail });
  if (!ok) failed = true;
};

const byName = {};
for (const n of wf.nodes) byName[n.name] = n;
const names = wf.nodes.map(n => n.name);

check('workflow version', /v6/.test(wf.name), `name = "${wf.name}"`);

const form = byName['Form Trigger - Topic Input'];
const fields = (form && form.parameters && form.parameters.formFields && form.parameters.formFields.values) || [];
const lsField = fields.find(f => f.fieldLabel === 'Life Stage');
check('Life Stage dropdown on form', !!lsField && lsField.fieldType === 'dropdown', lsField ? `fieldType = ${lsField.fieldType}` : 'missing');
const lsOptions = (lsField && lsField.fieldOptions && lsField.fieldOptions.values || []).map(o => o.option);
check('Life Stage options are puppy/adult/senior',
  JSON.stringify(lsOptions) === JSON.stringify(['puppy', 'adult', 'senior']),
  lsOptions.join(', '));

const resolver = byName['Resolve Life Stage Term ID'];
check('Resolve Life Stage Term ID node exists', !!resolver, resolver ? resolver.type : 'missing');
check('Resolver targets life_stage REST base',
  !!resolver && resolver.parameters && /\/wp-json\/wp\/v2\/life_stage\?slug=/.test(resolver.parameters.url),
  resolver && resolver.parameters ? resolver.parameters.url : 'missing');
check('Resolver reads the Life Stage field',
  !!resolver && resolver.parameters && resolver.parameters.url.includes("['Life Stage']"),
  'references $node[Form Trigger].json[\'Life Stage\']');

const create = byName['WordPress - Create Draft (category + life_stage + Yoast meta)'];
check('Create Draft node renamed & present', !!create, create ? create.type : 'missing');
const body = create && create.parameters ? create.parameters.bodyParametersJson : '';
check('Create Draft sends life_stage term-ID array',
  typeof body === 'string' && /"life_stage":\s*\[\s*\$node\['Resolve Life Stage Term ID'\]\.json\[0\]\.id\s*\]/.test(body),
  'catches the resolved term id');
check('Create Draft still sends categories',
  typeof body === 'string' && /"categories":\s*\[\s*\$node\['Resolve Silo Category ID'\]\.json\[0\]\.id\s*\]/.test(body),
  'catches the resolved category id');
check('Create Draft sends Yoast meta in same call',
  typeof body === 'string' && body.includes('_yoast_wpseo_title') && body.includes('_yoast_wpseo_focuskw'),
  'meta block present');
check('Create Draft goes through the WordPress Basic Auth credential',
  !!create && create.parameters && create.parameters.genericAuthType === 'httpBasicAuth',
  create && create.parameters ? create.parameters.genericAuthType : 'missing');

const killEdge = (wf.connections['Kill Switch - Autopilot Enabled?'] || { main: [[]] }).main[0] || [];
check('Kill switch reaches Resolve Life Stage Term ID',
  killEdge.some(r => r.node === 'Resolve Life Stage Term ID'),
  killEdge.map(r => r.node).join(', '));
const seoOut = wf.connections['Generate SEO Title + Meta Description'] || { main: [[]] };
check('SEO -> Create Draft edge intact',
  (seoOut.main[0] || []).some(r => r.node === 'WordPress - Create Draft (category + life_stage + Yoast meta)'),
  (seoOut.main[0] || []).map(r => r.node).join(', '));
const createOut = wf.connections[create.name] || { main: [[]] };
check('Create Draft -> Upload edge intact',
  (createOut.main[0] || []).some(r => r.node === 'WordPress - Upload + Attach Image'),
  (createOut.main[0] || []).map(r => r.node).join(', '));

for (const r of results) {
  console.log(`${r.ok ? 'PASS' : 'FAIL'} ${r.name} : ${r.detail}`);
}
console.log(`\n${results.filter(r => r.ok).length}/${results.length} checks passed (workflow v6, ${names.length} nodes)`);
process.exit(failed ? 1 : 0);