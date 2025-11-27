<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Portal Korlantas</title>
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
        
        /* Embed Container */
        .embed-container {
            background: #0d2137; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px; overflow: hidden; margin: 20px;
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
        
        /* Overlay */
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .sidebar-overlay.active { display: block; }
        
        /* Responsive */
        @media (min-height: 900px) {
            .embed-body, .embed-body tableau-viz { height: calc(100vh - 120px); }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .user-name { display: none; }
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
                    <a href="/dashboard" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
                </li>
            @endif
        </ul>
    </aside>

    <main class="main-content" id="mainContent">
        <header class="header">
            <div class="header-left">
                <button class="btn-toggle" onclick="toggleSidebar()"><i class="fas fa-bars" id="toggleIcon"></i></button>
                <div class="header-title">
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <p>@yield('page-subtitle', 'Selamat datang di Portal Dashboard Korlantas')</p>
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

        @yield('content')
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
