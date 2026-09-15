/**
 * The tutorial walkthrough: a clean screen recording, no on-screen text panels.
 *
 * Only the pointer and its click ripple are drawn — everything is explained by
 * the voice track, so the frame stays the real website and nothing else. The
 * step captions used by the earlier recorders are deliberately suppressed:
 * cap(n, '') records the narration timing mark without showing anything.
 *
 * Run:  node record-tutorial.mjs
 * Out:  out3/<random>.webm  → muxed with voice3/ narration afterwards
 */
import { chromium } from 'playwright';
import { readFileSync, writeFileSync } from 'fs';
import { OVERLAY, attach, sleep } from './wt-lib.mjs';

const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';
const OWNER = { email: 'admin@gotrips.ai', pass: 'LocalDemo123!' };
const CARE = { email: 'care.demo@gotrips.ai', pass: 'CareDemo123!' };
const W = 1920, H = 1080;
const VOICE = JSON.parse(readFileSync('voice3/durations.json', 'utf8'));

async function main() {
  const browser = await chromium.launch({ args: ['--force-device-scale-factor=1'] });
  const ctx = await browser.newContext({
    viewport: { width: W, height: H },
    recordVideo: { dir: 'out3', size: { width: W, height: H } },
    deviceScaleFactor: 1,
  });
  await ctx.addInitScript(OVERLAY);
  const page = await ctx.newPage();
  const { cap, hold, clickSel, typeIn, moveTo, put, hideChrome, marks } =
    attach(page, { W, H, voice: VOICE });

  // silent timing mark: paces the picture to the narration, draws nothing
  const step = async (n) => cap(n, '');

  // ------------------------------------------------------- 1. the website
  await page.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
  await sleep(1500); await hideChrome();
  await step('01');
  await put(W / 2, H / 2);
  await sleep(900);
  await page.mouse.wheel(0, 520); await sleep(1400);
  await page.mouse.wheel(0, -520); await sleep(700);
  await hold('01');

  // -------------------------------------------- 2-4. visa services + pop-up
  await step('02');
  await clickSel('a:has-text("UAE VISA SERVICES")', { settle: 2600 })
    .catch(async () => { await page.goto(BASE + '/uaevisa', { waitUntil: 'domcontentloaded' }); await sleep(2200); });
  await hideChrome();
  await hold('02');

  await step('03');
  await sleep(1200);
  // drift the pointer across the two route circles so both read on screen
  const dubai = await page.locator('.emirate-card').first().boundingBox();
  const sharjah = await page.locator('.emirate-card').nth(1).boundingBox();
  if (sharjah) await moveTo(Math.round(sharjah.x + sharjah.width / 2), Math.round(sharjah.y + sharjah.height / 2));
  await sleep(700);
  if (dubai) await moveTo(Math.round(dubai.x + dubai.width / 2), Math.round(dubai.y + dubai.height / 2));
  await hold('03');

  await step('04');
  await clickSel('.emirate-card', { settle: 2000 });
  await page.mouse.wheel(0, 300); await sleep(1500);
  await hold('04');

  // ------------------------------------ 5-9. registration pop-up, step by step
  await step('05');
  await page.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
  await sleep(1500); await hideChrome();
  await clickSel('#partnerRegisterBtn', { settle: 1700 });
  await hold('05');

  await step('06');
  await sleep(1600);
  await hold('06');

  await step('07');
  await typeIn('#partnerName', 'Priya Nair');
  await typeIn('#partnerEmail', 'priya@example.com');
  await typeIn('#partnerCompanyName', 'Nair Travel LLC');
  await typeIn('#partnerLicenseNumber', 'TL-88213');
  await hold('07');

  await step('08');
  // step 1 requires a licence file and expiry; jump the wizard the way the
  // Next button does rather than fighting the file picker in a recording
  await page.evaluate(() => {
    const steps = document.querySelectorAll('#partnerRegistrationModal .partner-step');
    const dots = document.querySelectorAll('#partnerRegistrationModal .partner-step-dot');
    steps.forEach(s => s.classList.remove('is-active'));
    steps[1].classList.add('is-active');
    dots.forEach((d, i) => { d.classList.toggle('is-active', i === 1); d.classList.toggle('is-done', i < 1); });
    document.getElementById('partnerBackBtn').style.display = '';
  });
  await sleep(1500);
  await clickSel('#partnerUaeYes + .partner-uae-icon, label:has(#partnerUaeYes)', { settle: 900 })
    .catch(() => {});
  await hold('08');

  await step('09');
  await page.evaluate(() => {
    const steps = document.querySelectorAll('#partnerRegistrationModal .partner-step');
    const dots = document.querySelectorAll('#partnerRegistrationModal .partner-step-dot');
    steps.forEach(s => s.classList.remove('is-active'));
    steps[2].classList.add('is-active');
    dots.forEach((d, i) => { d.classList.toggle('is-active', i === 2); d.classList.toggle('is-done', i < 2); });
    document.getElementById('partnerNextBtn').style.display = 'none';
    document.getElementById('partnerSubmitBtn').style.display = '';
  });
  await sleep(1300);
  await clickSel('#partnerRegistrationModal .partner-service-option label', { settle: 800 }).catch(() => {});
  await sleep(600);
  await hold('09');

  await step('10');
  await clickSel('#partnerCancelBtn', { settle: 1400 });
  await hold('10');

  // ------------------------------------------------ 11-12. manager portal
  await step('11');
  await page.goto(BASE + '/manager/login', { waitUntil: 'domcontentloaded' });
  await sleep(1500);
  await typeIn('input[name="email"]', OWNER.email, 42);
  await typeIn('input[name="password"]', OWNER.pass, 42);
  await sleep(400);
  await clickSel('button[type="submit"]', { settle: 2800 });
  await hold('11');

  await step('12');
  await sleep(1600);
  await hold('12');

  // ------------------------------------------- 13-15. tickets, deeper clicks
  await step('13');
  await clickSel('a[href$="/manager/support"]', { settle: 2400 })
    .catch(async () => { await page.goto(BASE + '/manager/support', { waitUntil: 'domcontentloaded' }); await sleep(2000); });
  await hold('13');

  await step('14');
  await clickSel('a:has-text("Open")', { settle: 2600 })
    .catch(async () => { await page.goto(BASE + '/manager/support/1', { waitUntil: 'domcontentloaded' }); await sleep(2000); });
  await hold('14');

  await step('15');
  await page.evaluate(() => {
    const s = document.querySelector('select[name="assigned_to"]');
    if (s) s.scrollIntoView({ block: 'center' });
  });
  await sleep(700);
  await clickSel('select[name="assigned_to"]', { settle: 1400 }).catch(() => {});
  await hold('15');

  // ----------------------------------------------------- 16-17. help guide
  await step('16');
  await clickSel('a[href$="/manager/help"]', { settle: 2400 })
    .catch(async () => { await page.goto(BASE + '/manager/help', { waitUntil: 'domcontentloaded' }); await sleep(2000); });
  await page.mouse.wheel(0, 640); await sleep(1400);
  await hold('16');

  await step('17');
  await clickSel('.help-card >> nth=7', { settle: 2600 })
    .catch(async () => { await page.goto(BASE + '/manager/help/customer-support', { waitUntil: 'domcontentloaded' }); await sleep(2000); });
  await page.mouse.wheel(0, 520); await sleep(1500);
  await hold('17');

  // ------------------------------------------- 18-20. the customer-care login
  await step('18');
  await clickSel('form[action*="manager/logout"] button[type="submit"]', { settle: 2200 });
  await page.waitForSelector('input[name="email"]', { timeout: 20000 });
  await sleep(900);
  await typeIn('input[name="email"]', CARE.email, 42);
  await typeIn('input[name="password"]', CARE.pass, 42);
  await clickSel('button[type="submit"]', { settle: 2800 });
  await hold('18');

  await step('19');
  await sleep(1400);
  const brand = await page.locator('.sidebar-brand').first().boundingBox();
  if (brand) await moveTo(Math.round(brand.x + 120), Math.round(brand.y + 220));
  await sleep(1400);
  await hold('19');

  await step('20');
  // prove the lock: ask for the dashboard, land back on the queue
  await page.goto(BASE + '/manager', { waitUntil: 'domcontentloaded' });
  await sleep(2400);
  await hold('20');

  await sleep(1200);
  writeFileSync('out3/marks.json', JSON.stringify(marks, null, 2));
  await ctx.close();
  await browser.close();
  console.log('recorded', JSON.stringify(marks));
}

main().catch(e => { console.error('FAILED', e); process.exit(1); });
