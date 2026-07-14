// Capture several viewport screenshots down a page for theming review.
// Usage: node scratch/shots.mjs <path> <prefix> [light|dark] [width]
import { chromium } from 'playwright';

const path   = process.argv[2] || '/';
const prefix = process.argv[3] || 'scratch/view';
const theme  = process.argv[4] || 'light';
const width  = parseInt(process.argv[5] || '1366', 10);
const base   = 'http://127.0.0.1:8000';

const browser = await chromium.launch();
const ctx = await browser.newContext({ viewport: { width, height: 860 } });
const page = await ctx.newPage();

await page.goto(base + path, { waitUntil: 'domcontentloaded' });
await page.evaluate((t) => { try { localStorage.setItem('gt-theme', t); } catch (e) {} }, theme);
await page.goto(base + path, { waitUntil: 'networkidle' }).catch(() => {});
await page.waitForTimeout(1000);
// Dismiss the auto lead popup so it doesn't block the page in shots.
await page.evaluate(() => {
  ['#gotripsLeadPopupOverlay', '.gotrips-lead-popup-overlay', '.modal-backdrop'].forEach((s) => {
    document.querySelectorAll(s).forEach((e) => { e.style.display = 'none'; });
  });
});

const total = await page.evaluate(() => document.body.scrollHeight);
const step = 800;
let i = 0;
for (let y = 0; y < total; y += step) {
  await page.evaluate((yy) => window.scrollTo(0, yy), y);
  await page.waitForTimeout(350);
  await page.screenshot({ path: `${prefix}-${i}.png` });
  i++;
  if (i > 12) break;
}
await browser.close();
console.log('shots ->', `${prefix}-0..${i - 1}.png`, '(' + theme + ')');
