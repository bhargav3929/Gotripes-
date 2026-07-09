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
    .emirate-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.82);
        backdrop-filter: blur(8px);
        z-index: 11000; /* Must be higher than header (usually 9999) but lower than camera modals if any */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .emirate-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    .emirate-modal {
        background: #0b0b0b;
        border: 1px solid rgba(255, 215, 0, 0.15);
        border-radius: 22px;
        padding: 36px 34px;
        width: 680px;
        max-width: 100%;
        max-height: 92vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.8);
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        font-family: 'Outfit', sans-serif;
        text-align: center;
    }
    .emirate-overlay.active .emirate-modal {
        transform: scale(1);
    }
    .emirate-close-btn {
        position: absolute;
        top: 16px;
        right: 20px;
        background: transparent;
        border: none;
        color: #888;
        font-size: 28px;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        transition: color 0.2s ease;
    }
    .emirate-close-btn:hover {
        color: #fff;
    }
    .emirate-logo {
        display: block;
        height: 68px;
        width: auto;
        margin: 0 auto 20px;
        object-fit: contain;
    }
    .emirate-title {
        color: #FFD700;
        font-size: 25px;
        font-weight: 800;
        margin: 0 0 26px;
        letter-spacing: 0.3px;
        line-height: 1.3;
    }
    .emirate-cards-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .emirate-card {
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
    .emirate-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px -8px rgba(255, 215, 0, 0.5);
        filter: brightness(1.04);
    }
    .emirate-card-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }
    .emirate-card-name {
        font-size: 20px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        line-height: 1.3;
    }

    /* --- Light Theme Support --- */
    html[data-theme="light"] .emirate-overlay {
        background: rgba(248, 249, 250, 0.92);
    }
    html[data-theme="light"] .emirate-modal {
        background: var(--gt-surface);
        border: 1px solid var(--gt-border-strong);
        box-shadow: var(--gt-shadow-lg);
    }
    html[data-theme="light"] .emirate-title {
        color: var(--gt-text);
    }
    html[data-theme="light"] .emirate-close-btn {
        color: var(--gt-text-muted);
    }
    html[data-theme="light"] .emirate-close-btn:hover {
        color: var(--gt-text);
    }
    .emirate-flag-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 12px;
        display: block;
    }

    @media (max-width: 575.98px) {
        .emirate-modal {
            padding: 28px 16px;
            width: 100%;
        }
        .emirate-title { font-size: 21px; }
        .emirate-cards-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .emirate-flag-img {
            height: 150px;
        }
        .emirate-card-name {
            font-size: 18px;
        }
    }
</style>

<div id="emirateSelectorOverlay" class="emirate-overlay">
    <div class="emirate-modal">
        <button type="button" class="emirate-close-btn" id="emirateCloseBtn" aria-label="Close modal">&times;</button>
        <img src="{{ $modalLogo }}" alt="{{ $modalName }}" class="emirate-logo">
        <h2 class="emirate-title">Which Emirates Visa Are You Applying For?</h2>

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
            <button type="button" class="emirate-card" data-emirate="${e.id}">
                <span class="emirate-card-icon">${e.image ? `<img class="emirate-flag-img" src="${e.image}" alt="${e.name}">` : '<i class="bi bi-flag-fill" style="font-size:40px;"></i>'}</span>
                <span class="emirate-card-name">${e.name}</span>
            </button>
        `).join('');

        // Attach Event Listeners to cards
        grid.querySelectorAll('.emirate-card').forEach(card => {
            card.addEventListener('click', function() {
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

        function selectEmirate(emirate) {
            // Sync with hidden form input if present
            if (hiddenInput) {
                hiddenInput.value = emirate;
            }

            // Trigger custom event so parent view can update
            document.dispatchEvent(new CustomEvent('emirateChanged', { detail: emirate }));

            closeEmirateSelector();
        }

        function showEmirateSelector() {
            overlay.classList.add('active');
        }

        function closeEmirateSelector() {
            overlay.classList.remove('active');
        }

        // Expose globally so the main page can trigger it (initial load + "Change" badge)
        window.showEmirateSelector = showEmirateSelector;
    })();
</script>
