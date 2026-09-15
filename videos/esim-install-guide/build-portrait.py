#!/usr/bin/env python3
"""Generate the 9:16 portrait cut of the eSIM installation guide.

Same script, same narration, same timings as the landscape composition — the
scenes are laid out again for a tall screen rather than scaled down, so the
type is bigger on a phone instead of smaller. Content sits above y=1340; the
captions track owns the band at 1380-1580.

Writes:  compositions/frames-portrait/*.html  and  index-portrait.html
"""
import pathlib

SRC = pathlib.Path(__file__).parent
# The portrait cut is its own project: one project may declare only one root
# composition, and assets are shared with the landscape cut by symlink.
ROOT = SRC.parent / "esim-install-guide-portrait"
OUT = ROOT / "compositions" / "frames"
OUT.mkdir(parents=True, exist_ok=True)

W, H = 1080, 1920
BAND_TOP = 1380          # captions keep-out
SAFE_TOP = 150

# (id, start, duration) mirrored exactly from index.html
SCENES = [
    ("00-logo-intro",           0.0,     2.5),
    ("01-no-second-guessing",   2.5,     6.743),
    ("02-three-things-first",   8.743,  10.453),
    ("03-iphone-path",         18.696,  10.191),
    ("04-one-phone-is-enough", 28.387,  13.692),
    ("05-android-path",        41.579,  13.927),
    ("06-make-it-the-travel-line", 55.006, 10.374),
    ("07-keep-the-profile",    64.88,   10.736),
    ("08-ready-to-travel",     75.016,   6.743),
    ("09-logo-outro",          81.259,   3.0),
]
VOICE = [
    ("01.wav",  2.5,   6.243), ("02.wav",  8.743,  9.953),
    ("03.wav", 18.696, 9.691), ("04.wav", 28.387, 13.192),
    ("05.wav", 41.579, 13.427), ("06.wav", 55.006, 9.874),
    ("07.wav", 64.88, 10.136), ("08.wav", 75.016, 6.243),
]
TOTAL = 84.259

BASE_CSS = """
    @font-face { font-family:"Barlow"; src:local("Arial"); font-style:normal; font-weight:400 900; }
    @font-face { font-family:"IBM Plex Mono"; src:local("Courier New"); font-style:normal; font-weight:400 700; }
    #root { position:relative; width:1080px; height:1920px; overflow:hidden;
            container-type:size; color:#F4EBD0; font-family:"Barlow"; }
    .stage { position:absolute; inset:0; overflow:hidden;
             background:
               radial-gradient(760px 620px at 50% 24%, rgba(212,175,55,.07), transparent 66%),
               #080706; }
    /* hairline frame, same device as the landscape cut */
    .edge { position:absolute; inset:40px; border:1px solid rgba(212,175,55,.15); pointer-events:none; }
    .col { position:absolute; left:100px; right:100px; top:__TOP__px; }
    .kicker { font-family:"IBM Plex Mono"; font-size:26px; font-weight:500; letter-spacing:.22em;
              text-transform:uppercase; color:#D4AF37; margin-bottom:34px; }
    .head { font-size:96px; line-height:1.04; font-weight:800; letter-spacing:-.025em; color:#fff; }
    .head em { font-style:normal; color:#D4AF37; }
    .sub { margin-top:34px; font-size:40px; line-height:1.45; color:#B9B3A3; font-weight:400; }
    .card { background:#12110F; border:1px solid #282826; border-radius:22px;
            padding:38px 40px; margin-top:26px; }
    .card-n { font-family:"IBM Plex Mono"; font-size:24px; letter-spacing:.2em; color:#D4AF37;
              margin-bottom:16px; }
    .card-t { font-size:46px; font-weight:700; color:#fff; letter-spacing:-.015em; line-height:1.15; }
    .card-d { margin-top:14px; font-size:34px; line-height:1.4; color:#A9A496; }
    .step-num { font-size:24px; }
""".replace("__TOP__", str(SAFE_TOP))


DUR = {sid: d for sid, _st, d in []}  # filled after SCENES is known


def frame(fid, body, css="", tl=""):
    dur = next((d for sid, _s, d in SCENES if sid == fid), 5.0)
    return f"""<template>
  <style>{BASE_CSS}{css}
  </style>

  <div id="root" data-composition-id="{fid}" data-width="{W}" data-height="{H}" data-duration="{dur}">
    <div class="stage">
      <div class="edge"></div>
{body}
    </div>
  </div>

  <script>
    (function () {{
      const frameId = "{fid}";
      window.__timelines = window.__timelines || {{}};
      const tl = gsap.timeline({{ paused: true }});
      const byId = (s) => document.getElementById(frameId + "-" + s);
      const byClass = (s) => Array.from(document.getElementsByClassName(frameId + "-" + s));
      // Absolute positions only: every state is a pure function of time.
      const up = (el, at, d) => {{
        if (!el) return;
        tl.fromTo(el, {{ opacity: 0, y: 34 }},
                      {{ opacity: 1, y: 0, duration: d || .72, ease: "power3.out" }}, at);
      }};
      const pop = (el, at, d) => {{
        if (!el) return;
        tl.fromTo(el, {{ opacity: 0, scale: .9 }},
                      {{ opacity: 1, scale: 1, duration: d || .8, ease: "expo.out" }}, at);
      }};
{tl}
      window.__timelines[frameId] = tl;
    }})();
  </script>
</template>
"""


def card(fid, n, title, detail, idx):
    return (f'        <div class="card {fid}-card" id="{fid}-card{idx}">\n'
            f'          <div class="card-n">{n}</div>\n'
            f'          <div class="card-t">{title}</div>\n'
            f'          <div class="card-d">{detail}</div>\n'
            f'        </div>')


def stagger(fid, cls, start, step=0.34):
    return (f'      byClass("{cls}").forEach(function (el, i) {{ '
            f'up(el, {start} + i * {step}); }});')


# ---------------------------------------------------------------- 00 intro ---
f = "00-logo-intro"
frames = {f: frame(f, f"""
      <div style="position:absolute;left:0;right:0;top:590px;text-align:center">
        <img id="{f}-logo" src="assets/brand/logo.png" alt=""
             style="width:400px;height:400px;filter:drop-shadow(0 0 70px rgba(212,175,55,.3))">
      </div>
      <div style="position:absolute;left:100px;right:100px;top:1075px;text-align:center">
        <div id="{f}-rule" style="width:150px;height:2px;margin:0 auto 40px;
             background:linear-gradient(90deg,transparent,#D4AF37,transparent)"></div>
        <div id="{f}-title" style="font-size:70px;font-weight:800;letter-spacing:-.02em;color:#D4AF37">
          eSIM Installation Guide</div>
        <div id="{f}-sub" style="margin-top:26px;font-family:'IBM Plex Mono';font-size:26px;
             letter-spacing:.22em;text-transform:uppercase;color:#6E6A60">gotrips.ai</div>
      </div>""",
      tl=f"""      pop(byId("logo"), 0.06, 1.1);
      tl.fromTo(byId("rule"), {{ scaleX: 0 }}, {{ scaleX: 1, duration: .7, ease: "power2.out" }}, .62);
      up(byId("title"), .70);
      up(byId("sub"), .95);""")}

# ------------------------------------------------------------------- 01 ------
f = "01-no-second-guessing"
frames[f] = frame(f, f"""
      <div class="col">
        <div class="kicker" id="{f}-k">Your travel eSIM</div>
        <div class="head" id="{f}-h">It is already<br>in your <em>inbox</em>.</div>
        <div class="sub" id="{f}-s">One minute, and it is safely on your phone.</div>
      </div>
      <div style="position:absolute;left:100px;right:100px;top:900px;display:flex;
                  align-items:center;justify-content:center;gap:52px">
        <div id="{f}-mail" style="width:300px;height:220px;border:3px solid #D4AF37;border-radius:20px;
             position:relative;background:#12110F">
          <div style="position:absolute;inset:0;overflow:hidden;border-radius:17px">
            <div style="position:absolute;left:-6px;right:-6px;top:-4px;height:150px;
                 border-bottom:3px solid #D4AF37;transform:skewY(12deg);transform-origin:left top"></div>
          </div>
        </div>
        <div id="{f}-qr" style="width:210px;height:210px;border:3px solid #D4AF37;border-radius:16px;
             background:
               linear-gradient(#F4EBD0 0 0) 22px 22px/56px 56px no-repeat,
               linear-gradient(#F4EBD0 0 0) 132px 22px/56px 56px no-repeat,
               linear-gradient(#F4EBD0 0 0) 22px 132px/56px 56px no-repeat,
               linear-gradient(#F4EBD0 0 0) 120px 128px/30px 30px no-repeat,
               #12110F"></div>
      </div>""",
      tl=f"""      up(byId("k"), .10); up(byId("h"), .26); up(byId("s"), .58);
      pop(byId("mail"), .80); pop(byId("qr"), 1.02);""")

# ------------------------------------------------------------------- 02 ------
f = "02-three-things-first"
cards = "\n".join([
    card(f, "01", "Connect to Wi-Fi", "Install over a stable connection.", 1),
    card(f, "02", "Check the phone", "eSIM capable, and not locked to a network.", 2),
    card(f, "03", "Open your QR code", "Or the activation details from GoTrips.", 3),
])
frames[f] = frame(f, f"""
      <div class="col">
        <div class="kicker" id="{f}-k">Before you begin</div>
        <div class="head" id="{f}-h">Three things<br>first.</div>
      </div>
      <div style="position:absolute;left:100px;right:100px;top:620px">
{cards}
      </div>""",
      tl=f"""      up(byId("k"), .10); up(byId("h"), .26);
{stagger(f, "card", 0.62, 0.40)}""")

# ------------------------------------------------------------------- 03 ------
f = "03-iphone-path"
rows = "\n".join(
    f'          <div class="{f}-row" id="{f}-row{i}" style="display:flex;align-items:center;'
    f'justify-content:space-between;padding:34px 34px;border-bottom:1px solid #232220">'
    f'<span style="font-size:40px;color:{"#fff" if i < 4 else "#8A867C"};font-weight:{700 if i==4 else 500}">{t}</span>'
    f'<span style="color:#D4AF37;font-size:34px">&rsaquo;</span></div>'
    for i, t in enumerate(["Settings", "Cellular / Mobile Data", "Add eSIM", "Use QR Code"], start=1))
frames[f] = frame(f, f"""
      <div class="col">
        <div class="kicker" id="{f}-k">Step by step</div>
        <div class="head" id="{f}-h">On <em>iPhone</em></div>
      </div>
      <div id="{f}-phone" style="position:absolute;left:150px;right:150px;top:560px;
           border:3px solid #33312C;border-radius:46px;background:#0C0B0A;overflow:hidden">
        <div style="padding:30px 34px 22px;font-family:'IBM Plex Mono';font-size:24px;
             letter-spacing:.18em;color:#6E6A60;text-transform:uppercase;
             border-bottom:1px solid #232220">Settings</div>
{rows}
      </div>
      <div class="sub" id="{f}-s" style="position:absolute;left:100px;right:100px;top:1210px;
           text-align:center;font-size:36px">Scan the code from another screen.</div>""",
      tl=f"""      up(byId("k"), .10); up(byId("h"), .26); pop(byId("phone"), .56, .9);
{stagger(f, "row", 0.90, 0.46)}
      up(byId("s"), 2.9);""")

# ------------------------------------------------------------------- 04 ------
f = "04-one-phone-is-enough"
frames[f] = frame(f, f"""
      <div class="col">
        <div class="kicker" id="{f}-k">Only one phone?</div>
        <div class="head" id="{f}-h">The QR code is on<br><em>this</em> phone.</div>
      </div>
      <div style="position:absolute;left:100px;right:100px;top:640px">
{card(f, "IOS 17.4 OR LATER", "Press and hold the code", "In your email or browser, then choose Add eSIM.", 1)}
{card(f, "ANY VERSION", "Or type it in", "Enter the activation details GoTrips sent you, by hand.", 2)}
      </div>
      <div class="sub" id="{f}-s" style="position:absolute;left:100px;right:100px;top:1185px;
           text-align:center;font-size:34px;color:#8A867C">Both routes install the same profile.</div>""",
      tl=f"""      up(byId("k"), .10); up(byId("h"), .26);
{stagger(f, "card", 0.66, 0.52)}
      up(byId("s"), 2.0);""")

# ------------------------------------------------------------------- 05 ------
f = "05-android-path"
def alt(i, a, b, c=None):
    opts = "".join(
        f'<span style="font-size:{"38" if n == 0 else "34"}px;color:{"#fff" if n == 0 else "#A9A496"};'
        f'font-weight:{700 if n == 0 else 500}">{o}</span>'
        + ('<span style="color:#D4AF37;font-size:26px;padding:0 18px">or</span>' if n < len([x for x in (a, b, c) if x]) - 1 else '')
        for n, o in enumerate([x for x in (a, b, c) if x]))
    return (f'        <div class="{f}-alt" id="{f}-alt{i}" style="margin-top:30px;padding:32px 36px;'
            f'background:#12110F;border:1px solid #282826;border-radius:20px;'
            f'display:flex;align-items:center;flex-wrap:wrap">{opts}</div>')
frames[f] = frame(f, f"""
      <div class="col">
        <div class="kicker" id="{f}-k">Menu names differ</div>
        <div class="head" id="{f}-h">On <em>Android</em>,<br>look for these.</div>
      </div>
      <div style="position:absolute;left:100px;right:100px;top:660px">
{alt(1, "Network &amp; internet", "Connections")}
{alt(2, "SIMs", "SIM manager")}
{alt(3, "Add eSIM", "Set up eSIM", "Add mobile plan")}
      </div>""",
      tl=f"""      up(byId("k"), .10); up(byId("h"), .26);
{stagger(f, "alt", 0.68, 0.55)}""")

# ------------------------------------------------------------------- 06 ------
f = "06-make-it-the-travel-line"
def check(i, t, d):
    return (f'        <div class="{f}-chk" id="{f}-chk{i}" style="display:flex;gap:28px;'
            f'align-items:flex-start;margin-top:36px">'
            f'<span style="flex:0 0 auto;width:46px;height:46px;border-radius:50%;'
            f'border:3px solid #D4AF37;color:#D4AF37;display:flex;align-items:center;'
            f'justify-content:center;font-size:26px;font-weight:800;margin-top:6px">&#10003;</span>'
            f'<span><span style="display:block;font-size:44px;font-weight:700;color:#fff;'
            f'letter-spacing:-.01em">{t}</span>'
            f'<span style="display:block;margin-top:10px;font-size:33px;color:#A9A496;'
            f'line-height:1.4">{d}</span></span></div>')
frames[f] = frame(f, f"""
      <div class="col">
        <div class="kicker" id="{f}-k">After it installs</div>
        <div class="head" id="{f}-h">Make it the<br><em>travel line</em>.</div>
      </div>
      <div style="position:absolute;left:100px;right:100px;top:680px">
{check(1, "Label it Travel", "So you can tell it from your home line.")}
{check(2, "Select it for mobile data", "Your home line can stay off.")}
{check(3, "Data roaming, only if told", "Follow the GoTrips plan instructions.")}
      </div>""",
      tl=f"""      up(byId("k"), .10); up(byId("h"), .26);
{stagger(f, "chk", 0.66, 0.50)}""")

# ------------------------------------------------------------------- 07 ------
f = "07-keep-the-profile"
frames[f] = frame(f, f"""
      <div class="col">
        <div class="kicker" id="{f}-k" style="color:#E8A33D">If it does not connect</div>
        <div class="head" id="{f}-h">Do <em>not</em> delete<br>the eSIM.</div>
      </div>
      <div style="position:absolute;left:100px;right:100px;top:660px">
{card(f, "01", "Restart the phone", "Give the profile a moment to attach.", 1)}
{card(f, "02", "Check the travel line is on", "And that it is selected for data.", 2)}
{card(f, "03", "Contact GoTrips support", "Quote your ticket details and we will pick it up.", 3)}
      </div>""",
      tl=f"""      up(byId("k"), .10); up(byId("h"), .26);
{stagger(f, "card", 0.64, 0.44)}""")

# ------------------------------------------------------------------- 08 ------
f = "08-ready-to-travel"
frames[f] = frame(f, f"""
      <div style="position:absolute;left:100px;right:100px;top:520px;text-align:center">
        <div id="{f}-ring" style="width:210px;height:210px;margin:0 auto 62px;border-radius:50%;
             border:5px solid #D4AF37;display:flex;align-items:center;justify-content:center;
             box-shadow:0 0 60px rgba(212,175,55,.25)">
          <span style="font-size:96px;color:#D4AF37;font-weight:800">&#10003;</span>
        </div>
        <div class="head" id="{f}-h" style="font-size:104px">Installed.<br>Connected.<br><em>Ready.</em></div>
        <div class="sub" id="{f}-s" style="text-align:center">Your GoTrips eSIM is ready to travel.</div>
      </div>""",
      tl=f"""      pop(byId("ring"), .08, 1.0); up(byId("h"), .42); up(byId("s"), .80);""")

# ------------------------------------------------------------------- 09 ------
f = "09-logo-outro"
frames[f] = frame(f, f"""
      <div style="position:absolute;left:0;right:0;top:600px;text-align:center">
        <img id="{f}-logo" src="assets/brand/logo.png" alt=""
             style="width:360px;height:360px;filter:drop-shadow(0 0 70px rgba(212,175,55,.28))">
      </div>
      <div style="position:absolute;left:100px;right:100px;top:1040px;text-align:center">
        <div id="{f}-dom" style="font-size:88px;font-weight:800;letter-spacing:-.02em;color:#F4EBD0">
          gotrips<span style="color:#D4AF37">.ai</span></div>
        <div id="{f}-rule" style="width:150px;height:2px;margin:38px auto;
             background:linear-gradient(90deg,transparent,#D4AF37,transparent)"></div>
        <div id="{f}-help" style="font-family:'IBM Plex Mono';font-size:25px;letter-spacing:.16em;
             text-transform:uppercase;color:#8A867C">Need help? Use the support window</div>
      </div>""",
      tl=f"""      pop(byId("logo"), .06, 1.0); up(byId("dom"), .46);
      tl.fromTo(byId("rule"), {{ scaleX: 0 }}, {{ scaleX: 1, duration: .6, ease: "power2.out" }}, .72);
      up(byId("help"), .84);""")

for fid, html in frames.items():
    (OUT / f"{fid}.html").write_text(html)

# --------------------------------------------------------------- root -------
scene_divs = []
for i, (fid, st, du) in enumerate(SCENES):
    scene_divs.append(f"""      <div
        id="el-{fid}"
        class="scene"
        data-composition-id="{fid}"
        data-composition-src="compositions/frames/{fid}.html"
        data-start="{st}"
        data-duration="{du}"
        data-track-index="{i % 2}"
      ></div>""")

audio = "\n".join(
    f'      <audio id="el-v{n}" src="assets/voice/{n}" data-start="{st}" '
    f'data-duration="{du}" data-track-index="10" data-volume="1"></audio>'
    for n, st, du in VOICE)

INDEX = f"""<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width={W}, height={H}" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.14.2/dist/gsap.min.js"></script>
    <style>
      * {{ margin:0; padding:0; box-sizing:border-box; }}
      html, body {{ width:{W}px; height:{H}px; overflow:hidden; background:#080706; }}
      #root {{ position:relative; width:{W}px; height:{H}px; overflow:hidden; background:#080706; }}
      .scene {{ position:absolute; inset:0; width:100%; height:100%; }}
    </style>
  </head>
  <body>
    <div
      id="root"
      data-composition-id="main"
      data-start="0"
      data-duration="{TOTAL}"
      data-width="{W}"
      data-height="{H}"
    >
{chr(10).join(scene_divs)}

      <!-- captions -->
      <div
        id="el-captions"
        class="scene"
        data-composition-id="captions"
        data-composition-src="compositions/captions.html"
        data-start="0"
        data-duration="{TOTAL}"
        data-track-index="9"
      ></div>

{audio}
      <audio id="el-bgm" src="assets/bgm/track.loop.mp3" data-start="0"
             data-duration="{TOTAL}" data-track-index="11" data-volume="0.12"></audio>
    </div>

    <script>
      window.__timelines = window.__timelines || {{}};
      window.__timelines["main"] = gsap.timeline({{ paused: true }});
    </script>
  </body>
</html>
"""
(ROOT / "index.html").write_text(INDEX)

# captions track + shared assets + project files
import shutil, json, os
shutil.copy(SRC / "compositions" / "captions-portrait.html", ROOT / "compositions" / "captions.html")
link = ROOT / "assets"
if not link.exists():
    os.symlink("../esim-install-guide/assets", link)
(ROOT / "package.json").write_text(json.dumps({
    "name": "esim-install-guide-portrait", "private": True, "type": "module",
    "scripts": {
        "dev": "npx --yes hyperframes@0.8.31 preview",
        "check": "npx --yes hyperframes@0.8.31 check",
        "render": "npx --yes hyperframes@0.8.31 render",
    }}, indent=2) + "\n")
(ROOT / "hyperframes.json").write_text(json.dumps({
    "$schema": "https://hyperframes.heygen.com/schema/hyperframes.json",
    "registry": "https://raw.githubusercontent.com/heygen-com/hyperframes/main/registry",
    "paths": {"blocks": "compositions", "components": "compositions/components", "assets": "assets"},
    "media": {"autoProxy": True}}, indent=2) + "\n")
(ROOT / "meta.json").write_text(json.dumps(
    {"id": "esim-install-guide-portrait", "name": "eSIM Installation Guide (9:16)"}, indent=2) + "\n")
(ROOT / ".gitignore").write_text("renders/\ncapture/\nsnapshots/\n.hyperframes/\n.media/\nnode_modules/\n")
print(f"wrote {len(frames)} portrait frames + index.html into {ROOT.name} ({TOTAL}s, {W}x{H})")
