// Screenshot a page in light (or dark) mode for theming verification.
// Usage: node scratch/shot.mjs <path> <out.png> [light|dark] [width]
import { chromium } from 'playwright';

const path  = process.argv[2] || '/';
const out   = process.argv[3] || 'scratch/shot.png';
const theme = process.argv[4] || 'light';
const width = parseInt(process.argv[5] || '1366', 10);
const base  = 'http://127.0.0.1:8000';

const browser = await chromium.launch();
const ctx = await browser.newContext({ viewport: { width, height: 900 } });
const page = await ctx.newPage();

// Visit once to establish the origin, set the theme, then reload so the
// no-FOUC script + CSS apply.
await page.goto(base + path, { waitUntil: 'domcontentloaded' });
await page.evaluate((t) => { try { localStorage.setItem('gt-theme', t); } catch (e) {} }, theme);
await page.goto(base + path, { waitUntil: 'networkidle' }).catch(() => {});
await page.waitForTimeout(1200);
await page.screenshot({ path: out, fullPage: true });

await browser.close();
console.log('shot ->', out, '(' + theme + ', ' + width + 'w)');
