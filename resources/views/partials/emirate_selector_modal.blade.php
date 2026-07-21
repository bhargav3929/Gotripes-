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
        padding: 32px 30px 28px;
        width: min(680px, 94vw);
        max-height: 92vh;
        overflow-y: auto;
        position: relative;
        box-shadow:
            0 0 0 1px rgba(255,215,0,0.06),
            0 24px 80px rgba(0, 0, 0, 0.9),
            0 0 60px rgba(255,215,0,0.04) inset;
        transform: scale(0.92) translateY(12px);
        transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        font-family: 'Outfit', sans-serif;
        text-align: center;
        /* Firefox */
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 215, 0, 0.35) transparent;
    }
    .emirate-overlay.active .emirate-modal {
        transform: scale(1) translateY(0);
    }

    /* Dark gold scrollbar — Chrome/Edge/Safari */
    .emirate-modal::-webkit-scrollbar {
        width: 8px;
    }
    .emirate-modal::-webkit-scrollbar-track {
        background: transparent;
        margin: 12px 0;
    }
    .emirate-modal::-webkit-scrollbar-thumb {
        background: rgba(255, 215, 0, 0.3);
        border-radius: 8px;
    }
    .emirate-modal::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 215, 0, 0.5);
    }
    /* Kill the classic Windows arrow buttons */
    .emirate-modal::-webkit-scrollbar-button {
        display: none;
        height: 0;
        width: 0;
    }
    .emirate-modal::-webkit-scrollbar-corner {
        background: transparent;
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
        font-size: 26px;
        font-weight: 800;
        margin: 0 0 22px;
        letter-spacing: 0.3px;
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
       GRID — 2x2 gold cards
    ============================================= */
    .emirate-cards-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* =============================================
       CARD — gold gradient card, image + name
    ============================================= */
    .emirate-card {
        position: relative;
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        border: none;
        border-radius: 18px;
        padding: 12px 12px 18px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
        color: #1a1400;
        font-family: 'Outfit', sans-serif;
        width: 100%;
        box-shadow: 0 10px 26px -8px rgba(255, 215, 0, 0.35);
    }

    /* Image sits inside the card, above the name.
       Height tracks viewport height so all 4 cards fit without scrolling. */
    .emirate-card .emirate-flag-img {
        width: 100%;
        height: clamp(120px, 26vh, 240px);
        object-fit: cover;
        border-radius: 12px;
        display: block;
    }

    .emirate-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px -8px rgba(255, 215, 0, 0.5);
        filter: brightness(1.04);
    }

    /* Selected state */
    .emirate-card.selected,
    .emirate-card[aria-pressed="true"] {
        box-shadow:
            0 0 0 3px rgba(26, 20, 0, 0.55),
            0 16px 40px -8px rgba(255, 215, 0, 0.5);
    }
    .emirate-card.selected::before {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 12px;
        z-index: 3;
        width: 22px;
        height: 22px;
        background: #1a1400;
        color: #FFD700;
        border-radius: 50%;
        font-size: 12px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Name below the image, dark on gold */
    .emirate-card-name {
        font-size: 20px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        line-height: 1.3;
    }

    /* Fallback icon when no image */
    .emirate-card-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: clamp(120px, 26vh, 240px);
        border-radius: 12px;
        background: linear-gradient(135deg, #1a1400, #2a2000);
    }

    /* "Select" hint on hover */
    .emirate-card-hover-hint {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 3;
        background: rgba(26, 20, 0, 0.88);
        color: #FFD700;
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

    /* Short viewports: compress header chrome so the 2x2 grid still fits */
    @media (max-height: 780px) {
        .emirate-modal {
            padding: 20px 24px 20px;
        }
        .emirate-logo {
            height: 30px;
        }
        .emirate-title {
            font-size: 21px;
            margin: 0 0 14px;
        }
        .emirate-divider {
            margin: 0 auto 12px;
        }
        .emirate-cards-grid {
            gap: 14px;
        }
        .emirate-card {
            padding: 10px 10px 14px;
            gap: 8px;
        }
        .emirate-card-name {
            font-size: 17px;
        }
    }

    @media (max-height: 620px) {
        .emirate-modal {
            padding: 14px 18px 16px;
        }
        .emirate-logo {
            height: 24px;
        }
        .emirate-title {
            font-size: 17px;
            margin: 0 0 10px;
        }
        .emirate-cards-grid {
            gap: 10px;
        }
        .emirate-card {
            padding: 8px 8px 10px;
            gap: 6px;
        }
        .emirate-card-name {
            font-size: 14px;
        }
    }

    /* Tablet: keep 2x2, tighten spacing */
    @media (max-width: 860px) {
        .emirate-cards-grid {
            gap: 14px;
        }
    }

    @media (max-width: 640px) {
        .emirate-modal {
            padding: 18px 14px 16px;
            border-radius: 16px;
        }
        .emirate-title {
            font-size: 18px;
            margin-bottom: 16px;
        }
        .emirate-logo {
            height: 32px;
        }
        .emirate-cards-grid {
            gap: 12px;
        }
        .emirate-card .emirate-flag-img,
        .emirate-card-icon {
            height: 130px;
        }
        .emirate-card-name {
            font-size: 16px;
            letter-spacing: 0.8px;
        }
    }

    /* Small mobile: single column, matching the original */
    @media (max-width: 575.98px) {
        .emirate-cards-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .emirate-card .emirate-flag-img,
        .emirate-card-icon {
            height: 150px;
        }
        .emirate-card-name {
            font-size: 18px;
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
