/**
 * Shared recorder plumbing for the GoTrips walkthrough videos.
 *
 * Playwright does not paint its own pointer into a recording, so an overlay
 * cursor, a click ripple and a step caption are injected into every document
 * (addInitScript survives navigation). `attach(page)` returns the helpers that
 * drive them: moveTo/clickSel/typeIn/cap/hold.
 */
export const sleep = ms => new Promise(r => setTimeout(r, ms));

export const OVERLAY = () => {
  if (window.__wt) return;
  window.__wt = true;
  const add = () => {
    if (document.getElementById('wt-cursor')) return;
    const c = document.createElement('div');
    c.id = 'wt-cursor';
    const cap = document.createElement('div');
    cap.id = 'wt-cap';
    const st = document.createElement('style');
    st.textContent = `
      #wt-cursor{position:fixed;left:0;top:0;width:26px;height:26px;z-index:2147483647;
        pointer-events:none;transform:translate(-4px,-2px);
        background:radial-gradient(circle at 34% 30%,#fff 0 34%,#F0CF6B 36% 62%,#8F6A16 64% 100%);
        border-radius:52% 52% 56% 8%;box-shadow:0 3px 14px rgba(0,0,0,.65),0 0 0 2px rgba(0,0,0,.35);
        opacity:0;transition:opacity .25s}
      #wt-cursor.on{opacity:1}
      #wt-ripple{position:fixed;z-index:2147483646;pointer-events:none;width:14px;height:14px;
        margin:-7px 0 0 -7px;border-radius:50%;border:3px solid #F0CF6B;opacity:0}
      @keyframes wtR{0%{transform:scale(.4);opacity:.95}100%{transform:scale(5.2);opacity:0}}
      #wt-ripple.go{animation:wtR .55s ease-out}
      #wt-cap{position:fixed;left:44px;bottom:42px;z-index:2147483647;pointer-events:none;
        display:flex;align-items:center;gap:16px;padding:16px 28px;border-radius:100px;
        background:rgba(8,7,6,.93);border:1px solid rgba(212,175,55,.5);
        box-shadow:0 18px 50px rgba(0,0,0,.6);opacity:0;transform:translateY(14px);
        transition:opacity .4s ease,transform .4s ease;
        font-family:"Avenir Next","Helvetica Neue",system-ui,sans-serif}
      #wt-cap.on{opacity:1;transform:translateY(0)}
      #wt-cap .n{font-family:Menlo,monospace;font-size:19px;letter-spacing:.14em;color:#D4AF37}
      #wt-cap .t{font-size:27px;font-weight:600;color:#F4EBD0;letter-spacing:-.01em}`;
    const rip = document.createElement('div');
    rip.id = 'wt-ripple';
    document.documentElement.appendChild(st);
    document.documentElement.appendChild(rip);
    document.documentElement.appendChild(c);
    document.documentElement.appendChild(cap);
  };
  if (document.documentElement) add();
  document.addEventListener('DOMContentLoaded', add);

  window.__wtCursor = (x, y) => {
    const c = document.getElementById('wt-cursor');
    if (c) { c.style.left = x + 'px'; c.style.top = y + 'px'; c.classList.add('on'); }
  };
  window.__wtRipple = (x, y) => {
    const r = document.getElementById('wt-ripple');
    if (!r) return;
    r.style.left = x + 'px'; r.style.top = y + 'px';
    r.classList.remove('go'); void r.offsetWidth; r.classList.add('go');
  };
  window.__wtCap = (n, t) => {
    const e = document.getElementById('wt-cap');
    if (!e) return;
    if (!t) { e.classList.remove('on'); return; }
    e.innerHTML = '<span class="n">' + n + '</span><span class="t">' + t + '</span>';
    e.classList.add('on');
  };
};


export function attach(page, { W, H, voice = {}, marks = [] } = {}) {
  let cx = W / 2, cy = H / 2;
  const T0 = Date.now();

  const put = async (x, y) =>
    page.evaluate(([a, b]) => window.__wtCursor && window.__wtCursor(a, b), [x, y]);

  const cap = async (n, t) => {
    await page.evaluate(([a, b]) => window.__wtCap && window.__wtCap(a, b), [n, t]);
    if (n) marks.push({ step: n, atMs: Date.now() - T0, dur: voice[n] || 0 });
  };

  // Hold the current step until its narration line has had room to finish.
  const hold = async (n, extra = 550) => {
    const m = marks.filter(x => x.step === n).pop();
    if (!m) return;
    const wait = m.atMs + (voice[n] || 0) * 1000 + extra - (Date.now() - T0);
    if (wait > 0) await sleep(wait);
  };

  async function moveTo(x, y, steps = 26) {
    const sx = cx, sy = cy;
    for (let i = 1; i <= steps; i++) {
      const p = i / steps;
      const e = p < 0.5 ? 2 * p * p : 1 - Math.pow(-2 * p + 2, 2) / 2; // easeInOutQuad
      const nx = sx + (x - sx) * e, ny = sy + (y - sy) * e;
      await page.mouse.move(nx, ny);
      await put(nx, ny);
      await sleep(16);
    }
    cx = x; cy = y;
  }

  async function clickSel(sel, { nudge = [0, 0], settle = 900 } = {}) {
    const el = page.locator(sel).first();
    await el.waitFor({ state: 'visible', timeout: 15000 });
    // Long pages put targets below the fold; measuring without scrolling first
    // yields off-screen coordinates and the click silently misses.
    await el.scrollIntoViewIfNeeded().catch(() => {});
    await sleep(320);
    const b = await el.boundingBox();
    if (!b) throw new Error('no box for ' + sel);
    const x = Math.round(b.x + b.width / 2 + nudge[0]);
    const y = Math.round(b.y + b.height / 2 + nudge[1]);
    await moveTo(x, y);
    await sleep(260);
    await page.evaluate(([a, b2]) => window.__wtRipple && window.__wtRipple(a, b2), [x, y]);
    await sleep(130);
    await page.mouse.click(x, y);
    await sleep(settle);
  }

  async function typeIn(sel, text, delay = 55) {
    const el = page.locator(sel).first();
    const b = await el.boundingBox();
    if (b) { await moveTo(Math.round(b.x + 30), Math.round(b.y + b.height / 2)); await sleep(180); }
    await el.click();
    await el.type(text, { delay });
    await sleep(320);
  }

  // Only the support launcher and the marketing lead pop-up are hidden: they
  // sit where the step caption goes. The sticky header must stay.
  const hideChrome = async () =>
    page.evaluate(() => {
      ['gtSupportLauncher', 'gtSupportPanel', 'gotripsLeadPopupOverlay'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
      });
    }).catch(() => {});

  return { cap, hold, moveTo, clickSel, typeIn, put, hideChrome, marks, cursor: () => [cx, cy] };
}
