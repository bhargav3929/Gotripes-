/**
 * Records a real screen walkthrough of the 15 Sep delivery.
 *
 * Playwright drives a real Chromium and records the viewport to webm. A cursor
 * and a step caption are injected into every document (addInitScript survives
 * navigation) so clicks are legible on playback — Playwright's own pointer is
 * not painted into the recording.
 *
 * Run:  node record.mjs
 * Out:  out/<random>.webm  (converted to mp4 by the shell step afterwards)
 */
import { chromium } from 'playwright';
import { readFileSync, writeFileSync } from 'fs';

const VOICE = JSON.parse(readFileSync('voice/durations.json', 'utf8'));
const MARKS = [];

const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';
const OWNER = { email: 'admin@gotrips.ai', pass: 'LocalDemo123!' };
const CARE = { email: 'care.demo@gotrips.ai', pass: 'CareDemo123!' };
const W = 1920, H = 1080;

const OVERLAY = () => {
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

const sleep = ms => new Promise(r => setTimeout(r, ms));

async function main() {
  const browser = await chromium.launch({ args: ['--force-device-scale-factor=1'] });
  const ctx = await browser.newContext({
    viewport: { width: W, height: H },
    recordVideo: { dir: 'out', size: { width: W, height: H } },
    deviceScaleFactor: 1,
    reducedMotion: 'no-preference',
  });
  await ctx.addInitScript(OVERLAY);
  const page = await ctx.newPage();

  let cx = W / 2, cy = H / 2;
  const put = async (x, y) => { await page.evaluate(([a, b]) => window.__wtCursor && window.__wtCursor(a, b), [x, y]); };
  const T0 = Date.now();
  const cap = async (n, t) => {
    await page.evaluate(([a, b]) => window.__wtCap && window.__wtCap(a, b), [n, t]);
    if (n) MARKS.push({ step: n, atMs: Date.now() - T0, dur: VOICE[n] || 0 });
  };
  // Hold the current step until its narration has had room to finish.
  const hold = async (n, extra = 550) => {
    const m = MARKS.filter(x => x.step === n).pop();
    if (!m) return;
    const until = m.atMs + (VOICE[n] || 0) * 1000 + extra;
    const wait = until - (Date.now() - T0);
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
    // Long sidebars put targets below the fold; measuring without scrolling
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

  // Hide only the support launcher and the marketing lead pop-up: they sit in
  // the bottom-left/centre where the step caption goes. The sticky header must
  // stay — it carries the Register Now button this walkthrough clicks.
  const hideChat = async () => {
    await page.evaluate(() => {
      ['gtSupportLauncher', 'gtSupportPanel', 'gotripsLeadPopupOverlay'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
      });
    }).catch(() => {});
  };

  // ---------------------------------------------------------------- 1. home
  await page.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
  await sleep(1500); await hideChat();
  await cap('01', 'The GoTrips homepage');
  await put(cx, cy);
  await sleep(1800);
  await page.mouse.wheel(0, 420); await sleep(1100);
  await page.mouse.wheel(0, -420); await sleep(900);

  // ------------------------------------------- 2. circular registration popup
  await hold('01');
  await cap('02', 'Join as a Partner / Customer');
  await sleep(900);
  await clickSel('#partnerRegisterBtn', { settle: 1500 });
  await hold('02');
  await cap('03', 'The registration window is now circular');
  await sleep(2600);
  await typeIn('#partnerName', 'Priya Nair');
  await typeIn('#partnerEmail', 'priya@example.com');
  await sleep(700);
  await hold('03');
  await cap('04', 'Same three steps, nothing else changed');
  await sleep(1400);
  await clickSel('#partnerCancelBtn', { settle: 1200 });

  // ------------------------------------------------------------ 3. visa page
  await hold('04');
  await cap('05', 'Opening the UAE visa page');
  await sleep(700);
  await page.goto(BASE + '/uaevisa', { waitUntil: 'domcontentloaded' });
  await sleep(1800); await hideChat();
  await hold('05');
  await cap('06', 'The route selector, with the thicker gold ring');
  await sleep(3200);
  await clickSel('.emirate-card', { settle: 1800 });
  await hold('06');
  await cap('07', 'Dubai selected, the form prices from it');
  await sleep(2200);

  // -------------------------------------------------------- 4. manager portal
  await hold('07');
  await cap('08', 'Now the manager portal');
  await sleep(800);
  await page.goto(BASE + '/manager/login', { waitUntil: 'domcontentloaded' });
  await sleep(1400);
  await hold('08');
  await cap('09', 'Signing in as the owner');
  await typeIn('input[name="email"]', OWNER.email, 40);
  await typeIn('input[name="password"]', OWNER.pass, 40);
  await sleep(400);
  await clickSel('button[type="submit"]', { settle: 2600 });

  // ------------------------------------------------------- 5. support + help
  await hold('09');
  await cap('10', 'Customer Care, the ticket queue');
  await sleep(900);
  await page.goto(BASE + '/manager/support', { waitUntil: 'domcontentloaded' });
  await sleep(2200);
  await hold('10');
  await cap('11', 'Create a customer-care login here');
  await sleep(900);
  await clickSel('button:has-text("Workflow settings"), a:has-text("Workflow settings")', { settle: 1500 }).catch(() => {});
  await page.evaluate(() => {
    const f = document.querySelector('form[action*="customer-care"]');
    if (f) f.scrollIntoView({ block: 'center' });
  });
  await sleep(2400);

  await hold('11');
  await cap('12', 'Eleven product help guides');
  await sleep(700);
  await page.goto(BASE + '/manager/help', { waitUntil: 'domcontentloaded' });
  await sleep(2000);
  await page.mouse.wheel(0, 600); await sleep(1600);
  await clickSel('.help-card >> nth=7', { settle: 2200 }).catch(async () => {
    await page.goto(BASE + '/manager/help/customer-support', { waitUntil: 'domcontentloaded' });
    await sleep(1800);
  });
  await hold('12');
  await cap('13', 'Written for someone new to the system');
  await sleep(2600);

  // ------------------------------------------ 6. the restricted care account
  await hold('13');
  await cap('14', 'Signing in as customer-care staff');
  await sleep(800);
  // The sidebar's Log Out is a real POST form; click it like a person would.
  await clickSel('form[action*="manager/logout"] button[type="submit"]', { settle: 2200 });
  await page.waitForURL('**/manager/login', { timeout: 20000 }).catch(() => {});
  await page.waitForSelector('input[name="email"]', { timeout: 20000 });
  await sleep(1000);
  await typeIn('input[name="email"]', CARE.email, 40);
  await typeIn('input[name="password"]', CARE.pass, 40);
  await sleep(400);
  await clickSel('button[type="submit"]', { settle: 2800 });
  await hold('14');
  await cap('15', 'Support Tickets and Help only. Nothing else.');
  await sleep(3600);
  await hold('15');
  await cap('', '');
  await sleep(900);

  await ctx.close();
  await browser.close();
  writeFileSync('out/marks.json', JSON.stringify(MARKS, null, 2));
  console.log('recorded', JSON.stringify(MARKS));
}

main().catch(e => { console.error('FAILED', e); process.exit(1); });
