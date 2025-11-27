@extends('layouts.admin')

@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu')
@section('page-subtitle', 'Edit {{ $menu->name }}')

@section('styles')
<style>
    .icon-preview { display: flex; align-items: center; gap: 15px; margin-top: 10px; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px; }
    .icon-preview i { font-size: 24px; color: #64b5f6; }
    .icon-preview span { font-size: 13px; color: #94a3b8; }
    .icon-list { display: grid; grid-template-columns: repeat(8, 1fr); gap: 8px; margin-top: 10px; max-height: 150px; overflow-y: auto; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 8px; }
    .icon-item { padding: 10px; text-align: center; border-radius: 6px; cursor: pointer; transition: all 0.2s; }
    .icon-item:hover, .icon-item.selected { background: rgba(30, 136, 229, 0.3); }
    .icon-item i { font-size: 18px; }
</style>
@endsection

@section('content')
<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h2 class="card-title">Form Edit Menu</h2>
    </div>

    <form action="{{ route('menus.update', $menu) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label">Nama Menu *</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $menu->name) }}" required>
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Tableau View Path *</label>
            <input type="text" name="tableau_view_path" class="form-input" value="{{ old('tableau_view_path', $menu->tableau_view_path) }}" required>
            <p class="form-hint">Path view di Tableau Server, contoh: /views/home/01_SummaryDAKGARLANTAS3</p>
            @error('tableau_view_path')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Icon (Font Awesome) *</label>
            <input type="text" name="icon" id="iconInput" class="form-input" value="{{ old('icon', $menu->icon) }}" required>
            <div class="icon-preview">
                <i id="iconPreview" class="{{ old('icon', $menu->icon) }}"></i>
                <span>Preview icon</span>
            </div>
            <div class="icon-list">
                @foreach(['fas fa-chart-bar', 'fas fa-chart-pie', 'fas fa-chart-line', 'fas fa-chart-area', 'fas fa-car', 'fas fa-motorcycle', 'fas fa-truck', 'fas fa-bus', 'fas fa-road', 'fas fa-traffic-light', 'fas fa-map-marked-alt', 'fas fa-file-alt', 'fas fa-clipboard-list', 'fas fa-database', 'fas fa-table', 'fas fa-th-large'] as $icon)
                <div class="icon-item {{ $menu->icon == $icon ? 'selected' : '' }}" onclick="selectIcon('{{ $icon }}')"><i class="{{ $icon }}"></i></div>
                @endforeach
            </div>
            @error('icon')<p class="form-error">{{ $message }}</p>@enderror
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
