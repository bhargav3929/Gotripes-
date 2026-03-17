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
        font-size: 5rem;
        color: #FFD700;
        opacity: 0.5;
        margin-bottom: 30px;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
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
        margin-bottom: 20px;
    }

    .coming-soon-title {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 15px;
        letter-spacing: -1px;
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
        max-width: 500px;
        margin: 0 auto 40px;
        line-height: 1.6;
    }

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
        .coming-soon-subtitle { font-size: 15px; }
        .coming-soon-icon { font-size: 3.5rem; }
    }
</style>

<div class="coming-soon-body">
    <section class="coming-soon-section">
        <div class="container">
            <div class="coming-soon-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="coming-soon-badge">Online Store</div>
            <h1 class="coming-soon-title">
                Coming <span>Soon</span>
            </h1>
            <p class="coming-soon-subtitle">
                Something exciting is on the way. Stay tuned!
            </p>
        </div>
    </section>
</div>

@include('footer')
