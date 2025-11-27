@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.user')

@section('title', isset($activeMenu) ? $activeMenu->name : 'Dashboard')
@section('page-title', isset($activeMenu) ? $activeMenu->name : 'Dashboard')
@section('page-subtitle', 'Selamat datang di Portal Dashboard Korlantas')

@section('content')
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
                device="desktop"
                toolbar="bottom"
                hide-tabs
            ></tableau-viz>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
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
</style>
@endsection

@section('tableau-scripts')
@if(isset($embed_url) && !empty($embed_url) && (!isset($failed) || !$failed))
<script type="module" src="{{ $server ?? config('tableau.server') }}/javascripts/api/tableau.embedding.3.latest.min.js"></script>
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
        viz.addEventListener('firstvizsizeknown', hideOverlay);
    }
    
    // Fallback timeout
    setTimeout(hideOverlay, 8000);
</script>
@endif
@endsection