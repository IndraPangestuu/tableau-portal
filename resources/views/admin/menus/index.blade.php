@extends('layouts.admin')

@section('title', 'Kelola Menu')
@section('page-title', 'Kelola Menu Dashboard')
@section('page-subtitle', 'Atur menu sidebar dan dashboard Tableau')

@section('styles')
<style>
    .menu-icon {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(34, 211, 238, 0.1));
        border-radius: 12px; display: flex; align-items: center; justify-content: center;
        color: #22d3ee; font-size: 18px;
        transition: all 0.3s;
    }
    .menu-info { display: flex; align-items: center; gap: 14px; }
    .menu-name { font-weight: 600; font-size: 15px; }
    .menu-path {
        font-size: 11px; color: #64748b; margin-top: 4px;
        max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        font-family: 'Monaco', 'Consolas', monospace;
    }
    .drag-handle {
        cursor: grab; color: #475569; padding: 8px;
        transition: all 0.3s; border-radius: 8px;
    }
    .drag-handle:hover { color: #22d3ee; background: rgba(34, 211, 238, 0.1); }
    .drag-handle:active { cursor: grabbing; }
    .table tr.dragging { opacity: 0.5; background: rgba(99, 102, 241, 0.15); }
    .table tr { transition: all 0.3s; }
    
    .order-badge {
        width: 32px; height: 32px; border-radius: 8px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(99, 102, 241, 0.1));
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 13px; color: #818cf8;
    }
    
    .path-code {
        font-size: 11px; color: #94a3b8; padding: 6px 10px;
        background: rgba(0, 0, 0, 0.2); border-radius: 6px;
        font-family: 'Monaco', 'Consolas', monospace;
        max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        display: inline-block;
    }
    
    .menu-stats {
        display: flex; gap: 16px; margin-bottom: 24px;
    }
    .menu-stat {
        padding: 16px 24px; border-radius: 12px;
        background: linear-gradient(135deg, rgba(20, 20, 40, 0.9), rgba(15, 15, 30, 0.95));
        border: 1px solid var(--border);
        display: flex; align-items: center; gap: 12px;
        animation: cardFadeIn 0.5s ease-out backwards;
    }
    .menu-stat:nth-child(1) { animation-delay: 0.1s; }
    .menu-stat:nth-child(2) { animation-delay: 0.2s; }
    .menu-stat-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
    }
    .menu-stat-icon.total { background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(99, 102, 241, 0.1)); color: #818cf8; }
    .menu-stat-icon.active { background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1)); color: #34d399; }
    .menu-stat-value { font-size: 22px; font-weight: 700; }
    .menu-stat-label { font-size: 12px; color: #64748b; }
</style>
@endsection

@section('content')
<div class="menu-stats">
    <div class="menu-stat">
        <div class="menu-stat-icon total"><i class="fas fa-th-list"></i></div>
        <div>
            <div class="menu-stat-value">{{ $menus->count() }}</div>
            <div class="menu-stat-label">Total Menu</div>
        </div>
    </div>
    <div class="menu-stat">
        <div class="menu-stat-icon active"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="menu-stat-value">{{ $menus->where('is_active', true)->count() }}</div>
            <div class="menu-stat-label">Menu Aktif</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-bars"></i> Daftar Menu Dashboard</h2>
        <a href="{{ route('menus.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Menu</a>
    </div>

    @if($menus->count() > 0)
    <div class="table-container">
        <table class="table" id="menuTable">
            <thead>
                <tr>
                    <th width="50"></th>
                    <th>Menu</th>
                    <th>Tableau Path</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="sortableMenu">
                @foreach($menus as $menu)
                <tr data-id="{{ $menu->id }}">
                    <td><span class="drag-handle"><i class="fas fa-grip-vertical"></i></span></td>
                    <td>
                        <div class="menu-info">
                            <div class="menu-icon"><i class="{{ $menu->icon }}"></i></div>
                            <div>
                                <div class="menu-name">{{ $menu->name }}</div>
                                <div class="menu-path">{{ $menu->tableau_view_path }}</div>
                            </div>
                        </div>
                    </td>
                    <td><code class="path-code">{{ $menu->tableau_view_path }}</code></td>
                    <td><div class="order-badge">{{ $menu->order }}</div></td>
                    <td><span class="badge badge-{{ $menu->is_active ? 'active' : 'inactive' }}">{{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('menus.edit', $menu) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Yakin hapus menu ini?')">
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
    @else
    <div class="empty-state">
        <i class="fas fa-bars"></i>
        <h3>Belum ada menu dashboard</h3>
        <p>Tambahkan menu untuk menampilkan dashboard Tableau di sidebar</p>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    const tbody = document.getElementById('sortableMenu');
    if (tbody) {
        let draggedRow = null;

        tbody.querySelectorAll('tr').forEach(row => {
            row.draggable = true;
            
            row.addEventListener('dragstart', function(e) {
                draggedRow = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });

            row.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                saveOrder();
            });

            row.addEventListener('dragover', function(e) {
                e.preventDefault();
                if (draggedRow !== this) {
                    const rect = this.getBoundingClientRect();
                    const midY = rect.top + rect.height / 2;
                    if (e.clientY < midY) {
                        this.parentNode.insertBefore(draggedRow, this);
                    } else {
                        this.parentNode.insertBefore(draggedRow, this.nextSibling);
                    }
                }
            });
        });

        function saveOrder() {
            const orders = {};
            tbody.querySelectorAll('tr').forEach((row, index) => {
                orders[row.dataset.id] = index;
                // Update visual order badge
                const badge = row.querySelector('.order-badge');
                if (badge) badge.textContent = index;
            });

            fetch('{{ route("menus.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ orders })
            }).then(response => {
                if (response.ok) {
                    // Optional: show success toast
                }
            });
        }
    }
</script>
@endsection