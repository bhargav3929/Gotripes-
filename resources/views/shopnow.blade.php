@include('header')

<style>
    .coming-soon-body {
        background-color: #000;
        color: #fff;
        font-family: 'Outfit', sans-serif;
    }

    .coming-soon-section {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #000;
        background-image:
            radial-gradient(ellipse at 50% 0%, rgba(255, 215, 0, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 50% 50%, #1a1a1a 0%, #000 70%);
        position: relative;
        text-align: center;
        padding: 60px 20px;
    }

    .coming-soon-icon {
        font-size: 4rem;
        color: #FFD700;
        margin-bottom: 25px;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .coming-soon-badge {
        display: inline-block;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.25), rgba(222, 187, 85, 0.15));
        border: 1px solid rgba(255, 215, 0, 0.4);
        padding: 10px 28px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #FFD700;
        margin-bottom: 25px;
    }

    .coming-soon-title {
        font-size: 42px;
        font-weight: 800;
        margin-bottom: 15px;
        letter-spacing: -1px;
        color: #fff;
    }

    .coming-soon-title span {
        background: linear-gradient(135deg, #FFD700, #debb55);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .coming-soon-subtitle {
        color: #999;
        font-size: 16px;
        max-width: 420px;
        margin: 0 auto 40px;
        line-height: 1.7;
    }

    /* Feature Icons */
    .features-row {
        display: flex;
        justify-content: center;
        gap: 40px;
        flex-wrap: wrap;
        margin-bottom: 40px;
    }

    .feature-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .feature-icon {
        width: 50px;
        height: 50px;
        border: 1.5px solid rgba(255, 215, 0, 0.3);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #FFD700;
        background: rgba(255, 215, 0, 0.05);
    }

    .feature-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #888;
    }

    /* Explore Button */
    .btn-explore {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #FFD700 0%, #b8860b 100%);
        color: #000;
        font-weight: 700;
        padding: 14px 36px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-size: 14px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        width: 100%;
        max-width: 350px;
        justify-content: center;
    }

    .btn-explore:hover {
        background: #fff;
        color: #000;
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(255, 255, 255, 0.2);
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .coming-soon-title { font-size: 32px; }
        .coming-soon-subtitle { font-size: 14px; }
        .coming-soon-icon { font-size: 3rem; }
        .features-row { gap: 25px; }
        .feature-icon { width: 44px; height: 44px; font-size: 1rem; }
        .feature-label { font-size: 10px; }
    }
</style>

<div class="coming-soon-body">
    <section class="coming-soon-section">
        <div class="container">
            <div class="coming-soon-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="coming-soon-badge">Online Store</div>
            <h1 class="coming-soon-title">
                Coming <span>Soon</span>
            </h1>
            <p class="coming-soon-subtitle">
                We're crafting an exceptional online shopping experience.
                Stay tuned for exclusive travel products and services.
            </p>

            <div class="features-row">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <span class="feature-label">Secure Payments</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <span class="feature-label">Fast Delivery</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <span class="feature-label">Exclusive Deals</span>
                </div>
            </div>

            <a href="{{ url('/activities') }}" class="btn-explore">
                Explore Activities <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>
</div>

@include('footer')
