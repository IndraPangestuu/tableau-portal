@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Aplikasi')
@section('page-subtitle', 'Konfigurasi tampilan dan fitur aplikasi')

@section('content')
<div class="card">
    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-cog"></i> Pengaturan Umum</h3>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Aplikasi</label>
                <input type="text" name="app_name" class="form-input" value="{{ old('app_name', $settings['app_name']) }}" required>
                @error('app_name')<span class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Subtitle</label>
                <input type="text" name="app_subtitle" class="form-input" value="{{ old('app_subtitle', $settings['app_subtitle']) }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Teks Footer</label>
            <input type="text" name="footer_text" class="form-input" value="{{ old('footer_text', $settings['footer_text']) }}">
            <span class="form-hint">Ditampilkan di bagian bawah halaman login</span>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Logo Aplikasi</label>
                @if($settings['app_logo'])
                <div style="margin-bottom: 12px;">
                    <img src="{{ Storage::url($settings['app_logo']) }}" alt="Logo" style="max-height: 60px; border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 8px; margin-top: 8px; cursor: pointer;">
                        <input type="checkbox" name="remove_logo" value="1"> Hapus logo
                    </label>
                </div>
                @endif
                <input type="file" name="app_logo" class="form-input" accept="image/*">
                <span class="form-hint">Format: PNG, JPG, SVG. Maks: 2MB</span>
            </div>
            <div class="form-group">
                <label class="form-label">Favicon</label>
                @if($settings['app_favicon'])
                <div style="margin-bottom: 12px;">
                    <img src="{{ Storage::url($settings['app_favicon']) }}" alt="Favicon" style="max-height: 32px;">
                </div>
                @endif
                <input type="file" name="app_favicon" class="form-input" accept="image/png,image/x-icon">
                <span class="form-hint">Format: PNG, ICO. Maks: 512KB</span>
            </div>
        </div>

        <hr style="border-color: var(--border); margin: 32px 0;">

        <h3 class="card-title" style="margin-bottom: 24px;"><i class="fas fa-chart-bar"></i> Pengaturan Dashboard</h3>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Auto Refresh Interval (detik)</label>
                <input type="number" name="dashboard_refresh_interval" class="form-input" value="{{ old('dashboard_refresh_interval', $settings['dashboard_refresh_interval']) }}" min="0" max="3600">
                <span class="form-hint">0 = tidak auto refresh. Rekomendasi: 300 (5 menit)</span>
            </div>
            <div class="form-group">
                <label class="form-label">Fitur</label>
                <div class="checkbox-group" style="margin-top: 12px;">
                    <input type="checkbox" name="enable_fullscreen" id="enable_fullscreen" value="1" {{ $settings['enable_fullscreen'] ? 'checked' : '' }}>
                    <label for="enable_fullscreen">Aktifkan tombol Fullscreen</label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
