@extends('layouts.admin')

@section('title', 'Kelola Menu')
@section('page-title', 'Kelola Menu Dashboard')
@section('page-subtitle', 'Atur menu sidebar dan dashboard Tableau')

@section('styles')
<style>
    .menu-icon { width: 32px; height: 32px; background: rgba(30, 136, 229, 0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #64b5f6; }
    .menu-path { font-size: 12px; color: #64748b; margin-top: 3px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .drag-handle { cursor: grab; color: #64748b; padding: 5px; }
    .drag-handle:hover { color: #94a3b8; }
    .table tr.dragging { opacity: 0.5; background: rgba(30, 136, 229, 0.2); }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Daftar Menu Dashboard</h2>
        <a href="{{ route('menus.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Menu</a>
    </div>

    @if($menus->count() > 0)
    <table class="table" id="menuTable">
        <thead>
            <tr>
                <th width="40"></th>
                <th>Menu</th>
                <th>Tableau View Path</th>
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
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="menu-icon"><i class="{{ $menu->icon }}"></i></div>
                        <div>
                            <strong>{{ $menu->name }}</strong>
                            <div class="menu-path">{{ $menu->tableau_view_path }}</div>
                        </div>
                    </div>
                </td>
                <td><code style="font-size: 12px; color: #94a3b8;">{{ $menu->tableau_view_path }}</code></td>
                <td>{{ $menu->order }}</td>
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
    @else
    <div class="empty-state">
        <i class="fas fa-bars"></i>
        <p>Belum ada menu dashboard</p>
        <p style="font-size: 13px; margin-top: 5px;">Tambahkan menu untuk menampilkan dashboard Tableau di sidebar</p>
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
            });

            fetch('{{ route("menus.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ orders })
            });
        }
    }
</script>
@endsection
