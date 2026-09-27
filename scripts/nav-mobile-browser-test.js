'use strict';
const target = process.argv[2];
const SCRIPT = `(async () => {
  const out = {};
  const btn = document.getElementById('tw-nav-toggle');
  const menu = document.getElementById('tw-primary-menu');
  const expanded = () => btn.getAttribute('aria-expanded');
  const menuDisplay = () => getComputedStyle(menu).display;

  out.initial = { expanded: expanded(), menu: menuDisplay() };

  btn.click();
  out.afterOpen = { expanded: expanded(), menu: menuDisplay() };

  document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
  out.afterEscape = { expanded: expanded(), focusBack: document.activeElement === btn };

  btn.click();
  document.dispatchEvent(new MouseEvent('click', { bubbles: true }));
  out.afterOutsideClick = { expanded: expanded(), menu: menuDisplay() };

  return JSON.stringify(out);
})()`;

(async function main() {
  let res = await fetch(`http://127.0.0.1:${target}/json/list`);
  let targets = await res.json();
  let page = targets.find(t => t.type === 'page' && t.url.includes('preview'));
  if (!page) page = targets.find(t => t.type === 'page');

  const ws = new WebSocket(page.webSocketDebuggerUrl);
  let seq = 0;
  const pending = new Map();
  ws.onmessage = (ev) => {
    const msg = JSON.parse(ev.data);
    if (msg.id && pending.has(msg.id)) { pending.get(msg.id)(msg); pending.delete(msg.id); }
  };
  await new Promise(r => ws.onopen = r);
  function send(method, params = {}) {
    return new Promise(resolve => { const id = ++seq; pending.set(id, resolve); ws.send(JSON.stringify({ id, method, params })); });
  }
  await send('Runtime.enable');
  await send('Emulation.setDeviceMetricsOverride', { width: 375, height: 667, deviceScaleFactor: 1, mobile: true });
  await new Promise(r => setTimeout(r, 600));
  const out = await send('Runtime.evaluate', { expression: SCRIPT, awaitPromise: true, returnByValue: true });
  const infos = await send('Runtime.evaluate', { expression: `({ ae: document.getElementById('tw-nav-toggle').getAttribute('aria-expanded'), ac: document.getElementById('tw-nav-toggle').getAttribute('aria-controls') })`, returnByValue: true });
  console.log(JSON.stringify({ nav: out.result.result.value, aria: infos.result.result.value }));
  ws.close();
})().catch(e => { console.error('TEST-FAIL', e); process.exit(1); });