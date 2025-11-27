@extends('layouts.admin')

@section('title', 'Tambah Menu')
@section('page-title', 'Tambah Menu Dashboard')
@section('page-subtitle', 'Pilih dashboard dari Tableau Server')

@section('styles')
<style>
    .icon-preview { display: flex; align-items: center; gap: 15px; margin-top: 10px; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px; }
    .icon-preview i { font-size: 24px; color: #64b5f6; }
    .icon-preview span { font-size: 13px; color: #94a3b8; }
    .icon-list { display: grid; grid-template-columns: repeat(8, 1fr); gap: 8px; margin-top: 10px; max-height: 150px; overflow-y: auto; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 8px; }
    .icon-item { padding: 10px; text-align: center; border-radius: 6px; cursor: pointer; transition: all 0.2s; }
    .icon-item:hover, .icon-item.selected { background: rgba(30, 136, 229, 0.3); }
    .icon-item i { font-size: 18px; }
    
    .btn-success { background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
    .btn-success:hover { background: rgba(34, 197, 94, 0.3); }
    
    .views-container { margin-top: 15px; }
    .views-list { max-height: 300px; overflow-y: auto; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; }
    .view-item { padding: 12px 15px; border-bottom: 1px solid rgba(255,255,255,0.05); cursor: pointer; transition: all 0.2s; display: flex; justify-content: space-between; align-items: center; }
    .view-item:last-child { border-bottom: none; }
    .view-item:hover { background: rgba(30, 136, 229, 0.1); }
    .view-item.selected { background: rgba(30, 136, 229, 0.2); border-left: 3px solid #1e88e5; }
    .view-name { font-weight: 500; font-size: 14px; }
    .view-workbook { font-size: 11px; color: #64748b; margin-top: 2px; }
    .view-path { font-size: 11px; color: #94a3b8; font-family: monospace; }
    
    .loading { text-align: center; padding: 30px; color: #94a3b8; }
    .loading i { font-size: 24px; animation: spin 1s linear infinite; margin-bottom: 10px; display: block; }
    @keyframes spin { to { transform: rotate(360deg); } }
    
    .error-msg { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 15px; border-radius: 8px; margin-top: 15px; }
    .info-msg { background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; padding: 15px; border-radius: 8px; margin-top: 15px; }
    
    .search-box { position: relative; margin-bottom: 10px; }
    .search-box input { width: 100%; padding: 10px 15px 10px 40px; }
    .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b; }
</style>
@endsection

@section('content')
{{-- Card: Pilih Dashboard dari Tableau --}}
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <div>
            <h2 class="card-title"><i class="fas fa-cloud-download-alt"></i> Daftar Dashboard Tableau</h2>
            <p style="font-size: 13px; color: #94a3b8; margin-top: 5px;">Masukkan Site ID (opsional) lalu klik tombol untuk mengambil daftar dashboard</p>
        </div>
    </div>

    {{-- Site ID Input --}}
    <div class="form-row" style="margin-bottom: 15px;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Site ID / Content URL</label>
            <input type="text" class="form-input" id="siteIdInput" placeholder="Kosongkan untuk default site" value="{{ config('tableau.site_id') }}">
            <p class="form-hint">Contoh: korlantas, site1, atau kosongkan untuk default site</p>
        </div>
        <div class="form-group" style="margin-bottom: 0; display: flex; align-items: flex-end;">
            <button type="button" class="btn btn-success" id="btnFetchViews" onclick="fetchTableauViews()" style="height: 46px;">
                <i class="fas fa-sync-alt"></i> Ambil Dashboard
            </button>
        </div>
    </div>

    <div id="siteInfo" style="display: none; padding: 10px 15px; background: rgba(30, 136, 229, 0.1); border-radius: 8px; margin-bottom: 15px; font-size: 13px;">
        <i class="fas fa-server"></i> Site: <strong id="currentSiteName">-</strong> | <span id="viewCount">0</span> dashboard ditemukan
    </div>

    <div id="viewsContainer" class="views-container" style="display: none;">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-input" id="searchViews" placeholder="Cari dashboard..." oninput="filterViews()">
        </div>
        <div class="views-list" id="viewsList"></div>
    </div>

    <div id="loadingViews" class="loading" style="display: none;">
        <i class="fas fa-spinner"></i>
        <p>Mengambil daftar dashboard dari Tableau Server...</p>
    </div>

    <div id="errorViews" class="error-msg" style="display: none;"></div>
    <div id="manualInput" class="info-msg" style="display: none;">
        <p><i class="fas fa-info-circle"></i> Jika tidak bisa mengambil daftar otomatis, Anda bisa input path manual di form bawah.</p>
    </div>
</div>

{{-- Card: Form Menu --}}
<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h2 class="card-title">Form Menu</h2>
    </div>

    <form action="{{ route('menus.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label class="form-label">Nama Menu *</label>
            <input type="text" name="name" id="menuName" class="form-input" value="{{ old('name') }}" placeholder="Contoh: Rangkuman Dakgar" required>
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Tableau View Path *</label>
            <input type="text" name="tableau_view_path" id="viewPath" class="form-input" value="{{ old('tableau_view_path') }}" placeholder="/views/workbook/dashboard" required>
            <p class="form-hint">Path view di Tableau Server. Pilih dari daftar di atas atau input manual.</p>
            @error('tableau_view_path')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Icon (Font Awesome) *</label>
            <input type="text" name="icon" id="iconInput" class="form-input" value="{{ old('icon', 'fas fa-chart-bar') }}" required>
            <div class="icon-preview">
                <i id="iconPreview" class="{{ old('icon', 'fas fa-chart-bar') }}"></i>
                <span>Preview icon</span>
            </div>
            <div class="icon-list">
                @foreach(['fas fa-chart-bar', 'fas fa-chart-pie', 'fas fa-chart-line', 'fas fa-chart-area', 'fas fa-car', 'fas fa-motorcycle', 'fas fa-truck', 'fas fa-bus', 'fas fa-road', 'fas fa-traffic-light', 'fas fa-map-marked-alt', 'fas fa-file-alt', 'fas fa-clipboard-list', 'fas fa-database', 'fas fa-table', 'fas fa-th-large'] as $icon)
                <div class="icon-item" onclick="selectIcon('{{ $icon }}')"><i class="{{ $icon }}"></i></div>
                @endforeach
            </div>
            @error('icon')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tableau Username</label>
                <input type="text" name="tableau_username" class="form-input" value="{{ old('tableau_username', 'korlantas_viewer_2') }}">
                <p class="form-hint">Username untuk Trusted Auth</p>
            </div>
            <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="order" class="form-input" value="{{ old('order', 0) }}" min="0">
            </div>
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active">Aktifkan menu ini</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('menus.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let allViews = [];

    function fetchTableauViews() {
        const btn = document.getElementById('btnFetchViews');
        const loading = document.getElementById('loadingViews');
        const container = document.getElementById('viewsContainer');
        const errorDiv = document.getElementById('errorViews');
        const manualDiv = document.getElementById('manualInput');
        const siteInfo = document.getElementById('siteInfo');
        const siteId = document.getElementById('siteIdInput').value.trim();

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengambil...';
        loading.style.display = 'block';
        container.style.display = 'none';
        errorDiv.style.display = 'none';
        manualDiv.style.display = 'none';
        siteInfo.style.display = 'none';

        // Build URL with site_id parameter
        let url = '{{ route("menus.fetch-views") }}';
        if (siteId) {
            url += '?site_id=' + encodeURIComponent(siteId);
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                loading.style.display = 'none';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';

                if (data.success && data.views && data.views.length > 0) {
                    allViews = data.views;
                    renderViews(allViews);
                    container.style.display = 'block';
                    
                    // Show site info
                    document.getElementById('currentSiteName').textContent = data.site_name || siteId || 'Default';
                    document.getElementById('viewCount').textContent = data.views.length;
                    siteInfo.style.display = 'block';
                } else {
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + (data.error || 'Tidak ada dashboard ditemukan di site ini.');
                    errorDiv.style.display = 'block';
                    manualDiv.style.display = 'block';
                }
            })
            .catch(err => {
                loading.style.display = 'none';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Coba Lagi';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Gagal terhubung ke server: ' + err.message;
                errorDiv.style.display = 'block';
                manualDiv.style.display = 'block';
            });
    }

    function renderViews(views) {
        const list = document.getElementById('viewsList');
        list.innerHTML = views.map((v, i) => `
            <div class="view-item" onclick="selectView(${i})">
                <div>
                    <div class="view-name">${v.name}</div>
                    <div class="view-workbook"><i class="fas fa-folder"></i> ${v.workbook}</div>
                </div>
                <div class="view-path">${v.viewPath}</div>
            </div>
        `).join('');
    }

    function filterViews() {
        const search = document.getElementById('searchViews').value.toLowerCase();
        const filtered = allViews.filter(v => 
            v.name.toLowerCase().includes(search) || 
            v.workbook.toLowerCase().includes(search) ||
            v.viewPath.toLowerCase().includes(search)
        );
        renderViews(filtered);
    }

    function selectView(index) {
        const items = document.querySelectorAll('.view-item');
        const viewPath = items[index]?.querySelector('.view-path')?.textContent;
        const view = allViews.find(v => v.viewPath === viewPath);

        if (view) {
            document.getElementById('menuName').value = view.name;
            document.getElementById('viewPath').value = view.viewPath;
            items.forEach((el, i) => el.classList.toggle('selected', i === index));
        }
    }

    function selectIcon(icon) {
        document.getElementById('iconInput').value = icon;
        document.getElementById('iconPreview').className = icon;
        document.querySelectorAll('.icon-item').forEach(el => el.classList.remove('selected'));
        event.target.closest('.icon-item').classList.add('selected');
    }

    document.getElementById('iconInput').addEventListener('input', function() {
        document.getElementById('iconPreview').className = this.value;
    });
</script>
@endsection
