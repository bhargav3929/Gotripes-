@include('header')

{{--
    Coming Soon placeholder for newly-introduced menu items (sub-menu services
    after e-Visa that don't have a full page yet). Driven by the $service array
    passed from the /coming-soon/{slug} route in routes/web.php.
    Compact, centered card with a modest related picture.
--}}

<style>
    .gt-cs-wrap {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: radial-gradient(circle at 50% 10%, #161616 0%, #050505 60%);
        overflow: hidden;
        padding: 120px 20px 70px;
    }
    /* faint, very dim copy of the image as ambient atmosphere */
    .gt-cs-atmos {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        filter: blur(48px) brightness(0.3) saturate(1.2);
        transform: scale(1.3);
        opacity: 0.45;
        z-index: 0;
    }

    /* one cohesive card */
    .gt-cs-card {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 480px;
        background: rgba(12, 12, 12, 0.92);
        border: 1px solid rgba(255, 215, 0, 0.22);
        border-radius: 22px;
        padding: 0 0 34px;
        overflow: hidden;
        box-shadow: 0 30px 70px rgba(0,0,0,0.65);
        backdrop-filter: blur(6px);
    }

    /* modest related picture at the top of the card */
    .gt-cs-figure {
        position: relative;
        height: 190px;
    }
    .gt-cs-figure img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .gt-cs-figure::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(12,12,12,0) 40%, rgba(12,12,12,0.95) 100%);
    }
    .gt-cs-ribbon {
        position: absolute;
        top: 14px;
        left: 14px;
        z-index: 3;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        letter-spacing: 2.5px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #1a1a1a;
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        border-radius: 999px;
        padding: 7px 15px;
        box-shadow: 0 6px 18px rgba(255, 215, 0, 0.3);
    }
    /* icon badge straddling the image / content seam */
    .gt-cs-icon {
        position: absolute;
        left: 50%;
        bottom: -30px;
        transform: translateX(-50%);
        z-index: 3;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 27px;
        color: #FFD700;
        background: #0c0c0c;
        border: 2px solid rgba(255, 215, 0, 0.6);
        box-shadow: 0 8px 22px rgba(0,0,0,0.6), 0 0 22px rgba(255, 215, 0, 0.18);
    }

    .gt-cs-body { padding: 48px 30px 0; }
    .gt-cs-title {
        color: #fff;
        font-size: clamp(24px, 4.5vw, 32px);
        font-weight: 800;
        line-height: 1.15;
        margin: 0 0 12px;
    }
    .gt-cs-sub {
        color: #b9b9b9;
        font-size: 15px;
        line-height: 1.65;
        margin: 0 auto 26px;
        max-width: 340px;
    }
    .gt-cs-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
        padding: 0 30px;
    }
    .gt-cs-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, color .2s ease;
    }
    .gt-cs-btn-primary {
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        color: #1a1a1a;
        box-shadow: 0 8px 24px rgba(255, 215, 0, 0.25);
    }
    .gt-cs-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(255, 215, 0, 0.38); color:#1a1a1a; }
    .gt-cs-btn-ghost {
        background: transparent;
        color: #fff;
        border: 1.5px solid rgba(255,255,255,0.25);
    }
    .gt-cs-btn-ghost:hover { transform: translateY(-2px); border-color:#FFD700; color:#FFD700; }
</style>

<div class="gt-cs-wrap">
    <div class="gt-cs-atmos" style="background-image: url('{{ $service['img'] }}');"></div>

    <div class="gt-cs-card">
        <div class="gt-cs-figure">
            <span class="gt-cs-ribbon"><i class="bi bi-hourglass-split"></i> Coming Soon</span>
            <img src="{{ $service['img'] }}" alt="{{ $service['title'] }}" loading="lazy">
            <div class="gt-cs-icon"><i class="bi {{ $service['icon'] }}"></i></div>
        </div>

        <div class="gt-cs-body">
            <h1 class="gt-cs-title">{{ $service['title'] }}</h1>
            <p class="gt-cs-sub">{{ $service['tagline'] }}</p>
        </div>

        <div class="gt-cs-actions">
            <a href="{{ url('/contact-us') . '?enquiry=' . urlencode($service['title']) }}" class="gt-cs-btn gt-cs-btn-primary">
                <i class="bi bi-chat-dots-fill"></i> Enquire Now
            </a>
            <a href="/" class="gt-cs-btn gt-cs-btn-ghost">
                <i class="bi bi-house"></i> Home
            </a>
        </div>
    </div>
</div>

@include('footer')
