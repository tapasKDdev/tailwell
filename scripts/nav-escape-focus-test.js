'use strict';
const target = process.argv[2];
const SCRIPT = `(() => {
  const btn = document.getElementById('tw-nav-toggle');
  btn.focus();
  btn.click();
  const openState = btn.getAttribute('aria-expanded');
  document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
  return JSON.stringify({ openAfterClick: openState, closedAfterEscape: btn.getAttribute('aria-expanded'), focusReturned: document.activeElement === btn });
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
  await new Promise(r => setTimeout(r, 600));
  const out = await send('Runtime.evaluate', { expression: SCRIPT, returnByValue: true });
  console.log(out.result.result.value);
  ws.close();
})().catch(e => { console.error('FAIL', e); process.exit(1); });