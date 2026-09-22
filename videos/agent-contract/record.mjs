/**
 * Screen recording of the agent-contract flow (Amer, 22 Sep 2026).
 *
 * One continuous session on the local site: publish a contract in the manager
 * portal, sign it as an applicant on /agent/register, show the same step in
 * the homepage pop-up, then review, open the signed PDF and approve.
 *
 * Nothing is drawn on the page but the pointer and its click ripple; the voice
 * track carries the explanation. Segment start/end times are written to
 * out/marks.json for build.sh to cut against.
 *
 * Run:  node record.mjs      (local server on 127.0.0.1:8000, no contract published)
 */
import { chromium } from 'playwright';
import { readFileSync, writeFileSync, renameSync, mkdirSync } from 'fs';
import { execFileSync } from 'child_process';
import { OVERLAY, attach, sleep } from '../walkthrough/wt-lib.mjs';

const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';
const OWNER = { email: 'admin@gotrips.ai', pass: 'LocalDemo123!' };
const APPLICANT = 'zayed.travel+' + Date.now() + '@example.com';
const W = 1600, H = 1000;
const VOICE = JSON.parse(readFileSync('voice/durations.json', 'utf8'));
const LEAD = 0.3, TAIL = 0.8;

const CONTRACT = `GOTRIPS B2B AGENT AGREEMENT (DRAFT)

1. Appointment
GoTrips appoints the Agent as a non-exclusive reseller of GoTrips travel products, including eSIM data plans, in the territory agreed in writing.

2. Orders and payment
The Agent orders through the GoTrips agent portal. Prices shown in the portal at the time of the order apply. Payment is due before fulfilment unless a credit limit has been agreed in writing.

3. Trade licence
The Agent keeps a valid trade licence for the term of this agreement and uploads a renewed copy before expiry. GoTrips may suspend portal access while a licence is expired.

4. Term and termination
Either party may end this agreement with 30 days written notice.

5. Governing law
This agreement is governed by the laws of the United Arab Emirates.

DRAFT FOR REVIEW — REPLACE WITH THE FINAL TEXT BEFORE USE.`;

mkdirSync('out/raw', { recursive: true });
const browser = await chromium.launch({ args: ['--force-device-scale-factor=1'] });
const ctx = await browser.newContext({
  viewport: { width: W, height: H }, deviceScaleFactor: 1,
  recordVideo: { dir: 'out/raw', size: { width: W, height: H } },
});
await ctx.addInitScript(OVERLAY);
await ctx.addInitScript(() => {
  try { sessionStorage.setItem('gotripsLeadPopupShown', 'true'); } catch (e) {}
});
const page = await ctx.newPage();
const T0 = Date.now();
const { put } = attach(page, { W, H });

// Wall-clock pointer moves: under recording each mouse event costs ~100ms, so
// step-count easing runs several times longer than planned.
let px = W / 2, py = H / 2;
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
async function clickSel(sel, { settle = 900, nudge = [0, 0] } = {}) {
  const el = page.locator(sel).first();
  await el.waitFor({ state: 'visible', timeout: 20000 });
  await el.scrollIntoViewIfNeeded().catch(() => {});
  await sleep(250);
  const b = await el.boundingBox();
  const x = Math.round(b.x + b.width / 2 + nudge[0]), y = Math.round(b.y + b.height / 2 + nudge[1]);
  await moveTo(x, y);
  await sleep(180);
  await page.evaluate(([a, c]) => window.__wtRipple && window.__wtRipple(a, c), [x, y]);
  await sleep(120);
  await page.mouse.click(x, y);
  await sleep(settle);
}
async function typeIn(sel, text, delay = 32, clear = false) {
  const el = page.locator(sel).first();
  await el.scrollIntoViewIfNeeded().catch(() => {});
  if (clear) await el.fill('');
  const b = await el.boundingBox();
  const x = Math.round(b.x + b.width * 0.62), y = Math.round(b.y + b.height / 2);
  await moveTo(x, y, 420);
  await page.mouse.click(x, y);
  await page.keyboard.type(text, { delay });
  await sleep(180);
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
  console.log(id, segs.at(-1));
}
const go = async path => {
  await page.goto(BASE + path, { waitUntil: 'domcontentloaded' });
  await sleep(2000);
  await put(px, py);
};

// ---------------------------------------------------------------- 1. sign in
await go('/manager/login');
await page.fill('input[name="email"]', OWNER.email);
await page.fill('input[name="password"]', OWNER.pass);
await page.click('button[type="submit"]');
await page.waitForTimeout(2500);

await go('/manager');
await seg('01', async () => {
  await moveTo(W / 2, H / 2, 1200);
});

// ------------------------------------------------- 2-4. publish the contract
await seg('02', async () => {
  await clickSel('a[href$="/manager/contracts"]', { settle: 1800 })
    .catch(async () => { await go('/manager/contracts'); });
  await at(6);
  await moveTo(620, 330, 700);
});

await seg('03', async () => {
  await typeIn('input[name="title"]', 'GoTrips B2B eSIM Agent Agreement', 24, true);
  await page.locator('textarea[name="body"]').scrollIntoViewIfNeeded();
  const b = await page.locator('textarea[name="body"]').boundingBox();
  await moveTo(Math.round(b.x + b.width / 2), Math.round(b.y + 40), 420);
  await page.mouse.click(Math.round(b.x + b.width / 2), Math.round(b.y + 40));
  await page.locator('textarea[name="body"]').fill(CONTRACT);
  // fill() leaves the caret at the end, which scrolls the box to its last
  // line; show the top of the contract instead.
  await page.locator('textarea[name="body"]').evaluate(el => { el.scrollTop = 0; });
  await sleep(400);
});

await seg('04', async () => {
  await clickSel('#contractForm button[type="submit"]', { settle: 2200 });
  await at(5);
  await moveTo(1300, 350, 800);
});

// -------------------------------------------------- 5-6. the applicant signs
await go('/agent/register');
await seg('05', async () => {
  await typeIn('input[name="name"]', 'Zayed Al Marri');
  await typeIn('input[name="phone"]', '+971502233445');
  await typeIn('input[name="email"]', APPLICANT, 18);
  await typeIn('input[name="company_name"]', 'Zayed Travel LLC');
  await typeIn('input[name="trade_license_number"]', 'TL-44210');
  await page.fill('input[name="trade_license_expiry_date"]', '2027-11-30');
  await typeIn('textarea[name="address"], input[name="address"]', 'Al Wasl Road, Dubai', 20);
  await page.selectOption('select[name="emirate"]', { index: 1 }).catch(() => {});
  await clickSel('label[for="service_esim"], #service_esim', { settle: 500 });
  await page.setInputFiles('input[name="trade_license_document"]',
    { name: 'trade-licence.pdf', mimeType: 'application/pdf', buffer: Buffer.from('%PDF-1.4 licence') });
  await page.fill('input[name="password"]', 'AgentPass123!');
  await page.fill('input[name="password_confirmation"]', 'AgentPass123!');
});

await seg('06', async () => {
  const box = await page.locator('.agreement-text').boundingBox();
  await moveTo(Math.round(box.x + box.width / 2), Math.round(box.y + box.height / 2), 700);
  for (let i = 0; i < 5; i++) { await page.mouse.wheel(0, 40); await sleep(150); }
  await at(3.4);
  await typeIn('input[name="signature_full_name"]', 'Zayed Al Marri', 40);
  await clickSel('#signatureAgreed', { settle: 500 });
  await at(6.4);
  await clickSel('button[type="submit"]', { settle: 2400 });
});

// ------------------------------------------------- 7. the same step in the pop-up
await go('/');
await seg('07', async () => {
  await clickSel('#partnerRegisterBtn', { settle: 1400 });
  await page.evaluate(() => {
    // Jump to the agreement step: the earlier steps are the ones we already
    // showed on the full page, and the wizard validates before advancing.
    const steps = document.querySelectorAll('#partnerRegistrationModal .partner-step');
    const dots = document.querySelectorAll('#partnerRegistrationModal .partner-step-dot');
    steps.forEach(s => s.classList.remove('is-active'));
    steps[4].classList.add('is-active');
    dots.forEach((d, i) => { d.classList.toggle('is-active', i === 4); d.classList.toggle('is-done', i < 4); });
    document.getElementById('partnerNextBtn').style.display = 'none';
    document.getElementById('partnerSubmitBtn').style.display = '';
    document.getElementById('partnerBackBtn').style.display = '';
  });
  await sleep(1200);
  const t = await page.locator('#partnerRegistrationModal .partner-agreement-text').boundingBox();
  if (t) await moveTo(Math.round(t.x + t.width / 2), Math.round(t.y + t.height / 2), 700);
  await sleep(900);
  const s = await page.locator('#partnerSignatureName').boundingBox();
  if (s) await moveTo(Math.round(s.x + s.width / 2), Math.round(s.y + s.height / 2), 600);
});

// --------------------------------------- 8-10. review, signed copy, approve
await go('/manager/agent-applications');
await clickSel('a:has-text("Zayed Al Marri")', { settle: 1500 })
  .catch(async () => { await clickSel('tbody tr:first-child a', { settle: 1500 }); });
await seg('08', async () => {
  await at(1.5);
  const card = await page.locator('.wp-card:has-text("Agreement")').last().boundingBox();
  if (card) {
    await moveTo(Math.round(card.x + 200), Math.round(card.y + 90), 800);
    await sleep(800);
    await moveTo(Math.round(card.x + 200), Math.round(card.y + 190), 700);
  }
});

await seg('09', async () => {
  const link = page.locator('a:has-text("Download PDF")').first();
  const href = await link.getAttribute('href');
  const b = await link.boundingBox();
  await moveTo(Math.round(b.x + b.width / 2), Math.round(b.y + b.height / 2), 700);
  await page.evaluate(([a, c]) => window.__wtRipple && window.__wtRipple(a, c),
    [Math.round(b.x + b.width / 2), Math.round(b.y + b.height / 2)]);
  await sleep(300);
  // Chromium downloads a PDF rather than rendering it, so the real stored
  // file is rasterised and shown full-screen instead — same document, visible.
  const local = '../../public' + href.replace(BASE, '');
  execFileSync('sips', ['-s', 'format', 'png', '--out', '/tmp/gt-signed.png', local], { stdio: 'ignore' });
  writeFileSync('/tmp/gt-signed.html',
    '<body style="margin:0;background:#2b2b2b;display:flex;justify-content:center">' +
    '<img src="file:///tmp/gt-signed.png" style="width:760px;box-shadow:0 18px 60px rgba(0,0,0,.6)">');
  await page.goto('file:///tmp/gt-signed.html', { waitUntil: 'load' });
  await sleep(2400);
});

await page.goBack({ waitUntil: 'domcontentloaded' });
await sleep(1800);
await seg('10', async () => {
  await clickSel('form[action*="approve"] button[type="submit"]', { settle: 2600 });
  await at(6.5);
  await moveTo(700, 300, 900);
});

await go('/manager/contracts');
await seg('11', async () => {
  await moveTo(620, 340, 1000);
  await sleep(600);
  await moveTo(1300, 350, 900);
});

const vid = page.video();
await ctx.close();
await browser.close();
renameSync(await vid.path(), 'out/session.webm');
writeFileSync('out/marks.json', JSON.stringify(segs, null, 1));
console.log('done, applicant', APPLICANT);
