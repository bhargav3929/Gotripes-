/**
 * Before/after screen recording of the round-2 changes (Amer, 18 Sep 2026).
 *
 * Two real browser sessions on the local site: one with the views as they
 * were before round 2 (./state.sh before), one as committed in e1f3c35
 * (./state.sh after). Each narrated segment records its start/end so the
 * final cut can interleave them: before, after, before, after.
 *
 * No text is drawn on the page. The pointer shows where to look: it traces
 * each ring, rests on each changed control, and clicks where the client
 * would click in the meeting.
 *
 * Run:  ./state.sh before && node record.mjs before
 *       ./state.sh after  && node record.mjs after
 * Out:  out/<state>.webm + out/<state>-marks.json
 */
import { chromium } from 'playwright';
import { readFileSync, writeFileSync, renameSync, mkdirSync } from 'fs';
import { OVERLAY, attach, sleep } from '../walkthrough/wt-lib.mjs';

const STATE = process.argv[2];
if (!['before', 'after'].includes(STATE)) throw new Error('usage: node record.mjs before|after');
const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';
const W = 1920, H = 1080;
const VOICE = JSON.parse(readFileSync('voice/durations.json', 'utf8'));
const LEAD = 0.3;   // seconds of picture before the voice starts
const TAIL = 0.8;   // seconds held after the voice ends

mkdirSync('out/raw', { recursive: true });
const browser = await chromium.launch({ args: ['--force-device-scale-factor=1'] });
const ctx = await browser.newContext({
  viewport: { width: W, height: H }, deviceScaleFactor: 1,
  recordVideo: { dir: 'out/raw', size: { width: W, height: H } },
});
await ctx.addInitScript(OVERLAY);
// The marketing lead pop-up opens on a timer and swallows clicks; it stays
// shut once it has been shown in this browser session.
await ctx.addInitScript(() => {
  try { sessionStorage.setItem('gotripsLeadPopupShown', 'true'); } catch (e) {}
});
const page = await ctx.newPage();
const T0 = Date.now();
const { put } = attach(page, { W, H });

// Pointer moves are driven by the wall clock, not by step count: under
// recording each mouse event costs ~100ms, so step-based moves ran 3x long.
let px = W / 2, py = H / 2;
const cursor = () => [px, py];
async function moveTo(x, y, ms = 650) {
  const sx = px, sy = py, t0 = Date.now();
  for (;;) {
    const p = Math.min(1, (Date.now() - t0) / ms);
    const e = p < 0.5 ? 2 * p * p : 1 - Math.pow(-2 * p + 2, 2) / 2;
    const nx = sx + (x - sx) * e, ny = sy + (y - sy) * e;
    await page.mouse.move(nx, ny); await put(nx, ny);
    if (p >= 1) break;
  }
  px = x; py = y;
}
async function clickSel(sel, { settle = 900 } = {}) {
  const el = page.locator(sel).first();
  await el.waitFor({ state: 'visible', timeout: 15000 });
  const b = await el.boundingBox();
  const x = Math.round(b.x + b.width / 2), y = Math.round(b.y + b.height / 2);
  await moveTo(x, y);
  await sleep(200);
  await page.evaluate(([a, c]) => window.__wtRipple && window.__wtRipple(a, c), [x, y]);
  await sleep(120);
  await page.mouse.click(x, y);
  await sleep(settle);
}
async function typeIn(sel, text, delay = 40) {
  const b = await page.locator(sel).first().boundingBox();
  // Aim past the left edge: the phone field's country flag sits over it.
  const x = Math.round(b.x + b.width * 0.62), y = Math.round(b.y + b.height / 2);
  await moveTo(x, y, 450);
  await page.mouse.click(x, y);
  await page.keyboard.type(text, { delay });
  await sleep(200);
}
const now = () => (Date.now() - T0) / 1000;

const segs = [];
let segT = 0;
const at = async s => { const w = (segT + LEAD + s - now()) * 1000; if (w > 0) await sleep(w); };
async function seg(id, fn) {
  segT = now();
  await fn();
  await at(VOICE[id] + TAIL);
  segs.push({ id, start: +segT.toFixed(3), end: +now().toFixed(3) });
  console.log(STATE, id, segs.at(-1));
}

const box = async sel => {
  const b = await page.locator(sel).first().boundingBox();
  if (!b) throw new Error('no box ' + sel);
  return { cx: b.x + b.width / 2, cy: b.y + b.height / 2, w: b.width, h: b.height };
};
// Trace a ring with the pointer: start at its left edge, go once round.
async function traceRing(cx, cy, r, ms = 2600) {
  await moveTo(Math.round(cx - r), Math.round(cy), 500);
  const t0 = Date.now();
  for (;;) {
    const p = Math.min(1, (Date.now() - t0) / ms);
    const a = Math.PI + p * Math.PI * 2;
    const x = cx + r * Math.cos(a), y = cy + r * Math.sin(a);
    await page.mouse.move(x, y); await put(x, y);
    if (p >= 1) break;
  }
  px = cx - r; py = cy;
}
const go = async path => {
  await page.goto(BASE + path, { waitUntil: 'domcontentloaded' });
  await sleep(2600);
  const [x, y] = cursor(); await put(x, y);
};
const openPopup = async () => {
  await page.evaluate(() => window.scrollTo(0, 0));
  await clickSel('#partnerRegisterBtn', { settle: 1500 });
};
const closePopup = async () => {
  await page.locator('#partnerCancelBtn').click().catch(() => {});
  await sleep(900);
};

if (STATE === 'before') {
  await go('/uaevisa');
  await seg('02', async () => {
    await moveTo(W / 2, H / 2 + 260, 900);
    await at(7.4);
    const d = await box('.emirate-modal');
    await traceRing(d.cx, d.cy, d.w * 0.4575, 2800);
    await at(11.2);
    const cards = page.locator('.emirate-card');
    const a = await cards.nth(0).boundingBox(), b = await cards.nth(1).boundingBox();
    await moveTo(Math.round(a.x + 6), Math.round(a.y + a.height / 2));
    await sleep(900);
    await moveTo(Math.round(b.x + b.width - 6), Math.round(b.y + b.height / 2));
  });

  await seg('04', async () => {
    await at(0.8);
    await clickSel('.emirate-brand-medallion', { settle: 400 });
  });

  await go('/');
  await seg('06', async () => {
    await at(5.6);
    await openPopup();
    await at(8.4);
    const d = await box('#partnerRegistrationModal .partner-modal-content');
    await traceRing(d.cx, d.cy, d.w * 0.4575, 2600);
    await at(11.6);
    const wz = await box('#partnerRegistrationModal .partner-wizard');
    await moveTo(Math.round(wz.cx), Math.round(wz.cy));
    for (let i = 0; i < 6; i++) { await page.mouse.wheel(0, 45); await sleep(160); }
    await sleep(700);
    for (let i = 0; i < 6; i++) { await page.mouse.wheel(0, -45); await sleep(120); }
    await at(14.6);
    const nb = await box('#partnerNextBtn'); await moveTo(Math.round(nb.cx), Math.round(nb.cy));
    await sleep(700);
    const cb = await box('#partnerCancelBtn'); await moveTo(Math.round(cb.cx), Math.round(cb.cy));
  });
  await closePopup();

  await seg('09', async () => {
    await at(3.0);
    const l = await box('#gtSupportLauncher');
    await moveTo(Math.round(l.cx - 4), Math.round(l.cy - 4));
  });
} else {
  await go('/');
  await seg('01', async () => {
    await moveTo(W / 2, H / 2, 1200);
    await at(4); await page.mouse.wheel(0, 420);
    await at(7); await page.mouse.wheel(0, -420);
  });

  await go('/uaevisa');
  await seg('03', async () => {
    await moveTo(W / 2, H / 2 + 260, 900);
    await at(2.2);
    const d = await box('.emirate-modal');
    await traceRing(d.cx, d.cy, d.w * 0.4775, 2600);
    await at(5.4);
    const cards = page.locator('.emirate-card');
    const a = await cards.nth(0).boundingBox(), b = await cards.nth(1).boundingBox();
    await moveTo(Math.round(a.x + 6), Math.round(a.y + a.height / 2));
    await sleep(900);
    await moveTo(Math.round(b.x + b.width - 6), Math.round(b.y + b.height / 2));
  });

  await seg('05', async () => {
    await at(1.2);
    const t = await box('.emirate-home-tag'); await moveTo(Math.round(t.cx), Math.round(t.cy));
    await at(4.4);
    await clickSel('.emirate-home', { settle: 400 });
    await page.waitForLoadState('domcontentloaded');
  });
  await sleep(1800);

  await openPopup();
  await seg('07', async () => {
    await at(0.6);
    const d = await box('#partnerRegistrationModal .partner-modal-content');
    await traceRing(d.cx, d.cy, d.w * 0.4775, 2400);
    await at(4.0);
    const dots = page.locator('#partnerRegistrationModal .partner-step-dot');
    for (let i = 0; i < 4; i++) {
      const b = await dots.nth(i).boundingBox();
      await moveTo(Math.round(b.x + b.width / 2), Math.round(b.y + b.height / 2 + 14), 300);
      await sleep(150);
    }
    await at(6.6);
    const nb = await box('#partnerNextBtn'); await moveTo(Math.round(nb.cx), Math.round(nb.cy));
    await at(8.8);
    const cb = await box('#partnerCancelBtn'); await moveTo(Math.round(cb.cx), Math.round(cb.cy));
  });

  await seg('08', async () => {
    await typeIn('#partnerName', 'Priya Nair');
    await typeIn('#partnerPhone', '501234567');
    await typeIn('#partnerEmail', 'priya@nairtravel.ae', 30);
    await clickSel('#partnerNextBtn', { settle: 1000 });
    const wz = await box('#partnerRegistrationModal .partner-wizard');
    await moveTo(Math.round(wz.cx + 120), Math.round(wz.cy + 60));
  });
  await closePopup();

  await seg('10', async () => {
    await at(0.8);
    const l = await box('#gtSupportLauncher');
    await moveTo(Math.round(l.cx - 4), Math.round(l.cy - 4));
    await at(8.0);
    await clickSel('#gtSupportLauncher', { settle: 600 });
  });

  await seg('11', async () => {
    await moveTo(W / 2 + 200, H / 2, 1200);
  });
}

const vid = page.video();
await ctx.close();
await browser.close();
renameSync(await vid.path(), `out/${STATE}.webm`);
writeFileSync(`out/${STATE}-marks.json`, JSON.stringify(segs, null, 1));
console.log('done', STATE);
