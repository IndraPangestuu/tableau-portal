<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appSettings['app_name'] ?? 'Portal Dashboard' }} - {{ $appSettings['footer_text'] ?? 'Korlantas POLRI' }}</title>
    @if(isset($appSettings['app_favicon']) && $appSettings['app_favicon'])
    <link rel="icon" type="image/png" href="{{ url($appSettings['app_favicon']) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a1628 0%, #1a365d 50%, #0d2137 100%);
            min-height: 100vh;
            color: #fff;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            background: rgba(10, 22, 40, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
        }

        .logo-text span {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: #64b5f6;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #fff;
        }

        .btn-login {
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: #fff;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(30, 136, 229, 0.3);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 120px 50px 80px;
            position: relative;
        }

        .hero-content {
            max-width: 600px;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(30, 136, 229, 0.2);
            border: 1px solid rgba(30, 136, 229, 0.3);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            color: #64b5f6;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .hero h1 span {
            background: linear-gradient(135deg, #1e88e5, #64b5f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 18px;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: #fff;
            padding: 14px 32px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(30, 136, 229, 0.4);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: #fff;
            padding: 14px 32px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.15);
        }

        /* Hero Image/Illustration */
        .hero-visual {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            width: 45%;
            max-width: 600px;
        }

        .dashboard-preview {
            background: linear-gradient(135deg, #0d2137, #132f4c);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        }

        .preview-header {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }

        .preview-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .preview-dot.red { background: #ef4444; }
        .preview-dot.yellow { background: #f59e0b; }
        .preview-dot.green { background: #10b981; }

        .preview-content {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .preview-card {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 15px;
        }

        .preview-card.large {
            grid-column: span 2;
            height: 120px;
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.2), rgba(30, 136, 229, 0.05));
        }

        .preview-card h4 {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .preview-card .value {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
        }

        .preview-card .value.blue { color: #1e88e5; }
        .preview-card .value.orange { color: #f59e0b; }
        .preview-card .value.green { color: #10b981; }

        /* Features Section */
        .features {
            padding: 80px 50px;
            background: rgba(0,0,0,0.2);
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .section-title p {
            color: #94a3b8;
            font-size: 16px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: linear-gradient(135deg, #0d2137, #132f4c);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 30px;
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: rgba(30, 136, 229, 0.3);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.2), rgba(30, 136, 229, 0.05));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #1e88e5;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            padding: 40px 50px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-text {
            color: #64748b;
            font-size: 13px;
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: #fff;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-visual {
                display: none;
            }
            .hero-content {
                max-width: 100%;
                text-align: center;
            }
            .hero-buttons {
                justify-content: center;
            }
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }
            .nav-links {
                display: none;
            }
            .hero {
                padding: 100px 20px 60px;
            }
            .hero h1 {
                font-size: 32px;
            }
            .features {
                padding: 60px 20px;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
            .footer {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
        }

        /* Floating Elements */
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.1), transparent);
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -100px;
        }

        .shape-2 {
            width: 300px;
            height: 300px;
            bottom: -50px;
            left: -50px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon">
                @if(isset($appSettings['app_logo']) && $appSettings['app_logo'])
                <img src="{{ url($appSettings['app_logo']) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                @else
                <i class="fas fa-shield-alt"></i>
                @endif
            </div>
            <div class="logo-text">
                {{ $appSettings['app_name'] ?? 'KORLANTAS' }}
                <span>{{ $appSettings['app_subtitle'] ?? 'Dashboard Portal' }}</span>
            </div>
        </div>
        <div class="nav-links">
            <a href="#features">Fitur</a>
            <a href="#about">Tentang</a>
            <a href="{{ route('login') }}" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
        </div>

        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-chart-line"></i> Dashboard Analytics Platform
            </div>
            <h1>{{ $appSettings['app_name'] ?? 'Portal Dashboard' }} <span>{{ $appSettings['footer_text'] ?? 'Korlantas POLRI' }}</span></h1>
            <p>Sistem informasi dashboard terintegrasi untuk monitoring dan analisis data lalu lintas secara real-time. Akses data ETLE, tilang, dan statistik kecelakaan dalam satu platform.</p>
            <div class="hero-buttons">
                <a href="{{ route('login') }}" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard
                </a>
                <a href="#features" class="btn-secondary">
                    <i class="fas fa-info-circle"></i> Pelajari Lebih Lanjut
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="dashboard-preview">
                <div class="preview-header">
                    <div class="preview-dot red"></div>
                    <div class="preview-dot yellow"></div>
                    <div class="preview-dot green"></div>
                </div>
                <div class="preview-content">
                    <div class="preview-card">
                        <h4>ETLE</h4>
                        <div class="value blue">68.392</div>
                    </div>
                    <div class="preview-card">
                        <h4>NON ETLE</h4>
                        <div class="value orange">24.747</div>
                    </div>
                    <div class="preview-card">
                        <h4>TOTAL TILANG</h4>
                        <div class="value green">93.139</div>
                    </div>
                    <div class="preview-card">
                        <h4>TEGURAN</h4>
                        <div class="value">232.581</div>
                    </div>
                    <div class="preview-card large"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-title">
            <h2>Fitur Utama</h2>
            <p>Platform dashboard dengan berbagai fitur untuk kebutuhan analisis data</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3>Dashboard Interaktif</h3>
                <p>Visualisasi data dengan grafik dan chart interaktif dari Tableau untuk analisis mendalam.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h3>Data Real-time</h3>
                <p>Monitoring data secara real-time dengan update otomatis dari berbagai sumber data.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Akses Aman</h3>
                <p>Sistem autentikasi dengan role-based access control untuk keamanan data.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Responsive Design</h3>
                <p>Akses dashboard dari berbagai perangkat dengan tampilan yang optimal.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3>Konfigurasi Mudah</h3>
                <p>Admin panel untuk mengelola menu dashboard dan user dengan mudah.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-file-export"></i>
                </div>
                <h3>Export Data</h3>
                <p>Kemampuan export data dan laporan dalam berbagai format.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="about">
        <div class="footer-text">
            &copy; {{ date('Y') }} {{ $appSettings['app_name'] ?? 'Portal Dashboard' }} {{ $appSettings['footer_text'] ?? 'Korlantas POLRI' }}. All rights reserved.
        </div>
        <div class="footer-links">
            <a href="#">Kebijakan Privasi</a>
            <a href="#">Syarat & Ketentuan</a>
            <a href="#">Kontak</a>
        </div>
    </footer>
</body>
</html>
