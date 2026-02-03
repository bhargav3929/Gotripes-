@include('header')

<style>
    .coming-soon-body {
        background-color: #000;
        color: #fff;
        font-family: 'R', sans-serif;
    }

    .coming-soon-section {
        padding-top: 150px;
        padding-bottom: 100px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #000;
        background-image:
            radial-gradient(ellipse at 50% 0%, rgba(255, 215, 0, 0.1) 0%, transparent 50%),
            radial-gradient(ellipse at 50% 100%, rgba(255, 215, 0, 0.05) 0%, transparent 40%),
            radial-gradient(circle at 50% 50%, #1a1a1a 0%, #000 70%);
        position: relative;
        overflow: hidden;
    }

    .coming-soon-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
        pointer-events: none;
    }

    /* Animated floating particles */
    .particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        pointer-events: none;
    }

    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: linear-gradient(135deg, #FFD700, #debb55);
        border-radius: 50%;
        animation: float-particle 15s infinite ease-in-out;
        opacity: 0.3;
    }

    .particle:nth-child(1) {
        left: 10%;
        animation-delay: 0s;
        animation-duration: 12s;
    }

    .particle:nth-child(2) {
        left: 20%;
        animation-delay: 2s;
        animation-duration: 15s;
    }

    .particle:nth-child(3) {
        left: 30%;
        animation-delay: 4s;
        animation-duration: 13s;
    }

    .particle:nth-child(4) {
        left: 40%;
        animation-delay: 1s;
        animation-duration: 14s;
    }

    .particle:nth-child(5) {
        left: 50%;
        animation-delay: 3s;
        animation-duration: 16s;
    }

    .particle:nth-child(6) {
        left: 60%;
        animation-delay: 5s;
        animation-duration: 12s;
    }

    .particle:nth-child(7) {
        left: 70%;
        animation-delay: 2s;
        animation-duration: 15s;
    }

    .particle:nth-child(8) {
        left: 80%;
        animation-delay: 4s;
        animation-duration: 14s;
    }

    .particle:nth-child(9) {
        left: 90%;
        animation-delay: 0s;
        animation-duration: 13s;
    }

    @keyframes float-particle {

        0%,
        100% {
            transform: translateY(100vh) scale(0);
            opacity: 0;
        }

        10% {
            opacity: 0.3;
        }

        50% {
            opacity: 0.6;
        }

        90% {
            opacity: 0.3;
        }

        95% {
            transform: translateY(-10vh) scale(1);
            opacity: 0;
        }
    }

    .coming-soon-card {
        background: rgba(17, 17, 17, 0.9);
        border: 1px solid #333;
        border-radius: 16px;
        padding: 60px 80px;
        width: 100%;
        max-width: 700px;
        box-shadow:
            0 20px 60px rgba(0, 0, 0, 0.8),
            0 0 40px rgba(255, 215, 0, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.05);
        position: relative;
        overflow: hidden;
        text-align: center;
        z-index: 1;
        backdrop-filter: blur(10px);
    }

    .coming-soon-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, transparent, #FFD700, #debb55, #FFD700, transparent);
    }

    .coming-soon-icon {
        margin-bottom: 30px;
    }

    .coming-soon-icon svg {
        width: 80px;
        height: 80px;
        fill: #FFD700;
        animation: pulse-glow 2s infinite ease-in-out;
    }

    @keyframes pulse-glow {

        0%,
        100% {
            filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.3));
            transform: scale(1);
        }

        50% {
            filter: drop-shadow(0 0 25px rgba(255, 215, 0, 0.6));
            transform: scale(1.05);
        }
    }

    .coming-soon-badge {
        display: inline-block;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(222, 187, 85, 0.1));
        border: 1px solid rgba(255, 215, 0, 0.3);
        padding: 8px 24px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #FFD700;
        margin-bottom: 25px;
    }

    .coming-soon-title {
        color: #fff;
        font-family: 'B', sans-serif;
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 15px;
        letter-spacing: -1px;
        line-height: 1.2;
    }

    .coming-soon-title span {
        background: linear-gradient(135deg, #FFD700, #debb55);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .coming-soon-subtitle {
        color: #888;
        font-size: 18px;
        margin-bottom: 40px;
        line-height: 1.6;
    }

    .coming-soon-divider {
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #FFD700, transparent);
        margin: 0 auto 40px;
    }

    .coming-soon-features {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .feature-item {
        text-align: center;
    }

    .feature-item i {
        font-size: 28px;
        color: #FFD700;
        margin-bottom: 10px;
        display: block;
    }

    .feature-item span {
        font-size: 13px;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-explore {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #FFD700 0%, #b8860b 100%);
        color: #000;
        font-weight: 700;
        font-family: 'B', sans-serif;
        padding: 16px 40px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-size: 14px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
    }

    .btn-explore:hover {
        background: #fff;
        color: #000;
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(255, 255, 255, 0.2);
        text-decoration: none;
    }

    .btn-explore svg {
        width: 18px;
        height: 18px;
        transition: transform 0.3s ease;
    }

    .btn-explore:hover svg {
        transform: translateX(5px);
    }

    .coming-soon-footer {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #222;
    }

    .coming-soon-footer p {
        color: #555;
        font-size: 13px;
        margin: 0;
    }

    .coming-soon-footer a {
        color: #FFD700;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .coming-soon-footer a:hover {
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .coming-soon-card {
            padding: 40px 30px;
            margin: 20px;
        }

        .coming-soon-title {
            font-size: 36px;
        }

        .coming-soon-subtitle {
            font-size: 16px;
        }

        .coming-soon-features {
            gap: 25px;
        }
    }
</style>

<div class="coming-soon-body">
    <section class="coming-soon-section">
        <!-- Floating particles -->
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <div class="coming-soon-card">
            <div class="coming-soon-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path
                        d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
                </svg>
            </div>

            <div class="coming-soon-badge">Online Store</div>

            <h1 class="coming-soon-title">
                <span>Coming</span> Soon
            </h1>

            <p class="coming-soon-subtitle">
                We're crafting an exceptional online shopping experience.<br>
                Stay tuned for exclusive travel products and services.
            </p>

            <div class="coming-soon-divider"></div>

            <div class="coming-soon-features">
                <div class="feature-item">
                    <i class="bi bi-shield-check"></i>
                    <span>Secure Payments</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-truck"></i>
                    <span>Fast Delivery</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-gift"></i>
                    <span>Exclusive Deals</span>
                </div>
            </div>

            <a href="/Activities" class="btn-explore">
                Explore Activities
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>

            <div class="coming-soon-footer">
                <p>Need to make a payment? <a href="/payonline">Pay Online Here</a></p>
            </div>
        </div>
    </section>
</div>

@include('footer')