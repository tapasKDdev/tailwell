'use strict';
// End-to-end test of the Parse Product CSV + Insert Real Affiliate Products logic.
function parseNodeRun($json) {
  const text = String($json.body ?? $json.data ?? $json).replace(/^\uFEFF/, '');
  const lines = text.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
  const header = lines.shift().split(',').map(h => h.trim());
  const rows = [];
  for (const line of lines) {
    const cells = [];
    let cur = '', q = false;
    for (let i = 0; i < line.length; i++) {
      const c = line[i];
      if (q) {
        if (c === '"') { if (line[i + 1] === '"') { cur += '"'; i++; } else q = false; }
        else cur += c;
      } else if (c === '"') q = true;
      else if (c === ',') { cells.push(cur); cur = ''; }
      else cur += c;
    }
    cells.push(cur);
    const row = {};
    header.forEach((h, i) => row[h] = (cells[i] ?? '').trim());
    rows.push(row);
  }
  const products = rows.filter(p => p.status === 'approved' && String(p.enabled).toLowerCase() === 'true');
  return [{ json: { products, parsed_count: products.length, total_rows: rows.length } }];
}

function insertNodeRun(merged) {
  const articleHtml = merged.section_html.join('\n\n');
  const products = merged.products || [];
  const silo = merged.silo || 'wellness-health';
  const home = String(merged.home || '').replace(/\/+$/, '');
  const placeholderRegex = /\[tw_product_placeholder category="([a-z-]+)"\]/g;
  let unmatched = [];
  const finalHtml = articleHtml.replace(placeholderRegex, (match, category) => {
    const approved = products.filter(p => p.category === category && p.status === 'approved');
    if (approved.length === 0) { unmatched.push(category); return '<!-- NO APPROVED PRODUCT FOUND for category: ' + category + ' - manual fill required before publish -->'; }
    const p = approved[0];
    const safe = (s) => String(s).replace(/"/g, '&quot;');
    return '[tw_product name="' + safe(p.name) + '" why="' + safe(p.why) + '" price="' + safe(p.price) + '" url="' + safe(p.url) + '" brand="' + safe(p.brand || '') + '" image="' + safe(p.image_url || '') + '" category="' + safe(p.category || '') + '" partner="' + safe(p.affiliate_partner || '') + '" vet="' + safe(p.vet_verified || '') + '"]';
  });
  const siloLink = home ? home + '/category/' + silo + '/' : '/category/' + silo + '/';
  const internalBlock = '<hr class="tw-internal-sep"><p class="tw-internal-links"><a href="' + siloLink + '">More ' + silo.replace(/-/g, ' ') + ' guides</a> &middot; <a href="' + (home || '/') + '">Browse all of TailWell</a></p>';
  return [{ json: { final_html: finalHtml + internalBlock, unmatched_categories: unmatched, has_unmatched: unmatched.length > 0, internal_links: 2 } }];
}

const results = [];

// Case 1: BOM + quoted commas + only approved+enabled survive.
const csvBom = '\uFEFFid,name,brand,category,silo,why,price,url,status,enabled\n' +
  'T01,"Joint, Deluxe",VetriScience,joint-supplement,wellness-health,"Why, we love it",$39.95,https://x.test/a,approved,true\n' +
  'T02,Second,Other,fresh-food,food-nutrition,,,https://x.test/b,pending,true\n' +
  'T03,Third,Other,insurance,senior-special-needs,,,https://x.test/c,approved,false\n';
const parsed1 = parseNodeRun({ body: csvBom })[0].json;
results.push({
  name: 'parse: BOM stripped, quotes handled, approved+enabled only',
  pass: parsed1.parsed_count === 1 && parsed1.total_rows === 3 && parsed1.products[0].id === 'T01' && parsed1.products[0].why === 'Why, we love it' && parsed1.products[0].name === 'Joint, Deluxe',
  got: parsed1,
});

// Case 2: unmatched placeholder -> comment + has_unmatched, QA should fail.
const merged2 = { section_html: ['<p>Use <a href="">[tw_product_placeholder category="dental"]</a> daily.</p>'], products: [] };
const ins2 = insertNodeRun(merged2)[0].json;
results.push({
  name: 'insert: no approved product -> visible comment + unmatched flag',
  pass: ins2.has_unmatched && ins2.unmatched_categories.includes('dental') && ins2.final_html.includes('NO APPROVED PRODUCT FOUND for category: dental'),
});

// Case 3: approved product matched -> real shortcode, no guess.
const parsed3 = parseNodeRun({ body: csvBom })[0].json;
const merged3 = { section_html: ['<p>Try [tw_product_placeholder category="joint-supplement"] today.</p>'], products: parsed3.products };
const ins3 = insertNodeRun(merged3)[0].json;
results.push({
  name: 'insert: approved product swapped in with real attributes',
  pass: !ins3.has_unmatched && ins3.final_html.includes('[tw_product name="Joint, Deluxe"') && ins3.final_html.includes('https://x.test/a')
    && ins3.final_html.includes('brand="VetriScience"') && ins3.final_html.includes('category="joint-supplement"')
    && !ins3.final_html.includes('undefined'),
});

// Case 4: v5 internal links appended (silo hub + home), flagged for QA rule 6.
const merged4 = { section_html: ['<p>Body.</p>'], products: [], silo: 'joint-supplement', home: 'https://tw.test' };
const ins4 = insertNodeRun(merged4)[0].json;
results.push({
  name: 'insert: internal silo+home links appended, count reported',
  pass: ins4.internal_links === 2
    && ins4.final_html.includes('<hr class="tw-internal-sep">')
    && ins4.final_html.includes('https://tw.test/category/joint-supplement/')
    && ins4.final_html.includes('href="https://tw.test"'),
});

let ok = true;
for (const r of results) { ok = ok && r.pass; console.log((r.pass ? 'PASS' : 'FAIL') + ' - ' + r.name + (r.pass ? '' : ' got=' + JSON.stringify(r.got ?? r))); }
console.log(ok ? '\nALL TESTS PASS' : '\nTEST FAILURES');
process.exit(ok ? 0 : 1);