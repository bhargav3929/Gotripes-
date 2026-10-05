#!/usr/bin/env python3
"""Writes the four Android-specific scenes (01, 03, 04, 05) into compositions/frames/.

Same visual system as the iPhone guide's scenes (Broadside in gold: ink-black
ground, cream type, hairline rules, a single gold accent, Barlow + IBM Plex Mono).
Cue times are seconds from the start of each scene's narration and come straight
from the HeyGen word timings in audio_meta.json, so every menu name lights up as
the narrator says it.

Re-run after changing a scene:  python3 build-android-frames.py
"""
import json
import os

HERE = os.path.dirname(os.path.abspath(__file__))
OUT = os.path.join(HERE, "compositions", "frames")

BASE_CSS = """
    @font-face { font-family: "Barlow"; src: local("Arial"); font-style: normal; font-weight: 400 900; }
    @font-face { font-family: "IBM Plex Mono"; src: local("Courier New"); font-style: normal; font-weight: 400 700; }
    #root { position: relative; width: 100%; height: 100%; overflow: hidden; container-type: size; color: #F4EBD0; font-family: "Barlow", system-ui, sans-serif; font-weight: 400; }
    #root * { box-sizing: border-box; }
    #root .bg { position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; background: #080706; }
    #root .rule { position: absolute; left: 5.5cqw; width: 89cqw; height: 1px; background: #282826; }
    #root .rule.top { top: 4.2cqh; }
    #root .rule.bottom { top: 82.75cqh; }
    #root .mono { font-family: "IBM Plex Mono", ui-monospace, monospace; letter-spacing: 0.14em; text-transform: uppercase; font-weight: 500; }
    #root .catalogue, #root .number { position: absolute; top: 1.65cqh; font-size: 0.72cqw; color: #D4AF37; }
    #root .catalogue { left: 5.5cqw; }
    #root .number { right: 5.5cqw; }
    #root .stage { position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; }
    #root .headline { position: absolute; top: 5.8cqh; left: 0; width: 100%; height: 9cqh; display: flex; align-items: baseline; justify-content: center; gap: 0.8cqw; font-size: 3.35cqw; line-height: 0.95; font-weight: 800; letter-spacing: -0.03em; white-space: nowrap; }
    #root .headline strong { color: #D4AF37; font-weight: 900; }
    #root .phone { position: absolute; top: 16.9cqh; height: 59.5cqh; border: 1px solid #D4AF37; background: #1A1A18; z-index: 5; }
    #root .phone-cap { height: 10cqh; padding: 2.35cqh 1.55cqw 1.6cqh; border-bottom: 1px solid #282826; display: flex; align-items: flex-end; justify-content: space-between; font-size: 0.72cqw; color: #D4AF37; }
    #root .phone-cap .status { color: #8C897F; }
    #root .phone-foot { position: absolute; left: 0; right: 0; bottom: 0; height: 4.1cqh; border-top: 1px solid #282826; display: flex; align-items: center; justify-content: center; gap: 1.1cqw; }
    #root .phone-foot span { width: 0.42cqw; height: 0.42cqw; border: 1px solid #504E48; border-radius: 50%; }
    #root .phone-foot span:nth-child(2) { background: #D4AF37; border-color: #D4AF37; }
    #root .viewport { position: absolute; left: 1.45cqw; right: 1.45cqw; top: 11.35cqh; height: 42.5cqh; overflow: hidden; }
    #root .kicker { font-size: 0.62cqw; color: #D4AF37; margin-bottom: 0.9cqh; }
    #root .stitle { font-size: 1.9cqw; line-height: 1.05; font-weight: 800; letter-spacing: -0.025em; margin-bottom: 2.1cqh; }
    #root .row { height: 8.1cqh; border-top: 1px solid #282826; display: flex; align-items: center; justify-content: space-between; color: #F4EBD0; font-size: 1.15cqw; }
    #root .row b { color: #D4AF37; font-size: 1.5cqw; font-weight: 400; }
    #root .action { position: relative; min-height: 9.4cqh; border-top: 1px solid #D4AF37; display: flex; align-items: center; justify-content: space-between; gap: 1cqw; color: #D4AF37; font-size: 1.4cqw; line-height: 1.12; font-weight: 700; letter-spacing: -0.01em; }
    #root .action b { font-size: 1.5cqw; font-weight: 400; flex: 0 0 auto; }
    #root .qr { position: absolute; background: #1A1A18; border: 1px solid #D4AF37; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 4; }
    #root .qr svg { width: 5.2cqw; height: 9.2cqh; margin-bottom: 1cqh; }
    #root .qr path { fill: none; stroke: #D4AF37; stroke-width: 5; stroke-linecap: square; stroke-linejoin: miter; }
    #root .qr rect { fill: #D4AF37; }
    #root .qr span { color: #F4EBD0; font-size: 1.2cqw; line-height: 1; font-weight: 800; letter-spacing: -0.02em; }
"""


def head(fid, num, cat, dur):
    return f"""<template>
  <div id="root" data-composition-id="{fid}" data-width="1920" data-height="1080" data-duration="{dur}">
    <div class="clip" data-start="0" data-duration="{dur}" data-track-index="0">
      <div class="bg"></div><div class="rule top"></div><div class="rule bottom"></div>
      <div class="mono catalogue">{cat}</div><div class="mono number">{num}</div>
    </div>
"""


QR_SVG = """<svg viewBox="0 0 132 132" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path class="draw" d="M10 48 V10 H48" /><path class="draw" d="M84 10 H122 V48" />
        <path class="draw" d="M122 84 V122 H84" /><path class="draw" d="M48 122 H10 V84" />
        <rect x="43" y="43" width="17" height="17" /><rect x="72" y="43" width="17" height="17" />
        <rect x="43" y="72" width="17" height="17" /><rect x="72" y="72" width="17" height="17" /></svg>"""


# ───────────────────────── phone walkthrough (Pixel / Samsung) ─────────────────────────
def walkthrough(fid, num, cat, headline, steps, screens, voice, slides, qr_at, qr_label, last_action, last_hi):
    dur = round(voice + 0.5, 3)
    P = "w" + num
    scr = ""
    for i, s in enumerate(screens):
        if i == len(screens) - 1:
            scr += f"""
            <article class="screen" data-layout-allow-overlap><div class="mono kicker" data-layout-allow-overlap>{s['kicker']}</div><div class="stitle" data-layout-allow-overlap>{s['title']}</div>
              <div class="action" id="{P}-act" data-layout-allow-overlap><span data-layout-allow-overlap>{last_action}</span><b data-layout-allow-overlap>+</b></div>
              <div class="qr" id="{P}-qr" data-layout-allow-overlap style="left:5.6cqw;top:24cqh;width:10.6cqw;height:17cqh;">{QR_SVG}<span>{qr_label}</span></div>
            </article>"""
        else:
            rows = "".join(
                f'<div class="row" id="{P}-s{i}r{j}" data-layout-allow-overlap><span data-layout-allow-overlap>{t}</span><b data-layout-allow-overlap>›</b></div>' for j, t in enumerate(s["rows"])
            )
            scr += f"""
            <article class="screen" data-layout-allow-overlap><div class="mono kicker" data-layout-allow-overlap>{s['kicker']}</div><div class="stitle" data-layout-allow-overlap>{s['title']}</div>{rows}</article>"""
    step_html = "".join(
        f'<div class="step" id="{P}-step{i}"><span class="mono sn">{i+1:02d}</span><span class="sl">{t}</span></div>'
        for i, (t, _) in enumerate(steps)
    )
    n = len(screens)
    html = head(fid, num, cat, dur) + f"""
    <div class="clip" data-start="0" data-duration="{dur}" data-track-index="1">
      <div class="stage">
        <header class="headline"><span>{headline[0]}</span><strong>{headline[1]}</strong></header>
        <section class="steps" id="{P}-steps">{step_html}</section>
        <div class="phone" id="{P}-phone" style="left:36.45cqw;width:27.1cqw;">
          <div class="mono phone-cap"><span>SETTINGS</span><span class="status">09:41</span></div>
          <div class="viewport"><div class="world" id="{P}-world" data-layout-allow-overflow style="width:{n*24.2}cqw;">{scr}
          </div></div>
          <div class="phone-foot"><span></span><span></span><span></span></div>
        </div>
        <aside class="now" id="{P}-now"><div class="mono nk">TAP</div><div class="nl" id="{P}-nl"></div><div class="mono nc" id="{P}-nc"></div></aside>
      </div>
    </div>
  </div>

  <style>{BASE_CSS}
    #root .steps {{ position: absolute; left: 5.7cqw; top: 22.5cqh; width: 26cqw; z-index: 4; }}
    #root .step {{ min-height: 8.1cqh; display: flex; align-items: center; gap: 1.2cqw; border-top: 1px solid #282826; }}
    #root .step:last-child {{ border-bottom: 1px solid #282826; }}
    #root .step .sn {{ font-size: 0.72cqw; color: #D4AF37; flex: 0 0 2cqw; }}
    #root .step .sl {{ font-size: 1.5cqw; line-height: 1.12; font-weight: 600; letter-spacing: -0.015em; color: #8C897F; }}
    #root .world {{ position: absolute; left: 0; top: 0; height: 100%; display: flex; will-change: transform; }}
    #root .screen {{ position: relative; width: 24.2cqw; height: 100%; flex: 0 0 24.2cqw; padding: 1.7cqh 1.25cqw; overflow: hidden; }}
    #root .now {{ position: absolute; left: 68.5cqw; top: 31cqh; width: 26cqw; z-index: 4; }}
    #root .nk {{ font-size: 0.72cqw; color: #D4AF37; margin-bottom: 1.6cqh; }}
    #root .nl {{ font-size: 3.1cqw; line-height: 1.02; font-weight: 800; letter-spacing: -0.03em; color: #D4AF37; min-height: 19cqh; }}
    #root .nc {{ font-size: 0.72cqw; color: #8C897F; margin-top: 1.2cqh; }}
  </style>

  <script>
    (() => {{
      const frame = document.querySelector('[data-composition-id="{fid}"]');
      const $ = (s) => frame.querySelector(s);
      const $$ = (s) => Array.from(frame.querySelectorAll(s));
      const tl = gsap.timeline({{ paused: true }});
      window.__timelines = window.__timelines || {{}};
      window.__timelines["{fid}"] = tl;

      const steps = {json.dumps(steps)};
      const slides = {json.dumps(slides)};
      const hi = {json.dumps(last_hi)};
      const duration = {voice};
      $$('.draw').forEach((p) => {{ const L = p.getTotalLength(); p.style.strokeDasharray = String(L); p.style.strokeDashoffset = String(L); }});

      tl.fromTo('.headline', {{ opacity: 0, y: 24 }}, {{ opacity: 1, y: 0, duration: 0.58, ease: 'power2.out' }}, 0.08);
      tl.fromTo('#{P}-phone', {{ opacity: 0.42, y: 54 }}, {{ opacity: 1, y: 0, duration: 0.82, ease: 'power3.out' }}, 0);
      tl.fromTo('#{P}-steps', {{ opacity: 0, x: -40 }}, {{ opacity: 1, x: 0, duration: 0.7, ease: 'power3.out' }}, 0.5);
      tl.fromTo('#{P}-now', {{ opacity: 0, x: 40 }}, {{ opacity: 1, x: 0, duration: 0.7, ease: 'power3.out' }}, 0.6);

      // each step lights up as it is spoken
      steps.forEach((s, i) => {{
                tl.to('#{P}-step' + i + ' .sl', {{ color: '#D4AF37', duration: 0.3 }}, s[1]);
        if (i > 0) tl.to('#{P}-step' + (i - 1) + ' .sl', {{ color: '#F4EBD0', duration: 0.3 }}, s[1]);
      }});

      // the screen rows that are tapped turn gold on their cue
      const rowCues = {json.dumps([[f"{P}-s{i}r{s['active']}", s['hi']] for i, s in enumerate(screens[:-1])])};
      rowCues.forEach((r) => {{
        tl.to('#' + r[0], {{ borderTopColor: '#D4AF37', duration: 0.3 }}, r[1]);
        tl.to('#' + r[0] + ' span', {{ color: '#D4AF37', fontWeight: 700, duration: 0.3 }}, r[1]);
      }});
      slides.forEach((t, i) => {{
        tl.to('#{P}-world', {{ x: (-24.2 * (i + 1)) + 'cqw', duration: 0.72, ease: 'power3.inOut' }}, t);
      }});
      tl.fromTo('#{P}-act', {{ opacity: 0, x: -30 }}, {{ opacity: 1, x: 0, duration: 0.5, ease: 'power2.out' }}, hi);

      // the big "tap this" label follows the narration
      const driver = {{ t: 0 }};
      const states = [[0, 'get ready', 'ANDROID']].concat(steps.map((s, i) => [s[1], s[0], 'STEP ' + (i + 1) + ' OF ' + steps.length]));
      const at = () => {{ let k = states[0]; for (const s of states) if (driver.t >= s[0]) k = s; return k; }};
      tl.to(driver, {{ t: duration + 1, duration: duration + 1, ease: 'none', onUpdate: () => {{
        const k = at(); $('#{P}-nl').textContent = k[1]; $('#{P}-nc').textContent = k[2];
      }} }}, 0);

      // scanner
      const qr = $('#{P}-qr');
      tl.fromTo('#{P}-qr', {{ opacity: 0, scale: 0.5 }}, {{ opacity: 1, scale: 1, duration: 0.6, ease: 'power3.out' }}, {qr_at});
      $$('#{P}-qr .draw').forEach((p, i) => tl.to(p, {{ strokeDashoffset: 0, duration: 0.36, ease: 'power2.out' }}, {qr_at} + 0.12 + i * 0.26));
      tl.fromTo('#{P}-qr rect', {{ opacity: 0 }}, {{ opacity: 1, duration: 0.3, stagger: 0.07, ease: 'power1.out' }}, {qr_at} + 1.1);
    }})();
  </script>
</template>
"""
    return html


# ───────────────────────── frame 1: hook ─────────────────────────
def hook():
    fid, num, voice = "01-android-two-minutes", "01", 9.613
    dur = round(voice + 0.5, 3)
    html = head(fid, num, "ANDROID / eSIM SETUP", dur) + f"""
    <div class="clip" data-start="0" data-duration="{dur}" data-track-index="1">
      <div class="stage">
        <div class="mono kick" id="h-kick">YOUR TRAVEL eSIM</div>
        <div class="big" id="h-big">android.</div>
        <div class="mid" id="h-mid"><span class="mono">IN YOUR INBOX</span><i></i></div>
        <div class="two" id="h-two"><span class="about">about</span><span class="num">two minutes.</span></div>
        <div class="mono together" id="h-together">LET&#8217;S DO IT TOGETHER</div>

        <div class="phone" id="h-phone" style="left:67cqw;width:21cqw;">
          <div class="mono phone-cap"><span>INBOX</span><span class="status">09:41</span></div>
          <div class="viewport">
            <div class="mail" id="h-mail"><div class="dot"></div><div><div class="from">GoTrips</div><div class="subj">your travel eSIM</div></div></div>
            <div class="qrcard" id="h-qrcard">{QR_SVG.replace('class="draw" ', '')}<span class="mono">SCAN LATER</span></div>
            <svg class="timer" id="h-timer" viewBox="0 0 120 120" aria-hidden="true">
              <circle cx="60" cy="60" r="48" class="trk"/><circle cx="60" cy="60" r="48" class="arc" id="h-arc" transform="rotate(-90 60 60)"/>
            </svg>
            <div class="clock" id="h-clock">2:00</div>
          </div>
          <div class="phone-foot"><span></span><span></span><span></span></div>
        </div>
      </div>
    </div>
  </div>

  <style>{BASE_CSS}
    #root .kick {{ position: absolute; left: 5.7cqw; top: 15.5cqh; font-size: 0.9cqw; color: #D4AF37; }}
    #root .big {{ position: absolute; left: 5.2cqw; top: 22.5cqh; font-size: 13cqw; line-height: 0.88; font-weight: 900; letter-spacing: -0.04em; }}
    #root .mid {{ position: absolute; left: 5.7cqw; top: 51.5cqh; width: 52cqw; display: flex; align-items: center; gap: 1.2cqw; font-size: 0.9cqw; color: #D4AF37; }}
    #root .mid i {{ display: block; height: 1px; flex: 1; background: #D4AF37; transform-origin: left; }}
    #root .two {{ position: absolute; left: 5.7cqw; top: 56.5cqh; display: flex; flex-direction: column; }}
    #root .about {{ font-size: 2.3cqw; font-weight: 600; letter-spacing: -0.02em; color: #F4EBD0; margin-bottom: 0.4cqh; }}
    #root .num {{ font-size: 7.2cqw; line-height: 0.95; font-weight: 900; letter-spacing: -0.04em; color: #D4AF37; }}
    #root .together {{ position: absolute; left: 5.7cqw; top: 77cqh; font-size: 0.9cqw; color: #F4EBD0; }}
    #root .mail {{ display: flex; gap: 1cqw; align-items: center; height: 9cqh; border-top: 1px solid #D4AF37; }}
    #root .mail .dot {{ width: 0.55cqw; height: 0.55cqw; border-radius: 50%; background: #D4AF37; flex: 0 0 auto; }}
    #root .from {{ font-size: 0.8cqw; color: #D4AF37; font-weight: 700; }}
    #root .subj {{ font-size: 1.25cqw; font-weight: 800; letter-spacing: -0.02em; }}
    #root .qrcard {{ position: absolute; left: 3.7cqw; top: 11cqh; width: 10.4cqw; height: 15cqh; border: 1px solid #282826; display: flex; flex-direction: column; align-items: center; justify-content: center; }}
    #root .qrcard svg {{ width: 5cqw; height: 8cqh; }}
    #root .qrcard svg path {{ fill: none; stroke: #504E48; stroke-width: 5; }}
    #root .qrcard svg rect {{ fill: #D4AF37; }}
    #root .qrcard span {{ font-size: 0.55cqw; color: #8C897F; margin-top: 0.8cqh; }}
    #root .timer {{ position: absolute; left: 4.6cqw; top: 27.5cqh; width: 8.6cqw; height: 14cqh; }}
    #root .timer circle {{ fill: none; stroke-width: 5; }}
    #root .timer .trk {{ stroke: #282826; }}
    #root .timer .arc {{ stroke: #D4AF37; stroke-linecap: butt; }}
    #root .clock {{ position: absolute; left: 4.6cqw; top: 27.5cqh; width: 8.6cqw; height: 14cqh; display: flex; align-items: center; justify-content: center; font-size: 1.7cqw; font-weight: 800; letter-spacing: -0.02em; }}
  </style>

  <script>
    (() => {{
      const tl = gsap.timeline({{ paused: true }});
      window.__timelines = window.__timelines || {{}};
      window.__timelines["{fid}"] = tl;
      const arc = document.getElementById('h-arc');
      const L = 2 * Math.PI * 48; arc.style.strokeDasharray = String(L); arc.style.strokeDashoffset = String(L);

      tl.fromTo('#h-phone', {{ opacity: 0.4, y: 60 }}, {{ opacity: 1, y: 0, duration: 0.9, ease: 'power3.out' }}, 0);
      tl.fromTo('#h-big', {{ opacity: 0, y: 40 }}, {{ opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }}, 0.5);
      tl.fromTo('#h-kick', {{ opacity: 0, y: 12 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' }}, 1.9);
      tl.fromTo('#h-mail', {{ opacity: 0, x: 40 }}, {{ opacity: 1, x: 0, duration: 0.6, ease: 'power3.out' }}, 2.1);
      tl.fromTo('#h-mid', {{ opacity: 0 }}, {{ opacity: 1, duration: 0.5 }}, 3.0);
      tl.fromTo('#h-mid i', {{ scaleX: 0 }}, {{ scaleX: 1, duration: 0.9, ease: 'power2.out' }}, 3.1);
      tl.fromTo('#h-qrcard', {{ opacity: 0, scale: 0.8 }}, {{ opacity: 1, scale: 1, duration: 0.5, ease: 'power3.out' }}, 3.95);
      tl.fromTo('#h-two', {{ opacity: 0, y: 30 }}, {{ opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }}, 5.7);
      tl.fromTo('#h-timer, #h-clock', {{ opacity: 0 }}, {{ opacity: 1, duration: 0.4 }}, 6.3);
      tl.to('#h-arc', {{ strokeDashoffset: 0, duration: 1.4, ease: 'power2.inOut' }}, 6.45);
      tl.fromTo('#h-together', {{ opacity: 0, y: 10 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' }}, 7.6);
    }})();
  </script>
</template>
"""
    return fid, html


# ───────────────────────── frame 5: same phone / manual entry ─────────────────────────
def manual():
    fid, num, voice = "05-same-phone-or-manual", "05", 15.882
    dur = round(voice + 0.5, 3)
    html = head(fid, num, "ANDROID / MANUAL ENTRY", dur) + f"""
    <div class="clip" data-start="0" data-duration="{dur}" data-track-index="1">
      <div class="stage">
        <header class="headline"><span>QR on this</span><strong>same phone?</strong></header>

        <div class="phone" id="m-a" style="left:8cqw;width:22cqw;">
          <div class="mono phone-cap"><span>YOUR EMAIL</span><span class="status">09:41</span></div>
          <div class="viewport">
            <div class="qr" id="m-qr" style="position:relative;left:1.6cqw;top:3.5cqh;width:15.6cqw;height:27cqh;">{QR_SVG.replace('class="draw" ', '')}<span>your QR</span>
              <svg class="slash" id="m-slash" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true"><path id="m-slash-p" d="M4 96 L96 4"/></svg></div>
          </div>
          <div class="phone-foot"><span></span><span></span><span></span></div>
        </div>
        <div class="mono tag a" id="m-tag-a">NO SECOND SCREEN</div>

        <div class="phone" id="m-b" style="left:38cqw;width:27.1cqw;">
          <div class="mono phone-cap"><span>ADD eSIM</span><span class="status">09:41</span></div>
          <div class="viewport">
            <div class="b-pad">
              <div class="mono kicker">SET UP BY HAND</div>
              <div class="stitle" style="margin-bottom:1.2cqh;">enter the details</div>
              <div class="action" id="m-act1"><span>enter code manually</span><b>+</b></div>
              <div class="action alt" id="m-act2"><span>enter activation code</span><b>+</b></div>
              <div class="field" id="m-f1"><span class="mono fl">ADDRESS</span><span class="fv" id="m-v1"></span></div>
              <div class="field" id="m-f2"><span class="mono fl">ACTIVATION CODE</span><span class="fv" id="m-v2"></span></div>
            </div>
          </div>
          <div class="phone-foot"><span></span><span></span><span></span></div>
        </div>

        <section class="notes">
          <div class="note" id="m-n1"><span class="mono nn">01</span><span class="nt">skip the scanner</span></div>
          <div class="note" id="m-n2"><span class="mono nn">02</span><span class="nt">enter code manually, or enter activation code</span></div>
          <div class="note" id="m-n3"><span class="mono nn">03</span><span class="nt">address and activation code, from your email</span></div>
        </section>
        <div class="mono vary" id="m-vary">MENU NAMES VARY BY MODEL</div>
      </div>
    </div>
  </div>

  <style>{BASE_CSS}
    #root .slash {{ position: absolute; left: 0; top: 0; width: 100%; height: 100%; z-index: 6; overflow: visible; pointer-events: none; }}
    #root .qr .slash {{ width: 100%; height: 100%; margin: 0; }}
    #root .slash path {{ fill: none; stroke: #D4AF37; stroke-width: 2.2; vector-effect: non-scaling-stroke; stroke-linecap: square; }}
    #root .tag {{ position: absolute; font-size: 0.72cqw; color: #D4AF37; top: 79cqh; }}
    #root .tag.a {{ left: 8cqw; }}
    #root .b-pad {{ padding: 1.7cqh 1.25cqw; }}
    #root .action.alt {{ margin-top: 0.8cqh; }}
    #root .b-pad .action {{ min-height: 6.6cqh; }}
    #root .field {{ margin-top: 1.3cqh; border: 1px solid #282826; height: 6.2cqh; padding: 0.9cqh 1cqw; display: flex; flex-direction: column; justify-content: center; gap: 0.4cqh; }}
    #root .field .fl {{ font-size: 0.55cqw; color: #D4AF37; }}
    #root .field .fv {{ font-size: 1.1cqw; letter-spacing: 0.04em; color: #F4EBD0; min-height: 1.3cqw; }}
    #root .notes {{ position: absolute; left: 70cqw; top: 24cqh; width: 24cqw; z-index: 4; }}
    #root .note {{ min-height: 11cqh; display: flex; gap: 1.2cqw; align-items: center; border-top: 1px solid #282826; }}
    #root .note:last-child {{ border-bottom: 1px solid #282826; }}
    #root .note .nn {{ font-size: 0.72cqw; color: #D4AF37; flex: 0 0 1.8cqw; }}
    #root .note .nt {{ font-size: 1.5cqw; line-height: 1.15; font-weight: 600; letter-spacing: -0.015em; color: #8C897F; }}
    #root .vary {{ position: absolute; left: 70cqw; top: 66cqh; font-size: 0.72cqw; color: #8C897F; }}
  </style>

  <script>
    (() => {{
      const tl = gsap.timeline({{ paused: true }});
      window.__timelines = window.__timelines || {{}};
      window.__timelines["{fid}"] = tl;
      document.querySelectorAll('#m-qr .draw, #m-slash-p').forEach((p) => {{ if (p.getTotalLength) {{ const L = p.getTotalLength(); p.style.strokeDasharray = String(L); p.style.strokeDashoffset = String(L); }} }});

      tl.fromTo('.headline', {{ opacity: 0, y: 24 }}, {{ opacity: 1, y: 0, duration: 0.58, ease: 'power2.out' }}, 0.08);
      tl.fromTo('#m-a', {{ opacity: 0.4, y: 50 }}, {{ opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }}, 0.3);
      tl.fromTo('#m-b', {{ opacity: 0.4, y: 50 }}, {{ opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }}, 0.45);
      tl.fromTo('#m-qr', {{ opacity: 0 }}, {{ opacity: 1, duration: 0.5 }}, 0.9);

      // 3.16 "skip the scanner"
      tl.to('#m-slash-p', {{ strokeDashoffset: 0, duration: 0.6, ease: 'power2.out' }}, 3.16);
      tl.to('#m-qr', {{ opacity: 0.32, duration: 0.5 }}, 3.3);
      tl.fromTo('#m-tag-a', {{ opacity: 0, y: 8 }}, {{ opacity: 1, y: 0, duration: 0.4 }}, 3.4);
      tl.to('#m-n1 .nt', {{ color: '#F4EBD0', duration: 0.4 }}, 3.16);

      // 5.59 "Enter code manually" · 7.55 "Enter activation code"
      tl.fromTo('#m-act1', {{ opacity: 0, x: -30 }}, {{ opacity: 1, x: 0, duration: 0.5, ease: 'power2.out' }}, 5.59);
      tl.to('#m-n2 .nt', {{ color: '#F4EBD0', duration: 0.4 }}, 5.59);
      tl.fromTo('#m-act2', {{ opacity: 0, x: 30 }}, {{ opacity: 1, x: 0, duration: 0.5, ease: 'power2.out' }}, 7.55);

      // 10.37 address · 11.05 activation code (placeholders only, never real data)
      tl.fromTo('#m-f1', {{ opacity: 0, y: 14 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' }}, 10.37);
      tl.to('#m-n3 .nt', {{ color: '#F4EBD0', duration: 0.4 }}, 10.37);
      tl.fromTo('#m-f2', {{ opacity: 0, y: 14 }}, {{ opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' }}, 11.05);
      const typed = {{ a: 0, b: 0 }};
      const A = 'smdp.example.com', B = 'XXXXX-XXXXX-XXXXX';
      tl.to(typed, {{ a: A.length, duration: 0.9, ease: 'none', onUpdate: () => {{ document.getElementById('m-v1').textContent = A.slice(0, Math.round(typed.a)); }} }}, 10.7);
      tl.to(typed, {{ b: B.length, duration: 0.9, ease: 'none', onUpdate: () => {{ document.getElementById('m-v2').textContent = B.slice(0, Math.round(typed.b)); }} }}, 11.4);

      tl.fromTo('#m-vary', {{ opacity: 0 }}, {{ opacity: 1, duration: 0.5 }}, 13.31);
    }})();
  </script>
</template>
"""
    return fid, html


def write(fid, html):
    path = os.path.join(OUT, fid + ".html")
    with open(path, "w") as f:
        f.write(html)
    print("wrote", path, len(html), "bytes")


if __name__ == "__main__":
    os.makedirs(OUT, exist_ok=True)

    fid, h = hook()
    write(fid, h)

    pixel = walkthrough(
        "03-google-pixel", "03", "ANDROID / GOOGLE PIXEL", ["on a google", "pixel"],
        steps=[["open settings", 2.09], ["network & internet", 3.24], ["SIMs", 4.99], ["add SIM", 6.61],
               ["download a SIM instead", 8.58], ["scan the QR code", 10.71]],
        screens=[
            {"kicker": "SETTINGS", "title": "settings", "rows": ["network & internet", "connected devices", "apps"], "active": 0, "hi": 3.24},
            {"kicker": "NETWORK", "title": "network &amp; internet", "rows": ["internet", "SIMs", "hotspot &amp; tethering"], "active": 1, "hi": 4.99},
            {"kicker": "SIMS", "title": "SIMs", "rows": ["add SIM", "preferred SIMs", "calls &amp; SMS"], "active": 0, "hi": 6.61},
            {"kicker": "ADD A SIM", "title": "add a SIM"},
        ],
        voice=13.192, slides=[4.1, 5.8, 7.5], qr_at=10.71, qr_label="scan QR",
        last_action="download a SIM instead", last_hi=8.58,
    )
    write("03-google-pixel", pixel)

    samsung = walkthrough(
        "04-samsung-galaxy", "04", "ANDROID / SAMSUNG GALAXY", ["on a samsung", "galaxy"],
        steps=[["open settings", 2.48], ["connections", 3.71], ["SIM manager", 5.08], ["add eSIM", 7.04],
               ["scan QR code from service provider", 8.88], ["point the camera at your code", 11.78]],
        screens=[
            {"kicker": "SETTINGS", "title": "settings", "rows": ["connections", "sounds &amp; vibration", "notifications"], "active": 0, "hi": 3.71},
            {"kicker": "CONNECTIONS", "title": "connections", "rows": ["Wi-Fi", "bluetooth", "SIM manager"], "active": 2, "hi": 5.08},
            {"kicker": "SIM MANAGER", "title": "SIM manager", "rows": ["add eSIM", "preferred SIM", "SIM 1"], "active": 0, "hi": 7.04},
            {"kicker": "ADD eSIM", "title": "add eSIM"},
        ],
        voice=13.845, slides=[4.5, 6.3, 7.9], qr_at=11.78, qr_label="scan QR",
        last_action="scan QR code from service provider", last_hi=8.88,
    )
    write("04-samsung-galaxy", samsung)

    fid, h = manual()
    write(fid, h)
