@extends('layouts.admin')

@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu')
@section('page-subtitle', 'Edit {{ $menu->name }}')

@section('styles')
<style>
    .menu-header {
        display: flex; align-items: center; gap: 18px;
        padding: 20px; margin-bottom: 24px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(34, 211, 238, 0.05));
        border-radius: 14px; border: 1px solid rgba(99, 102, 241, 0.2);
    }
    .menu-header-icon {
        width: 64px; height: 64px; border-radius: 16px;
        background: linear-gradient(135deg, #6366f1, #22d3ee);
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
    }
    .menu-header-info h3 { font-size: 18px; font-weight: 700; }
    .menu-header-info p { font-size: 12px; color: #94a3b8; margin-top: 4px; font-family: 'Monaco', 'Consolas', monospace; }
    
    .icon-preview {
        display: flex; align-items: center; gap: 16px; margin-top: 12px;
        padding: 18px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(34, 211, 238, 0.05));
        border-radius: 12px; border: 1px solid rgba(99, 102, 241, 0.2);
    }
    .icon-preview i { font-size: 28px; color: #22d3ee; }
    .icon-preview span { font-size: 13px; color: #94a3b8; }
    .icon-list {
        display: grid; grid-template-columns: repeat(8, 1fr); gap: 10px;
        margin-top: 12px; max-height: 160px; overflow-y: auto;
        padding: 14px; background: rgba(0,0,0,0.2); border-radius: 12px;
    }
    .icon-item {
        padding: 12px; text-align: center; border-radius: 10px;
        cursor: pointer; transition: all 0.3s; border: 2px solid transparent;
    }
    .icon-item:hover { background: rgba(99, 102, 241, 0.2); border-color: rgba(99, 102, 241, 0.3); }
    .icon-item.selected { background: rgba(99, 102, 241, 0.3); border-color: #6366f1; }
    .icon-item i { font-size: 20px; }
</style>
@endsection

@section('content')
<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-edit"></i> Form Edit Menu</h2>
    </div>

    <div class="menu-header">
        <div class="menu-header-icon"><i class="{{ $menu->icon }}"></i></div>
        <div class="menu-header-info">
            <h3>{{ $menu->name }}</h3>
            <p>{{ $menu->tableau_view_path }}</p>
        </div>
    </div>

    <form action="{{ route('menus.update', $menu) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label">Nama Menu *</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $menu->name) }}" required>
            @error('name')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Parent Menu</label>
            <select name="parent_id" class="form-select">
                <option value="">-- Tidak ada (Menu Utama) --</option>
                @foreach($parentMenus as $parent)
                <option value="{{ $parent->id }}" {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                @endforeach
            </select>
            <p class="form-hint">Pilih parent jika ini adalah sub-menu. Kosongkan untuk menu utama.</p>
            @error('parent_id')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Tableau View Path</label>
            <input type="text" name="tableau_view_path" class="form-input" value="{{ old('tableau_view_path', $menu->tableau_view_path) }}">
            <p class="form-hint">Path view di Tableau Server. Kosongkan jika menu ini hanya sebagai parent/grup.</p>
            @error('tableau_view_path')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Icon (Font Awesome) *</label>
            <input type="text" name="icon" id="iconInput" class="form-input" value="{{ old('icon', $menu->icon) }}" required>
            <div class="icon-preview">
                <i id="iconPreview" class="{{ old('icon', $menu->icon) }}"></i>
                <span>Preview icon yang dipilih</span>
            </div>
            <div class="icon-list">
                @foreach(['fas fa-chart-bar', 'fas fa-chart-pie', 'fas fa-chart-line', 'fas fa-chart-area', 'fas fa-car', 'fas fa-motorcycle', 'fas fa-truck', 'fas fa-bus', 'fas fa-road', 'fas fa-traffic-light', 'fas fa-map-marked-alt', 'fas fa-file-alt', 'fas fa-clipboard-list', 'fas fa-database', 'fas fa-table', 'fas fa-th-large'] as $icon)
                <div class="icon-item {{ $menu->icon == $icon ? 'selected' : '' }}" onclick="selectIcon('{{ $icon }}')"><i class="{{ $icon }}"></i></div>
                @endforeach
            </div>
            @error('icon')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tableau Username</label>
                <input type="text" name="tableau_username" class="form-input" value="{{ old('tableau_username', $menu->tableau_username) }}">
                <p class="form-hint">Username untuk Trusted Auth</p>
            </div>
            <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="order" class="form-input" value="{{ old('order', $menu->order) }}" min="0">
            </div>
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $menu->is_active) ? 'checked' : '' }}>
                <label for="is_active">Aktifkan menu ini</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            <a href="{{ route('menus.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
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