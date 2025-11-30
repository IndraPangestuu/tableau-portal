<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Portal Korlantas</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1; --primary-light: #818cf8; --primary-dark: #4f46e5;
            --accent: #22d3ee; --accent-glow: rgba(34, 211, 238, 0.4);
            --bg-dark: #0f0f1a; --bg-card: rgba(15, 15, 30, 0.8); --bg-sidebar: rgba(15, 15, 30, 0.95);
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
        }
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
        
        .content { padding: 28px 32px; }
        .content:has(.embed-container) { padding: 0; }
        .content .embed-container { border-radius: 0; border: none; height: calc(100vh - 77px); width: 100%; }

        /* Cards & Containers */
        .card {
            background: linear-gradient(135deg, rgba(20, 20, 40, 0.9), rgba(15, 15, 30, 0.95));
            border: 1px solid var(--border); border-radius: 16px; padding: 28px;
            backdrop-filter: blur(10px); position: relative; overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: cardFadeIn 0.6s ease-out;
        }
        .card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.5), transparent); }
        .card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 60px rgba(99, 102, 241, 0.1); border-color: rgba(99, 102, 241, 0.3); }
        @keyframes cardFadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .card-title { font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .card-title i { color: var(--accent); }
        
        .embed-container {
            background: linear-gradient(135deg, rgba(20, 20, 40, 0.9), rgba(15, 15, 30, 0.95));
            border: 1px solid var(--border); border-radius: 16px; overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            height: calc(100vh - 140px);
        }
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
            animation: spin 1s linear infinite;
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
        
        /* Buttons */
        .btn {
            padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer;
            font-size: 14px; font-weight: 600; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none; display: inline-flex; align-items: center; gap: 10px;
            position: relative; overflow: hidden;
        }
        .btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s; }
        .btn:hover::before { left: 100%; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4); }
        .btn-warning { background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.1)); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .btn-warning:hover { background: rgba(245, 158, 11, 0.3); transform: translateY(-2px); }
        .btn-danger { background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1)); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .btn-danger:hover { background: rgba(239, 68, 68, 0.3); transform: translateY(-2px); }
        .btn-secondary { background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--border); }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); color: #fff; transform: translateY(-2px); }
        .btn-sm { padding: 8px 14px; font-size: 12px; }
        
        /* Table */
        .table-container { overflow-x: auto; border-radius: 12px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 16px 18px; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { color: var(--text-muted); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; background: rgba(0,0,0,0.2); }
        .table td { font-size: 14px; }
        .table tr { transition: all 0.3s; }
        .table tr:hover { background: linear-gradient(90deg, rgba(99, 102, 241, 0.05), transparent); }
        
        /* Badge */
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-admin { background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(139, 92, 246, 0.1)); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.3); }
        .badge-user { background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(34, 197, 94, 0.1)); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
        .badge-active { background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(34, 197, 94, 0.1)); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
        .badge-inactive { background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1)); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

        /* Form */
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-input, .form-select {
            width: 100%; padding: 14px 18px;
            background: rgba(255,255,255,0.03); border: 1px solid var(--border);
            border-radius: 10px; color: var(--text); font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-input:focus, .form-select:focus {
            outline: none; border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        .form-input::placeholder { color: #64748b; }
        .form-select option { background: var(--bg-dark); color: var(--text); }
        .form-hint { font-size: 12px; color: #64748b; margin-top: 8px; }
        .form-error { color: #f87171; font-size: 12px; margin-top: 8px; display: flex; align-items: center; gap: 6px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .form-actions { display: flex; gap: 14px; margin-top: 32px; }
        .checkbox-group { display: flex; align-items: center; gap: 12px; }
        .checkbox-group input[type="checkbox"] { width: 20px; height: 20px; accent-color: var(--primary); cursor: pointer; }
        
        /* Alert */
        .alert {
            padding: 16px 24px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;
            display: flex; align-items: center; gap: 14px; position: relative;
            animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .alert-success { background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05)); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; }
        .alert-error { background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05)); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }
        .alert-close { background: none; border: none; color: inherit; cursor: pointer; margin-left: auto; opacity: 0.7; transition: opacity 0.3s; }
        .alert-close:hover { opacity: 1; }
        
        .actions { display: flex; gap: 10px; }
        .empty-state { text-align: center; padding: 60px 40px; color: var(--text-muted); }
        .empty-state i { font-size: 56px; margin-bottom: 20px; opacity: 0.3; display: block; background: linear-gradient(135deg, var(--primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        .pagination { display: flex; justify-content: center; gap: 6px; margin-top: 24px; }
        .pagination a, .pagination span { padding: 10px 14px; border-radius: 8px; background: rgba(255,255,255,0.03); color: var(--text-muted); text-decoration: none; font-size: 13px; border: 1px solid var(--border); transition: all 0.3s; }
        .pagination a:hover { background: rgba(99, 102, 241, 0.2); color: var(--accent); border-color: rgba(99, 102, 241, 0.3); }
        .pagination .active span { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; border-color: var(--primary); }
        
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 99; }
        .sidebar-overlay.active { display: block; }
        
        @media (min-height: 900px) { .embed-container { height: calc(100vh - 120px); } }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-280px); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .user-details { display: none; }
            .form-row { grid-template-columns: 1fr; }
            .table { display: block; overflow-x: auto; }
            .content { padding: 20px 16px; }
            .header { padding: 8px 12px; flex-wrap: wrap; gap: 8px; }
            .header-left { gap: 10px; }
            .header-title h1 { font-size: 14px; }
            .header-title p { font-size: 10px; display: none; }
            .btn-toggle { width: 36px; height: 36px; font-size: 14px; }
            .user-avatar { width: 32px; height: 32px; font-size: 12px; }
            .user-info { padding: 4px 8px; }
            .btn-logout { padding: 6px 10px; font-size: 11px; }
            .btn-logout span { display: none; }
            .content .embed-container { height: calc(100vh - 60px); }
            .embed-body { height: 100%; }
            tableau-viz, .embed-body iframe { height: 100% !important; min-height: calc(100vh - 60px); }
        }
        
        .animate-in { animation: fadeInUp 0.6s ease-out forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        
        @yield('styles')
    </style>
</head>
<body>
    <div class="bg-animated"></div>
    <div class="particles" id="particles"></div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <div class="logo-icon">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; padding: 4px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <i class="fas fa-shield-alt" style="display: none; color: #6366f1;"></i>
            </div>
            <div class="logo-text">DAKGAR LANTAS<span>Dashboard Portal</span></div>
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
                    <a href="/dashboard" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
                </li>
            @endif
            
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
            <li class="nav-item">
                <a href="{{ route('backups.index') }}" class="nav-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                    <i class="fas fa-database"></i> Backup Database
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
                    <div class="user-details">
                        <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>
                        <span class="user-role">{{ ucfirst(Auth::user()->role ?? 'User') }}</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></button>
                </form>
            </div>
        </header>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span> <button class="alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <span>{{ session('error') }}</span> <button class="alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button></div>
            @endif
            @yield('content')
        </div>
    </main>

    @yield('tableau-scripts')
    <script>
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
            if (overlay) { overlay.style.opacity = '0'; setTimeout(() => overlay.remove(), 500); }
        }
        setTimeout(hideLoading, 5000);
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('animate-in'); });
        }, { threshold: 0.1 });
        document.querySelectorAll('.card').forEach(el => observer.observe(el));
    </script>
    @yield('scripts')
</body>
</html>