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
        <button class="toolbar-btn" onclick="previewPDF()" title="Preview & Export PDF">
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

<!-- PDF Preview Modal -->
<div id="pdfPreviewModal" class="pdf-modal">
    <div class="pdf-modal-content">
        <div class="pdf-modal-header">
            <h3>Preview PDF - {{ $activeMenu->name ?? 'Dashboard' }}</h3>
            <button class="pdf-modal-close" onclick="closePDFPreview()">&times;</button>
        </div>
        <div class="pdf-modal-body">
            <div class="pdf-preview-container">
                <div class="pdf-preview-header">
                    <div class="pdf-preview-title">
                        <i class="{{ $activeMenu->icon ?? 'fas fa-chart-bar' }}"></i>
                        <span>{{ $activeMenu->name ?? 'Dashboard' }}</span>
                    </div>
                    <div class="pdf-preview-meta">
                        <span class="pdf-date">{{ date('d/m/Y H:i') }}</span>
                        <span class="pdf-user">{{ Auth::user()->name ?? 'User' }}</span>
                    </div>
                </div>
                <div class="pdf-preview-dashboard" id="pdfPreviewContent">
                    <div class="pdf-preview-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Menyiapkan preview...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="pdf-modal-footer">
            <button class="pdf-btn pdf-btn-secondary" onclick="closePDFPreview()">
                <i class="fas fa-times"></i> Batal
            </button>
            <button class="pdf-btn pdf-btn-primary" onclick="downloadPDF()">
                <i class="fas fa-download"></i> Download PDF
            </button>
            <button class="pdf-btn pdf-btn-print" onclick="printPDF()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
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
    
    /* PDF Preview Modal Styles */
    .pdf-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
        animation: fadeIn 0.3s ease;
    }
    
    .pdf-modal-content {
        background: linear-gradient(135deg, rgba(20, 20, 40, 0.95), rgba(15, 15, 30, 0.98));
        border: 1px solid var(--border);
        border-radius: 16px;
        margin: 2% auto;
        width: 90%;
        max-width: 900px;
        height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        animation: slideIn 0.3s ease;
    }
    
    .pdf-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
    }
    
    .pdf-modal-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin: 0;
    }
    
    .pdf-modal-close {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 24px;
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }
    
    .pdf-modal-close:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }
    
    .pdf-modal-body {
        flex: 1;
        padding: 24px;
        overflow: auto;
    }
    
    .pdf-preview-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        min-height: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .pdf-preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .pdf-preview-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
    }
    
    .pdf-preview-title i {
        color: #6366f1;
        font-size: 24px;
    }
    
    .pdf-preview-meta {
        text-align: right;
        font-size: 12px;
        color: #6b7280;
    }
    
    .pdf-date, .pdf-user {
        display: block;
        margin-bottom: 4px;
    }
    
    .pdf-preview-dashboard {
        min-height: 400px;
        background: #f9fafb;
        border-radius: 8px;
        padding: 20px;
        position: relative;
    }
    
    .pdf-preview-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 300px;
        color: #6b7280;
    }
    
    .pdf-preview-loading i {
        font-size: 48px;
        margin-bottom: 16px;
        color: #6366f1;
    }
    
    .pdf-preview-loading p {
        font-size: 16px;
        margin: 0;
    }
    
    .pdf-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px 24px;
        border-top: 1px solid var(--border);
    }
    
    .pdf-btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
    }
    
    .pdf-btn-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: white;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }
    
    .pdf-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    }
    
    .pdf-btn-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }
    
    .pdf-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    
    .pdf-btn-print {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    
    .pdf-btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
        .pdf-modal-content {
            margin: 0;
            width: 100%;
            height: 100%;
            border-radius: 0;
        }
        
        .pdf-modal-header, .pdf-modal-footer {
            padding: 16px;
        }
        
        .pdf-modal-body {
            padding: 16px;
        }
        
        .pdf-preview-container {
            padding: 20px;
        }
    }
    
    /* Enhanced PDF Preview Styles */
    .pdf-preview-enhanced {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        border-radius: 12px;
        padding: 30px;
        min-height: 400px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .preview-header-section {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .preview-icon-large {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
    }
    
    .preview-title-section h3 {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 8px 0;
    }
    
    .preview-subtitle {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }
    
    .preview-dashboard-mockup {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    
    .mockup-filters {
        display: flex;
        gap: 12px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    
    .filter-item {
        background: #f3f4f6;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }
    
    .mockup-charts {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .chart-container {
        background: #f9fafb;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e5e7eb;
    }
    
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .chart-header h4 {
        font-size: 14px;
        color: #374151;
        margin: 0;
    }
    
    .chart-value {
        font-size: 20px;
        font-weight: 700;
        color: #6366f1;
    }
    
    .chart-trend {
        font-size: 14px;
        color: #10b981;
    }
    
    .chart-visual {
        display: flex;
        align-items: end;
        gap: 8px;
        height: 80px;
    }
    
    .bar {
        flex: 1;
        background: linear-gradient(135deg, #6366f1, #818cf8);
        border-radius: 4px 4px 0 0;
        min-height: 20px;
    }
    
    .chart-line {
        position: relative;
        height: 60px;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        border-radius: 4px;
    }
    
    .line-point {
        position: absolute;
        width: 8px;
        height: 8px;
        background: #6366f1;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .preview-info-box {
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        padding: 16px;
        border-radius: 8px;
        margin-top: 20px;
    }
    
    .preview-info-box i {
        color: #3b82f6;
        margin-right: 8px;
    }
    
    .preview-info-box p {
        margin: 0;
        color: #1e40af;
        font-size: 14px;
    }
    
    .preview-metadata {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 25px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px;
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    
    .meta-item i {
        color: #6b7280;
        font-size: 14px;
    }
    
    .meta-item span {
        color: #374151;
        font-size: 13px;
    }
    
    @media (max-width: 768px) {
        .preview-dashboard-mockup {
            padding: 20px;
        }
        
        .mockup-charts {
            grid-template-columns: 1fr;
        }
        
        .preview-header-section {
            flex-direction: column;
            text-align: center;
        }
        
        .preview-metadata {
            grid-template-columns: 1fr;
        }
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
    
    // PDF Preview Modal Functions
    function previewPDF() {
        const modal = document.getElementById('pdfPreviewModal');
        const previewContent = document.getElementById('pdfPreviewContent');
        const viz = document.getElementById('tableauViz');
        
        // Show modal
        modal.style.display = 'block';
        
        // Prepare preview content
        previewContent.innerHTML = `
            <div class="pdf-preview-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Menyiapkan preview...</p>
            </div>
        `;
        
        // Try multiple methods to get preview
        setTimeout(() => {
            // Method 1: Try to get iframe content
            const iframe = viz?.querySelector('iframe');
            if (iframe) {
                try {
                    // Try to get canvas or image from iframe
                    const canvas = iframe.contentDocument?.querySelector('canvas');
                    if (canvas) {
                        const dataUrl = canvas.toDataURL('image/png');
                        previewContent.innerHTML = `
                            <div class="pdf-preview-screenshot">
                                <img src="${dataUrl}" alt="Dashboard Preview" style="width: 100%; height: auto; border-radius: 8px;">
                                <div class="pdf-preview-note">
                                    <p><i class="fas fa-info-circle"></i> Ini adalah preview dari dashboard saat ini. File PDF akan berisi data terbaru.</p>
                                </div>
                            </div>
                        `;
                        return;
                    }
                } catch (e) {
                    console.log('Cannot access iframe content due to CORS');
                }
            }
            
            // Method 2: Try Tableau API methods
            if (viz) {
                // Try different Tableau API methods
                const methods = [
                    'getCurrentScreenshotAsync',
                    'exportImageAsync',
                    'getScreenshotAsync',
                    'captureScreenshot'
                ];
                
                for (let method of methods) {
                    if (viz[method] && typeof viz[method] === 'function') {
                        try {
                            viz[method]().then((result) => {
                                if (result) {
                                    previewContent.innerHTML = `
                                        <div class="pdf-preview-screenshot">
                                            <img src="${result}" alt="Dashboard Preview" style="width: 100%; height: auto; border-radius: 8px;">
                                            <div class="pdf-preview-note">
                                                <p><i class="fas fa-info-circle"></i> Ini adalah preview dari dashboard saat ini. File PDF akan berisi data terbaru.</p>
                                            </div>
                                        </div>
                                    `;
                                    return;
                                }
                            }).catch(() => {
                                // Try next method
                            });
                            return;
                        } catch (e) {
                            continue;
                        }
                    }
                }
            }
            
            // Method 3: Create visual preview from current state
            showEnhancedPDFPreview();
        }, 1000);
    }
    
    function showEnhancedPDFPreview() {
        const previewContent = document.getElementById('pdfPreviewContent');
        const menuName = document.querySelector('.toolbar-title')?.textContent || 'Dashboard';
        const menuIcon = document.querySelector('.toolbar-title i')?.className || 'fas fa-chart-line';
        
        previewContent.innerHTML = `
            <div class="pdf-preview-enhanced">
                <div class="preview-header-section">
                    <div class="preview-icon-large">
                        <i class="${menuIcon}"></i>
                    </div>
                    <div class="preview-title-section">
                        <h3>${menuName}</h3>
                        <p class="preview-subtitle">Dashboard Preview</p>
                    </div>
                </div>
                
                <div class="preview-dashboard-mockup">
                    <div class="mockup-filters">
                        <div class="filter-item">Filter: Periode</div>
                        <div class="filter-item">Filter: Wilayah</div>
                        <div class="filter-item">Filter: Kategori</div>
                    </div>
                    
                    <div class="mockup-charts">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h4>Statistik Utama</h4>
                                <span class="chart-value">1,234</span>
                            </div>
                            <div class="chart-visual">
                                <div class="bar" style="height: 70%;"></div>
                                <div class="bar" style="height: 85%;"></div>
                                <div class="bar" style="height: 60%;"></div>
                                <div class="bar" style="height: 90%;"></div>
                            </div>
                        </div>
                        
                        <div class="chart-container">
                            <div class="chart-header">
                                <h4>Tren Data</h4>
                                <span class="chart-trend">↗️ +15%</span>
                            </div>
                            <div class="chart-line">
                                <div class="line-point" style="left: 0%; bottom: 60%;"></div>
                                <div class="line-point" style="left: 25%; bottom: 75%;"></div>
                                <div class="line-point" style="left: 50%; bottom: 45%;"></div>
                                <div class="line-point" style="left: 75%; bottom: 85%;"></div>
                                <div class="line-point" style="left: 100%; bottom: 70%;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="preview-info-box">
                        <i class="fas fa-info-circle"></i>
                        <p>Preview ini menampilkan tampilan umum dashboard. File PDF akan berisi data aktual dari Tableau.</p>
                    </div>
                </div>
                
                <div class="preview-metadata">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Generated: ${new Date().toLocaleString('id-ID')}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>User: {{ Auth::user()->name ?? 'User' }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-file-pdf"></i>
                        <span>Format: PDF High Quality</span>
                    </div>
                </div>
            </div>
        `;
    }
    
    function showPDFPlaceholder() {
        const previewContent = document.getElementById('pdfPreviewContent');
        const menuName = document.querySelector('.toolbar-title')?.textContent || 'Dashboard';
        
        previewContent.innerHTML = `
            <div class="pdf-preview-placeholder">
                <div class="placeholder-header">
                    <i class="fas fa-chart-line"></i>
                    <h4>${menuName}</h4>
                </div>
                <div class="placeholder-content">
                    <div class="placeholder-chart">
                        <div class="chart-bar" style="height: 60%;"></div>
                        <div class="chart-bar" style="height: 80%;"></div>
                        <div class="chart-bar" style="height: 45%;"></div>
                        <div class="chart-bar" style="height: 90%;"></div>
                        <div class="chart-bar" style="height: 70%;"></div>
                    </div>
                    <div class="placeholder-info">
                        <p><i class="fas fa-info-circle"></i> Preview akan menampilkan tampilan dashboard saat ini</p>
                        <p><i class="fas fa-download"></i> File PDF akan berisi data terbaru dari Tableau</p>
                        <p><i class="fas fa-file-alt"></i> Format: PDF dengan kualitas tinggi</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    function closePDFPreview() {
        document.getElementById('pdfPreviewModal').style.display = 'none';
    }
    
    // Download PDF
    function downloadPDF() {
        const viz = document.getElementById('tableauViz');
        const menuName = document.querySelector('.toolbar-title')?.textContent || 'dashboard';
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        const filename = `dashboard-${menuName.replace(/[^a-zA-Z0-9]/g, '-')}-${timestamp}.pdf`;
        
        if (viz && viz.exportPDFAsync) {
            Toast.info('Mempersiapkan PDF untuk download...');
            viz.exportPDFAsync().then((pdfBlob) => {
                if (pdfBlob) {
                    // Create download link
                    const url = URL.createObjectURL(pdfBlob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                    
                    Toast.success('PDF berhasil di-download!');
                    closePDFPreview();
                } else {
                    downloadPDFFallback(filename);
                }
            }).catch((err) => {
                console.error('Export PDF error:', err);
                downloadPDFFallback(filename);
            });
        } else {
            downloadPDFFallback(filename);
        }
    }
    
    function downloadPDFFallback(filename) {
        // Fallback: use browser print to generate PDF
        Toast.info('Menggunakan metode alternatif untuk generate PDF...');
        
        // Hide UI elements temporarily
        const toolbar = document.querySelector('.dashboard-toolbar');
        const sidebar = document.querySelector('.sidebar');
        const header = document.querySelector('.header');
        const originalTitle = document.title;
        
        // Set print title
        document.title = filename.replace('.pdf', '');
        
        // Hide elements
        if (toolbar) toolbar.style.display = 'none';
        if (sidebar) sidebar.style.display = 'none';
        if (header) header.style.display = 'none';
        
        // Trigger print with custom settings
        setTimeout(() => {
            window.print();
            
            // Restore elements
            setTimeout(() => {
                if (toolbar) toolbar.style.display = '';
                if (sidebar) sidebar.style.display = '';
                if (header) header.style.display = '';
                document.title = originalTitle;
                closePDFPreview();
            }, 500);
        }, 300);
    }
    
    // Print PDF
    function printPDF() {
        closePDFPreview();
        setTimeout(() => {
            window.print();
        }, 100);
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('pdfPreviewModal');
        if (event.target == modal) {
            closePDFPreview();
        }
    }
    
    // Keyboard shortcut ESC to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePDFPreview();
        }
    });
</script>
@endsection