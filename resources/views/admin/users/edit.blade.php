@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-subtitle', 'Ubah data {{ $user->name }}')

@section('styles')
<style>
    .form-card { max-width: 600px; }
    .user-header {
        display: flex; align-items: center; gap: 18px;
        padding: 20px; margin-bottom: 24px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(34, 211, 238, 0.05));
        border-radius: 14px; border: 1px solid rgba(99, 102, 241, 0.2);
    }
    .user-header-avatar {
        width: 64px; height: 64px; border-radius: 16px;
        background: linear-gradient(135deg, #6366f1, #22d3ee);
        display: flex; align-items: center; justify-content: center;
        font-size: 26px; font-weight: 700;
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
    }
    .user-header-info h3 { font-size: 18px; font-weight: 700; }
    .user-header-info p { font-size: 13px; color: #94a3b8; margin-top: 4px; }
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
        <h2 class="card-title"><i class="fas fa-user-edit"></i> Form Edit User</h2>
    </div>

    <div class="user-header">
        <div class="user-header-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div class="user-header-info">
            <h3>{{ $user->name }}</h3>
            <p>@{{ $user->username }} • {{ ucfirst($user->role) }}</p>
        </div>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label">Nama Lengkap *</label>
            <div class="input-icon-wrapper">
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                <i class="fas fa-user"></i>
            </div>
            @error('name')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Username *</label>
                <div class="input-icon-wrapper">
                    <input type="text" name="username" class="form-input" value="{{ old('username', $user->username) }}" required>
                    <i class="fas fa-at"></i>
                </div>
                @error('username')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">NRP</label>
                <div class="input-icon-wrapper">
                    <input type="text" name="nrp" class="form-input" value="{{ old('nrp', $user->nrp) }}">
                    <i class="fas fa-id-badge"></i>
                </div>
                @error('nrp')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <div class="input-icon-wrapper">
                <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}">
                <i class="fas fa-envelope"></i>
            </div>
            @error('email')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Role *</label>
            <div class="role-select">
                <label class="role-option user-role {{ old('role', $user->role) == 'user' ? 'selected' : '' }}" onclick="selectRole('user')">
                    <i class="fas fa-user"></i>
                    <span>User</span>
                </label>
                <label class="role-option admin-role {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}" onclick="selectRole('admin')">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </label>
            </div>
            <input type="hidden" name="role" id="roleInput" value="{{ old('role', $user->role) }}">
            @error('role')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <div class="input-icon-wrapper">
                    <input type="password" name="password" class="form-input" placeholder="Kosongkan jika tidak diubah">
                    <i class="fas fa-lock"></i>
                </div>
                <p class="form-hint">Kosongkan jika tidak ingin mengubah password</p>
                @error('password')<p class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-icon-wrapper">
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password baru">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
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