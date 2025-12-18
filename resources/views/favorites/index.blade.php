@extends('layouts.user')

@section('title', 'Menu Favorit')
@section('page-title', 'Menu Favorit')
@section('page-subtitle', 'Daftar menu dashboard favorit Anda')

@section('styles')
<style>
    .favorites-container { max-width: 900px; margin: 0 auto; padding: 24px; }
    .favorites-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .favorite-card {
        background: linear-gradient(135deg, rgba(20, 20, 40, 0.9), rgba(15, 15, 30, 0.95));
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }
    .favorite-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--accent));
        opacity: 0;
        transition: opacity 0.3s;
    }
    .favorite-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        border-color: rgba(99, 102, 241, 0.3);
    }
    .favorite-card:hover::before { opacity: 1; }
    .favorite-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }
    .favorite-name { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
    .favorite-actions { display: flex; gap: 10px; margin-top: 16px; }
    .btn-view {
        flex: 1;
        padding: 10px 16px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
        border: none;
        border-radius: 10px;
        text-decoration: none;
        text-align: center;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s;
    }
    .btn-view:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); }
    .btn-remove {
        padding: 10px 14px;
        background: rgba(239, 68, 68, 0.1);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .btn-remove:hover { background: rgba(239, 68, 68, 0.2); }
    .empty-state {
        text-align: center;
        padding: 60px 40px;
        color: var(--text-muted);
    }
    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.3;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .empty-state h3 { font-size: 20px; margin-bottom: 8px; color: var(--text); }
</style>
@endsection

@section('content')
<div class="favorites-container">
    @if($favorites->count() > 0)
    <div class="favorites-grid">
        @foreach($favorites as $favorite)
        <div class="favorite-card" id="favorite-{{ $favorite->menu_id }}">
            <div class="favorite-icon">
                <i class="{{ $favorite->menu->icon ?? 'fas fa-chart-bar' }}"></i>
            </div>
            <div class="favorite-name">{{ $favorite->menu->name }}</div>
            <div class="favorite-actions">
                <a href="{{ route('view.menu', $favorite->menu) }}" class="btn-view">
                    <i class="fas fa-external-link-alt"></i> Buka Dashboard
                </a>
                <button class="btn-remove" onclick="removeFavorite({{ $favorite->menu_id }})" title="Hapus dari favorit">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-star"></i>
        <h3>Belum Ada Favorit</h3>
        <p>Klik icon ⭐ di toolbar dashboard untuk menambahkan ke favorit</p>
    </div>
    @endif
</div>

<script>
function removeFavorite(menuId) {
    if (!confirm('Hapus dari favorit?')) return;
    
    const baseUrl = '{{ url("/") }}';
    fetch(`${baseUrl}/favorites/${menuId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.is_favorite) {
            document.getElementById(`favorite-${menuId}`).remove();
            Toast.success('Dihapus dari favorit');
            
            // Check if empty
            if (document.querySelectorAll('.favorite-card').length === 0) {
                location.reload();
            }
        }
    })
    .catch(() => Toast.error('Gagal menghapus favorit'));
}
</script>
@endsection
