/**
 * Second walkthrough: the two items that were still open on 15 Sep, shown on
 * the site — the 9:16 portrait eSIM cut, and the medallion treatment on all
 * four full-page registration forms.
 *
 * Recorded against the local server, which is byte-identical to production
 * (every file was verified with cmp against the deployed copy). Production is
 * used where it will serve a browser agent without rate-limiting it.
 *
 * Run:  node record-update.mjs
 * Out:  out2/<random>.webm  → muxed with voice2/ narration afterwards
 */
import { chromium } from 'playwright';
import { readFileSync, writeFileSync } from 'fs';
import { OVERLAY, attach, sleep } from './wt-lib.mjs';

const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';
const W = 1920, H = 1080;
const VOICE = JSON.parse(readFileSync('voice2/durations.json', 'utf8'));

async function main() {
  const browser = await chromium.launch({ args: ['--force-device-scale-factor=1'] });
  const ctx = await browser.newContext({
    viewport: { width: W, height: H },
    recordVideo: { dir: 'out2', size: { width: W, height: H } },
    deviceScaleFactor: 1,
  });
  await ctx.addInitScript(OVERLAY);
  const page = await ctx.newPage();
  const { cap, hold, clickSel, moveTo, hideChrome, marks } =
    attach(page, { W, H, voice: VOICE });

  // ------------------------------------------------------------ 1. opening
  await page.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
  await sleep(1400); await hideChrome();
  await cap('01', 'The two open items are done');
  await sleep(1200);
  await page.mouse.wheel(0, 380); await sleep(900);
  await hold('01');

  // --------------------------------------------- 2-4. the portrait eSIM cut
  await cap('02', 'The eSIM page');
  await page.goto(BASE + '/esim', { waitUntil: 'domcontentloaded' });
  await sleep(1600); await hideChrome();
  await page.evaluate(() => {
    const v = document.querySelector('.esim-install-video');
    if (v) v.scrollIntoView({ block: 'center' });
  });
  await sleep(1500);
  await hold('02');

  await cap('03', 'A second link: the full-screen version');
  await sleep(700);
  // point at the new link rather than following it: the raw file opens in a new
  // tab, and the recording follows this page.
  const box = await page.locator('.esim-install-video-alt a').first().boundingBox();
  if (box) {
    await moveTo(Math.round(box.x + box.width / 2), Math.round(box.y + box.height / 2));
    await page.evaluate(([x, y]) => window.__wtRipple && window.__wtRipple(x, y),
      [Math.round(box.x + box.width / 2), Math.round(box.y + box.height / 2)]);
  }
  await sleep(1200);
  await hold('03');

  await cap('04', '1080 x 1920, laid out for a phone');
  await page.goto(BASE + '/assets/esim/how-to-install-esim-portrait.mp4',
    { waitUntil: 'domcontentloaded' });
  await sleep(800);
  await page.evaluate(() => {
    const v = document.querySelector('video');
    if (v) { v.muted = true; v.currentTime = 9; v.play().catch(() => {}); }
  }).catch(() => {});
  await hold('04', 900);

  // ------------------------------------- 5-9. the four registration pages
  await cap('05', 'All registration forms, not just the pop-up');
  await sleep(900);
  await hold('05');

  const pages = [
    ['06', 'Agent registration',      '/agent/register'],
    ['07', 'Agency registration',     '/agency/register'],
    ['08', 'Freelancer registration', '/freelancer/register'],
    ['09', 'Partner registration',    '/partner/register'],
  ];
  for (const [n, label, url] of pages) {
    await cap(n, label);
    await page.goto(BASE + url, { waitUntil: 'domcontentloaded' });
    await sleep(1500); await hideChrome();
    await page.evaluate(() => {
      const el = document.querySelector('.gm-ring-panel');
      if (el) el.scrollIntoView({ block: 'center' });
    }).catch(() => {});
    await sleep(900);
    await hold(n);
  }

  // ------------------------------------------------ 10-12. the deploy note
  await page.goto(BASE + '/', { waitUntil: 'domcontentloaded' });
  await sleep(1300); await hideChrome();
  await cap('10', 'One honest note about the deploy');
  await sleep(900);
  await hold('10');
  await cap('11', 'Stale PHP cache after the file swap. Reset, and clean since.');
  await sleep(900);
  await hold('11');
  await cap('12', 'All of it is live on gotrips.ai');
  await sleep(900);
  await hold('12');
  await cap('', '');
  await sleep(900);

  writeFileSync('out2/marks.json', JSON.stringify(marks, null, 2));
  await ctx.close();
  await browser.close();
  console.log('recorded', JSON.stringify(marks));
}

main().catch(e => { console.error('FAILED', e); process.exit(1); });
