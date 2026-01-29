<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $appSettings['app_name'] ?? 'Dashboard SIDAK LANTAS' }}</title>
    
    <link rel="shortcut icon" href="{{ url('images/korlantas.png') }}" type="image/png">
    <link rel="icon" href="{{ url('images/korlantas.png') }}" type="image/png">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Montserrat", sans-serif;
        }

        body {
            background: #0b1120;
            min-height: 100vh;
            overflow: hidden;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Animated Background */
        .background-animation {
            position: fixed;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background: radial-gradient(circle at center, rgba(0, 58, 145, 0.4), rgba(0, 15, 40, 0.9));
            animation: rotateBg 30s linear infinite;
            z-index: -2;
        }

        @keyframes rotateBg {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #1A73E8;
            border-radius: 50%;
            opacity: 0.4;
            animation: floatParticle 20s infinite linear;
        }

        @keyframes floatParticle {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.4; }
            90% { opacity: 0.4; }
            100% { transform: translateY(-100vh) rotate(720deg); opacity: 0; }
        }

        /* Login Container */
        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 45px 40px;
            background: rgba(0, 30, 70, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(15px);
            box-shadow: 0 0 40px rgba(0, 70, 160, 0.4), 0 25px 50px rgba(0, 0, 0, 0.3);
            animation: fadeIn 1s ease-out;
            position: relative;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #1A73E8, #ffffff, #1A73E8, transparent);
            border-radius: 20px 20px 0 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .logo-area {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }

        .logo-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 30px rgba(26, 115, 232, 0.6);
            animation: logoGlow 3s ease-in-out infinite;
            overflow: hidden;
        }

        @keyframes logoGlow {
            0%, 100% { box-shadow: 0 0 30px rgba(26, 115, 232, 0.6); }
            50% { box-shadow: 0 0 50px rgba(255, 255, 255, 0.4); }
        }

        .title {
            text-align: center;
            font-weight: 800;
            font-size: 24px;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* Error Box */
        .error-box {
            background: rgba(255, 0, 0, 0.15);
            padding: 12px;
            border-left: 4px solid #ff4444;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 13px;
            color: #ff8888;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        /* Input Group */
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group input {
            width: 100%;
            padding: 16px 14px 16px 48px;
            border: none;
            border-bottom: 2px solid rgba(26, 115, 232, 0.5);
            background: rgba(255, 255, 255, 0.03);
            color: white;
            outline: none;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s;
        }

        .input-group label {
            position: absolute;
            left: 48px;
            top: 16px;
            color: #888;
            font-size: 14px;
            pointer-events: none;
            transition: all 0.3s;
        }

        .input-group input:focus ~ label,
        .input-group input:not(:placeholder-shown) ~ label {
            top: -12px;
            left: 10px;
            font-size: 12px;
            color: #1A73E8;
            font-weight: 700;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #1A73E8;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #003A91, #1A73E8);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: 0.3s;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(26, 115, 232, 0.5);
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 1px;
        }

        @media (max-width: 480px) {
            .login-container { width: 90%; padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <div class="background-animation"></div>
    <div class="particles" id="particles"></div>

    <div class="login-container">
        <div class="logo-area">
            <div class="logo-circle">
                @if(isset($appSettings['app_logo']) && $appSettings['app_logo'])
                    <img src="{{ url($appSettings['app_logo']) }}" alt="Logo" style="width: 75%; height: 75%; object-fit: contain;">
                @else
                    <i class="fas fa-shield-alt" style="font-size: 40px; color: #1A73E8;"></i>
                @endif
            </div>
        </div>

        <h1 class="title">{{ $appSettings['app_name'] ?? 'DASHBOARD SIDAK LANTAS' }}</h1>
        <p class="subtitle">Silakan masuk untuk melanjutkan</p>

        @if($errors->has('loginError'))
            <div class="error-box">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first('loginError') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="text" name="login" id="login" placeholder=" " value="{{ old('login') }}" required>
                <i class="fas fa-user input-icon"></i>
                <label for="login">Username / NRP</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="password" placeholder=" " required>
                <i class="fas fa-lock input-icon"></i>
                <label for="password">Password</label>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>

        <p class="footer-text">
            {{ $appSettings['footer_text'] ?? 'KORLANTAS POLRI' }} &copy; {{ date('Y') }}
        </p>
    </div>

    <script>
        // Generate Particles
        const container = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + '%';
            p.style.animationDelay = Math.random() * 20 + 's';
            p.style.animationDuration = (15 + Math.random() * 10) + 's';
            container.appendChild(p);
        }
    </script>
</body>
</html>