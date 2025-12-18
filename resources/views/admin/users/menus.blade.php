@extends('layouts.admin')

@section('title', 'Akses Menu User')
@section('page-title', 'Akses Menu User')
@section('page-subtitle', 'Atur menu yang dapat diakses oleh {{ $user->nama }}')

@section('styles')
<style>
    .menu-tree { list-style: none; padding: 0; }
    .menu-item { background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 10px; margin-bottom: 12px; overflow: hidden; }
    .menu-parent { padding: 16px 20px; display: flex; align-items: center; gap: 14px; cursor: pointer; transition: background 0.3s; }
    .menu-parent:hover { background: rgba(99, 102, 241, 0.05); }
    .menu-parent input[type="checkbox"] { width: 20px; height: 20px; accent-color: var(--primary); }
    .menu-parent i.icon { width: 24px; text-align: center; color: var(--accent); }
    .menu-parent .name { flex: 1; font-weight: 500; }
    .menu-children { padding: 0 20px 16px 58px; display: none; }
    .menu-item.expanded .menu-children { display: block; }
    .menu-child { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-top: 1px solid var(--border); }
    .menu-child:first-child { border-top: none; }
    .menu-child input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--primary); }
    .toggle-arrow { transition: transform 0.3s; color: var(--text-muted); }
    .menu-item.expanded .toggle-arrow { transform: rotate(180deg); }
    .select-actions { display: flex; gap: 12px; margin-bottom: 20px; }
    .select-btn { padding: 8px 16px; background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-radius: 8px; color: var(--text-muted); cursor: pointer; font-size: 13px; transition: all 0.3s; }
    .select-btn:hover { background: rgba(99, 102, 241, 0.1); color: var(--text); }
    .info-box { background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(99, 102, 241, 0.05)); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 10px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
    .info-box i { color: var(--primary); font-size: 20px; }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-shield"></i> Akses Menu: {{ $user->nama }}</h3>
        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Catatan:</strong> Jika tidak ada menu yang dipilih, user akan memiliki akses ke semua menu.
            Admin selalu memiliki akses ke semua menu.
        </div>
    </div>

    <form action="{{ route('users.menus.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="select-actions">
            <button type="button" class="select-btn" onclick="selectAll()"><i class="fas fa-check-double"></i> Pilih Semua</button>
            <button type="button" class="select-btn" onclick="deselectAll()"><i class="fas fa-times"></i> Hapus Semua</button>
        </div>

        <ul class="menu-tree">
            @foreach($menus as $menu)
            <li class="menu-item {{ $menu->children->count() > 0 ? 'has-children' : '' }}">
                <div class="menu-parent" onclick="toggleMenu(this)">
                    <input type="checkbox" name="menus[]" value="{{ $menu->id }}" {{ in_array($menu->id, $allowedMenus) ? 'checked' : '' }} onclick="event.stopPropagation()">
                    <i class="{{ $menu->icon }} icon"></i>
                    <span class="name">{{ $menu->name }}</span>
                    @if($menu->children->count() > 0)
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                    @endif
                </div>
                @if($menu->children->count() > 0)
                <div class="menu-children">
                    @foreach($menu->children as $child)
                    <div class="menu-child">
                        <input type="checkbox" name="menus[]" value="{{ $child->id }}" {{ in_array($child->id, $allowedMenus) ? 'checked' : '' }}>
                        <i class="{{ $child->icon }}" style="color: var(--text-muted);"></i>
                        <span>{{ $child->name }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </li>
            @endforeach
        </ul>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Akses Menu</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
function toggleMenu(el) {
    el.closest('.menu-item').classList.toggle('expanded');
}

function selectAll() {
    document.querySelectorAll('.menu-tree input[type="checkbox"]').forEach(cb => cb.checked = true);
}

function deselectAll() {
    document.querySelectorAll('.menu-tree input[type="checkbox"]').forEach(cb => cb.checked = false);
}

// Auto expand items with checked children
document.querySelectorAll('.menu-item.has-children').forEach(item => {
    const hasChecked = item.querySelector('.menu-children input:checked');
    if (hasChecked) item.classList.add('expanded');
});
</script>
@endsection
