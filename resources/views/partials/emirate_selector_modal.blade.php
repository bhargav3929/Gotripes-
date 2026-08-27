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
        border-radius: 22px;
        padding: 36px 34px 32px;
        width: min(720px, 94vw);
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
        margin-bottom: 20px;
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
        height: 118px;
        width: auto;
        object-fit: contain;
        flex-shrink: 0;
    }

    .emirate-title {
        color: #FFD700;
        font-size: 24px;
        font-weight: 800;
        margin: 0 0 12px;
        letter-spacing: 0.2px;
        line-height: 1.35;
        text-shadow: 0 0 30px rgba(255,215,0,0.2);
    }

    /* Divider */
    .emirate-divider {
        width: 56px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #FFD700, transparent);
        margin: 0 auto 26px;
        border-radius: 2px;
    }

    /* =============================================
       GRID — ONE unified gold box, split into selectable
       sections (not separate floating cards).
    ============================================= */
    .emirate-cards-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid rgba(255, 215, 0, 0.5);
        box-shadow: 0 14px 34px -10px rgba(255, 215, 0, 0.35);
    }
    /* A trailing odd-one-out section (3 emirates, 5, etc.) becomes a full-
       width row of its own within the same box, divided by a top border,
       instead of stranding a smaller floating card off to one side. */
    .emirate-cards-grid > .emirate-card:last-child:nth-child(odd) {
        grid-column: 1 / -1;
        border-top: 1px solid rgba(26, 20, 0, 0.15);
    }
    /* Divider between side-by-side sections in the same row. */
    .emirate-cards-grid > .emirate-card:nth-child(odd):not(:last-child) {
        border-right: 1px solid rgba(26, 20, 0, 0.15);
    }

    /* =============================================
       SECTION — one selectable half of the unified box
    ============================================= */
    .emirate-card {
        position: relative;
        background: transparent;
        border: none;
        border-radius: 0;
        padding: 18px 18px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: background-color 0.2s ease, filter 0.2s ease;
        color: #1a1400;
        font-family: 'Outfit', sans-serif;
        width: 100%;
    }

    /* Image sits inside the card, above the name.
       Height tracks viewport height so all 4 cards fit without scrolling. */
    .emirate-card .emirate-flag-img {
        width: 100%;
        height: clamp(110px, 20vh, 190px);
        object-fit: cover;
        border-radius: 11px;
        display: block;
    }

    .emirate-card:hover {
        background: rgba(26, 20, 0, 0.06);
    }

    /* Selected state — an inset ring instead of a floating shadow, since
       these are sections of one box, not independent cards. */
    .emirate-card.selected,
    .emirate-card[aria-pressed="true"] {
        background: rgba(26, 20, 0, 0.08);
        box-shadow: inset 0 0 0 3px rgba(26, 20, 0, 0.55);
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
        height: clamp(110px, 20vh, 190px);
        border-radius: 11px;
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
            height: 84px;
        }
        .emirate-title {
            font-size: 21px;
            margin: 0 0 14px;
        }
        .emirate-divider {
            margin: 0 auto 12px;
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
            height: 66px;
        }
        .emirate-title {
            font-size: 17px;
            margin: 0 0 10px;
        }
        .emirate-card {
            padding: 8px 8px 10px;
            gap: 6px;
        }
        .emirate-card-name {
            font-size: 14px;
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
            height: 88px;
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

    /* Small mobile: single column, matching the original. Dividers switch
       from vertical (side-by-side) to horizontal (stacked) accordingly. */
    @media (max-width: 575.98px) {
        .emirate-cards-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }
        .emirate-cards-grid > .emirate-card:nth-child(odd):not(:last-child) {
            border-right: none;
        }
        .emirate-cards-grid > .emirate-card:last-child:nth-child(odd) {
            border-top: none;
        }
        .emirate-cards-grid > .emirate-card:not(:last-child) {
            border-bottom: 1px solid rgba(26, 20, 0, 0.15);
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
