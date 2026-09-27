'use strict';
const target = process.argv[2];
const SCRIPT = `(() => {
  const menu = document.getElementById('tw-primary-menu');
  const chain = [];
  let el = menu;
  while (el && el !== document.documentElement) {
    const cs = getComputedStyle(el);
    const b = el.getBoundingClientRect();
    chain.push({ cls: el.className || el.tagName, pos: cs.position, w: Math.round(b.width), left: Math.round(b.left), right: Math.round(b.right), mrgnL: cs.marginLeft, mrgnR: cs.marginRight, padL: cs.paddingLeft, padR: cs.paddingRight, isOffsetParent: el === menu.offsetParent });
    el = el.parentNode;
  }
  return JSON.stringify({
    menuW: Math.round(menu.getBoundingClientRect().width),
    offsetParent: menu.offsetParent ? menu.offsetParent.className : null,
    htmlW: document.documentElement.clientWidth,
    bodyW: document.body.clientWidth,
    bodyScrollW: document.body.scrollWidth,
    chain,
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
  await send('Emulation.setDeviceMetricsOverride', { width: 375, height: 667, deviceScaleFactor: 1, mobile: true });
  await send('Page.navigate', { url: page.url });
  await new Promise(r => setTimeout(r, 700));
  await send('Runtime.evaluate', { expression: `document.getElementById('tw-nav-toggle').click()` });
  await new Promise(r => setTimeout(r, 250));
  const out = await send('Runtime.evaluate', { expression: SCRIPT, returnByValue: true });
  console.log(out.result.result.value);
  ws.close();
})().catch(e => { console.error('FAIL', e); process.exit(1); });