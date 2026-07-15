@include('header')

<main class="study-abroad-page" style="background: #000; min-height: 70vh; display: flex; align-items: center; justify-content: center; color: #fff; font-family: 'Outfit', sans-serif; padding: 80px 24px; text-align: center; position: relative; overflow: hidden;">
    {{-- Decorative gold radial glow background matching site style --}}
    <div style="position: absolute; top: -10%; right: -10%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(255,215,0,0.08) 0%, transparent 70%); pointer-events: none; z-index: 1;"></div>
    <div style="position: absolute; bottom: -10%; left: -10%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(255,215,0,0.05) 0%, transparent 70%); pointer-events: none; z-index: 1;"></div>

    <div class="container" style="position: relative; z-index: 2; max-width: 680px; margin: 0 auto;">
        <!-- Coming Soon Badge -->
        <span class="badge-coming-soon" style="display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: #FFD700; border: 1px solid rgba(255,215,0,0.3); border-radius: 50px; padding: 8px 20px; margin-bottom: 25px; background: rgba(255,215,0,0.03);">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #FFD700; display: inline-block;"></span> Coming Soon
        </span>

        <!-- Title -->
        <h1 style="font-size: clamp(32px, 6vw, 56px); font-weight: 800; line-height: 1.1; margin-bottom: 20px; text-transform: uppercase; background: linear-gradient(135deg, #FFD700 0%, #FFB200 50%, #B8960C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            Study Abroad
        </h1>

        <!-- Description -->
        <p style="font-size: clamp(15px, 2.5vw, 18px); color: #ccc; line-height: 1.6; font-weight: 300; margin-bottom: 35px; max-width: 580px; margin-left: auto; margin-right: auto;">
            We're preparing exciting Study Abroad programs. Our team is working on this section and it will be available soon.
        </p>

        <!-- CTA Buttons -->
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="/contact-us?enquiry=Study+Abroad" class="btn-primary-gold" style="background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%); color: #000; font-weight: 700; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-size: 15px; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 15px rgba(255,215,0,0.15); display: inline-block;">
                <i class="bi bi-envelope-fill me-2"></i> Get Notified
            </a>
            <a href="/" class="btn-ghost-white" style="background: transparent; color: #fff; border: 1px solid rgba(255,255,255,0.15); font-weight: 600; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-size: 15px; transition: all 0.2s; display: inline-block;">
                Back to Homepage
            </a>
        </div>
    </div>
</main>

<style>
    .btn-primary-gold:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255,215,0,0.25) !important;
    }
    .btn-ghost-white:hover {
        border-color: #FFD700 !important;
        color: #FFD700 !important;
    }

    /* Light Theme overrides mapping */
    html[data-theme="light"] .study-abroad-page {
        background: #f8f9fa !important;
        color: #111 !important;
    }
    html[data-theme="light"] .study-abroad-page p {
        color: #555 !important;
    }
    html[data-theme="light"] .badge-coming-soon {
        background: rgba(212, 175, 55, 0.05) !important;
        color: #B8860B !important;
        border-color: rgba(212, 175, 55, 0.3) !important;
    }
    html[data-theme="light"] .badge-coming-soon span {
        background: #B8860B !important;
    }
    html[data-theme="light"] .btn-ghost-white {
        color: #333 !important;
        border-color: #ccc !important;
    }
    html[data-theme="light"] .btn-ghost-white:hover {
        border-color: #D4AF37 !important;
        color: #D4AF37 !important;
    }
</style>

@include('footer')
