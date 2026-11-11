@php
    $supportCompany = current_company();
    $supportName = $supportCompany?->name ?? 'GoTrips';
    $supportHours = $supportCompany?->getSetting('support_hours', config('support.default_hours')) ?? config('support.default_hours');
    $supportSla = (int) ($supportCompany?->getSetting('support_sla_minutes', config('support.default_sla_minutes')) ?? 240);
@endphp

@include('partials.gold-medallion-tokens')
<div class="gt-support" id="gtSupport" data-create-url="{{ route('support.tickets.store') }}" data-track-url="{{ route('support.tickets.track') }}" data-reply-url="{{ route('support.tickets.reply') }}">
    <button class="gt-support-launcher" id="gtSupportLauncher" type="button" aria-haspopup="dialog" aria-controls="gtSupportPanel" aria-expanded="false" aria-label="Help: raise or track a support ticket">
        <span class="gt-support-medal" aria-hidden="true">
            {{-- Three support agents take turns: an Emirati man, an Emirati woman
                 and an Asian man (Amer: "one after other pop up ... add one asian
                 also"). They were faceless gold silhouettes; Amer asked for
                 characters people can actually see (11 Nov 2026), so each now has
                 a face, a headset and real clothing. --}}
            <span class="gt-support-persona" title="Emirati support agent">
                <svg viewBox="0 0 64 64" fill="none" role="img" aria-label="Support agent">
                    <path d="M8 64c0-10.5 10.7-16.5 24-16.5S56 53.5 56 64z" fill="#f4f1e8"/>
                    <path d="M32 49v9" stroke="#cfc8b4" stroke-width="1"/>
                    <path d="M14.8 31c0-11.3 7.7-19.6 17.2-19.6S49.2 19.7 49.2 31v19c-4 1.3-8.4 1.4-11 .2V38H26v12.2c-2.6 1.2-7 1.1-11.2-.2z" fill="#fbfaf6" stroke="#cfc8b4" stroke-width=".7"/>
                    <rect x="28.4" y="37" width="7.2" height="9" rx="2" fill="#c98f61"/>
                    <ellipse cx="32" cy="29" rx="9.4" ry="11.4" fill="#d9a072"/>
                    <path d="M23.4 31.2c.4 6 4 10.2 8.6 10.2s8.2-4.2 8.6-10.2c-1.6 2.6-3.2 3.7-4.7 3.9-1.2-1.4-2.4-2-3.9-2s-2.7.6-3.9 2c-1.5-.2-3.1-1.3-4.7-3.9z" fill="#2a1d14"/>
                    <ellipse cx="28" cy="28" rx="1.45" ry="1.5" fill="#1b130d"/><ellipse cx="36" cy="28" rx="1.45" ry="1.5" fill="#1b130d"/><circle cx="28.5" cy="27.5" r=".45" fill="#fff"/><circle cx="36.5" cy="27.5" r=".45" fill="#fff"/><path d="M25.4 24.6q2.6-1.5 5 0M33.6 24.6q2.6-1.5 5 0" stroke="#2a1d14" stroke-width="1.2" fill="none" stroke-linecap="round"/><path d="M32 29.2v2.6q-.7.5-1.5.3" stroke="rgba(0,0,0,.25)" stroke-width=".9" fill="none" stroke-linecap="round"/>
                    <path d="M29.3 35.8q2.7 1.8 5.4 0" stroke="#f4f1e8" stroke-width="1.1" fill="none" stroke-linecap="round"/>
                    <path d="M22.4 23.4c1.4-5.4 5.2-8.4 9.6-8.4s8.2 3 9.6 8.4c-2.8-1.3-6.2-1.9-9.6-1.9s-6.8.6-9.6 1.9z" fill="#fbfaf6" stroke="#cfc8b4" stroke-width=".6"/>
                    <g class="gt-headset" fill="none" stroke="#e0b93b" stroke-linecap="round"><path d="M19.6 28a12.4 13.4 0 0 1 24.8 0" stroke-width="2.2"/><path d="M21 33.5q1.2 5.6 7.6 5.8" stroke-width="1.5"/></g><rect x="18" y="25.5" width="4" height="7" rx="2" fill="#e0b93b"/><rect x="42" y="25.5" width="4" height="7" rx="2" fill="#e0b93b"/><circle cx="29.2" cy="39.3" r="1.6" fill="#e0b93b"/>
                    <ellipse cx="32" cy="14.6" rx="11.2" ry="2.6" stroke="#111" stroke-width="2.3"/>
                    <ellipse cx="32" cy="17.2" rx="10.4" ry="2.2" stroke="#111" stroke-width="1.5"/>
                </svg>
            </span>
            <span class="gt-support-persona" title="Emirati support agent">
                <svg viewBox="0 0 64 64" fill="none" role="img" aria-label="Support agent">
                    <path d="M8 64c0-10.5 10.7-16.5 24-16.5S56 53.5 56 64z" fill="#161616" stroke="#d4af37" stroke-width="1"/>
                    <path d="M14.4 31c0-11.6 7.8-20 17.6-20s17.6 8.4 17.6 20c0 8-2 14-6 19-3.6 1.5-7.6 2.1-11.6 2.1s-8-.6-11.6-2.1c-4-5-6-11-6-19z" fill="#1c1c1c" stroke="#d4af37" stroke-width="1"/>
                    <ellipse cx="32" cy="29.5" rx="9" ry="11" fill="#e4b48c"/>
                    <ellipse cx="28" cy="28" rx="1.45" ry="1.5" fill="#1b130d"/><ellipse cx="36" cy="28" rx="1.45" ry="1.5" fill="#1b130d"/><circle cx="28.5" cy="27.5" r=".45" fill="#fff"/><circle cx="36.5" cy="27.5" r=".45" fill="#fff"/><path d="M25.6 27.1l-1-.8M38.4 27.1l1-.8" stroke="#141414" stroke-width=".9" stroke-linecap="round"/><path d="M25.4 24.6q2.6-1.5 5 0M33.6 24.6q2.6-1.5 5 0" stroke="#3a2414" stroke-width="1.2" fill="none" stroke-linecap="round"/><path d="M32 29.2v2.6q-.7.5-1.5.3" stroke="rgba(0,0,0,.25)" stroke-width=".9" fill="none" stroke-linecap="round"/><circle cx="25.6" cy="32" r="1.7" fill="#e8907a" opacity=".3"/><circle cx="38.4" cy="32" r="1.7" fill="#e8907a" opacity=".3"/>
                    <path d="M28.8 34.6q3.2 2.6 6.4 0" stroke="#b5523f" stroke-width="1.4" fill="none" stroke-linecap="round"/>
                    <path d="M22.6 24.4c1-5.8 4.8-9.2 9.4-9.2s8.4 3.4 9.4 9.2c-2.8-1.5-6-2.2-9.4-2.2s-6.6.7-9.4 2.2z" fill="#1c1c1c"/>
                    <ellipse cx="32" cy="29.5" rx="9.5" ry="11.5" stroke="#d4af37" stroke-width=".7"/>
                    <g class="gt-headset" fill="none" stroke="#e0b93b" stroke-linecap="round"><path d="M19.6 28a12.4 13.4 0 0 1 24.8 0" stroke-width="2.2"/><path d="M21 33.5q1.2 5.6 7.6 5.8" stroke-width="1.5"/></g><rect x="18" y="25.5" width="4" height="7" rx="2" fill="#e0b93b"/><rect x="42" y="25.5" width="4" height="7" rx="2" fill="#e0b93b"/><circle cx="29.2" cy="39.3" r="1.6" fill="#e0b93b"/>
                </svg>
            </span>
            <span class="gt-support-persona" title="Support agent">
                <svg viewBox="0 0 64 64" fill="none" role="img" aria-label="Support agent">
                    <path d="M8 64c0-10.5 10.7-16.5 24-16.5S56 53.5 56 64z" fill="#e6c65a"/>
                    <path d="M25.6 48.2l6.4 6.6 6.4-6.6" fill="#f7e7a4" stroke="#b8961c" stroke-width=".8"/>
                    <rect x="28.4" y="37" width="7.2" height="11" rx="2" fill="#e2b58a"/>
                    <ellipse cx="32" cy="28.5" rx="10" ry="12" fill="#f1c9a0"/>
                    <ellipse cx="28" cy="28" rx="1.45" ry="1.15" fill="#1b130d"/><ellipse cx="36" cy="28" rx="1.45" ry="1.15" fill="#1b130d"/><circle cx="28.5" cy="27.5" r=".45" fill="#fff"/><circle cx="36.5" cy="27.5" r=".45" fill="#fff"/><path d="M25.4 24.6q2.6-1.5 5 0M33.6 24.6q2.6-1.5 5 0" stroke="#141414" stroke-width="1.2" fill="none" stroke-linecap="round"/><path d="M32 29.2v2.6q-.7.5-1.5.3" stroke="rgba(0,0,0,.25)" stroke-width=".9" fill="none" stroke-linecap="round"/><circle cx="25.6" cy="32" r="1.7" fill="#e8907a" opacity=".3"/><circle cx="38.4" cy="32" r="1.7" fill="#e8907a" opacity=".3"/>
                    <path d="M28.6 34q3.4 2.8 6.8 0" stroke="#9a3b2a" stroke-width="1.4" fill="none" stroke-linecap="round"/>
                    <path d="M21.4 26.5c-1.2-9.4 4.4-15 10.8-15 7.4 0 11.8 5.2 10.6 14-1.6-3.8-4-6.2-7.4-6.8-3.8 2.5-9.6 4-14 7.8z" fill="#141414"/>
                    <g class="gt-headset" fill="none" stroke="#e0b93b" stroke-linecap="round"><path d="M19.6 28a12.4 13.4 0 0 1 24.8 0" stroke-width="2.2"/><path d="M21 33.5q1.2 5.6 7.6 5.8" stroke-width="1.5"/></g><rect x="18" y="25.5" width="4" height="7" rx="2" fill="#e0b93b"/><rect x="42" y="25.5" width="4" height="7" rx="2" fill="#e0b93b"/><circle cx="29.2" cy="39.3" r="1.6" fill="#e0b93b"/>
                </svg>
            </span>
        </span>
        <span class="gt-support-tag">Help</span>
    </button>

    <section class="gt-support-panel" id="gtSupportPanel" role="dialog" aria-modal="false" aria-labelledby="gtSupportTitle" hidden>
        <header class="gt-support-header">
            <div>
                <strong id="gtSupportTitle">Chat with {{ $supportName }}</strong>
                <span>A real person replies here</span>
            </div>
            <button type="button" class="gt-support-close" data-support-close aria-label="Close support window">&times;</button>
        </header>

        <div class="gt-support-body" aria-live="polite">
            <section data-support-screen="home">
                <p class="gt-support-greeting">How can we help?</p>
                <p class="gt-support-muted">Start a new conversation or pick up one you already started.</p>
                <button type="button" class="gt-support-choice" data-support-go="problem"><i class="fas fa-comment-dots"></i><span><strong>Start a chat</strong><small>Tell us what's going on — we'll reply here</small></span></button>
                <button type="button" class="gt-support-choice" data-support-go="track"><i class="fas fa-comments"></i><span><strong>Continue a chat</strong><small>Enter the code we sent you</small></span></button>
                <div class="gt-support-hours"><i class="far fa-clock"></i><span><strong>Support hours</strong>{{ $supportHours }}<br>First response target: within {{ max(1, (int) ceil($supportSla / 60)) }} hours.</span></div>
            </section>

            <section data-support-screen="problem" hidden>
                <button type="button" class="gt-support-back" data-support-go="home">← Back</button>
                <h3>What's going on?</h3>
                <label class="gt-support-field">Your message
                    <textarea id="gtSupportProblem" rows="6" minlength="10" maxlength="4000" placeholder="Tell us what you were trying to do and what happened…" required></textarea>
                </label>
                <button type="button" class="gt-support-primary" id="gtSupportProblemNext">Continue</button>
            </section>

            <section data-support-screen="contact" hidden>
                <button type="button" class="gt-support-back" data-support-go="problem">← Back</button>
                <h3>Where should we send replies?</h3>
                <form id="gtSupportCreateForm">
                    <label class="gt-support-field">Name<input type="text" name="name" maxlength="120" autocomplete="name" required></label>
                    <label class="gt-support-field">Email<input type="email" name="email" maxlength="255" autocomplete="email" required></label>
                    <label class="gt-support-field">Phone / WhatsApp<input type="tel" name="phone" minlength="7" maxlength="40" autocomplete="tel" required></label>
                    <button type="submit" class="gt-support-primary">Start chat</button>
                </form>
            </section>

            <section data-support-screen="track" hidden>
                <button type="button" class="gt-support-back" data-support-go="home">← Back</button>
                <h3>Continue your chat</h3>
                <p class="gt-support-muted">Enter the code we sent you and we'll open the conversation.</p>
                <form id="gtSupportTrackForm">
                    <label class="gt-support-field">Conversation code<input type="text" name="ticket_number" maxlength="32" placeholder="GT-260908-ABC123" autocomplete="off" required></label>
                    <button type="submit" class="gt-support-primary">Open conversation</button>
                </form>
            </section>

            <section data-support-screen="ticket" hidden>
                <button type="button" class="gt-support-back" data-support-go="home">← Support home</button>
                <div id="gtSupportTicketSummary"></div>
                <div class="gt-support-thread" id="gtSupportThread"></div>
                <form id="gtSupportReplyForm">
                    <label class="gt-support-field">Type a message<textarea name="message" rows="3" maxlength="4000" required></textarea></label>
                    <button type="submit" class="gt-support-primary">Send</button>
                </form>
            </section>

            <div class="gt-support-error" id="gtSupportError" role="alert" hidden></div>
            <div class="gt-support-loading" id="gtSupportLoading" hidden><span></span> Sending…</div>
        </div>
    </section>
</div>

<style>
.gt-support{font-family:'Outfit',Arial,sans-serif;position:relative;z-index:12000}/* The site theme styles bare `section` and `body`; both bleed in here. Reset
   them at the widget boundary so the panel owns its own metrics. */
.gt-support-panel,.gt-support-panel section{padding:0;margin:0}
.gt-support-panel{font-size:15px;line-height:1.5}
.gt-support-launcher{position:fixed;right:20px;bottom:22px;border:0;border-radius:999px;background:linear-gradient(135deg,#ffd700,#d4a017);color:#151000;display:flex;align-items:center;gap:9px;padding:13px 19px;font-size:15px;font-weight:800;box-shadow:0 12px 32px rgba(0,0,0,.38),0 0 0 1px rgba(255,255,255,.25) inset;cursor:pointer;transition:transform .2s}.gt-support-launcher:hover{transform:translateY(-2px)}.gt-support-launcher i{font-size:19px}.gt-support-panel{--gt-lift:82px;position:fixed;right:20px;bottom:var(--gt-lift);width:min(390px,calc(100vw - 24px));max-height:min(650px,calc(100vh - var(--gt-lift) - 24px));background:#111;color:#f5f5f5;border:1px solid rgba(255,215,0,.38);border-radius:18px;box-shadow:0 24px 70px rgba(0,0,0,.62);overflow:hidden;display:flex;flex-direction:column}.gt-support-header{flex:0 0 auto;display:flex;align-items:center;justify-content:space-between;gap:12px;background:linear-gradient(135deg,#1b1708,#080808);border-bottom:2px solid #d4af37;padding:16px 18px}.gt-support-header strong{display:block;font-size:16px;line-height:1.3;color:#fff}.gt-support-header span{display:block;font-size:12px;line-height:1.4;color:#d4af37}.gt-support-close{background:transparent;border:0;color:#bbb;font-size:28px;line-height:1;cursor:pointer}.gt-support-body{flex:1 1 auto;min-height:0;padding:18px;overflow:auto;-webkit-overflow-scrolling:touch}.gt-support-greeting{color:#fff;font-size:21px;font-weight:800;margin:0 0 4px}.gt-support-muted{color:#aaa;font-size:13px;line-height:1.5;margin:0 0 16px}.gt-support-choice{width:100%;display:flex;align-items:center;gap:13px;text-align:left;background:#1d1d1d;color:#fff;border:1px solid rgba(255,215,0,.2);border-radius:12px;padding:14px;margin-bottom:10px;cursor:pointer}.gt-support-choice:hover{border-color:#d4af37;background:#24210f}.gt-support-choice>i{width:34px;height:34px;display:grid;place-items:center;border-radius:50%;background:rgba(255,215,0,.12);color:#ffd700}.gt-support-choice strong,.gt-support-choice small{display:block}.gt-support-choice small{color:#999;margin-top:2px}.gt-support-hours{display:flex;gap:10px;background:#191919;border-radius:10px;padding:12px;margin-top:16px;color:#aaa;font-size:11px;line-height:1.5}.gt-support-hours i{color:#d4af37;margin-top:2px}.gt-support-hours strong{display:block;color:#ddd}.gt-support h3{color:#fff;font-size:18px;margin:7px 0 16px}.gt-support-back{background:none;border:0;color:#d4af37;padding:0;margin-bottom:8px;cursor:pointer}.gt-support-field{display:block;color:#ddd;font-size:12px;font-weight:700;margin-bottom:13px}.gt-support-field input,.gt-support-field textarea{display:block;width:100%;box-sizing:border-box;background:#1d1d1d;color:#fff;border:1px solid #383838;border-radius:9px;padding:11px 12px;margin-top:5px;font:inherit;font-size:14px;resize:vertical}.gt-support-field input:focus,.gt-support-field textarea:focus{outline:0;border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.16)}.gt-support-primary{width:100%;border:0;border-radius:9px;padding:11px 14px;background:linear-gradient(135deg,#ffd700,#d4a017);color:#151000;font-weight:800;cursor:pointer}.gt-support-error{background:rgba(220,38,38,.15);border:1px solid rgba(248,113,113,.4);color:#fca5a5;border-radius:8px;padding:10px;margin-top:12px;font-size:12px}.gt-support-loading{display:flex;align-items:center;justify-content:center;gap:8px;color:#bbb;margin-top:12px}.gt-support-loading span{width:14px;height:14px;border:2px solid #555;border-top-color:#ffd700;border-radius:50%;animation:gtSupportSpin .8s linear infinite}@keyframes gtSupportSpin{to{transform:rotate(360deg)}}.gt-support-ticket-card{background:#1b1b1b;border:1px solid rgba(255,215,0,.25);border-radius:11px;padding:13px;margin-bottom:14px}.gt-support-ticket-card strong{display:block;color:#ffd700;font-size:18px}.gt-support-ticket-card span{display:block;font-size:12px;line-height:1.45;color:#aaa}.gt-support-status{display:inline-flex!important;margin-top:8px;background:rgba(255,215,0,.12);color:#ffe16b!important;border-radius:99px;padding:3px 8px}.gt-support-thread{display:flex;flex-direction:column;gap:9px;max-height:260px;overflow:auto;margin-bottom:14px}.gt-support-message{border-radius:10px;padding:10px 11px;font-size:13px;line-height:1.45;white-space:pre-wrap}.gt-support-message small{display:block;color:#888;margin-bottom:3px}.gt-support-message-customer{background:#282828;margin-right:25px}.gt-support-message-staff{background:rgba(255,215,0,.1);border:1px solid rgba(255,215,0,.2);margin-left:25px}.gt-support-message-system{background:rgba(59,130,246,.1);color:#cbd5e1}.gt-support[aria-busy="true"] button,.gt-support[aria-busy="true"] input,.gt-support[aria-busy="true"] textarea{pointer-events:none;opacity:.7}@media(max-width:1100px){.gt-support-launcher{bottom:90px}.gt-support-panel{--gt-lift:148px}}@media(max-width:520px){.gt-support-launcher{right:12px}.gt-support-panel{right:12px;--gt-lift:146px}}
/* Help launcher as a black-and-gold medallion (client round 2, 17 Sep 2026:
   "the help button we use same theme ... something unique and someone
   standing out"). Same brushed ring as the logo and the route selector, a
   support agent inside, a HELP tag in the style of the selector's HOME tag,
   and a slow gold halo so it catches the eye. The halo stops once the panel
   is open, and for anyone who prefers reduced motion. */
.gt-support-launcher{right:22px;bottom:26px;z-index:9000;padding:0;border:0;border-radius:50%;background:none;box-shadow:none;display:block;gap:0;color:inherit}
.gt-support-launcher:hover{transform:none}
.gt-support-launcher:focus-visible{outline:none}
.gt-support-medal{position:relative;display:grid;place-items:center;width:80px;height:80px;border-radius:50%;border:4px solid transparent;background:radial-gradient(circle at 50% 36%,#1d1d1d 0,#060606 72%) padding-box,var(--gm-gold-metal) border-box;box-shadow:0 14px 34px rgba(0,0,0,.55),0 0 0 1px rgba(255,236,170,.3);transition:transform .2s ease,box-shadow .2s ease}
.gt-support-medal svg{position:absolute;left:50%;top:-6px;width:92px;height:92px;transform:translateX(-50%)}
/* Three personas share the same spot, popping in and out in turn rather than
   one fixed face — see the persona SVGs above for why. 6s loop, three 2s
   slots, each visible for most of its slot with a quick pop in/out at the
   edges so the medallion is never left empty between faces. */
.gt-support-persona{position:absolute;inset:0;display:grid;place-items:center;border-radius:50%;overflow:hidden;opacity:0;transform:scale(.55);animation:gtPersonaCycle 6s ease-in-out infinite}
.gt-support-persona:nth-child(2){animation-delay:2s}
.gt-support-persona:nth-child(3){animation-delay:4s}
@keyframes gtPersonaCycle{0%,100%{opacity:0;transform:scale(.55)}5%,28%{opacity:1;transform:scale(1)}33%{opacity:0;transform:scale(.55)}}
@media(prefers-reduced-motion:reduce){.gt-support-persona{animation:none;opacity:0}.gt-support-persona:first-child{opacity:1;transform:scale(1)}}
.gt-support-medal::after{content:"";position:absolute;inset:-5px;border-radius:50%;border:2px solid rgba(240,207,107,.75);pointer-events:none;animation:gtHelpHalo 3.2s ease-out infinite}
@keyframes gtHelpHalo{0%{transform:scale(1);opacity:.8}70%,100%{transform:scale(1.5);opacity:0}}
.gt-support-tag{position:absolute;left:50%;bottom:-9px;transform:translateX(-50%);padding:4px 11px;border-radius:100px;background:var(--gm-gold-metal);color:#120e03;font-size:11px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;line-height:1;white-space:nowrap;box-shadow:0 4px 10px rgba(0,0,0,.5),0 0 0 2px #050505;transition:filter .2s ease}
.gt-support-launcher:hover .gt-support-medal,.gt-support-launcher:focus-visible .gt-support-medal{transform:translateY(-3px);box-shadow:0 18px 38px rgba(0,0,0,.6),0 0 0 3px #f0cf6b,0 0 26px rgba(212,175,55,.5)}
.gt-support-launcher:hover .gt-support-tag,.gt-support-launcher:focus-visible .gt-support-tag{filter:brightness(1.1)}
.gt-support-launcher[aria-expanded="true"] .gt-support-medal::after{animation:none;opacity:0}
/* The medallion stands about 90px tall with its tag, so the panel sits higher
   than it did above the old 50px pill. */
.gt-support-panel{--gt-lift:126px}
@media(max-width:1100px){.gt-support-launcher{bottom:92px}.gt-support-panel{--gt-lift:180px}}
@media(max-width:520px){.gt-support-launcher{right:14px}.gt-support-panel{right:12px;--gt-lift:178px}}
@media(prefers-reduced-motion:reduce){.gt-support-medal::after{animation:none;opacity:0}.gt-support-medal{transition:none}}
</style>

<script>
(function(){
    const root=document.getElementById('gtSupport'); if(!root)return;
    const launcher=document.getElementById('gtSupportLauncher'),panel=document.getElementById('gtSupportPanel');
    const errorBox=document.getElementById('gtSupportError'),loading=document.getElementById('gtSupportLoading');
    const screens=[...root.querySelectorAll('[data-support-screen]')];
    const csrf=document.querySelector('meta[name="csrf-token"]')?.content||'';
    let problem='',tracked={ticket_number:''},pollTimer=null;
    const stopPoll=()=>{if(pollTimer){clearInterval(pollTimer);pollTimer=null}};
    const show=(name)=>{if(name!=='ticket')stopPoll();screens.forEach(s=>s.hidden=s.dataset.supportScreen!==name);errorBox.hidden=true;const focus=root.querySelector(`[data-support-screen="${name}"] input, [data-support-screen="${name}"] textarea`);setTimeout(()=>focus?.focus(),30)};
    const open=()=>{panel.hidden=false;launcher.setAttribute('aria-expanded','true');show('home')};
    const close=()=>{stopPoll();panel.hidden=true;launcher.setAttribute('aria-expanded','false');launcher.focus()};
    const busy=(on)=>{root.setAttribute('aria-busy',on?'true':'false');loading.hidden=!on};
    const fail=(message)=>{errorBox.textContent=message||'Something went wrong. Please try again.';errorBox.hidden=false};
    const request=async(url,payload)=>{busy(true);errorBox.hidden=true;try{const res=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify(payload)});const data=await res.json().catch(()=>({}));if(!res.ok){const validation=data.errors?Object.values(data.errors).flat()[0]:null;throw new Error(validation||data.message||'Request failed. Please try again.')}return data}finally{busy(false)}};
    let lastCount=0;
    const refreshTicket=async()=>{
        if(!tracked.ticket_number||panel.hidden)return;
        try{
            const res=await fetch(root.dataset.trackUrl,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({ticket_number:tracked.ticket_number})});
            if(!res.ok)return;
            const data=await res.json();
            if((data.ticket?.messages||[]).length!==lastCount)renderTicket(data.ticket);
        }catch(e){}
    };
    // 6s, not 20s: the client wants this to read as chat, not a ticket queue
    // (docs/client-feedback-2026-09-27.md, D7) — replies should feel close to
    // live without standing up a websocket/push backend for it.
    const startPoll=()=>{stopPoll();if(tracked.ticket_number)pollTimer=setInterval(refreshTicket,6000)};
    const escapeHtml=(value)=>String(value??'').replace(/[&<>'"]/g,ch=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[ch]));
    const renderTicket=(ticket)=>{
        tracked.ticket_number=ticket.ticket_number;
        document.getElementById('gtSupportTicketSummary').innerHTML=`<div class="gt-support-ticket-card"><strong>${escapeHtml(ticket.ticket_number)}</strong><span>${escapeHtml(ticket.subject)}</span><span class="gt-support-status">${escapeHtml(ticket.status_label)}</span></div>`;
        document.getElementById('gtSupportThread').innerHTML=(ticket.messages||[]).map(m=>`<div class="gt-support-message gt-support-message-${escapeHtml(m.sender_type)}"><small>${escapeHtml(m.sender_name||m.sender_type)} · ${escapeHtml(new Date(m.created_at).toLocaleString())}</small>${escapeHtml(m.message)}</div>`).join('');
        document.getElementById('gtSupportReplyForm').hidden=ticket.status==='closed';
        lastCount=(ticket.messages||[]).length;
        show('ticket');
        startPoll();
        const thread=document.getElementById('gtSupportThread');
        thread.scrollTop=thread.scrollHeight;
        try{sessionStorage.setItem('gt_support_ticket',JSON.stringify({ticket_number:tracked.ticket_number}))}catch(e){}
    };
    launcher.addEventListener('click',()=>panel.hidden?open():close());
    root.querySelector('[data-support-close]').addEventListener('click',close);
    root.querySelectorAll('[data-support-go]').forEach(btn=>btn.addEventListener('click',()=>show(btn.dataset.supportGo)));
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!panel.hidden)close()});
    document.getElementById('gtSupportProblemNext').addEventListener('click',()=>{const field=document.getElementById('gtSupportProblem');if(!field.reportValidity())return;problem=field.value.trim();if(problem.length<10){fail('Please describe the issue in at least 10 characters.');return}show('contact')});
    document.getElementById('gtSupportCreateForm').addEventListener('submit',async e=>{e.preventDefault();const el=e.currentTarget,form=new FormData(el);try{const data=await request(root.dataset.createUrl,{message:problem,name:form.get('name'),email:form.get('email'),phone:form.get('phone'),source_url:location.href});renderTicket(data.ticket)}catch(err){fail(err.message)}});
    document.getElementById('gtSupportTrackForm').addEventListener('submit',async e=>{e.preventDefault();const form=new FormData(e.currentTarget);tracked={ticket_number:String(form.get('ticket_number')).trim().toUpperCase()};try{const data=await request(root.dataset.trackUrl,tracked);renderTicket(data.ticket)}catch(err){fail(err.message)}});
    // `e.currentTarget` is null once the handler has awaited — it is only set
    // during dispatch — so hold the element before the request, or a reply that
    // succeeded server-side reports a failure to the customer.
    document.getElementById('gtSupportReplyForm').addEventListener('submit',async e=>{e.preventDefault();const el=e.currentTarget,form=new FormData(el);try{const data=await request(root.dataset.replyUrl,{...tracked,message:form.get('message')});el.reset();renderTicket(data.ticket)}catch(err){fail(err.message)}});
    try{const saved=JSON.parse(sessionStorage.getItem('gt_support_ticket')||'null');if(saved?.ticket_number){root.querySelector('#gtSupportTrackForm [name="ticket_number"]').value=saved.ticket_number}}catch(e){}
})();
</script>
