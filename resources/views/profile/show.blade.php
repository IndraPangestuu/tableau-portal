@extends('layouts.user')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('styles')
<style>
    .profile-container { max-width: 900px; margin: 0 auto; padding: 24px; }
    .profile-card { background: linear-gradient(135deg, rgba(20, 20, 40, 0.9), rgba(15, 15, 30, 0.95)); border: 1px solid var(--border); border-radius: 16px; padding: 28px; margin-bottom: 24px; }
    .profile-header { display: flex; align-items: center; gap: 24px; margin-bottom: 32px; }
    .profile-avatar { width: 100px; height: 100px; border-radius: 20px; background: linear-gradient(135deg, var(--primary), var(--accent)); display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: 700; position: relative; overflow: hidden; }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .profile-info h2 { font-size: 24px; margin-bottom: 4px; }
    .profile-info p { color: var(--text-muted); }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 8px; }
    .form-input { width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-size: 14px; }
    .form-input:focus { outline: none; border-color: var(--primary); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .btn { padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; }
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4); }
    .section-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .section-title i { color: var(--accent); }
    .recent-list { display: flex; flex-direction: column; gap: 12px; }
    .recent-item { display: flex; align-items: center; gap: 14px; padding: 14px; background: rgba(255,255,255,0.02); border-radius: 10px; transition: all 0.3s; text-decoration: none; color: var(--text); }
    .recent-item:hover { background: rgba(99, 102, 241, 0.1); transform: translateX(4px); }
    .recent-icon { width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .recent-info { flex: 1; }
    .recent-name { font-weight: 500; }
    .recent-time { font-size: 12px; color: var(--text-muted); }
    .empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
    @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } .profile-header { flex-direction: column; text-align: center; } }
</style>
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                @if($user->foto)
                    <img src="{{ asset($user->foto) }}" alt="Avatar">
                @else
                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                @endif
            </div>
            <div class="profile-info">
                <h2>{{ $user->nama }}</h2>
                <p>{{ $user->username }} • {{ ucfirst($user->role) }}</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h3 class="section-title"><i class="fas fa-user"></i> Informasi Profil</h3>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-input" value="{{ old('nama', $user->nama) }}" required>
                    @error('nama')<span style="color: #f87171; font-size: 12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}">
                    @error('email')<span style="color: #f87171; font-size: 12px;">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="telp" class="form-input" value="{{ old('telp', $user->telp) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto" class="form-input" accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </form>
    </div>

    <div class="profile-card">
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <h3 class="section-title"><i class="fas fa-lock"></i> Ubah Password</h3>

            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-input" required>
                @error('current_password')<span style="color: #f87171; font-size: 12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password')<span style="color: #f87171; font-size: 12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Ubah Password</button>
        </form>
    </div>

    <div class="profile-card">
        <h3 class="section-title"><i class="fas fa-history"></i> Dashboard Terakhir Diakses</h3>

        @if($recentDashboards->count() > 0)
        <div class="recent-list">
            @foreach($recentDashboards as $recent)
            <a href="{{ route('view.menu', $recent->menu) }}" class="recent-item">
                <div class="recent-icon"><i class="{{ $recent->menu->icon ?? 'fas fa-chart-bar' }}"></i></div>
                <div class="recent-info">
                    <div class="recent-name">{{ $recent->menu->name }}</div>
                    <div class="recent-time">{{ $recent->accessed_at->diffForHumans() }}</div>
                </div>
                <i class="fas fa-chevron-right" style="color: var(--text-muted);"></i>
            </a>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-clock" style="font-size: 32px; margin-bottom: 12px; opacity: 0.3;"></i>
            <p>Belum ada dashboard yang diakses</p>
        </div>
        @endif
    </div>
</div>
@endsection
