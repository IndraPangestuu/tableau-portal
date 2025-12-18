<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Portal Korlantas</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1; --primary-light: #818cf8; --primary-dark: #4f46e5;
            --accent: #22d3ee; --accent-glow: rgba(34, 211, 238, 0.4);
            --bg-dark: rgb(21, 19, 110); --bg-card: rgba(21, 19, 110, 0.8); --bg-sidebar: rgba(21, 19, 110, 0.95);
            --border: rgba(255, 255, 255, 0.08); --text: #e2e8f0; --text-muted: #94a3b8;
            --success: #10b981; --warning: #f59e0b; --danger: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); min-height: 100vh; color: var(--text); overflow-x: hidden; }
        
        .bg-animated {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: -1;
            background: radial-gradient(ellipse at 20% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 80%, rgba(34, 211, 238, 0.1) 0%, transparent 50%),
                        radial-gradient(ellipse at 50% 50%, rgba(139, 92, 246, 0.05) 0%, transparent 70%);
            animation: bgPulse 15s ease-in-out infinite;
        }
        @keyframes bgPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        
        .particles { position: fixed; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; z-index: -1; overflow: hidden; }
        .particle { position: absolute; width: 3px; height: 3px; background: var(--accent); border-radius: 50%; opacity: 0.2; animation: float 25s infinite linear; }
        @keyframes float { 0% { transform: translateY(100vh) rotate(0deg); opacity: 0; } 10% { opacity: 0.3; } 90% { opacity: 0.3; } 100% { transform: translateY(-100vh) rotate(720deg); opacity: 0; } }
        
        .sidebar {
            position: fixed; left: 0; top: 0; width: 280px; height: 100vh;
            background: var(--bg-sidebar); backdrop-filter: blur(20px);
            border-right: 1px solid var(--border); padding: 24px 0; z-index: 100;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
            overflow-y: auto; overflow-x: hidden;
        }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.3); border-radius: 3px; }
        .sidebar::-webkit-scrollbar-thumb:hover { background: rgba(99, 102, 241, 0.5); }
        .sidebar::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, var(--accent), transparent); animation: shimmer 3s infinite; }
        @keyframes shimmer { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }
        .sidebar.collapsed { transform: translateX(-280px); }
        
        .logo { display: flex; align-items: center; gap: 14px; padding: 0 24px 28px; border-bottom: 1px solid var(--border); margin-bottom: 24px; }
        .logo-icon {
            width: 50px; height: 50px; background: #ffffff;
            border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); position: relative; overflow: hidden;
        }
        .logo-icon::after { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent); animation: logoShine 4s infinite; }
        @keyframes logoGlow { 0%, 100% { box-shadow: 0 8px 32px rgba(99, 102, 241, 0.3); } 50% { box-shadow: 0 8px 48px rgba(34, 211, 238, 0.4); } }
        @keyframes logoShine { 0% { transform: translateX(-100%) rotate(45deg); } 100% { transform: translateX(100%) rotate(45deg); } }
        .logo-text { font-size: 20px; font-weight: 700; letter-spacing: -0.5px; }
        .logo-text span { display: block; font-size: 11px; font-weight: 400; color: var(--accent); letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }
        
        .nav-menu { list-style: none; padding: 0 16px; }
        .nav-item { margin-bottom: 6px; }
        .nav-link {
            display: flex; align-items: center; gap: 14px; padding: 14px 18px; color: var(--text-muted);
            text-decoration: none; border-radius: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 14px; font-weight: 500; position: relative; overflow: hidden;
        }
        .nav-link::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, var(--primary), var(--accent)); opacity: 0; transition: opacity 0.3s; z-index: -1; }
        .nav-link:hover { color: #fff; transform: translateX(4px); }
        .nav-link:hover::before { opacity: 0.15; }
        .nav-link.active { color: #fff; background: linear-gradient(90deg, rgba(99, 102, 241, 0.2), transparent); border-left: 3px solid var(--accent); box-shadow: 0 4px 20px rgba(99, 102, 241, 0.2); }
        .nav-link i { width: 22px; text-align: center; font-size: 16px; transition: transform 0.3s; }
        .nav-link:hover i { transform: scale(1.2); }
        .nav-divider { border-top: 1px solid var(--border); margin: 20px 16px; }
        .nav-label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; padding: 12px 18px 8px; font-weight: 600; }
        
        /* Submenu styles */
        .nav-parent { justify-content: space-between; }
        .submenu-arrow { font-size: 12px; transition: transform 0.3s; width: auto !important; }
        .has-submenu.open .submenu-arrow { transform: rotate(180deg); }
        .submenu { list-style: none; padding: 0; max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; background: rgba(0, 0, 0, 0.15); border-radius: 8px; margin: 4px 0; }
        .has-submenu.open .submenu { max-height: 500px; }
        .submenu .nav-link { padding: 12px 18px 12px 48px; font-size: 13px; }
        .submenu .nav-link i { font-size: 14px; }
        
        .main-content { margin-left: 280px; min-height: 100vh; transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .main-content.expanded { margin-left: 0; }
        
        .header {
            background: rgba(15, 15, 30, 0.8); backdrop-filter: blur(20px);
            padding: 16px 32px; display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 50;
        }
        .header-left { display: flex; align-items: center; gap: 20px; }
        .btn-toggle {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(34, 211, 238, 0.1));
            border: 1px solid rgba(99, 102, 241, 0.3); color: var(--accent);
            width: 44px; height: 44px; border-radius: 12px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; font-size: 18px;
            transition: all 0.3s; position: relative; overflow: hidden;
        }
        .btn-toggle:hover { transform: scale(1.05); box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3); }
        .header-title h1 { font-size: 22px; font-weight: 700; background: linear-gradient(90deg, #fff, var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .header-title p { font-size: 13px; color: var(--text-muted); margin-top: 2px; }
        
        .header-actions { display: flex; align-items: center; gap: 16px; }
        .user-info { display: flex; align-items: center; gap: 12px; padding: 8px 16px; background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px solid var(--border); }
        .user-avatar {
            width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        .user-details { display: flex; flex-direction: column; }
        .user-name { font-size: 14px; font-weight: 600; }
        .user-role { font-size: 11px; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; }
        .btn-logout {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
            color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 10px 18px; border-radius: 10px; cursor: pointer; font-size: 13px; font-weight: 500;
            transition: all 0.3s; display: flex; align-items: center; gap: 8px;
        }
        .btn-logout:hover { background: rgba(239, 68, 68, 0.3); transform: translateY(-2px); box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2); }

        .embed-container {
            background: linear-gradient(135deg, rgba(20, 20, 40, 0.9), rgba(15, 15, 30, 0.95));
            border: none; border-radius: 0; overflow: hidden; margin: 0; padding: 0;
            box-shadow: none;
            animation: cardFadeIn 0.6s ease-out;
            height: calc(100vh - 77px);
            width: 100%;
        }
        @keyframes cardFadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .embed-body { position: relative; height: 100%; width: 100%; overflow: hidden; }
        .embed-body iframe, .embed-body tableau-viz { display: block; width: 100%; height: 100%; border: none; }
        tableau-viz { min-height: 100%; width: 100% !important; }
        
        .loading-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(15, 15, 30, 0.98), rgba(20, 20, 40, 0.98));
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px;
        }
        .spinner-container { position: relative; width: 80px; height: 80px; }
        .spinner {
            width: 80px; height: 80px; border: 3px solid transparent;
            border-top-color: var(--accent); border-radius: 50%;
            animation: spin 1s linear infinite; position: absolute;
        }
        .spinner::before, .spinner::after {
            content: ''; position: absolute; border-radius: 50%;
            border: 3px solid transparent;
        }
        .spinner::before { top: 8px; left: 8px; right: 8px; bottom: 8px; border-top-color: var(--primary); animation: spin 1.5s linear infinite reverse; }
        .spinner::after { top: 18px; left: 18px; right: 18px; bottom: 18px; border-top-color: var(--primary-light); animation: spin 2s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { color: var(--text-muted); font-size: 14px; animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%, 100% { opacity: 0.5; } 50% { opacity: 1; } }
        
        .error-box {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5;
            padding: 32px; border-radius: 16px; text-align: center; margin: 24px;
        }
        .error-box h3 { font-size: 18px; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .error-box i { color: var(--danger); }
        
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 99; }
        .sidebar-overlay.active { display: block; }
        
        @media (min-height: 900px) { .embed-container { height: calc(100vh - 120px); } }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-280px); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .user-details { display: none; }
            .header { padding: 8px 12px; flex-wrap: wrap; gap: 8px; }
            .header-left { gap: 10px; }
            .header-title h1 { font-size: 14px; }
            .header-title p { font-size: 10px; display: none; }
            .btn-toggle { width: 36px; height: 36px; font-size: 14px; }
            .user-avatar { width: 32px; height: 32px; font-size: 12px; }
            .user-info { padding: 4px 8px; }
            .btn-logout { padding: 6px 10px; font-size: 11px; }
            .btn-logout span { display: none; }
            .embed-container { margin: 0; height: calc(100vh - 60px); }
            .embed-body { height: 100%; }
            tableau-viz, .embed-body iframe { height: 100% !important; min-height: calc(100vh - 60px); }
        }
        
        /* Page Transition */
        .page-transition { animation: pageIn 0.4s ease-out; }
        @keyframes pageIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Ripple Effect */
        .ripple { position: relative; overflow: hidden; }
        .ripple-effect { position: absolute; border-radius: 50%; background: rgba(255, 255, 255, 0.3); transform: scale(0); animation: rippleAnim 0.6s linear; pointer-events: none; }
        @keyframes rippleAnim { to { transform: scale(4); opacity: 0; } }
        
        /* Swipe Indicator */
        .swipe-indicator { position: fixed; left: 0; top: 50%; transform: translateY(-50%); width: 20px; height: 100px; z-index: 98; display: none; }
        @media (max-width: 768px) { .swipe-indicator { display: block; } }
        .swipe-indicator::after { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 4px; height: 40px; background: linear-gradient(180deg, transparent, var(--accent), transparent); border-radius: 2px; opacity: 0.3; animation: swipeHint 2s ease-in-out infinite; }
        @keyframes swipeHint { 0%, 100% { opacity: 0.1; transform: translateY(-50%) translateX(0); } 50% { opacity: 0.4; transform: translateY(-50%) translateX(8px); } }
        
        @yield('styles')
    </style>
</head>
<body>
    <div class="bg-animated"></div>
    <div class="particles" id="particles"></div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="swipe-indicator" id="swipeIndicator"></div>
    
    @include('components.toast')
    @include('components.search-modal')

    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <div class="logo-icon">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; padding: 4px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <i class="fas fa-shield-alt" style="display: none; color: #6366f1;"></i>
            </div>
            <div class="logo-text">DAKGAR LANTAS<span>Dashboard Portal</span></div>
        </div>
        
        <ul class="nav-menu">
            @if(isset($sidebarMenus) && $sidebarMenus->count() > 0)
                @foreach($sidebarMenus as $m)
                    @if($m->activeChildren->count() > 0)
                    {{-- Parent menu with children --}}
                    <li class="nav-item has-submenu">
                        <a href="javascript:void(0)" class="nav-link nav-parent" onclick="toggleSubmenu(this)">
                            <i class="{{ $m->icon }}"></i> {{ $m->name }}
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            @foreach($m->activeChildren as $child)
                            <li class="nav-item">
                                <a href="{{ route('view.menu', $child) }}" class="nav-link {{ (isset($activeMenu) && $activeMenu->id == $child->id) ? 'active' : '' }}">
                                    <i class="{{ $child->icon }}"></i> {{ $child->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @else
                    {{-- Single menu without children --}}
                    <li class="nav-item">
                        <a href="{{ $m->tableau_view_path ? route('view.menu', $m) : 'javascript:void(0)' }}" class="nav-link {{ (isset($activeMenu) && $activeMenu->id == $m->id) ? 'active' : '' }}">
                            <i class="{{ $m->icon }}"></i> {{ $m->name }}
                        </a>
                    </li>
                    @endif
                @endforeach
            @else
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
                </li>
            @endif
            
            {{-- Favorites & Recent Section --}}
            @auth
            @php
                $userFavorites = auth()->user()->favorites()->with('menu')->limit(5)->get();
                $recentDashboards = \App\Models\RecentDashboard::getRecent(auth()->user()->id_user, 5);
            @endphp
            
            @if($userFavorites->count() > 0)
            <li class="nav-divider"></li>
            <li class="nav-label">Favorit</li>
            @foreach($userFavorites as $fav)
            <li class="nav-item">
                <a href="{{ route('view.menu', $fav->menu) }}" class="nav-link {{ (isset($activeMenu) && $activeMenu->id == $fav->menu_id) ? 'active' : '' }}">
                    <i class="fas fa-star" style="color: #fbbf24;"></i> {{ Str::limit($fav->menu->name, 20) }}
                </a>
            </li>
            @endforeach
            @endif
            
            @if($recentDashboards->count() > 0)
            <li class="nav-divider"></li>
            <li class="nav-label">Terakhir Diakses</li>
            @foreach($recentDashboards as $recent)
            <li class="nav-item">
                <a href="{{ route('view.menu', $recent->menu) }}" class="nav-link {{ (isset($activeMenu) && $activeMenu->id == $recent->menu_id) ? 'active' : '' }}">
                    <i class="fas fa-history" style="color: var(--text-muted);"></i> {{ Str::limit($recent->menu->name, 20) }}
                </a>
            </li>
            @endforeach
            @endif
            @endauth
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
                <button class="btn-toggle" onclick="openSearch()" title="Cari Menu (Ctrl+K)" style="margin-right: 8px;"><i class="fas fa-search"></i></button>
                <a href="{{ route('profile.show') }}" class="user-info" style="text-decoration: none;">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                    <div class="user-details">
                        <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>
                        <span class="user-role">{{ ucfirst(Auth::user()->role ?? 'User') }}</span>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></button>
                </form>
            </div>
        </header>

        @yield('content')
    </main>

    @yield('tableau-scripts')
    <script>
        // Particles
        function createParticles() {
            const container = document.getElementById('particles');
            for (let i = 0; i < 25; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 25 + 's';
                particle.style.animationDuration = (20 + Math.random() * 15) + 's';
                container.appendChild(particle);
            }
        }
        createParticles();
        
        // Sidebar Toggle
        let sidebarOpen = window.innerWidth > 768;
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
            // Save preference
            localStorage.setItem('sidebarOpen', sidebarOpen);
        }
        
        // Restore sidebar state
        const savedSidebarState = localStorage.getItem('sidebarOpen');
        if (savedSidebarState !== null && window.innerWidth > 768) {
            sidebarOpen = savedSidebarState === 'true';
            if (!sidebarOpen) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('expanded');
                document.getElementById('toggleIcon').classList.replace('fa-times', 'fa-bars');
            }
        }
        
        // Loading
        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) { overlay.style.opacity = '0'; setTimeout(() => overlay.remove(), 500); }
        }
        setTimeout(hideLoading, 5000);
        
        // Submenu
        function toggleSubmenu(el) {
            const parent = el.closest('.has-submenu');
            parent.classList.toggle('open');
        }
        
        // Auto-open submenu if child is active
        document.querySelectorAll('.submenu .nav-link.active').forEach(el => {
            el.closest('.has-submenu').classList.add('open');
        });
        
        // Swipe Gesture for Mobile
        let touchStartX = 0;
        let touchEndX = 0;
        const swipeThreshold = 80;
        
        document.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        document.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
        
        function handleSwipe() {
            const swipeDistance = touchEndX - touchStartX;
            
            // Swipe right to open sidebar (from left edge)
            if (swipeDistance > swipeThreshold && touchStartX < 50 && !sidebarOpen) {
                toggleSidebar();
            }
            // Swipe left to close sidebar
            else if (swipeDistance < -swipeThreshold && sidebarOpen && window.innerWidth <= 768) {
                toggleSidebar();
            }
        }
        
        // Ripple Effect
        document.querySelectorAll('.nav-link, .btn-toggle, .btn-logout').forEach(el => {
            el.classList.add('ripple');
            el.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                ripple.className = 'ripple-effect';
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });
        
        // Page visibility - pause animations when hidden
        document.addEventListener('visibilitychange', () => {
            const particles = document.getElementById('particles');
            const bgAnimated = document.querySelector('.bg-animated');
            if (document.hidden) {
                particles.style.animationPlayState = 'paused';
                bgAnimated.style.animationPlayState = 'paused';
            } else {
                particles.style.animationPlayState = 'running';
                bgAnimated.style.animationPlayState = 'running';
            }
        });
    </script>
    @yield('scripts')
</body>
</html>