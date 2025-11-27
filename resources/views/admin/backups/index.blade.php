@extends('layouts.admin')

@section('title', 'Backup Database')
@section('page-title', 'Backup Database')
@section('page-subtitle', 'Kelola backup dan restore database')

@section('styles')
<style>
    .backup-stats {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px; margin-bottom: 28px;
    }
    .stat-card {
        background: linear-gradient(135deg, rgba(20, 20, 40, 0.9), rgba(15, 15, 30, 0.95));
        border: 1px solid var(--border); border-radius: 16px; padding: 24px;
        display: flex; align-items: center; gap: 18px;
        transition: all 0.3s; animation: cardFadeIn 0.6s ease-out backwards;
    }
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3); }
    .stat-icon {
        width: 56px; height: 56px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; font-size: 24px;
    }
    .stat-icon.backup { background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(99, 102, 241, 0.1)); color: #818cf8; }
    .stat-icon.size { background: linear-gradient(135deg, rgba(34, 211, 238, 0.2), rgba(34, 211, 238, 0.1)); color: #22d3ee; }
    .stat-value { font-size: 28px; font-weight: 700; }
    .stat-label { font-size: 13px; color: #94a3b8; margin-top: 2px; }
    
    .backup-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 20px; border-bottom: 1px solid var(--border);
        transition: all 0.3s;
    }
    .backup-item:last-child { border-bottom: none; }
    .backup-item:hover { background: linear-gradient(90deg, rgba(99, 102, 241, 0.05), transparent); }
    
    .backup-info { display: flex; align-items: center; gap: 16px; }
    .backup-icon {
        width: 48px; height: 48px; border-radius: 12px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(34, 211, 238, 0.1));
        display: flex; align-items: center; justify-content: center;
        color: #22d3ee; font-size: 20px;
    }
    .backup-details h4 { font-size: 15px; font-weight: 600; margin-bottom: 4px; }
    .backup-meta { display: flex; gap: 16px; font-size: 12px; color: #64748b; }
    .backup-meta span { display: flex; align-items: center; gap: 6px; }
    
    .backup-actions { display: flex; gap: 8px; }
    
    .btn-icon {
        width: 38px; height: 38px; border-radius: 10px; border: none;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.3s; font-size: 14px;
    }
    .btn-icon.download {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1));
        color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .btn-icon.download:hover { background: rgba(16, 185, 129, 0.3); transform: translateY(-2px); }
    .btn-icon.restore {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.1));
        color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .btn-icon.restore:hover { background: rgba(245, 158, 11, 0.3); transform: translateY(-2px); }
    .btn-icon.delete {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
        color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .btn-icon.delete:hover { background: rgba(239, 68, 68, 0.3); transform: translateY(-2px); }
    
    .empty-backups {
        text-align: center; padding: 60px 40px; color: #64748b;
    }
    .empty-backups i { font-size: 56px; margin-bottom: 16px; opacity: 0.3; color: #6366f1; }
    .empty-backups h3 { font-size: 18px; color: #94a3b8; margin-bottom: 8px; }
    
    .warning-box {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
        border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px;
        padding: 16px 20px; margin-bottom: 24px;
        display: flex; align-items: center; gap: 14px; font-size: 14px; color: #fbbf24;
    }
    .warning-box i { font-size: 20px; }
</style>
@endsection

@section('content')
<div class="backup-stats">
    <div class="stat-card">
        <div class="stat-icon backup"><i class="fas fa-database"></i></div>
        <div>
            <div class="stat-value">{{ count($backups) }}</div>
            <div class="stat-label">Total Backup</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon size"><i class="fas fa-hdd"></i></div>
        <div>
            @php
                $totalSize = collect($backups)->sum('size_bytes');
                $formattedSize = $totalSize >= 1048576 
                    ? round($totalSize / 1048576, 2) . ' MB' 
                    : round($totalSize / 1024, 2) . ' KB';
            @endphp
            <div class="stat-value">{{ $formattedSize }}</div>
            <div class="stat-label">Total Ukuran</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-database"></i> Daftar Backup</h2>
        <form action="{{ route('backups.store') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Backup Baru
            </button>
        </form>
    </div>

    <div class="warning-box">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Pastikan untuk membuat backup secara berkala. Restore akan menimpa semua data yang ada!</span>
    </div>

    @if(count($backups) > 0)
        <div class="backup-list">
            @foreach($backups as $backup)
            <div class="backup-item">
                <div class="backup-info">
                    <div class="backup-icon"><i class="fas fa-file-archive"></i></div>
                    <div class="backup-details">
                        <h4>{{ $backup['filename'] }}</h4>
                        <div class="backup-meta">
                            <span><i class="fas fa-calendar"></i> {{ $backup['created_at'] }}</span>
                            <span><i class="fas fa-weight"></i> {{ $backup['size'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="backup-actions">
                    <a href="{{ route('backups.download', $backup['filename']) }}" 
                       class="btn-icon download" title="Download">
                        <i class="fas fa-download"></i>
                    </a>
                    <form action="{{ route('backups.restore', $backup['filename']) }}" method="POST" 
                          onsubmit="return confirm('PERINGATAN: Restore akan menimpa semua data saat ini!\n\nYakin ingin restore dari backup ini?')">
                        @csrf
                        <button type="submit" class="btn-icon restore" title="Restore">
                            <i class="fas fa-undo"></i>
                        </button>
                    </form>
                    <form action="{{ route('backups.destroy', $backup['filename']) }}" method="POST"
                          onsubmit="return confirm('Yakin hapus backup ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon delete" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-backups">
            <i class="fas fa-database"></i>
            <h3>Belum ada backup</h3>
            <p>Klik tombol "Buat Backup Baru" untuk membuat backup database</p>
        </div>
    @endif
</div>
@endsection