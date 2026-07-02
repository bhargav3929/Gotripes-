<!-- EMIRATE SELECTION POPUP MODAL -->
<style>
    .emirate-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
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
        border-radius: 18px;
        padding: 36px 30px;
        width: 480px;
        max-width: 100%;
        position: relative;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.7);
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
    .emirate-title {
        color: #FFD700;
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 10px;
        letter-spacing: 0.5px;
        line-height: 1.3;
    }
    .emirate-subtitle {
        color: #888;
        font-size: 14px;
        margin: 0 0 24px;
        font-weight: 400;
        line-height: 1.5;
    }
    .emirate-cards-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .emirate-card {
        background: #111;
        border: 1px solid #222;
        border-radius: 14px;
        padding: 28px 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
        color: #fff;
        font-family: 'Outfit', sans-serif;
        width: 100%;
    }
    .emirate-card:hover {
        border-color: #FFD700;
        background: #161616;
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(255, 215, 0, 0.1);
    }
    .emirate-card-icon {
        font-size: 36px;
        line-height: 1;
    }
    .emirate-card-name {
        font-size: 15px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* --- Light Theme Support --- */
    html[data-theme="light"] .emirate-overlay {
        background: rgba(248, 249, 250, 0.9);
    }
    html[data-theme="light"] .emirate-modal {
        background: var(--gt-surface);
        border: 1px solid var(--gt-border-strong);
        box-shadow: var(--gt-shadow-lg);
    }
    html[data-theme="light"] .emirate-title {
        color: var(--gt-text);
    }
    html[data-theme="light"] .emirate-subtitle {
        color: var(--gt-text-body);
    }
    html[data-theme="light"] .emirate-card {
        background: var(--gt-surface-2);
        border: 1px solid var(--gt-border-strong);
        color: var(--gt-text);
    }
    html[data-theme="light"] .emirate-card:hover {
        border-color: var(--gt-gold);
        background: var(--gt-surface-3);
        box-shadow: 0 8px 24px rgba(169, 123, 10, 0.12);
    }
    html[data-theme="light"] .emirate-close-btn {
        color: var(--gt-text-muted);
    }
    html[data-theme="light"] .emirate-close-btn:hover {
        color: var(--gt-text);
    }
    .emirate-flag-svg {
        width: 64px;
        height: 44px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        display: block;
    }
    html[data-theme="light"] .emirate-flag-svg {
        border: 1px solid rgba(0, 0, 0, 0.15);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    }
</style>

<div id="emirateSelectorOverlay" class="emirate-overlay">
    <div class="emirate-modal">
        <button type="button" class="emirate-close-btn" id="emirateCloseBtn" aria-label="Close modal">&times;</button>
        <h2 class="emirate-title">Where are you travelling in the UAE?</h2>
        <p class="emirate-subtitle">Please select the emirate for your visa application.</p>
        
        <div class="emirate-cards-grid" id="emirateGrid">
            <!-- Dynamic selection cards rendered in JS -->
        </div>
    </div>
</div>

<script>
    (function() {
        const AVAILABLE_EMIRATES = @json($activeEmirates->map(fn($e) => [
            'id' => $e->emiratesName,
            'name' => $e->emiratesName,
            'icon' => $e->emiratesImage ? '<img class="emirate-flag-svg" src="' . asset($e->emiratesImage) . '" alt="' . $e->emiratesName . '">' : '<i class="fas fa-flag fa-2x"></i>'
        ])->toArray());

        const overlay = document.getElementById('emirateSelectorOverlay');
        const grid = document.getElementById('emirateGrid');
        const closeBtn = document.getElementById('emirateCloseBtn');
        const hiddenInput = document.getElementById('selectedEmirate');

        if (!overlay || !grid) return;

        // Render Cards dynamically
        grid.innerHTML = AVAILABLE_EMIRATES.map(e => `
            <button type="button" class="emirate-card" data-emirate="${e.id}">
                <span class="emirate-card-icon">${e.icon}</span>
                <span class="emirate-card-name">${e.name}</span>
            </button>
        `).join('');

        // Attach Event Listeners to cards
        grid.querySelectorAll('.emirate-card').forEach(card => {
            card.addEventListener('click', function() {
                const emirate = this.getAttribute('data-emirate');
                selectEmirate(emirate);
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
            localStorage.setItem('selected_emirate', emirate);
            
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

        // Initialize state on page load
        function initEmirateSelector() {
            const saved = localStorage.getItem('selected_emirate');
            if (saved) {
                if (hiddenInput) hiddenInput.value = saved;
                document.dispatchEvent(new CustomEvent('emirateChanged', { detail: saved }));
            } else {
                // Auto-show modal if selection is missing
                setTimeout(showEmirateSelector, 500);
            }
        }

        // Expose globally so main page can trigger it via "Change" button
        window.showEmirateSelector = showEmirateSelector;

        // Let page load settle before initializing selector
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initEmirateSelector);
        } else {
            initEmirateSelector();
        }
    })();
</script>
