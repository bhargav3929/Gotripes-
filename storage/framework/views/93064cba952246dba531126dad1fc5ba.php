<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GoTrips Freelancers — Sell Travel, Earn From Anywhere</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/index_files/logo.png')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --gold: #FFD700;
            --gold-deep: #D4AF37;
            --bg: #050505;
            --card: #0d0d0d;
            --text: #f0f0f0;
            --muted: #8a8f98;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg); color: var(--text);
            overflow-x: hidden;
        }
        .nav {
            position: fixed; top: 0; left: 0; right: 0;
            padding: 18px 40px;
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(5,5,5,0.85);
            backdrop-filter: blur(16px);
            z-index: 100;
            border-bottom: 1px solid rgba(255,215,0,0.08);
        }
        .nav-logo { display: flex; align-items: center; gap: 10px; font-family: 'Outfit'; font-weight: 800; font-size: 20px; }
        .nav-logo img { height: 32px; }
        .nav-logo span { color: var(--gold); }
        .nav-cta {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-deep) 100%);
            color: #000; border: none; border-radius: 99px;
            font-weight: 700; font-size: 13px; letter-spacing: 1.2px;
            text-decoration: none; text-transform: uppercase;
            transition: all 0.2s;
        }
        .nav-cta:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(255,215,0,0.3); color: #000; }

        .hero {
            min-height: 100vh;
            padding: 140px 40px 80px;
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 800px 400px at 80% 20%, rgba(255,215,0,0.08), transparent 60%),
                radial-gradient(ellipse 600px 400px at 20% 80%, rgba(212,175,55,0.06), transparent 60%);
            z-index: 0;
        }
        .hero-inner {
            max-width: 1180px; width: 100%;
            position: relative; z-index: 1;
            text-align: center;
        }
        .hero-eyebrow {
            display: inline-block;
            font-family: 'Outfit'; font-size: 12px; font-weight: 600;
            letter-spacing: 4px; color: var(--gold);
            text-transform: uppercase;
            padding: 8px 18px;
            background: rgba(255,215,0,0.08);
            border: 1px solid rgba(255,215,0,0.2);
            border-radius: 99px;
            margin-bottom: 28px;
        }
        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(40px, 7vw, 88px);
            font-weight: 800;
            line-height: 1.02;
            letter-spacing: -0.035em;
            margin-bottom: 24px;
            color: #fff;
        }
        .hero-title .gold {
            background: linear-gradient(120deg, #B8860B 0%, #FFD700 25%, #FFFAE5 50%, #FFD700 75%, #B8860B 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 5s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        .hero-sub {
            max-width: 720px; margin: 0 auto 40px;
            font-size: clamp(16px, 1.5vw, 19px);
            line-height: 1.65;
            color: rgba(240,240,240,0.7);
            font-weight: 400;
        }
        .hero-ctas {
            display: flex; justify-content: center; gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 80px;
        }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 18px 36px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-deep) 100%);
            color: #000; border: none; border-radius: 99px;
            font-weight: 700; font-size: 15px; letter-spacing: 1.2px;
            text-decoration: none; text-transform: uppercase;
            transition: all 0.2s; cursor: pointer;
            box-shadow: 0 8px 28px rgba(255,215,0,0.25);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 40px rgba(255,215,0,0.45); color: #000; }
        .btn-ghost {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 18px 36px;
            background: transparent;
            color: #fff; border: 1px solid rgba(255,255,255,0.2); border-radius: 99px;
            font-weight: 600; font-size: 15px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-ghost:hover { border-color: var(--gold); color: var(--gold); text-decoration: none; }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            max-width: 920px;
            margin: 0 auto;
        }
        .stat {
            padding: 28px 20px;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,215,0,0.1);
            border-radius: 16px;
            backdrop-filter: blur(8px);
        }
        .stat-num {
            font-family: 'Outfit'; font-size: 36px; font-weight: 800;
            color: var(--gold); letter-spacing: -0.02em;
            margin-bottom: 6px;
        }
        .stat-label { font-size: 13px; color: var(--muted); letter-spacing: 0.5px; }

        .features {
            padding: 100px 40px;
            max-width: 1180px; margin: 0 auto;
        }
        .features-title {
            font-family: 'Outfit'; font-size: clamp(32px, 4vw, 56px);
            font-weight: 800; letter-spacing: -0.025em;
            line-height: 1.1; margin-bottom: 16px;
            color: #fff; max-width: 800px;
        }
        .features-sub {
            font-size: 17px; line-height: 1.6;
            color: rgba(240,240,240,0.6);
            max-width: 600px; margin-bottom: 56px;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .feature {
            padding: 36px 30px;
            background: var(--card);
            border: 1px solid rgba(255,215,0,0.1);
            border-radius: 20px;
            transition: all 0.25s;
        }
        .feature:hover { border-color: rgba(255,215,0,0.3); transform: translateY(-4px); }
        .feature-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--gold), var(--gold-deep));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            color: #000; font-size: 22px;
            margin-bottom: 20px;
        }
        .feature h3 {
            font-family: 'Outfit'; font-size: 20px; font-weight: 700;
            margin-bottom: 10px; color: #fff; letter-spacing: -0.01em;
        }
        .feature p { font-size: 14px; line-height: 1.6; color: rgba(240,240,240,0.6); }

        .cta-band {
            margin: 80px 40px 100px;
            max-width: 1180px;
            margin-left: auto; margin-right: auto;
            padding: 64px 40px;
            background: linear-gradient(135deg, rgba(255,215,0,0.08) 0%, rgba(212,175,55,0.04) 100%);
            border: 1px solid rgba(255,215,0,0.25);
            border-radius: 24px;
            text-align: center;
        }
        .cta-band h2 {
            font-family: 'Outfit'; font-size: clamp(32px, 4vw, 48px);
            font-weight: 800; letter-spacing: -0.02em; line-height: 1.1;
            margin-bottom: 14px; color: #fff;
        }
        .cta-band p { font-size: 16px; color: rgba(240,240,240,0.7); margin-bottom: 30px; max-width: 580px; margin-left: auto; margin-right: auto; line-height: 1.6; }

        footer {
            padding: 40px; text-align: center;
            border-top: 1px solid rgba(255,255,255,0.05);
            color: var(--muted); font-size: 13px;
        }

        @media (max-width: 768px) {
            .nav { padding: 14px 20px; }
            .hero { padding: 120px 20px 60px; }
            .hero-ctas { flex-direction: column; }
            .btn-primary, .btn-ghost { width: 100%; justify-content: center; }
            .stats { grid-template-columns: repeat(2, 1fr); }
            .features-grid { grid-template-columns: 1fr; }
            .cta-band { margin: 40px 20px 60px; padding: 40px 24px; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a class="nav-logo" href="/freelancer">
            <img src="<?php echo e(asset('assets/index_files/logo.png')); ?>" alt="GoTrips">
            <span>GoTrips</span> Freelancers
        </a>
        <a href="<?php echo e(route('referral.login')); ?>" class="nav-cta">
            Sign In <i class="fas fa-arrow-right"></i>
        </a>
    </nav>

    <section class="hero">
        <div class="hero-inner">
            <span class="hero-eyebrow">For Independent Travel Sellers</span>
            <h1 class="hero-title">
                Sell Travel.<br>
                <span class="gold">Earn From Anywhere.</span>
            </h1>
            <p class="hero-sub">
                Build your own travel storefront powered by GoTrips. Market UAE activities, visas, eSIMs and tour packages — and earn commissions on every sale, paid directly to your bank.
            </p>
            <div class="hero-ctas">
                <a href="<?php echo e(route('freelancer.register')); ?>" class="btn-primary">
                    Become a Freelancer <i class="fas fa-arrow-right"></i>
                </a>
                <a href="<?php echo e(route('referral.login')); ?>" class="btn-ghost">
                    I already have an account
                </a>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-num">186+</div>
                    <div class="stat-label">Countries with eSIM</div>
                </div>
                <div class="stat">
                    <div class="stat-num">15%</div>
                    <div class="stat-label">Avg. commission</div>
                </div>
                <div class="stat">
                    <div class="stat-num">24h</div>
                    <div class="stat-label">Payout window</div>
                </div>
                <div class="stat">
                    <div class="stat-num">$0</div>
                    <div class="stat-label">Setup fees</div>
                </div>
            </div>
        </div>
    </section>

    <section class="features">
        <h2 class="features-title">Everything you need to sell.</h2>
        <p class="features-sub">A complete travel desk on day one — no inventory to hold, no operations to run.</p>

        <div class="features-grid">
            <div class="feature">
                <div class="feature-icon"><i class="fas fa-link"></i></div>
                <h3>Personalised storefront</h3>
                <p>Your own referral link. Share it on WhatsApp, Instagram, or your website — every sale is tracked back to you.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fas fa-coins"></i></div>
                <h3>Commission on every sale</h3>
                <p>Earn on activities, visas, eSIMs and tour packages. Real-time dashboard shows clicks, conversions, earnings.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <h3>Direct bank payouts</h3>
                <p>Withdraw to your bank account when you hit the threshold. We handle KYC, taxes, and compliance.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <h3>Customer ops handled</h3>
                <p>We answer support tickets, process refunds, and deliver eSIM QR codes — you focus on selling.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Live performance dashboard</h3>
                <p>Track every click, every booking, every dirham earned — updated in real-time, 24/7.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fas fa-shield-halved"></i></div>
                <h3>Trusted by 10,000+</h3>
                <p>GoTrips is a regulated UAE travel operator. Your customers buy from a brand they trust.</p>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <h2>Start earning in under 2 minutes.</h2>
        <p>No approvals, no waiting list. Sign up today and your storefront is live by tonight.</p>
        <a href="<?php echo e(route('freelancer.register')); ?>" class="btn-primary">
            Create your account <i class="fas fa-arrow-right"></i>
        </a>
    </section>

    <footer>
        © <?php echo e(date('Y')); ?> GoTrips Freelancers · A division of GoTrips · <a href="https://gotrips.ai" style="color: var(--gold); text-decoration: none;">gotrips.ai</a>
    </footer>
</body>
</html>
<?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/freelancer/landing.blade.php ENDPATH**/ ?>