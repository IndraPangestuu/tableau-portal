@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen pengguna sistem')

@section('styles')
<style>
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px; }
    .stat-card {
        background: linear-gradient(135deg, rgba(20, 20, 40, 0.9), rgba(15, 15, 30, 0.95));
        border: 1px solid var(--border); border-radius: 16px; padding: 24px;
        display: flex; align-items: center; gap: 18px;
        transition: all 0.3s; animation: cardFadeIn 0.6s ease-out backwards;
    }
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3); }
    .stat-icon {
        width: 56px; height: 56px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; font-size: 24px;
    }
    .stat-icon.users { background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(99, 102, 241, 0.1)); color: #818cf8; }
    .stat-icon.admin { background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(139, 92, 246, 0.1)); color: #a78bfa; }
    .stat-icon.active { background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1)); color: #34d399; }
    .stat-value { font-size: 28px; font-weight: 700; }
    .stat-label { font-size: 13px; color: #94a3b8; margin-top: 2px; }
    
    .user-cell { display: flex; align-items: center; gap: 14px; }
    .user-avatar-sm {
        width: 42px; height: 42px; border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #22d3ee);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 16px;
    }
    .user-info-cell .name { font-weight: 600; }
    .user-info-cell .username { font-size: 12px; color: #64748b; margin-top: 2px; }
</style>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon users"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-value">{{ $users->total() }}</div>
            <div class="stat-label">Total User</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon admin"><i class="fas fa-user-shield"></i></div>
        <div>
            <div class="stat-value">{{ $users->where('role', 'admin')->count() }}</div>
            <div class="stat-label">Admin</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon active"><i class="fas fa-user-check"></i></div>
        <div>
            <div class="stat-value">{{ $users->where('role', 'user')->count() }}</div>
            <div class="stat-label">User Biasa</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-users"></i> Daftar User</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah User</a>
    </div>

    @if($users->count() > 0)
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>NRP</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div class="user-info-cell">
                                <div class="name">{{ $user->name }}</div>
                                <div class="username">@{{ $user->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->nrp ?? '-' }}</td>
                    <td>{{ $user->email ?? '-' }}</td>
                    <td><span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->links() }}</div>
    @else
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <h3>Belum ada user terdaftar</h3>
        <p>Tambahkan user baru untuk memulai</p>
    </div>
    @endif
</div>
@endsection