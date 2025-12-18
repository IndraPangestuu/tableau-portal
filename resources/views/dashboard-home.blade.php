@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.user')

@section('title', isset($activeMenu) ? $activeMenu->name : 'Dashboard')
@section('page-title', isset($activeMenu) ? $activeMenu->name : 'Dashboard')
@section('page-subtitle', 'Selamat datang di Portal Dashboard Korlantas')

@section('content')
@if(isset($activeMenu) && $activeMenu)
<div class="dashboard-toolbar">
    <div class="toolbar-left">
        <span class="toolbar-title"><i class="{{ $activeMenu->icon ?? 'fas fa-chart-bar' }}"></i> {{ $activeMenu->name }}</span>
    </div>
    <div class="toolbar-right">
        <button class="toolbar-btn" onclick="toggleFavorite({{ $activeMenu->id }})" id="favoriteBtn" title="Tambah ke Favorit">
            <i class="fa{{ isset($isFavorite) && $isFavorite ? 's' : 'r' }} fa-star"></i>
        </button>
        <button class="toolbar-btn" onclick="exportToPDF()" title="Export ke PDF">
            <i class="fas fa-file-pdf"></i>
        </button>
        @if(isset($appSettings) && $appSettings['enable_fullscreen'])
        <button class="toolbar-btn" onclick="toggleFullscreen()" title="Fullscreen (F11)">
            <i class="fas fa-expand" id="fullscreenIcon"></i>
        </button>
        @endif
        <button class="toolbar-btn" onclick="refreshDashboard()" title="Refresh">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>
@endif
<div class="embed-container">
    <div class="embed-body" id="embedBody">
        @if(isset($failed) && $failed)
            <div class="error-box">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Gagal Memuat Dashboard</h3>
                <p>{{ $error_message ?? 'Terjadi kesalahan saat memuat dashboard.' }}</p>
                <p class="error-hint">Pastikan IP server sudah terdaftar sebagai Trusted Host di Tableau Server.</p>
                @if(isset($embed_url))
                <p class="error-url">URL: {{ $embed_url }}</p>
                @endif
                <button class="btn-retry" onclick="location.reload()">
                    <i class="fas fa-redo"></i> Coba Lagi
                </button>
            </div>
        @elseif(!isset($embed_url) || empty($embed_url))
            <div class="empty-dashboard">
                <div class="empty-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3>Belum Ada Dashboard</h3>
                <p>Silakan tambahkan menu dashboard melalui panel admin.</p>
            </div>
        @else
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loader">
                    <div class="loader-ring"></div>
                    <div class="loader-ring"></div>
                    <div class="loader-ring"></div>
                    <div class="loader-core"></div>
                </div>
                <p class="loading-text">Memuat dashboard<span class="dots"></span></p>
                <div class="loading-progress">
                    <div class="progress-bar"></div>
                </div>
            </div>
            <tableau-viz
                id="tableauViz"
                src="{{ $embed_url }}"
                toolbar="hidden"
                hide-tabs
                device="default"
                width="100%"
                height="100%"
            ></tableau-viz>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    .dashboard-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        background: rgba(15, 15, 30, 0.6);
        border-bottom: 1px solid var(--border);
    }
    .toolbar-left { display: flex; align-items: center; gap: 12px; }
    .toolbar-title { font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; }
    .toolbar-title i { color: var(--accent); }
    .toolbar-right { display: flex; align-items: center; gap: 8px; }
    .toolbar-btn {
        width: 36px; height: 36px; border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border);
        color: var(--text-muted);
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .toolbar-btn:hover { background: rgba(99, 102, 241, 0.2); color: var(--accent); border-color: rgba(99, 102, 241, 0.3); }
    .toolbar-btn.active { color: #fbbf24; }
    .toolbar-btn.active i { color: #fbbf24; }
    
    .embed-container { height: calc(100vh - 125px) !important; }
    @media (max-width: 768px) {
        .dashboard-toolbar { padding: 8px 12px; }
        .toolbar-title { font-size: 12px; }
        .toolbar-btn { width: 32px; height: 32px; }
        .embed-container { height: calc(100vh - 105px) !important; }
    }
    
    .error-box {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        min-height: 500px; padding: 48px; text-align: center;
    }
    .error-icon {
        width: 100px; height: 100px; border-radius: 50%;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.05));
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 24px; animation: errorPulse 2s ease-in-out infinite;
    }
    .error-icon i { font-size: 42px; color: #f87171; }
    @keyframes errorPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
    .error-box h3 { font-size: 24px; font-weight: 700; margin-bottom: 12px; color: #fff; }
    .error-box p { color: #94a3b8; margin-bottom: 8px; }
    .error-hint { font-size: 13px; color: #64748b; }
    .error-url { font-size: 11px; color: #475569; margin-top: 16px; word-break: break-all; }
    .btn-retry {
        margin-top: 24px; padding: 14px 28px; background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff; border: none; border-radius: 12px; font-size: 14px; font-weight: 600;
        cursor: pointer; display: flex; align-items: center; gap: 10px;
        transition: all 0.3s; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
    }
    .btn-retry:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4); }
    
    .empty-dashboard {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        min-height: 500px; padding: 48px; text-align: center;
    }
    .empty-icon {
        width: 120px; height: 120px; border-radius: 24px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(34, 211, 238, 0.1));
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 28px; animation: emptyFloat 3s ease-in-out infinite;
    }
    .empty-icon i { font-size: 48px; background: linear-gradient(135deg, #6366f1, #22d3ee); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    @keyframes emptyFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .empty-dashboard h3 { font-size: 24px; font-weight: 700; margin-bottom: 12px; color: #fff; }
    .empty-dashboard p { color: #94a3b8; }
    
    .loading-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(15, 15, 30, 0.98), rgba(20, 20, 40, 0.98));
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 28px;
        transition: opacity 0.5s ease-out;
    }
    
    .loader { position: relative; width: 100px; height: 100px; }
    .loader-ring {
        position: absolute; width: 100%; height: 100%;
        border: 3px solid transparent; border-radius: 50%;
    }
    .loader-ring:nth-child(1) { border-top-color: #22d3ee; animation: loaderSpin 1.5s linear infinite; }
    .loader-ring:nth-child(2) { width: 75%; height: 75%; top: 12.5%; left: 12.5%; border-right-color: #6366f1; animation: loaderSpin 2s linear infinite reverse; }
    .loader-ring:nth-child(3) { width: 50%; height: 50%; top: 25%; left: 25%; border-bottom-color: #818cf8; animation: loaderSpin 1s linear infinite; }
    .loader-core {
        position: absolute; width: 20px; height: 20px; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #6366f1, #22d3ee);
        border-radius: 50%; animation: corePulse 1.5s ease-in-out infinite;
    }
    @keyframes loaderSpin { to { transform: rotate(360deg); } }
    @keyframes corePulse { 0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 1; } 50% { transform: translate(-50%, -50%) scale(1.3); opacity: 0.7; } }
    
    .loading-text { color: #94a3b8; font-size: 15px; font-weight: 500; }
    .dots::after { content: ''; animation: dots 1.5s steps(4, end) infinite; }
    @keyframes dots { 0% { content: ''; } 25% { content: '.'; } 50% { content: '..'; } 75% { content: '...'; } }
    
    .loading-progress { width: 200px; height: 4px; background: rgba(255,255,255,0.1); border-radius: 4px; overflow: hidden; }
    .progress-bar { height: 100%; width: 0; background: linear-gradient(90deg, #6366f1, #22d3ee); border-radius: 4px; animation: progressAnim 3s ease-in-out infinite; }
    @keyframes progressAnim { 0% { width: 0; } 50% { width: 70%; } 100% { width: 100%; } }
    
    /* Tableau viz full width */
    #tableauViz {
        width: 100% !important;
        height: 100% !important;
        min-width: 100%;
    }
    .embed-container, .embed-body {
        overflow: hidden;
        background: #0f0f1a;
    }
    /* Hide scrollbar but allow scroll if needed */
    .embed-body::-webkit-scrollbar { display: none; }
    .embed-body { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Force tableau iframe to fill */
    #tableauViz iframe {
        width: 100% !important;
        height: 100% !important;
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        .embed-container {
            height: calc(100vh - 60px) !important;
            margin: 0 !important;
        }
        .embed-body {
            height: 100% !important;
        }
        #tableauViz {
            min-height: calc(100vh - 60px) !important;
        }
        .error-box, .empty-dashboard {
            min-height: 300px;
            padding: 24px;
        }
        .error-box h3, .empty-dashboard h3 { font-size: 18px; }
        .error-icon, .empty-icon { width: 80px; height: 80px; }
        .error-icon i { font-size: 32px; }
        .empty-icon i { font-size: 36px; }
    }
    
    /* Print styles for PDF export */
    @media print {
        body { background: white !important; }
        .bg-animated, .particles, .sidebar, .header, .dashboard-toolbar, .sidebar-overlay, .swipe-indicator { display: none !important; }
        .main-content { margin-left: 0 !important; }
        .embed-container { 
            height: 100vh !important; 
            margin: 0 !important; 
            background: white !important;
            border: none !important;
        }
        .embed-body { height: 100% !important; }
        #tableauViz, #tableauViz iframe { 
            width: 100% !important; 
            height: 100% !important; 
        }
        .loading-overlay { display: none !important; }
    }
</style>
@endsection

@section('tableau-scripts')
@if(isset($embed_url) && !empty($embed_url) && (!isset($failed) || !$failed))
{{-- Load Tableau Embedding API v3 --}}
<script type="module" src="{{ $server }}/javascripts/api/tableau.embedding.3.latest.min.js"></script>
<script type="module">
    const viz = document.getElementById('tableauViz');
    const overlay = document.getElementById('loadingOverlay');
    
    function hideOverlay() {
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => overlay.remove(), 500);
        }
    }
    
    if (viz) {
        viz.addEventListener('firstinteractive', hideOverlay);
        viz.addEventListener('firstvizsizeknown', () => {
            console.log('Tableau viz size known');
        });
    }
    
    setTimeout(hideOverlay, 10000);
</script>
@endif

<script>
    // Toggle Favorite
    function toggleFavorite(menuId) {
        const baseUrl = '{{ url("/") }}';
        fetch(`${baseUrl}/favorites/${menuId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            const btn = document.getElementById('favoriteBtn');
            const icon = btn.querySelector('i');
            if (data.is_favorite) {
                icon.classList.replace('far', 'fas');
                btn.classList.add('active');
                Toast.success('Ditambahkan ke favorit');
            } else {
                icon.classList.replace('fas', 'far');
                btn.classList.remove('active');
                Toast.info('Dihapus dari favorit');
            }
        })
        .catch((err) => {
            console.error('Favorite error:', err);
            Toast.error('Gagal mengubah favorit');
        });
    }
    
    // Toggle Fullscreen
    function toggleFullscreen() {
        const elem = document.documentElement;
        const icon = document.getElementById('fullscreenIcon');
        
        if (!document.fullscreenElement) {
            elem.requestFullscreen().then(() => {
                icon.classList.replace('fa-expand', 'fa-compress');
            }).catch(err => Toast.error('Gagal masuk fullscreen'));
        } else {
            document.exitFullscreen().then(() => {
                icon.classList.replace('fa-compress', 'fa-expand');
            });
        }
    }
    
    // Refresh Dashboard
    function refreshDashboard() {
        const viz = document.getElementById('tableauViz');
        if (viz && viz.refreshDataAsync) {
            viz.refreshDataAsync().then(() => {
                Toast.success('Dashboard di-refresh');
            }).catch(() => {
                location.reload();
            });
        } else {
            location.reload();
        }
    }
    
    // Export to PDF
    function exportToPDF() {
        const viz = document.getElementById('tableauViz');
        
        if (viz && viz.exportPDFAsync) {
            Toast.info('Mempersiapkan PDF...');
            viz.exportPDFAsync().then(() => {
                Toast.success('PDF berhasil di-export');
            }).catch((err) => {
                console.error('Export PDF error:', err);
                // Fallback: use Tableau's built-in export
                exportPDFFallback();
            });
        } else {
            exportPDFFallback();
        }
    }
    
    function exportPDFFallback() {
        // Fallback method using browser print
        Toast.info('Membuka dialog print untuk export PDF...');
        
        // Hide toolbar and sidebar temporarily
        const toolbar = document.querySelector('.dashboard-toolbar');
        const sidebar = document.querySelector('.sidebar');
        const header = document.querySelector('.header');
        
        if (toolbar) toolbar.style.display = 'none';
        if (sidebar) sidebar.style.display = 'none';
        if (header) header.style.display = 'none';
        
        // Trigger print
        setTimeout(() => {
            window.print();
            
            // Restore elements
            setTimeout(() => {
                if (toolbar) toolbar.style.display = '';
                if (sidebar) sidebar.style.display = '';
                if (header) header.style.display = '';
            }, 500);
        }, 300);
    }
    
    // Keyboard shortcut F11 for fullscreen
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F11') {
            e.preventDefault();
            toggleFullscreen();
        }
    });
    
    // Auto refresh if configured
    @if(isset($appSettings) && $appSettings['dashboard_refresh_interval'] > 0)
    setInterval(function() {
        refreshDashboard();
    }, {{ $appSettings['dashboard_refresh_interval'] * 1000 }});
    @endif
    
    // Update favorite button state
    @if(isset($isFavorite) && $isFavorite)
    document.getElementById('favoriteBtn')?.classList.add('active');
    @endif
</script>
@endsection