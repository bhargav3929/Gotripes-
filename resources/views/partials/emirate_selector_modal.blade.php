@php
    $modalLogo = (isset($company) && $company && $company->logo) ? asset('storage/' . $company->logo) : asset('assets/index_files/logo.png');
    $modalName = (isset($company) && $company && $company->name) ? $company->name : 'Go Trips';
    $modalEmirates = $activeEmirates->map(function ($e) {
        return [
            'id' => $e->emiratesName,
            'name' => $e->emiratesName,
            'image' => $e->emiratesImage ? asset($e->emiratesImage) : null,
        ];
    })->values();
@endphp
<!-- EMIRATE SELECTION POPUP MODAL -->
<style>
    /* =============================================
       EMIRATE SELECTOR MODAL — REDESIGNED
       Premium black & gold theme | No-scroll layout
    ============================================= */

    .emirate-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        z-index: 11000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .emirate-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .emirate-modal {
        background: linear-gradient(145deg, #111111 0%, #0d0d0d 100%);
        border: 1px solid rgba(255, 215, 0, 0.22);
        border-radius: 20px;
        padding: 24px 24px 22px;
        width: min(860px, 96vw);
        /* CRITICAL: No overflow-y auto on desktop — layout fits the viewport */
        max-height: 88vh;
        overflow: hidden;
        position: relative;
        box-shadow:
            0 0 0 1px rgba(255,215,0,0.06),
            0 24px 80px rgba(0, 0, 0, 0.9),
            0 0 60px rgba(255,215,0,0.04) inset;
        transform: scale(0.92) translateY(12px);
        transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        font-family: 'Outfit', sans-serif;
        text-align: center;
    }
    .emirate-overlay.active .emirate-modal {
        transform: scale(1) translateY(0);
    }

    /* Header area */
    .emirate-modal-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-bottom: 14px;
        position: relative;
    }

    .emirate-close-btn {
        position: absolute;
        top: -4px;
        right: 0;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        color: #aaa;
        font-size: 18px;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .emirate-close-btn:hover {
        background: rgba(255, 215, 0, 0.15);
        border-color: rgba(255, 215, 0, 0.4);
        color: #FFD700;
        transform: rotate(90deg);
    }

    .emirate-logo {
        display: block;
        height: 38px;
        width: auto;
        object-fit: contain;
        flex-shrink: 0;
    }

    .emirate-title {
        color: #FFD700;
        font-size: 17px;
        font-weight: 800;
        margin: 0 0 16px;
        letter-spacing: 0.2px;
        line-height: 1.3;
        text-shadow: 0 0 30px rgba(255,215,0,0.2);
    }

    /* Divider */
    .emirate-divider {
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #FFD700, transparent);
        margin: 0 auto 16px;
        border-radius: 2px;
    }

    /* =============================================
       GRID — 4 columns desktop, 3 tablet, 2 mobile
    ============================================= */
    .emirate-cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    /* =============================================
       CARD — compact image card
    ============================================= */
    .emirate-card {
        position: relative;
        background: transparent;
        border: 2px solid rgba(255, 215, 0, 0.18);
        border-radius: 12px;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
        color: #fff;
        font-family: 'Outfit', sans-serif;
        width: 100%;
        overflow: hidden;
        aspect-ratio: 4 / 3.2;
        /* Fixed height per card — no variation */
    }

    /* Image fills card */
    .emirate-card .emirate-flag-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
        transition: transform 0.4s ease;
        display: block;
    }

    /* Dark gradient overlay at bottom */
    .emirate-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to top,
            rgba(0,0,0,0.82) 0%,
            rgba(0,0,0,0.30) 50%,
            rgba(0,0,0,0.05) 100%
        );
        border-radius: 10px;
        transition: background 0.25s ease;
        z-index: 1;
    }

    /* Gold border glow on hover */
    .emirate-card:hover {
        border-color: #FFD700;
        box-shadow:
            0 0 0 1px rgba(255,215,0,0.3),
            0 12px 32px -6px rgba(255,215,0,0.25);
        transform: translateY(-3px) scale(1.02);
    }
    .emirate-card:hover .emirate-flag-img {
        transform: scale(1.06);
    }
    .emirate-card:hover::after {
        background: linear-gradient(
            to top,
            rgba(0,0,0,0.90) 0%,
            rgba(0,0,0,0.35) 50%,
            rgba(0,0,0,0.08) 100%
        );
    }

    /* Selected state */
    .emirate-card.selected,
    .emirate-card[aria-pressed="true"] {
        border-color: #FFD700;
        box-shadow:
            0 0 0 3px rgba(255,215,0,0.45),
            0 8px 24px -4px rgba(255,215,0,0.3);
    }
    .emirate-card.selected::before {
        content: '✓';
        position: absolute;
        top: 7px;
        right: 8px;
        z-index: 3;
        width: 22px;
        height: 22px;
        background: #FFD700;
        color: #000;
        border-radius: 50%;
        font-size: 12px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Name badge at bottom */
    .emirate-card-name {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 2;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #FFD700;
        text-align: center;
        padding: 6px 4px 8px;
        line-height: 1.2;
    }

    /* Fallback icon when no image */
    .emirate-card-icon {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
        background: linear-gradient(135deg, #1a1400, #2a2000);
    }

    /* Gold "Apply" hint on hover */
    .emirate-card-hover-hint {
        position: absolute;
        top: 8px;
        left: 8px;
        z-index: 3;
        background: rgba(255, 215, 0, 0.9);
        color: #000;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 3px 7px;
        border-radius: 20px;
        opacity: 0;
        transform: translateY(-4px);
        transition: all 0.2s ease;
    }
    .emirate-card:hover .emirate-card-hover-hint {
        opacity: 1;
        transform: translateY(0);
    }

    /* =============================================
       RESPONSIVE BREAKPOINTS
    ============================================= */

    /* Large tablet: 3 columns */
    @media (max-width: 860px) {
        .emirate-cards-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 9px;
        }
    }

    /* Tablet: 3 columns (tight) */
    @media (max-width: 640px) {
        .emirate-modal {
            padding: 18px 14px 16px;
            border-radius: 16px;
        }
        .emirate-title {
            font-size: 15px;
            margin-bottom: 12px;
        }
        .emirate-logo {
            height: 32px;
        }
        .emirate-cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        .emirate-card-name {
            font-size: 10px;
            letter-spacing: 0.8px;
        }
    }

    /* Mobile: 2 columns */
    @media (max-width: 380px) {
        .emirate-cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 7px;
        }
        .emirate-card-name {
            font-size: 9.5px;
        }
    }

    /* =============================================
       LIGHT THEME SUPPORT
    ============================================= */
    html[data-theme="light"] .emirate-overlay {
        background: rgba(10, 10, 10, 0.80);
    }
    html[data-theme="light"] .emirate-modal {
        background: linear-gradient(145deg, #1a1a1a, #111);
        border-color: rgba(255, 215, 0, 0.3);
    }
</style>

<div id="emirateSelectorOverlay" class="emirate-overlay" role="dialog" aria-modal="true" aria-labelledby="emirateModalTitle">
    <div class="emirate-modal">
        <!-- Header -->
        <div class="emirate-modal-header">
            <img src="{{ $modalLogo }}" alt="{{ $modalName }}" class="emirate-logo">
            <button type="button" class="emirate-close-btn" id="emirateCloseBtn" aria-label="Close modal">&times;</button>
        </div>

        <h2 class="emirate-title" id="emirateModalTitle">Which Emirates Visa Are You Applying For?</h2>
        <div class="emirate-divider"></div>

        <div class="emirate-cards-grid" id="emirateGrid">
            <!-- Dynamic selection cards rendered in JS -->
        </div>
    </div>
</div>

<script>
    (function() {
        const AVAILABLE_EMIRATES = @json($modalEmirates);

        const overlay = document.getElementById('emirateSelectorOverlay');
        const grid = document.getElementById('emirateGrid');
        const closeBtn = document.getElementById('emirateCloseBtn');
        const hiddenInput = document.getElementById('selectedEmirate');

        if (!overlay || !grid) return;

        // Render Cards dynamically
        grid.innerHTML = AVAILABLE_EMIRATES.map(e => `
            <button type="button" class="emirate-card" data-emirate="${e.id}" aria-label="Select ${e.name}">
                ${e.image
                    ? `<img class="emirate-flag-img" src="${e.image}" alt="${e.name}" loading="lazy">`
                    : `<span class="emirate-card-icon"><i class="bi bi-flag-fill" style="font-size:28px;color:#FFD700;"></i></span>`
                }
                <span class="emirate-card-hover-hint">Select</span>
                <span class="emirate-card-name">${e.name}</span>
            </button>
        `).join('');

        // Attach Event Listeners to cards
        grid.querySelectorAll('.emirate-card').forEach(card => {
            card.addEventListener('click', function() {
                // Visual selected state
                grid.querySelectorAll('.emirate-card').forEach(c => {
                    c.classList.remove('selected');
                    c.removeAttribute('aria-pressed');
                });
                this.classList.add('selected');
                this.setAttribute('aria-pressed', 'true');

                selectEmirate(this.getAttribute('data-emirate'));
            });
        });

        // Close button click handler
        if (closeBtn) {
            closeBtn.addEventListener('click', closeEmirateSelector);
        }

        // Click outside modal container closes it
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeEmirateSelector();
            }
        });

        // Keyboard: Escape closes modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('active')) {
                closeEmirateSelector();
            }
        });

        function selectEmirate(emirate) {
            // Sync with hidden form input if present
            if (hiddenInput) {
                hiddenInput.value = emirate;
            }

            // Trigger custom event so parent view can update
            document.dispatchEvent(new CustomEvent('emirateChanged', { detail: emirate }));

            // Small delay for visual feedback before closing
            setTimeout(closeEmirateSelector, 180);
        }

        function showEmirateSelector() {
            overlay.classList.add('active');
            // Reset selections on open
            grid.querySelectorAll('.emirate-card').forEach(c => {
                c.classList.remove('selected');
                c.removeAttribute('aria-pressed');
            });
        }

        function closeEmirateSelector() {
            overlay.classList.remove('active');
        }

        // Expose globally so the main page can trigger it (initial load + "Change" badge)
        window.showEmirateSelector = showEmirateSelector;
    })();
</script>
