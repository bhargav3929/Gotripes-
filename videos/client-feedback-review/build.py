#!/usr/bin/env python3
"""Generate index.html for the client-feedback review video.

Timing is derived from the real narration wav durations so the picture can
never drift from the voice. Re-run after regenerating any voice clip.
"""
import subprocess, json, html, pathlib

ROOT = pathlib.Path(__file__).parent
GAP = 0.45          # breath between scenes
TAIL = 0.50         # hold after the voice stops
OUTRO_EXTRA = 1.20  # a little longer on the closing card


def dur(path):
    out = subprocess.run(
        ["ffprobe", "-v", "error", "-show_entries", "format=duration",
         "-of", "default=nw=1:nk=1", str(path)],
        capture_output=True, text=True, check=True).stdout.strip()
    return round(float(out), 3)


# ---------------------------------------------------------------- content ---
# status: done | open | withyou
SCENES = [
    dict(id="s01-title", status=None, kind="title",
         eyebrow="GoTrips · Client feedback",
         head="What you asked for,<br><em>and what shipped</em>",
         sub="Amer's comments of 15 September 2026, point by point."),

    dict(id="s02-legend", status=None, kind="legend",
         head="How to read this",
         items=[("done", "Shipped", "Live on the site right now."),
                ("open", "Open", "Named, with the work that remains."),
                ("withyou", "With you", "Waiting on your side.")]),

    dict(id="s03-border", status="done", chip="Shipped", kind="split",
         label="Emirates visa route selector",
         quote="Circular design thickness should be increased for golden border, "
               "not matching with our Logo golden border thickness.",
         head="The gold ring is now as heavy as the logo",
         bullets=["Your logo's ring is 10% of its width. The selector's was 2.2%.",
                  "It is now 8.5%, plus the thin inner outline.",
                  "Painted as brushed gold rather than one flat band."],
         stat=("2.2%", "8.5%"),
         shot="shot-selector.png", fit="cover"),

    dict(id="s04-registration", status="done", chip="Shipped", kind="split",
         label="Customer registration window",
         quote="If we do same for customer registration pop window also, "
               "as circular, for all registrations forms.",
         head="The registration form moved into the same medallion",
         bullets=["Same black disc, same brushed ring, your logo on top.",
                  "Every field, step and check is exactly as it was.",
                  "On a phone it becomes a rounded card with the same ring.",
                  "This is the pop-up only. The separate agent, agency and partner pages are next, once you approve the look."],
         shot="shot-popup.png", fit="cover"),

    dict(id="s05-videologo", status="done", chip="Shipped", kind="split",
         label="eSIM installation video",
         quote="Log of our company in beginning of video and ending.",
         head="Logo title cards at both ends",
         bullets=["Opening and closing cards added, then re-rendered.",
                  "The guide now runs 84 seconds.",
                  "Fixed a logo file that had a checkerboard baked into it."],
         shot="shot-video-intro.png", fit="cover"),

    dict(id="s06-portrait", status="open", chip="Open", kind="ratio",
         label="eSIM installation video",
         quote="Can we have this 9:11 screen size too.",
         head="The portrait cut is the one thing not finished",
         bullets=["We read 9:11 as 9:16, the normal phone shape. Please confirm.",
                  "The captions track for it is already built.",
                  "Ten scenes still need laying out for a tall screen.",
                  "Shrinking the wide cut would only make the text smaller."]),

    dict(id="s07-care", status="done", chip="Shipped", kind="split",
         label="Support tickets",
         quote="Can we assign / route this ticket to an employee joined, "
               "or login only to handle customer care.",
         head="Yes. There is now a customer-care login",
         bullets=["It opens on the ticket queue and nothing else.",
                  "Every other manager page sends them straight back.",
                  "You create the person in support settings; they get a password by email.",
                  "Assigning a ticket now emails them and notes it on the thread."],
         shot="shot-care-sidebar.png", fit="contain"),

    dict(id="s08-routing", status="done", chip="Answered", kind="routing",
         label="Support tickets",
         quote="Whre is the ticket hitting? Email, and Manager dashboard...",
         head="Every ticket lands in three places",
         routes=[("01", "Manager portal", "Always. Saved before any email is attempted, so a ticket is never lost."),
                 ("02", "Your support inbox", "The address you set in Workflow settings."),
                 ("03", "The customer", "Ticket number, support hours and the response target.")],
         note="WhatsApp is built and switched off until you send the Business API details."),

    dict(id="s09-help", status="done", chip="Shipped", kind="split",
         label="Product documentation",
         quote="We will also need a product help guide for all work you do for website.",
         head="Eleven guides, one for each product",
         bullets=["Inside the manager portal, under Help.",
                  "Written for someone who has never seen the system.",
                  "Customer-care staff can read them too."],
         shot="shot-help.png", fit="contain"),

    dict(id="s10-withyou", status="withyou", chip="With you", kind="list",
         label="Nothing blocked on us",
         head="Four things are on your side",
         bullets=["Testing the support widget and logging a ticket.",
                  "The WhatsApp Business API cost and credentials.",
                  "The blocked supplier card on the UAE visa.",
                  "The support proposal, which you said is not urgent."]),

    dict(id="s11-close", status=None, kind="close",
         big="6 shipped · 1 open",
         sub="Live on gotrips.ai now. The ticket workflow demo is ready for next week's meeting."),
]

# ------------------------------------------------------------------ timing ---
t = 0.0
for i, s in enumerate(SCENES):
    v = ROOT / "assets" / "voice" / f"{i+1:02d}.wav"
    vd = dur(v)
    s["voice"] = f"assets/voice/{i+1:02d}.wav"
    s["vstart"] = round(t, 3)
    s["vdur"] = vd
    extra = TAIL + (OUTRO_EXTRA if i == len(SCENES) - 1 else 0)
    s["start"] = round(t, 3)
    s["dur"] = round(vd + extra, 3)
    t = round(t + vd + extra + (GAP if i < len(SCENES) - 1 else 0), 3)
TOTAL = round(t, 3)

# --------------------------------------------------------------- rendering ---
def esc(x):
    return html.escape(x, quote=False)


def bullets_html(bs):
    return "\n".join(
        f'<li class="bul"><span class="bul-dot"></span><span>{esc(b)}</span></li>' for b in bs)


def chip_html(s):
    if not s.get("chip"):
        return ""
    return f'<div class="chip chip--{s["status"]} anim-chip">{esc(s["chip"])}</div>'


def scene_body(s):
    k = s["kind"]
    if k == "title":
        return f"""
      <div class="title-wrap">
        <img class="title-logo anim-logo" src="assets/shots/logo.png" alt="">
        <div class="title-eyebrow anim-a">{esc(s['eyebrow'])}</div>
        <h1 class="title-head anim-b">{s['head']}</h1>
        <div class="title-rule anim-rule"></div>
        <p class="title-sub anim-c">{esc(s['sub'])}</p>
      </div>"""
    if k == "legend":
        rows = "\n".join(
            f"""<div class="legend-row anim-item">
                  <div class="chip chip--{c}">{esc(n)}</div>
                  <div class="legend-text">{esc(d)}</div>
                </div>""" for c, n, d in s["items"])
        return f"""
      <div class="center-wrap">
        <h2 class="sec-head anim-b">{esc(s['head'])}</h2>
        <div class="legend-list">{rows}</div>
      </div>"""
    if k == "split":
        stat = ""
        if s.get("stat"):
            a, b = s["stat"]
            stat = f"""<div class="stat anim-stat">
                  <span class="stat-from">{esc(a)}</span>
                  <span class="stat-arrow">to</span>
                  <span class="stat-to">{esc(b)}</span>
                </div>"""
        return f"""
      <div class="split">
        <div class="col-text">
          <div class="label anim-a">{esc(s['label'])}</div>
          <blockquote class="quote anim-q">{esc(s['quote'])}</blockquote>
          <h2 class="sec-head anim-b">{esc(s['head'])}</h2>
          <ul class="buls">{bullets_html(s['bullets'])}</ul>
          {stat}
        </div>
        <div class="col-shot anim-shot">
          <div class="shot-frame">
            <img class="shot-img" src="assets/shots/{s['shot']}" alt="" style="object-fit:{s['fit']}">
          </div>
        </div>
      </div>"""
    if k == "ratio":
        return f"""
      <div class="split">
        <div class="col-text">
          <div class="label anim-a">{esc(s['label'])}</div>
          <blockquote class="quote anim-q">{esc(s['quote'])}</blockquote>
          <h2 class="sec-head anim-b">{esc(s['head'])}</h2>
          <ul class="buls">{bullets_html(s['bullets'])}</ul>
        </div>
        <div class="col-shot">
          <div class="ratio-pair">
            <div class="ratio-box ratio-wide anim-r1"><span>16 : 9</span><em>done</em></div>
            <div class="ratio-box ratio-tall anim-r2"><span>9 : 16</span><em>to build</em></div>
          </div>
        </div>
      </div>"""
    if k == "routing":
        cards = "\n".join(
            f"""<div class="route anim-item">
                  <div class="route-num">{esc(n)}</div>
                  <div class="route-name">{esc(nm)}</div>
                  <div class="route-desc">{esc(d)}</div>
                </div>""" for n, nm, d in s["routes"])
        return f"""
      <div class="center-wrap">
        <div class="label anim-a">{esc(s['label'])}</div>
        <blockquote class="quote quote--center anim-q">{esc(s['quote'])}</blockquote>
        <h2 class="sec-head sec-head--center anim-b">{esc(s['head'])}</h2>
        <div class="routes">{cards}</div>
        <div class="route-note anim-note">{esc(s['note'])}</div>
      </div>"""
    if k == "list":
        return f"""
      <div class="center-wrap">
        <div class="label anim-a">{esc(s['label'])}</div>
        <h2 class="sec-head sec-head--center anim-b">{esc(s['head'])}</h2>
        <ul class="buls buls--wide">{bullets_html(s['bullets'])}</ul>
      </div>"""
    if k == "close":
        return f"""
      <div class="title-wrap">
        <img class="title-logo anim-logo" src="assets/shots/logo.png" alt="">
        <div class="close-big anim-b">{esc(s['big'])}</div>
        <div class="title-rule anim-rule"></div>
        <p class="title-sub anim-c">{esc(s['sub'])}</p>
      </div>"""
    return ""


scene_divs, audio_divs, tl_lines = [], [], []
for i, s in enumerate(SCENES):
    scene_divs.append(f"""
    <div id="{s['id']}" class="clip scene" data-start="{s['start']}" data-duration="{s['dur']}" data-track-index="{i % 2}">
      <div class="scene-inner">
        {chip_html(s)}
        {scene_body(s)}
      </div>
    </div>""")
    audio_divs.append(
        f'    <audio id="v-{s["id"]}" src="{s["voice"]}" data-start="{s["vstart"]}" '
        f'data-duration="{s["vdur"]}" data-track-index="10" data-volume="1"></audio>')

    st = s["start"]
    q = f'"#{s["id"]} '
    # Entrances are absolute-positioned on the master timeline: seek-safe.
    tl_lines.append(f'  a({q}.anim-logo", {{opacity:0, scale:.86}}, {st + 0.10:.3f}, 1.10);')
    tl_lines.append(f'  a({q}.anim-a", {{opacity:0, y:14}}, {st + 0.18:.3f}, .70);')
    tl_lines.append(f'  a({q}.anim-q", {{opacity:0, y:16}}, {st + 0.34:.3f}, .80);')
    tl_lines.append(f'  a({q}.anim-b", {{opacity:0, y:22}}, {st + 0.50:.3f}, .85);')
    tl_lines.append(f'  a({q}.anim-c", {{opacity:0, y:14}}, {st + 0.80:.3f}, .80);')
    tl_lines.append(f'  a({q}.anim-chip", {{opacity:0, x:18}}, {st + 0.30:.3f}, .70);')
    tl_lines.append(f'  w({q}.anim-rule", {st + 0.70:.3f}, .90);')
    tl_lines.append(f'  stagger({q}.bul", {st + 0.85:.3f});')
    tl_lines.append(f'  stagger({q}.anim-item", {st + 0.80:.3f});')
    tl_lines.append(f'  a({q}.anim-stat", {{opacity:0, y:16}}, {st + 1.60:.3f}, .80);')
    tl_lines.append(f'  a({q}.anim-note", {{opacity:0, y:12}}, {st + 2.40:.3f}, .80);')
    tl_lines.append(f'  a({q}.anim-r1", {{opacity:0, x:-24}}, {st + 0.70:.3f}, .85);')
    tl_lines.append(f'  a({q}.anim-r2", {{opacity:0, x:24}}, {st + 1.00:.3f}, .85);')
    if s.get("shot"):
        tl_lines.append(f'  a({q}.anim-shot", {{opacity:0, x:26}}, {st + 0.55:.3f}, .90);')
        # slow drift inside the frame for the life of the scene
        tl_lines.append(
            f'  tl.fromTo({q}.shot-img", {{scale:1.0}}, {{scale:1.05, ease:"none", '
            f'duration:{s["dur"]:.3f}}}, {st:.3f});')

# two music passes cover the full runtime
BGM = 84.259
bgm_divs = [
    f'    <audio id="bgm-1" src="assets/bgm/track.loop.mp3" data-start="0" data-duration="{BGM}" data-track-index="11" data-volume="0.16"></audio>',
    f'    <audio id="bgm-2" src="assets/bgm/track.loop.mp3" data-start="{BGM}" data-duration="{round(TOTAL - BGM, 3)}" data-track-index="11" data-volume="0.16"></audio>',
]

HTML = f"""<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=1920, height=1080" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.14.2/dist/gsap.min.js"></script>
    <style>
      * {{ margin:0; padding:0; box-sizing:border-box; }}
      html, body {{ width:1920px; height:1080px; overflow:hidden; background:#080706; }}
      :root {{
        --ink:#080706; --panel:#121110; --line:#2A2824;
        --gold:#D4AF37; --gold-lite:#F0CF6B; --gold-deep:#8F6A16;
        --cream:#F4EBD0; --muted:#8A867C; --slate:#7C8A96;
        --display:"Avenir Next","Optima","Gill Sans",system-ui,sans-serif;
        --body:"Avenir Next","Helvetica Neue",system-ui,sans-serif;
        --mono:"Menlo","SF Mono",monospace;
      }}
      .scene {{ position:absolute; inset:0; width:1920px; height:1080px;
               background:
                 radial-gradient(1200px 700px at 78% 14%, rgba(212,175,55,.055), transparent 62%),
                 var(--ink);
               color:var(--cream); font-family:var(--body); }}
      .scene-inner {{ position:absolute; inset:0; padding:92px 116px; }}
      /* hairline frame so every scene reads as one deliberate system */
      .scene-inner::before {{ content:""; position:absolute; inset:44px 56px;
               border:1px solid rgba(212,175,55,.16); pointer-events:none; }}

      .chip {{ position:absolute; top:60px; right:78px; z-index:5;
               font-family:var(--body); font-size:19px; font-weight:700;
               letter-spacing:.16em; text-transform:uppercase;
               padding:11px 22px; border-radius:100px; }}
      .chip--done {{ background:linear-gradient(135deg,#F0CF6B,#D4AF37 55%,#9A7418);
                     color:#120E03; border:1px solid transparent; }}
      .chip--open {{ color:var(--gold); border:2px solid rgba(212,175,55,.75); background:transparent; }}
      .chip--withyou {{ color:var(--slate); border:2px solid rgba(124,138,150,.6); background:transparent; }}

      .split {{ display:grid; grid-template-columns:1fr 880px; gap:76px; height:100%; align-items:center; }}
      .col-text {{ max-width:760px; }}
      .label {{ font-family:var(--mono); font-size:19px; letter-spacing:.20em;
                text-transform:uppercase; color:var(--gold); margin-bottom:26px; }}
      .quote {{ position:relative; font-family:Georgia,"Times New Roman",serif;
                font-style:italic; font-size:30px; line-height:1.48; color:#CFC7B2;
                padding-left:30px; margin-bottom:34px; }}
      .quote::before {{ content:""; position:absolute; left:0; top:6px; bottom:6px;
                width:3px; background:linear-gradient(var(--gold),rgba(212,175,55,.15)); }}
      .quote--center {{ max-width:1180px; margin:0 auto 34px; }}
      .sec-head {{ font-family:var(--display); font-size:62px; line-height:1.08;
                   font-weight:600; letter-spacing:-.022em; color:#fff; margin-bottom:38px; }}
      .sec-head--center {{ text-align:center; }}
      .buls {{ list-style:none; display:flex; flex-direction:column; gap:20px; }}
      .buls--wide {{ max-width:1200px; margin:0 auto; }}
      .bul {{ display:flex; gap:20px; align-items:flex-start;
              font-size:28px; line-height:1.5; color:#D8D2C2; }}
      .bul-dot {{ flex:0 0 auto; width:9px; height:9px; margin-top:14px; border-radius:50%;
                  background:var(--gold); box-shadow:0 0 0 5px rgba(212,175,55,.12); }}

      .col-shot {{ display:flex; align-items:center; justify-content:center; }}
      .shot-frame {{ width:880px; height:560px; border-radius:16px; overflow:hidden;
                     border:1px solid rgba(212,175,55,.32); background:#0B0A09;
                     box-shadow:0 34px 90px rgba(0,0,0,.72), 0 0 48px rgba(212,175,55,.08); }}
      .shot-img {{ width:100%; height:100%; display:block; transform-origin:50% 45%; }}

      .stat {{ display:flex; align-items:baseline; gap:20px; margin-top:42px;
               font-family:var(--display); }}
      .stat-from {{ font-size:46px; color:var(--muted); text-decoration:line-through;
                    text-decoration-color:rgba(138,134,124,.55); }}
      .stat-arrow {{ font-family:var(--mono); font-size:20px; letter-spacing:.18em;
                     text-transform:uppercase; color:var(--muted); }}
      .stat-to {{ font-size:70px; font-weight:700; letter-spacing:-.02em;
                  background:linear-gradient(135deg,#F0CF6B,#D4AF37 60%,#B8922A);
                  -webkit-background-clip:text; background-clip:text; color:transparent; }}

      .title-wrap {{ height:100%; display:flex; flex-direction:column;
                     align-items:center; justify-content:center; text-align:center; }}
      .title-logo {{ width:190px; height:190px; margin-bottom:44px; }}
      .title-eyebrow {{ font-family:var(--mono); font-size:21px; letter-spacing:.34em;
                        text-transform:uppercase; color:var(--gold); margin-bottom:30px; }}
      .title-head {{ font-family:var(--display); font-size:104px; line-height:1.04;
                     font-weight:600; letter-spacing:-.03em; color:#fff; }}
      .title-head em {{ font-style:normal;
                        background:linear-gradient(135deg,#F0CF6B,#D4AF37 55%,#B8922A);
                        -webkit-background-clip:text; background-clip:text; color:transparent; }}
      .title-rule {{ width:220px; height:2px; margin:40px 0 32px;
                     background:linear-gradient(90deg,transparent,var(--gold),transparent); }}
      .title-sub {{ font-size:30px; line-height:1.55; color:#B8B2A2; max-width:1080px; }}
      .close-big {{ font-family:var(--display); font-size:112px; font-weight:700;
                    letter-spacing:-.03em;
                    background:linear-gradient(135deg,#F0CF6B,#D4AF37 55%,#B8922A);
                    -webkit-background-clip:text; background-clip:text; color:transparent; }}

      .center-wrap {{ height:100%; display:flex; flex-direction:column;
                      align-items:center; justify-content:center; text-align:center; }}
      .legend-list {{ display:flex; flex-direction:column; gap:30px; margin-top:16px; }}
      .legend-row {{ display:flex; align-items:center; gap:34px; text-align:left; }}
      .legend-row .chip {{ position:static; min-width:220px; text-align:center; }}
      .legend-text {{ font-size:30px; color:#C9C3B4; }}

      .routes {{ display:grid; grid-template-columns:repeat(3,1fr); gap:30px;
                 width:100%; max-width:1560px; margin-top:12px; }}
      .route {{ background:var(--panel); border:1px solid var(--line);
                border-radius:14px; padding:38px 34px; text-align:left; }}
      .route-num {{ font-family:var(--mono); font-size:19px; letter-spacing:.2em;
                    color:var(--gold); margin-bottom:20px; }}
      .route-name {{ font-family:var(--display); font-size:37px; font-weight:600;
                     color:#fff; margin-bottom:16px; letter-spacing:-.015em; }}
      .route-desc {{ font-size:24px; line-height:1.5; color:#B3AE9F; }}
      .route-note {{ margin-top:40px; font-size:25px; color:var(--muted); }}

      .ratio-pair {{ display:flex; align-items:flex-end; gap:56px; }}
      .ratio-box {{ display:flex; flex-direction:column; align-items:center;
                    justify-content:center; gap:12px; border-radius:14px;
                    font-family:var(--mono); letter-spacing:.14em; }}
      .ratio-box span {{ font-size:26px; }}
      .ratio-box em {{ font-style:normal; font-size:18px; letter-spacing:.2em;
                       text-transform:uppercase; }}
      .ratio-wide {{ width:520px; height:293px; border:2px solid rgba(212,175,55,.75);
                     color:var(--gold); background:rgba(212,175,55,.05); }}
      .ratio-tall {{ width:293px; height:521px; border:2px dashed rgba(138,134,124,.7);
                     color:var(--muted); }}
    </style>
  </head>
  <body>
    <div id="root" data-composition-id="main" data-start="0" data-duration="{TOTAL}"
         data-width="1920" data-height="1080">
{chr(10).join(scene_divs)}

{chr(10).join(audio_divs)}
{chr(10).join(bgm_divs)}
    </div>

    <script>
      window.__timelines = window.__timelines || {{}};
      const tl = gsap.timeline({{ paused: true }});
      // Absolute-positioned tweens only: every state is a pure function of time,
      // so scrubbing and frame-accurate rendering agree.
      function a(sel, from, at, d) {{
        if (!document.querySelector(sel)) return;
        tl.fromTo(sel, from, {{ opacity:1, x:0, y:0, scale:1,
                                ease:"power2.out", duration:d }}, at);
      }}
      function w(sel, at, d) {{
        if (!document.querySelector(sel)) return;
        tl.fromTo(sel, {{ scaleX:0, opacity:0 }},
                       {{ scaleX:1, opacity:1, ease:"power2.out", duration:d }}, at);
      }}
      function stagger(sel, at) {{
        const els = document.querySelectorAll(sel);
        els.forEach(function (el, i) {{
          tl.fromTo(el, {{ opacity:0, y:16 }},
                        {{ opacity:1, y:0, ease:"power2.out", duration:.62 }},
                        at + i * 0.16);
        }});
      }}
{chr(10).join(tl_lines)}
      window.__timelines["main"] = tl;
    </script>
  </body>
</html>
"""

(ROOT / "index.html").write_text(HTML)
print(f"total duration {TOTAL}s over {len(SCENES)} scenes")
for s in SCENES:
    print(f"  {s['id']:<16} start {s['start']:>7.3f}  dur {s['dur']:>6.3f}  voice {s['vdur']:>6.3f}")
