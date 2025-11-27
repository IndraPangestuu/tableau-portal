@extends('layouts.admin')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')
@section('page-subtitle', 'Buat akun pengguna baru')

@section('styles')
<style>
    .form-card { max-width: 600px; }
    .form-icon {
        width: 48px; height: 48px; border-radius: 12px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(34, 211, 238, 0.1));
        display: flex; align-items: center; justify-content: center;
        color: #22d3ee; font-size: 20px; margin-right: 14px;
    }
    .input-icon-wrapper { position: relative; }
    .input-icon-wrapper .form-input { padding-left: 48px; }
    .input-icon-wrapper i {
        position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
        color: #64748b; font-size: 16px; transition: color 0.3s;
    }
    .input-icon-wrapper:focus-within i { color: #22d3ee; }
    .role-select { display: flex; gap: 12px; }
    .role-option {
        flex: 1; padding: 16px; border-radius: 12px; cursor: pointer;
        background: rgba(255,255,255,0.03); border: 2px solid rgba(255,255,255,0.1);
        transition: all 0.3s; text-align: center;
    }
    .role-option:hover { border-color: rgba(99, 102, 241, 0.3); }
    .role-option.selected { border-color: #6366f1; background: rgba(99, 102, 241, 0.1); }
    .role-option i { font-size: 24px; margin-bottom: 8px; display: block; }
    .role-option.user-role i { color: #4ade80; }
    .role-option.admin-role i { color: #a78bfa; }
    .role-option span { font-weight: 600; font-size: 14px; }
</style>
@endsection

@section('content')
<div class="card form-card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-user-plus"></i> Form Tambah User</h2>
    </div>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama Lengkap *</label>
            <div class="input-icon-wrapper">
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                <i class="fas fa-user"></i>
            </div>
            @error('name')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Username *</label>
                <div class="input-icon-wrapper">
                    <input type="text" name="username" class="form-input" value="{{ old('username') }}" placeholder="Username" required>
                    <i class="fas fa-at"></i>
                </div>
                @error('username')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">NRP</label>
                <div class="input-icon-wrapper">
                    <input type="text" name="nrp" class="form-input" value="{{ old('nrp') }}" placeholder="NRP (opsional)">
                    <i class="fas fa-id-badge"></i>
                </div>
                @error('nrp')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <div class="input-icon-wrapper">
                <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="Email (opsional)">
                <i class="fas fa-envelope"></i>
            </div>
            @error('email')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Role *</label>
            <div class="role-select">
                <label class="role-option user-role {{ old('role', 'user') == 'user' ? 'selected' : '' }}" onclick="selectRole('user')">
                    <i class="fas fa-user"></i>
                    <span>User</span>
                </label>
                <label class="role-option admin-role {{ old('role') == 'admin' ? 'selected' : '' }}" onclick="selectRole('admin')">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </label>
            </div>
            <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'user') }}">
            @error('role')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Password *</label>
                <div class="input-icon-wrapper">
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                </div>
                @error('password')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password *</label>
                <div class="input-icon-wrapper">
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function selectRole(role) {
        document.getElementById('roleInput').value = role;
        document.querySelectorAll('.role-option').forEach(el => el.classList.remove('selected'));
        document.querySelector(`.role-option.${role}-role`).classList.add('selected');
    }
</script>
@endsection