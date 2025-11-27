@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.user')

@section('title', isset($activeMenu) ? $activeMenu->name : 'Dashboard')
@section('page-title', isset($activeMenu) ? $activeMenu->name : 'Dashboard')
@section('page-subtitle', 'Selamat datang di Portal Dashboard Korlantas')

@section('content')
<div class="embed-container">
    <div class="embed-body" id="embedBody">
        @if(isset($failed) && $failed)
            <div class="error-box">
                <h3><i class="fas fa-exclamation-circle"></i> Gagal Memuat Dashboard</h3>
                <p>{{ $error_message ?? 'Terjadi kesalahan saat memuat dashboard.' }}</p>
                <p style="margin-top: 10px; font-size: 12px;">Pastikan IP server sudah terdaftar sebagai Trusted Host di Tableau Server.</p>
                @if(isset($embed_url))
                <p style="margin-top: 10px; font-size: 11px; color: #94a3b8;">URL: {{ $embed_url }}</p>
                @endif
            </div>
        @elseif(!isset($embed_url) || empty($embed_url))
            <div class="error-box">
                <h3><i class="fas fa-info-circle"></i> Belum Ada Dashboard</h3>
                <p>Silakan tambahkan menu dashboard melalui panel admin.</p>
            </div>
        @else
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner"></div>
                <p>Memuat dashboard...</p>
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

@section('tableau-scripts')
@if(isset($embed_url) && !empty($embed_url) && (!isset($failed) || !$failed))
<script type="module" src="{{ $server ?? config('tableau.server') }}/javascripts/api/tableau.embedding.3.latest.min.js"></script>
<script type="module">
    const viz = document.getElementById('tableauViz');
    if (viz) {
        viz.addEventListener('firstinteractive', function() {
            document.getElementById('loadingOverlay')?.remove();
        });
        viz.addEventListener('firstvizsizeknown', function() {
            document.getElementById('loadingOverlay')?.remove();
        });
    }
</script>
@endif
@endsection
