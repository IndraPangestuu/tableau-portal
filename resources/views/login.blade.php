<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Portal Dashboard Korlantas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1; --primary-light: #818cf8; --primary-dark: #4f46e5;
            --accent: #22d3ee; --bg-dark: #0f0f1a;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh; margin: 0;
            display: flex; justify-content: center; align-items: center;
            background: var(--bg-dark); overflow: hidden;
        }
        
        .bg-animated {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 0;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(99, 102, 241, 0.2) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(34, 211, 238, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 40% 60%, rgba(139, 92, 246, 0.1) 0%, transparent 60%);
            animation: bgPulse 10s ease-in-out infinite;
        }
        @keyframes bgPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        
        .particles { position: fixed; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; z-index: 1; overflow: hidden; }
        .particle {
            position: absolute; width: 4px; height: 4px;
            background: var(--accent); border-radius: 50%; opacity: 0.3;
            animation: float 20s infinite linear;
        }
        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.4; } 90% { opacity: 0.4; }
            100% { transform: translateY(-100vh) rotate(720deg); opacity: 0; }
        }
        
        .orb {
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.4;
            animation: orbFloat 15s ease-in-out infinite;
        }
        .orb-1 { width: 400px; height: 400px; background: var(--primary); top: -150px; left: -100px; }
        .orb-2 { width: 300px; height: 300px; background: var(--accent); bottom: -100px; right: -80px; animation-delay: 3s; }
        .orb-3 { width: 200px; height: 200px; background: #8b5cf6; top: 50%; left: 60%; animation-delay: 6s; }
        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        
        .login-container {
            position: relative; z-index: 10; width: 100%; max-width: 440px; padding: 20px;
            animation: containerFadeIn 0.8s ease-out;
        }
        @keyframes containerFadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        
        .login-box {
            background: rgba(15, 15, 30, 0.8); backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px; padding: 48px 40px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5), 0 0 100px rgba(99, 102, 241, 0.1);
            position: relative; overflow: hidden;
        }
        .login-box::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
        }
        
        .logo-section { text-align: center; margin-bottom: 36px; }
        .logo-icon {
            width: 80px; height: 80px; margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 20px; display: flex; align-items: center; justify-content: center;
            font-size: 36px; color: #fff;
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4);
            animation: logoGlow 3s ease-in-out infinite;
            position: relative; overflow: hidden;
        }
        .logo-icon::after {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.15), transparent);
            animation: logoShine 4s infinite;
        }
        @keyframes logoGlow { 0%, 100% { box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4); } 50% { box-shadow: 0 15px 60px rgba(34, 211, 238, 0.5); } }
        @keyframes logoShine { 0% { transform: translateX(-100%) rotate(45deg); } 100% { transform: translateX(100%) rotate(45deg); } }
        
        .logo-text { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .logo-text span { display: block; font-size: 12px; font-weight: 400; color: var(--accent); letter-spacing: 3px; text-transform: uppercase; margin-top: 6px; }
        
        .login-title { font-size: 18px; color: #94a3b8; font-weight: 400; margin-top: 8px; }
        
        .error-box {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5;
            padding: 14px 18px; margin-bottom: 24px; border-radius: 12px;
            font-size: 14px; display: flex; align-items: center; gap: 10px;
            animation: shake 0.5s ease;
        }
        @keyframes shake { 20%, 60% { transform: translateX(-5px); } 40%, 80% { transform: translateX(5px); } }
        
        .form-group { margin-bottom: 20px; position: relative; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: #94a3b8; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .input-wrapper { position: relative; }
        .input-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 16px; transition: color 0.3s; }
        
        .form-input {
            width: 100%; padding: 16px 16px 16px 48px;
            background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px; color: #fff; font-size: 15px; font-family: 'Inter', sans-serif;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus {
            outline: none; border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }
        .form-input:focus + .input-icon, .input-wrapper:focus-within .input-icon { color: var(--accent); }
        
        .btn-login {
            width: 100%; padding: 16px; font-size: 15px; font-weight: 600;
            border-radius: 12px; border: none; color: #fff; cursor: pointer;
            margin-top: 8px; position: relative; overflow: hidden;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            font-family: 'Inter', sans-serif;
        }
        .btn-login::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-login:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(99, 102, 241, 0.5); }
        .btn-login:hover::before { left: 100%; }
        .btn-login:active { transform: translateY(-1px); }
        
        .btn-login .ripple {
            position: absolute; background: rgba(255, 255, 255, 0.5);
            border-radius: 50%; transform: scale(0); animation: rippleAnim 0.6s linear;
        }
        @keyframes rippleAnim { to { transform: scale(4); opacity: 0; } }
        
        .footer-text { text-align: center; margin-top: 28px; font-size: 13px; color: #475569; }
        .footer-text a { color: var(--accent); text-decoration: none; }
    </style>
</head>
<body>
    <div class="bg-animated"></div>
    <div class="particles" id="particles"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="login-container">
        <div class="login-box">
            <div class="logo-section">
                <div class="logo-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="logo-text">KORLANTAS<span>Dashboard Portal</span></div>
                <p class="login-title">Masuk ke akun Anda</p>
            </div>

            @if($errors->has('loginError'))
                <div class="error-box">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first('loginError') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Username / NRP</label>
                    <div class="input-wrapper">
                        <input type="text" name="login" class="form-input" placeholder="Masukkan username atau NRP" value="{{ old('login') }}" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" class="form-input" placeholder="Masukkan password" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <p class="footer-text">Portal Dashboard Korlantas &copy; {{ date('Y') }}</p>
        </div>
    </div>

    <script>
        // Create particles
        const container = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';
            container.appendChild(particle);
        }

        // Ripple effect
        document.getElementById("btnLogin").addEventListener("click", function(e) {
            const btn = this;
            const rect = btn.getBoundingClientRect();
            const ripple = document.createElement("span");
            const size = Math.max(btn.clientWidth, btn.clientHeight);
            ripple.style.width = ripple.style.height = size + "px";
            ripple.style.left = (e.clientX - rect.left - size / 2) + "px";
            ripple.style.top = (e.clientY - rect.top - size / 2) + "px";
            ripple.classList.add("ripple");
            btn.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    </script>
</body>
</html>