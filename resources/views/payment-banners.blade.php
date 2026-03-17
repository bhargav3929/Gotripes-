<style>
    .payment-gateway-banner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        padding: 14px 28px;
        background: rgba(10, 10, 10, 0.75);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 215, 0, 0.25);
        border-radius: 50px;
        margin: 0 auto 25px auto;
        width: fit-content;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.08);
        z-index: 10;
        position: relative;
        transition: all 0.3s ease;
    }

    .payment-gateway-banner:hover {
        border-color: rgba(255, 215, 0, 0.5);
        box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2), inset 0 1px 1px rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
    }

    .payment-gateway-banner-text {
        color: #FFD700;
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2.5px;
        margin-right: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
        flex-shrink: 0;
    }

    .payment-gateway-banner-text i {
        font-size: 19px;
        color: #4CAF50;
    }

    .payment-logos-wrapper {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .payment-logo-box {
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        padding: 0 18px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }

    .payment-logo-box:hover {
        transform: translateY(-3px) scale(1.08);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
    }

    .payment-logo-box svg {
        display: block;
        flex-shrink: 0;
    }

    .payment-logo-box.apple-pay-box {
        background: #000;
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 0 16px;
    }

    .apple-pay-text {
        font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
        font-size: 20px;
        font-weight: 600;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 2px;
        letter-spacing: -0.5px;
    }

    .apple-logo {
        display: inline-block;
        width: 18px;
        height: 22px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 384 512'%3E%3Cpath fill='%23ffffff' d='M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z'/%3E%3C/svg%3E");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
    }

    .payment-logo-box.google-pay-box {
        padding: 0 12px;
    }

    .payment-logo-box.tabby-box {
        background: #3EEDCB;
        /* Tabby brand color */
        padding: 0 16px;
    }

    .tabby-text {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        font-size: 21px;
        font-weight: 800;
        color: #000;
        letter-spacing: -0.5px;
        line-height: 1;
    }

    @media (max-width: 768px) {
        .payment-gateway-banner {
            gap: 10px;
            padding: 12px 20px;
            border-radius: 16px;
            margin-bottom: 20px;
        }

        .payment-gateway-banner-text {
            width: 100%;
            margin-right: 0;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .payment-logos-wrapper {
            gap: 10px;
        }

        .payment-logo-box {
            height: 32px;
            padding: 0 10px;
            border-radius: 6px;
        }

        .payment-logo-box svg {
            height: 18px !important;
            width: auto !important;
        }

        .tabby-text {
            font-size: 16px;
        }
    }
</style>

<div class="payment-gateway-banner">
    <div class="payment-gateway-banner-text">
        <i class="bi bi-shield-check"></i> We Accept
    </div>
    <div class="payment-logos-wrapper">
        <!-- Visa -->
        <div class="payment-logo-box">
            <svg width="70" height="24" viewBox="0 0 750 471" xmlns="http://www.w3.org/2000/svg">
                <path fill="#1A1F71" d="M278.198 334.228l33.36-195.763h53.358l-33.384 195.763h-53.334zm246.11-191.005c-10.569-3.966-27.135-8.222-47.822-8.222-52.726 0-89.863 26.551-90.18 64.604-.631 28.129 26.538 43.822 46.791 53.199 20.568 9.536 27.478 15.67 27.373 24.197-.159 13.063-16.412 19.041-31.616 19.041-21.041 0-32.216-2.926-49.49-10.136l-6.82-3.082-7.394 43.299c12.295 5.391 35.003 10.079 58.617 10.319 56.031 0 92.389-26.199 92.863-66.833.199-22.269-14.034-39.216-44.8-53.188-18.65-9.056-30.072-15.099-29.951-24.269 0-8.137 9.668-16.838 30.56-16.838 17.446-.271 30.109 3.534 39.937 7.497l4.78 2.259 7.152-40.847zm137.95-4.399h-41.234c-12.772 0-22.332 3.477-27.942 16.218l-79.245 179.404h56.031s9.159-24.121 11.231-29.418c6.124 0 60.554.084 68.336.084 1.596 6.854 6.492 29.334 6.492 29.334h49.512l-43.181-195.622zm-65.417 126.408c4.413-11.279 21.26-54.723 21.26-54.723-.316.525 4.38-11.32 7.074-18.663l3.606 16.861s10.217 46.729 12.353 56.525h-44.293zm-363.4-126.408l-52.239 133.496-5.565-27.129c-9.726-31.274-40.025-65.157-73.898-82.12l47.767 171.204 56.455-.063 84.004-195.388h-56.524" />
                <path fill="#F9A533" d="M146.92 138.465H63.03l-.682 4.073c66.939 16.204 111.232 55.363 129.618 102.415l-18.709-89.96c-3.229-12.396-12.597-16.095-27.337-16.528" />
            </svg>
        </div>
        <!-- Mastercard -->
        <div class="payment-logo-box">
            <svg width="46" height="28" viewBox="0 0 152 100" xmlns="http://www.w3.org/2000/svg">
                <rect fill="#FF5F00" x="52" y="17" width="48" height="66"/>
                <path fill="#EB001B" d="M55.28 50c0-13.39 6.17-25.33 15.82-33.15A41.66 41.66 0 0 0 45.73 8.5C23.75 8.5 5.89 27.08 5.89 50S23.75 91.5 45.73 91.5a41.66 41.66 0 0 0 25.37-8.35C61.45 75.33 55.28 63.39 55.28 50z"/>
                <path fill="#F79E1B" d="M146.11 50c0 22.92-17.86 41.5-39.84 41.5a41.66 41.66 0 0 1-25.37-8.35c9.65-7.82 15.82-19.76 15.82-33.15s-6.17-25.33-15.82-33.15a41.66 41.66 0 0 1 25.37-8.35c21.98 0 39.84 18.58 39.84 41.5z"/>
            </svg>
        </div>
        <!-- Apple Pay -->
        <div class="payment-logo-box apple-pay-box">
            <span class="apple-pay-text"><span class="apple-logo"></span>Pay</span>
        </div>
        <!-- Google Pay -->
        <div class="payment-logo-box google-pay-box">
            <svg width="58" height="24" viewBox="0 0 435 173" xmlns="http://www.w3.org/2000/svg">
                <path fill="#5F6368" d="M206.2 84.7v50.5h-16V8.8h42.4c10.2-.2 20.1 3.7 27.5 10.8 7.5 6.8 11.7 16.4 11.5 26.4.2 10.1-4 19.8-11.5 26.6-7.5 7-16.7 10.6-27.6 10.6h-26.3v1.5zm0-60.1v44.3h26.6c6.1.2 12-2.2 16.3-6.6 8.5-8.3 8.7-22 .4-30.5l-.4-.4c-4.3-4.5-10.2-7-16.3-6.8h-26.6z"/>
                <path fill="#5F6368" d="M309.1 46.3c11.8 0 21.1 3.2 28 9.5 6.9 6.3 10.3 15 10.3 26v52.5h-15.3v-11.8h-.7c-6.6 9.7-15.4 14.6-26.4 14.6-9.4 0-17.2-2.8-23.5-8.3-6.1-5.2-9.6-12.8-9.4-20.8 0-8.8 3.3-15.8 10-21 6.6-5.2 15.5-7.8 26.6-7.8 9.5 0 17.3 1.7 23.4 5.2v-3.7c0-5.6-2.4-10.9-6.6-14.6-4.2-3.9-9.7-6-15.5-6-9 0-16.1 3.8-21.3 11.4l-14.1-8.9c7.6-11.2 19-16.8 34-16.8l.5-.5zm-20.6 62.3c0 4.2 2 8.2 5.4 10.8 3.5 3 8 4.6 12.6 4.5 6.8 0 13.3-2.7 18.1-7.6 5.3-5 7.9-10.9 7.9-17.6-4.9-4-11.8-6-20.5-6-6.3 0-11.6 1.6-15.8 4.7-4.5 3-7.7 7.5-7.7 11.2z"/>
                <path fill="#5F6368" d="M436.7 49.1l-53.4 123h-16.6l19.8-43-35.1-80h17.5l25.1 60.8h.3l24.5-60.8h17.9z"/>
                <path fill="#4285F4" d="M142.1 73.1c0-4.8-.4-9.5-1.2-14.1H73v26.7h38.9c-1.6 8.9-6.8 16.9-14.4 21.9v18h23.2c13.6-12.5 21.4-31 21.4-52.5z"/>
                <path fill="#34A853" d="M73 144c19.4 0 35.8-6.4 47.7-17.4l-23.2-18c-6.5 4.4-14.8 6.9-24.5 6.9-18.8 0-34.7-12.7-40.4-29.7H8.7v18.5C20.4 128.3 45 144 73 144z"/>
                <path fill="#FBBC04" d="M32.6 85.8c-3-8.9-3-18.5 0-27.4V39.9H8.7C-1.4 59.2-1.4 82.8 8.7 102l23.9-16.2z"/>
                <path fill="#EA4335" d="M73 28.7c10.3-.2 20.2 3.7 27.6 10.8l20.5-20.5C108.1 6.5 91 .2 73 .5 45 .5 20.4 16.2 8.7 40.2l23.9 18.5c5.7-17.3 21.6-30 40.4-30z"/>
            </svg>
        </div>
        <!-- Tabby -->
        <div class="payment-logo-box tabby-box">
            <span class="tabby-text">tabby</span>
        </div>
    </div>
</div>