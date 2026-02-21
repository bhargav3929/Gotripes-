<!-- SEARCH MODAL -->
<div class="gt-search-modal" id="searchModal">
    <div class="gt-search-backdrop" id="searchBackdrop"></div>
    <div class="gt-search-container">
        <button class="gt-search-close" id="searchClose" aria-label="Close Search">
            <i class="bi bi-x"></i>
        </button>
        
        <div class="gt-search-header">
            <i class="bi bi-search gt-search-icon-lg"></i>
            <input type="text" class="gt-search-input" id="searchInput" 
                   placeholder="Search destinations, packages, services…" 
                   autocomplete="off">
            <div id="searchLoader" class="gt-search-loader-inline" style="display: none;">
                <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
            </div>
        </div>
        
        <div class="gt-search-results-wrapper">
            <div class="gt-search-results" id="searchResults">
                <!-- Suggestions or Results will be injected here -->
                <div class="gt-search-suggestions" id="searchSuggestions">
                    <div class="gt-search-category-title">Quick Suggestions</div>
                    <div class="gt-suggestion-pills">
                        <span class="gt-pill" onclick="quickSearch('Dubai')">Dubai</span>
                        <span class="gt-pill" onclick="quickSearch('Visa')">UAE Visa</span>
                        <span class="gt-pill" onclick="quickSearch('Abu Dhabi')">Abu Dhabi</span>
                        <span class="gt-pill" onclick="quickSearch('Desert')">Desert Safari</span>
                        <span class="gt-pill" onclick="quickSearch('Burj Khalifa')">Burj Khalifa</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="gt-search-footer">
            <span><kbd>↑↓</kbd> to navigate</span>
            <span><kbd>↵</kbd> to select</span>
            <span><kbd>ESC</kbd> to close</span>
        </div>
    </div>
</div>

<style>
    /* Premium Glassmorphism Search Modal Styles */
    .gt-search-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
        display: none;
        opacity: 0;
        transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(8px); /* Background blur for the whole modal area */
        -webkit-backdrop-filter: blur(8px);
    }

    .gt-search-modal.active {
        display: flex;
        opacity: 1;
    }

    .gt-search-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6); /* Slightly lighter to show blur */
    }

    .gt-search-container {
        position: relative;
        width: 90%;
        max-width: 800px;
        margin: 80px auto 0; /* Fixed top margin */
        z-index: 10001;
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform: translateY(-20px) scale(0.95);
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .gt-search-modal.active .gt-search-container {
        transform: translateY(0) scale(1);
    }

    /* Glossy Header */
    .gt-search-header {
        background: rgba(30, 30, 30, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        border-left: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 15px 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 
            0 25px 50px -12px rgba(0, 0, 0, 0.5),
            0 0 0 1px rgba(255, 255, 255, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .gt-search-icon-lg {
        font-size: 24px;
        color: rgba(255, 255, 255, 0.6);
    }

    .gt-search-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        font-family: 'Outfit', sans-serif;
        font-size: 20px;
        font-weight: 400;
        color: #FFFFFF;
        padding: 5px 0;
    }

    .gt-search-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .gt-search-close {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .gt-search-close:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    /* Results Wrapper - Glassy too */
    .gt-search-results-wrapper {
        background: rgba(20, 20, 20, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 20px;
        max-height: 600px;
        overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        display: none; /* Hidden by default until content exists */
    }
    
    /* Show wrapper when it has content (script will toggle this) or just keep it mostly transparent */
    .gt-search-results-wrapper:not(:empty) {
        display: block;
    }

    /* Custom Scrollbar */
    .gt-search-results-wrapper::-webkit-scrollbar {
        width: 8px;
    }
    .gt-search-results-wrapper::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
    }
    .gt-search-results-wrapper::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }
    .gt-search-results-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .gt-search-category-title {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.4);
        margin: 20px 0 10px 10px;
    }
    .gt-search-category-title:first-child {
        margin-top: 0;
    }

    .gt-suggestion-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 5px;
    }

    .gt-pill {
        padding: 10px 20px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.8);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .gt-pill:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        color: #fff;
        transform: translateY(-2px);
    }

    .gt-search-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 8px;
        text-decoration: none;
        color: #fff;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        background: transparent;
    }

    .gt-search-item:hover, .gt-search-item.selected {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .gt-search-item-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 215, 0, 0.1); /* Gold tint for branding */
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFD700;
        font-size: 18px;
        flex-shrink: 0;
    }

    .gt-search-item-title {
        font-size: 16px;
        font-weight: 500;
        color: #fff;
        margin-bottom: 4px;
    }

    .gt-search-item-desc {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.5);
        margin: 0;
        line-height: 1.4;
    }

    .gt-search-footer {
        display: flex;
        justify-content: center;
        gap: 25px;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.3);
        margin-top: 10px;
    }

    .gt-search-footer kbd {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
        margin-right: 6px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .gt-search-no-results {
        text-align: center;
        padding: 40px 20px;
        color: rgba(255, 255, 255, 0.5);
    }

    @media (max-width: 768px) {
        .gt-search-container { 
            width: 95%; 
            margin-top: 20px;
        }
        .gt-search-header { padding: 12px 15px; }
        .gt-search-input { font-size: 16px; }
        .gt-search-footer { display: none; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('searchModal');
        if (!modal) return;

        const input = document.getElementById('searchInput');
        const results = document.getElementById('searchResults');
        const loader = document.getElementById('searchLoader');
        const closeBtn = document.getElementById('searchClose');
        const backdrop = document.getElementById('searchBackdrop');
        const triggers = document.querySelectorAll('.gt-search-icon, [data-trigger="search"]');

        let debounceTimer;
        let selectedIndex = -1;

        const defaultContent = `
            <div class="gt-search-suggestions">
                <div class="gt-search-category-title">Quick Suggestions</div>
                <div class="gt-suggestion-pills">
                    <span class="gt-pill" onclick="window.quickSearch('Dubai')">Dubai</span>
                    <span class="gt-pill" onclick="window.quickSearch('Visa')">UAE Visa</span>
                    <span class="gt-pill" onclick="window.quickSearch('Abu Dhabi')">Abu Dhabi</span>
                    <span class="gt-pill" onclick="window.quickSearch('Hajj')">Hajj & Umrah</span>
                    <span class="gt-pill" onclick="window.quickSearch('Desert Safari')">Desert Safari</span>
                </div>
            </div>
        `;

        window.quickSearch = function(text) {
            input.value = text;
            input.dispatchEvent(new Event('input'));
        };

        function openSearch() {
            modal.classList.add('active');
            setTimeout(() => input.focus(), 150);
        }

        function closeSearch() {
            modal.classList.remove('active');
            input.value = '';
            results.innerHTML = defaultContent;
            selectedIndex = -1;
        }

        triggers.forEach(t => t.addEventListener('click', e => {
            e.preventDefault();
            openSearch();
        }));

        closeBtn.addEventListener('click', closeSearch);
        backdrop.addEventListener('click', closeSearch);

        input.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(debounceTimer);
            selectedIndex = -1;

            if (!query) {
                results.innerHTML = defaultContent;
                return;
            }

            if (query.length < 2) return;

            loader.style.display = 'block';
            debounceTimer = setTimeout(() => {
                fetch(`/api/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        loader.style.display = 'none';
                        renderResults(data, query);
                    })
                    .catch(() => {
                        loader.style.display = 'none';
                        results.innerHTML = '<div class="gt-search-no-results"><i class="bi bi-exclamation-triangle"></i><p>Search failed. Try again.</p></div>';
                    });
            }, 300);
        });

        function renderResults(data, query) {
            if (data.total === 0) {
                results.innerHTML = `
                    <div class="gt-search-no-results">
                        <i class="bi bi-search" style="font-size: 32px; color: rgba(255,255,255,0.2); margin-bottom: 15px; display: block;"></i>
                        <p style="color:#fff; font-size:16px; font-weight:500; margin-bottom: 5px;">No results found</p>
                        <p style="color:rgba(255,255,255,0.5); font-size: 14px;">We couldn't find anything for "${query}"</p>
                    </div>
                `;
                return;
            }

            let html = '';
            // Display order: Countries -> Emirates -> Activities -> Visas -> Pages
            if (data.countries?.length) html += renderGroup('Popular Destinations', data.countries, 'globe-americas');
            if (data.emirates?.length) html += renderGroup('UAE Emirates', data.emirates, 'geo-alt');
            if (data.activities?.length) html += renderGroup('Activities', data.activities, 'ticket-perforated');
            if (data.visas?.length) html += renderGroup('Visa Services', data.visas, 'passport');
            if (data.pages?.length) html += renderGroup('Website Pages', data.pages, 'layout-text-window-reverse');

            results.innerHTML = html;
        }

        function renderGroup(title, items, icon) {
            let chunk = `<div class="gt-search-category-title">${title}</div>`;
            items.forEach(item => {
                chunk += `
                    <a href="${item.url}" class="gt-search-item">
                        <div class="gt-search-item-icon"><i class="bi bi-${icon}"></i></div>
                        <div class="gt-search-item-content">
                            <div class="gt-search-item-title">${item.title}</div>
                            ${item.description ? `<p class="gt-search-item-desc">${item.description}</p>` : ''}
                        </div>
                    </a>
                `;
            });
            return chunk;
        }

        // Keyboard Navigation
        input.addEventListener('keydown', e => {
            const items = results.querySelectorAll('.gt-search-item');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % items.length;
                updateSelection(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                updateSelection(items);
            } else if (e.key === 'Enter' && selectedIndex > -1) {
                e.preventDefault();
                items[selectedIndex].click();
            } else if (e.key === 'Escape') {
                closeSearch();
            }
        });

        function updateSelection(items) {
            items.forEach((item, idx) => {
                if (idx === selectedIndex) {
                    item.classList.add('selected');
                    item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                } else {
                    item.classList.remove('selected');
                }
            });
        }
    });
</script>
