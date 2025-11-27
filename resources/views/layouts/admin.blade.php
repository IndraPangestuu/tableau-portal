<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Portal Korlantas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0a1628; min-height: 100vh; color: #fff; }
        
        /* Sidebar */
        .sidebar {
            position: fixed; left: 0; top: 0; width: 260px; height: 100vh;
            background: linear-gradient(180deg, #0d2137 0%, #0a1628 100%);
            border-right: 1px solid rgba(255,255,255,0.1); padding: 20px 0; z-index: 100;
            transition: transform 0.3s ease;
        }
        .sidebar.collapsed { transform: translateX(-260px); }
        
        .logo { display: flex; align-items: center; gap: 12px; padding: 0 20px 25px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .logo-icon { width: 45px; height: 45px; background: linear-gradient(135deg, #1e88e5, #1565c0); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .logo-text { font-size: 18px; font-weight: 700; }
        .logo-text span { display: block; font-size: 11px; font-weight: 400; color: #64b5f6; }
        
        .nav-menu { list-style: none; padding: 0 15px; }
        .nav-item { margin-bottom: 5px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 15px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition: all 0.2s; font-size: 14px; }
        .nav-link:hover, .nav-link.active { background: rgba(30, 136, 229, 0.15); color: #64b5f6; }
        .nav-link.active { background: linear-gradient(90deg, rgba(30, 136, 229, 0.3), transparent); border-left: 3px solid #1e88e5; }
        .nav-link i { width: 20px; text-align: center; }
        .nav-divider { border-top: 1px solid rgba(255,255,255,0.1); margin: 15px 0; }
        .nav-label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; padding: 10px 15px 5px; }
        
        /* Main Content */
        .main-content { margin-left: 260px; min-height: 100vh; transition: margin-left 0.3s ease; }
        .main-content.expanded { margin-left: 0; }
        
        /* Header */
        .header {
            background: rgba(13, 33, 55, 0.8); backdrop-filter: blur(10px);
            padding: 15px 30px; display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1); position: sticky; top: 0; z-index: 50;
        }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .btn-toggle {
            background: rgba(30, 136, 229, 0.2); border: 1px solid rgba(30, 136, 229, 0.3);
            color: #64b5f6; width: 40px; height: 40px; border-radius: 8px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; font-size: 18px; transition: all 0.2s;
        }
        .btn-toggle:hover { background: rgba(30, 136, 229, 0.3); }
        .header-title h1 { font-size: 20px; font-weight: 600; }
        .header-title p { font-size: 12px; color: #64b5f6; margin-top: 2px; }
        
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 38px; height: 38px; background: linear-gradient(135deg, #1e88e5, #1565c0); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .user-name { font-size: 14px; font-weight: 500; }
        .btn-logout { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px; transition: all 0.2s; }
        .btn-logout:hover { background: rgba(239, 68, 68, 0.3); }
        
        /* Content */
        .content { padding: 25px 30px; }
        
        /* Embed Container (untuk dashboard) */
        .embed-container {
            background: #0d2137; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px; overflow: hidden; margin: 0;
        }
        .embed-body { position: relative; min-height: 800px; }
        .embed-body iframe, .embed-body tableau-viz {
            display: block; width: 100%; height: 850px; min-height: 600px; border: none;
        }
        .loading-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: #0d2137; display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 15px;
        }
        .spinner {
            width: 50px; height: 50px;
            border: 4px solid rgba(30, 136, 229, 0.2); border-top-color: #1e88e5;
            border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .error-box {
            background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171; padding: 20px; border-radius: 8px; text-align: center; margin: 20px;
        }
        @media (min-height: 900px) {
            .embed-body, .embed-body tableau-viz { height: calc(100vh - 120px); }
        }
        
        /* Card */
        .card { background: linear-gradient(135deg, #0d2137, #132f4c); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 25px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card-title { font-size: 18px; font-weight: 600; }
        
        /* Buttons */
        .btn { padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, #1e88e5, #1565c0); color: #fff; }
        .btn-primary:hover { background: linear-gradient(135deg, #1565c0, #0d47a1); }
        .btn-warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .btn-warning:hover { background: rgba(245, 158, 11, 0.3); }
        .btn-danger { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .btn-danger:hover { background: rgba(239, 68, 68, 0.3); }
        .btn-secondary { background: rgba(255,255,255,0.1); color: #94a3b8; }
        .btn-secondary:hover { background: rgba(255,255,255,0.15); color: #fff; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        /* Table */
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .table th { color: #94a3b8; font-weight: 500; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .table td { font-size: 14px; }
        .table tr:hover { background: rgba(30, 136, 229, 0.05); }
        
        /* Badge */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
        .badge-admin { background: rgba(139, 92, 246, 0.2); color: #a78bfa; }
        .badge-user { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .badge-active { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .badge-inactive { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        
        /* Form */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 8px; }
        .form-input, .form-select { width: 100%; padding: 12px 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-size: 14px; transition: all 0.2s; }
        .form-input:focus, .form-select:focus { outline: none; border-color: #1e88e5; background: rgba(30, 136, 229, 0.1); }
        .form-input::placeholder { color: #64748b; }
        .form-select option { background: #0d2137; color: #fff; }
        .form-hint { font-size: 11px; color: #64748b; margin-top: 5px; }
        .form-error { color: #f87171; font-size: 12px; margin-top: 5px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-actions { display: flex; gap: 12px; margin-top: 30px; }
        
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input[type="checkbox"] { width: 18px; height: 18px; accent-color: #1e88e5; }
        
        /* Alert */
        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; }
        .alert-error { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }
        
        /* Actions */
        .actions { display: flex; gap: 8px; }
        
        /* Empty State */
        .empty-state { text-align: center; padding: 40px; color: #94a3b8; }
        .empty-state i { font-size: 48px; margin-bottom: 15px; opacity: 0.5; display: block; }
        
        /* Pagination */
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
        .pagination a, .pagination span { padding: 8px 12px; border-radius: 6px; background: rgba(255,255,255,0.05); color: #94a3b8; text-decoration: none; font-size: 13px; }
        .pagination a:hover { background: rgba(30, 136, 229, 0.2); color: #64b5f6; }
        .pagination .active span { background: #1e88e5; color: #fff; }
        
        /* Overlay */
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .sidebar-overlay.active { display: block; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .user-name { display: none; }
            .form-row { grid-template-columns: 1fr; }
            .table { display: block; overflow-x: auto; }
        }
        
        @yield('styles')
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <div class="logo-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="logo-text">KORLANTAS<span>Dashboard Portal</span></div>
        </div>
        
        <ul class="nav-menu">
            {{-- Dashboard Menus --}}
            @php $menus = \App\Models\Menu::active()->get(); @endphp
            @if($menus->count() > 0)
                @foreach($menus as $m)
                <li class="nav-item">
                    <a href="{{ route('view.menu', $m) }}" class="nav-link {{ (isset($activeMenu) && $activeMenu->id == $m->id) ? 'active' : '' }}">
                        <i class="{{ $m->icon }}"></i> {{ $m->name }}
                    </a>
                </li>
                @endforeach
            @else
                <li class="nav-item">
                    <a href="/dashboard" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
                </li>
            @endif
            
            {{-- Admin Section --}}
            @if(Auth::user()->role === 'admin')
            <li class="nav-divider"></li>
            <li class="nav-label">Admin Panel</li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Kelola User
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('menus.index') }}" class="nav-link {{ request()->routeIs('menus.*') ? 'active' : '' }}">
                    <i class="fas fa-th-list"></i> Kelola Menu
                </a>
            </li>
            @endif
        </ul>
    </aside>

    <main class="main-content" id="mainContent">
        <header class="header">
            <div class="header-left">
                <button class="btn-toggle" onclick="toggleSidebar()"><i class="fas fa-bars" id="toggleIcon"></i></button>
                <div class="header-title">
                    <h1>@yield('page-title', 'Admin')</h1>
                    <p>@yield('page-subtitle', 'Panel Administrasi')</p>
                </div>
            </div>
            <div class="header-actions">
                <div class="user-info">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                    <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </header>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif
            
            @yield('content')
        </div>
    </main>

    @yield('tableau-scripts')

    <script>
        let sidebarOpen = true;
        function toggleSidebar() {
            sidebarOpen = !sidebarOpen;
            document.getElementById('sidebar').classList.toggle('collapsed', !sidebarOpen);
            document.getElementById('mainContent').classList.toggle('expanded', !sidebarOpen);
            const icon = document.getElementById('toggleIcon');
            icon.classList.toggle('fa-bars', !sidebarOpen);
            icon.classList.toggle('fa-times', sidebarOpen);
            
            if (window.innerWidth <= 768) {
                document.getElementById('sidebarOverlay').classList.toggle('active', sidebarOpen);
                document.getElementById('sidebar').classList.toggle('open', sidebarOpen);
            }
        }
        
        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.style.display = 'none';
        }
        setTimeout(hideLoading, 5000);
    </script>
    @yield('scripts')
</body>
</html>
