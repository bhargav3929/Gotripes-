/**
 * Does every circular pop-up stay a circle, stay inside the window, and keep
 * its form inside the inner gold ring?
 *
 * Written after the founder's laptop (browser window 1338x605) showed a rounded
 * box instead of the circle: the pop-ups only drew the circle in windows at
 * least 770px tall, and nothing in the test suite ever opened one in a short
 * window. This opens each pop-up at a spread of real laptop/desktop/phone window
 * sizes, steps through every step, and measures instead of eyeballing:
 *
 *   shape    circle at/above the breakpoint, rounded card below it
 *   window   the drawn (scaled) disc fits the window
 *   ring     every visible control/label/title sits inside the inner ring,
 *            with at least MIN_CLEARANCE px to spare
 *   text     smallest rendered font size, after the scale is applied
 *   stable   focusing/scrolling to the last controls does not move the circle
 *
 * Run (local server on 127.0.0.1:8000):
 *      node tests/browser/medallion-fit.mjs [partner|freelancer|lead|all]
 *      BASE_URL=https://gotrips.ai node tests/browser/medallion-fit.mjs
 * Exit code 1 if any circle-mode check fails, so it can gate a deploy.
 */
import { chromium } from 'playwright';
import { mkdirSync } from 'fs';

const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';
const SHOTS = process.env.SHOTS || '';          // directory for screenshots, optional
const only = process.argv[2] || 'all';

// name, width, height. Heights are *window* heights (browser toolbar excluded).
const WINDOWS = [
  ['founder laptop', 1338, 605],
  ['win 1366x768',   1366, 650],
  ['hd 1280x720',    1280, 600],
  ['mac 1440x900',   1440, 790],
  ['full hd',        1920, 960],
  ['4k-ish tall',    2560, 1300],
  ['tablet',          820, 1180],
  ['phone',           390, 844],
  ['phone landscape', 844, 390],
];
const MIN_FONT_PX = 7.4;          // rendered size below which text is unreadable
const MIN_CLEARANCE = 8;          // px a control must keep from the inner gold ring

const MODALS = {
  partner: {
    page: '/', open: '#partnerRegisterBtn', modal: '#partnerRegistrationModal',
    shell: '.partner-modal-content', step: '.partner-step', dot: '.partner-step-dot',
    within: '.partner-modal-body',
  },
  freelancer: {
    page: '/freelancer', open: '[data-open-freelancer-register]', modal: '#flRegistrationModal',
    shell: '.fl-modal-content', step: '.fl-step', dot: '.fl-step-dot', within: '.fl-modal-body',
  },
  lead: {
    page: '/', open: null, modal: '#gotripsLeadPopupOverlay',
    shell: '.gotrips-lead-modal', step: null, dot: null, within: '.gotrips-lead-content-wrapper',
  },
};

let failures = 0;
const browser = await chromium.launch();
if (SHOTS) mkdirSync(SHOTS, { recursive: true });

for (const [key, m] of Object.entries(MODALS)) {
  if (only !== 'all' && only !== key) continue;
  console.log(`\n== ${key} ==`);

  for (const [label, w, h] of WINDOWS) {
    const ctx = await browser.newContext({ viewport: { width: w, height: h } });
    // keep the marketing lead pop-up out of the way of the other pop-ups
    await ctx.addInitScript((isLead) => {
      try { if (!isLead) sessionStorage.setItem('gotripsLeadPopupShown', 'true'); } catch (e) {}
    }, key === 'lead');
    const page = await ctx.newPage();
    await page.goto(BASE + m.page, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(2200);

    if (m.open) {
      await page.locator(m.open).first().click();
    } else {
      await page.evaluate(sel => document.querySelector(sel).classList.add('active'), m.modal);
    }
    await page.waitForTimeout(900);

    const stepCount = m.step ? await page.locator(m.step).count() : 1;
    for (let i = 0; i < stepCount; i++) {
      if (m.step) {
        await page.evaluate(([mm, idx]) => {
          const root = document.querySelector(mm.modal);
          const steps = root.querySelectorAll(mm.step), dots = root.querySelectorAll(mm.dot);
          steps.forEach((s, k) => s.classList.toggle('is-active', k === idx));
          dots.forEach((d, k) => { d.classList.toggle('is-active', k === idx); d.classList.toggle('is-done', k < idx); });
        }, [m, i]);
        await page.waitForTimeout(150);
      }

      const r = await page.evaluate(([mm]) => {
        const root = document.querySelector(mm.modal);
        const shell = root.querySelector(mm.shell);
        const box = shell.getBoundingClientRect();
        const cs = getComputedStyle(shell);
        const scale = parseFloat(cs.scale) || 1;
        const isCircle = cs.borderTopLeftRadius.includes('%') || parseFloat(cs.borderTopLeftRadius) > box.width / 2 / scale - 1;
        // inner-ring radius: band 4.5% + hairline 1.2% of the disc
        const cx = box.left + box.width / 2, cy = box.top + box.height / 2;
        const R = box.width / 2 * (1 - 0.045 - 0.012);
        let worst = 0, outside = [], minFont = 99, tightest = 1e9;
        const area = root.querySelector(mm.within) || shell;
        const els = area.querySelectorAll('input:not([type=hidden]),select,textarea,button,label,h1,h2,h3,p,a,span,.partner-step-dot,.fl-step-dot');
        els.forEach(el => {
          const b = el.getBoundingClientRect();
          if (b.width < 2 || b.height < 2) return;
          const st = getComputedStyle(el);
          if (st.visibility === 'hidden' || st.display === 'none' || st.opacity === '0') return;
          if (el.closest('[hidden],.is-scroll-hidden')) return;
          // skip things that are not currently shown (inactive steps, closed dropdowns)
          if (el.offsetParent === null && st.position !== 'fixed') return;
          const fs = parseFloat(st.fontSize) * scale;
          if ((el.childNodes.length && [...el.childNodes].some(n => n.nodeType === 3 && n.textContent.trim())) && fs < minFont) minFont = fs;
          for (const [x, y] of [[b.left, b.top], [b.right, b.top], [b.left, b.bottom], [b.right, b.bottom]]) {
            const d = Math.hypot(x - cx, y - cy) - R;
            if (d > worst) { worst = d; }
            if (-d < tightest) tightest = -d;
            if (d > 1.5) outside.push((el.id || el.className || el.tagName).toString().slice(0, 28));
          }
        });
        return {
          circle: isCircle && Math.abs(box.width - box.height) < 2,
          disc: Math.round(box.width), scale: +scale.toFixed(3),
          fitsWindow: box.left >= -1 && box.top >= -1 && box.right <= innerWidth + 1 && box.bottom <= innerHeight + 1,
          worst: Math.round(worst), clearance: Math.round(tightest), outside: [...new Set(outside)].slice(0, 4), minFont: +minFont.toFixed(1),
          hscroll: document.documentElement.scrollWidth > innerWidth + 1,
        };
      }, [m]);

      // Focus and scrollIntoView must never move the circle: a scaled disc keeps its
      // full layout size, which once made the overlay scrollable and shifted the
      // circle up when a field near the bottom took focus.
      let moved = 0;
      if (i === 0 && !(w < 900 || h < 536)) {
        moved = await page.evaluate(([mm]) => {
          const root = document.querySelector(mm.modal), shell = root.querySelector(mm.shell);
          const before = shell.getBoundingClientRect().top;
          const targets = [...root.querySelectorAll('button,input:not([type=hidden]),select,a')].filter(e => e.offsetParent !== null);
          targets.slice(-3).forEach(t => { t.focus(); t.scrollIntoView(); });
          return Math.abs(shell.getBoundingClientRect().top - before);
        }, [m]);
      }

      const cardExpected = w < 900 || h < 536;
      let verdict;
      if (cardExpected) {
        verdict = r.circle ? 'FAIL (expected card)' : 'ok card';
        if (r.circle) failures++;
      } else {
        const bad = !r.circle || !r.fitsWindow || moved > 0.5 || r.worst > 1.5 || r.clearance < MIN_CLEARANCE || r.minFont < MIN_FONT_PX;
        verdict = bad ? 'FAIL' : 'ok circle';
        if (bad) failures++;
      }
      console.log(
        `${label.padEnd(16)} ${String(w).padStart(4)}x${String(h).padEnd(5)} step ${i + 1}/${stepCount}  ` +
        `disc ${String(r.disc).padStart(4)} scale ${String(r.scale).padEnd(5)} ` +
        `shift ${moved.toFixed(0)}px  ring-clearance ${String(r.clearance).padStart(3)}px  min-text ${String(r.minFont).padStart(4)}px  ${verdict}` +
        (r.outside.length && !cardExpected ? `  outside: ${r.outside.join(', ')}` : '')
      );

      if (SHOTS && i === 0) await page.screenshot({ path: `${SHOTS}/${key}-${label.replace(/\W+/g, '-')}.png` });
    }
    await ctx.close();
  }
}

await browser.close();
console.log(failures ? `\n${failures} check(s) failed` : '\nall checks passed');
process.exit(failures ? 1 : 0);
