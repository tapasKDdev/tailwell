'use strict';
const target = process.argv[2];
const SCRIPT = `(() => {
  const de = document.documentElement;
  const vw = de.clientWidth;
  const hScroll = de.scrollWidth - de.clientWidth;
  const scrollLeft = de.scrollLeft;
  const btn = document.getElementById('tw-nav-toggle');
  const menu = document.getElementById('tw-primary-menu');
  const brand = document.querySelector('.tw-brand');
  const r = (el) => { const b = el.getBoundingClientRect(); return { left: Math.round(b.left), right: Math.round(b.right), top: Math.round(b.top), w: Math.round(b.width) }; };
  const open = btn.getAttribute('aria-expanded') === 'true';
  const bg = () => getComputedStyle(document.querySelector('.site-header')).backgroundColor;
  return JSON.stringify({
    vw, hScroll, scrollLeft, open,
    btnEl: r(btn), menuEl: r(menu), brandEl: r(brand),
    menuSpansViewport: open && r(menu).left === 0 && r(menu).right === vw,
    noOverflow: hScroll === 0,
    oneRow: r(brand).top === r(btn).top,
    headerBg: bg(),
  });
})()`;

(async function main() {
  const res = await fetch(`http://127.0.0.1:${target}/json/list`);
  const targets = await res.json();
  const page = targets.find(x => x.type === 'page' && x.url.includes('preview'));
  const ws = new WebSocket(page.webSocketDebuggerUrl);
  let seq = 0;
  const pending = new Map();
  ws.onmessage = (ev) => { const m = JSON.parse(ev.data); if (m.id && pending.has(m.id)) { pending.get(m.id)(m); pending.delete(m.id); } };
  await new Promise(r => ws.onopen = r);
  const send = (m, p = {}) => new Promise(res => { const id = ++seq; pending.set(id, res); ws.send(JSON.stringify({ id, method: m, params: p })); });
  await send('Runtime.enable');
  await send('Page.enable');

  async function meas(w, h, open) {
    await send('Emulation.setDeviceMetricsOverride', { width: w, height: h, deviceScaleFactor: 1, mobile: true });
    await send('Page.navigate', { url: page.url });
    await new Promise(r => setTimeout(r, 700));
    if (open) {
      await send('Runtime.evaluate', { expression: `document.getElementById('tw-nav-toggle').click()` });
      await new Promise(r => setTimeout(r, 250));
    }
    const out = await send('Runtime.evaluate', { expression: SCRIPT, returnByValue: true });
    return JSON.parse(out.result.result.value);
  }

  const results = {};
  results['375 closed'] = await meas(375, 667, false);
  results['375 open'] = await meas(375, 667, true);
  results['320 open'] = await meas(320, 568, true);
  results['900 open'] = await meas(900, 700, true);
  console.log(JSON.stringify(results));
  ws.close();
})().catch(e => { console.error('FAIL', e); process.exit(1); });