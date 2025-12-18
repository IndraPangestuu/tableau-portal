{{-- Search Modal Component --}}
<div id="searchModal" class="search-modal">
    <div class="search-backdrop" onclick="closeSearch()"></div>
    <div class="search-container">
        <div class="search-header">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari menu dashboard..." autocomplete="off">
            <kbd>ESC</kbd>
        </div>
        <div class="search-results" id="searchResults">
            <div class="search-empty">
                <i class="fas fa-search"></i>
                <p>Ketik untuk mencari menu...</p>
            </div>
        </div>
        <div class="search-footer">
            <span><kbd>↑</kbd><kbd>↓</kbd> Navigasi</span>
            <span><kbd>Enter</kbd> Buka</span>
            <span><kbd>Ctrl+K</kbd> Buka Pencarian</span>
        </div>
    </div>
</div>

<style>
    .search-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        display: none;
        align-items: flex-start;
        justify-content: center;
        padding-top: 15vh;
    }
    
    .search-modal.active {
        display: flex;
    }
    
    .search-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
    }
    
    .search-container {
        position: relative;
        width: 100%;
        max-width: 600px;
        background: linear-gradient(135deg, rgba(20, 20, 40, 0.98), rgba(15, 15, 30, 0.98));
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        animation: searchSlideIn 0.2s ease-out;
    }
    
    @keyframes searchSlideIn {
        from { opacity: 0; transform: translateY(-20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .search-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    
    .search-header i {
        color: var(--accent);
        font-size: 18px;
    }
    
    .search-header input {
        flex: 1;
        background: none;
        border: none;
        color: var(--text);
        font-size: 16px;
        outline: none;
    }
    
    .search-header input::placeholder {
        color: var(--text-muted);
    }
    
    .search-header kbd {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 11px;
        color: var(--text-muted);
    }
    
    .search-results {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .search-results::-webkit-scrollbar { width: 6px; }
    .search-results::-webkit-scrollbar-track { background: transparent; }
    .search-results::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.3); border-radius: 3px; }
    
    .search-empty {
        padding: 40px;
        text-align: center;
        color: var(--text-muted);
    }
    
    .search-empty i {
        font-size: 32px;
        margin-bottom: 12px;
        opacity: 0.3;
    }
    
    .search-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        color: var(--text);
    }
    
    .search-item:hover,
    .search-item.selected {
        background: rgba(99, 102, 241, 0.15);
    }
    
    .search-item-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    
    .search-item-info {
        flex: 1;
    }
    
    .search-item-name {
        font-weight: 500;
        margin-bottom: 2px;
    }
    
    .search-item-path {
        font-size: 12px;
        color: var(--text-muted);
    }
    
    .search-item-favorite {
        color: #fbbf24;
        font-size: 14px;
    }
    
    .search-footer {
        display: flex;
        gap: 20px;
        padding: 12px 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 12px;
        color: var(--text-muted);
    }
    
    .search-footer kbd {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 10px;
        margin-right: 4px;
    }
    
    .search-loading {
        padding: 20px;
        text-align: center;
        color: var(--text-muted);
    }
    
    @media (max-width: 768px) {
        .search-modal { padding: 10px; padding-top: 10vh; }
        .search-footer { display: none; }
    }
</style>

<script>
    let searchTimeout = null;
    let selectedIndex = -1;
    let searchResults = [];
    
    function openSearch() {
        document.getElementById('searchModal').classList.add('active');
        document.getElementById('searchInput').focus();
        document.body.style.overflow = 'hidden';
    }
    
    function closeSearch() {
        document.getElementById('searchModal').classList.remove('active');
        document.getElementById('searchInput').value = '';
        document.getElementById('searchResults').innerHTML = '<div class="search-empty"><i class="fas fa-search"></i><p>Ketik untuk mencari menu...</p></div>';
        document.body.style.overflow = '';
        selectedIndex = -1;
        searchResults = [];
    }
    
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        if (searchTimeout) clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            document.getElementById('searchResults').innerHTML = '<div class="search-empty"><i class="fas fa-search"></i><p>Ketik minimal 2 karakter...</p></div>';
            return;
        }
        
        document.getElementById('searchResults').innerHTML = '<div class="search-loading"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>';
        
        searchTimeout = setTimeout(() => {
            fetch(`{{ route('search.menus') }}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    searchResults = data.results;
                    renderSearchResults();
                })
                .catch(() => {
                    document.getElementById('searchResults').innerHTML = '<div class="search-empty"><i class="fas fa-exclamation-circle"></i><p>Gagal mencari</p></div>';
                });
        }, 300);
    });
    
    function renderSearchResults() {
        const container = document.getElementById('searchResults');
        
        if (searchResults.length === 0) {
            container.innerHTML = '<div class="search-empty"><i class="fas fa-folder-open"></i><p>Tidak ada hasil ditemukan</p></div>';
            return;
        }
        
        container.innerHTML = searchResults.map((item, index) => `
            <a href="${item.url}" class="search-item ${index === selectedIndex ? 'selected' : ''}">
                <div class="search-item-icon"><i class="${item.icon}"></i></div>
                <div class="search-item-info">
                    <div class="search-item-name">${item.name}</div>
                </div>
                ${item.is_favorite ? '<i class="fas fa-star search-item-favorite"></i>' : ''}
            </a>
        `).join('');
    }
    
    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, searchResults.length - 1);
            renderSearchResults();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, 0);
            renderSearchResults();
        } else if (e.key === 'Enter' && selectedIndex >= 0 && searchResults[selectedIndex]) {
            window.location.href = searchResults[selectedIndex].url;
        } else if (e.key === 'Escape') {
            closeSearch();
        }
    });
    
    // Keyboard shortcut Ctrl+K
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openSearch();
        }
        if (e.key === 'Escape' && document.getElementById('searchModal').classList.contains('active')) {
            closeSearch();
        }
    });
</script>
