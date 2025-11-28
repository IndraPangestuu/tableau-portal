<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard SIDAK LANTAS</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        }

        /* Animated Background */
        .background-animation {
            position: fixed;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background: radial-gradient(circle at center,
                rgba(0, 58, 145, 0.4),
                rgba(0, 15, 40, 0.9)
            );
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
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 440px;
            padding: 45px 40px;
            background: rgba(0, 30, 70, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(15px);
            box-shadow: 
                0 0 40px rgba(0, 70, 160, 0.4),
                0 25px 50px rgba(0, 0, 0, 0.3);
            animation: fadeIn 1s ease-out;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #1A73E8, #ffffff, #1A73E8, transparent);
            border-radius: 20px 20px 0 0;
        }

        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translate(-50%, -60%); 
            }
            to { 
                opacity: 1; 
                transform: translate(-50%, -50%); 
            }
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
            background: linear-gradient(135deg, #003A91, #1A73E8);
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 
                0 0 30px rgba(26, 115, 232, 0.6),
                0 10px 30px rgba(0, 0, 0, 0.3);
            animation: logoGlow 3s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .logo-circle::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: logoShine 4s infinite;
        }

        @keyframes logoGlow {
            0%, 100% { box-shadow: 0 0 30px rgba(26, 115, 232, 0.6); }
            50% { box-shadow: 0 0 50px rgba(255, 255, 255, 0.4); }
        }

        @keyframes logoShine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .logo-circle i {
            font-size: 42px;
            color: #ffffff;
            z-index: 1;
        }

        .logo-text {
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 2px;
            z-index: 1;
        }

        /* Title */
        .title {
            text-align: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 24px;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 30px;
        }

        /* Error Box */
        .error-box {
            background: linear-gradient(135deg, rgba(255, 0, 0, 0.2), rgba(255, 0, 0, 0.1));
            padding: 14px 18px;
            border-left: 4px solid #ff4444;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 8px;
            font-size: 14px;
            color: #ff8888;
            animation: shake 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        @keyframes shake {
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        /* Input Group */
        .input-group {
            position: relative;
            margin-bottom: 28px;
        }

        .input-group input {
            width: 100%;
            padding: 16px 14px;
            padding-left: 48px;
            border: none;
            border-bottom: 2px solid rgba(26, 115, 232, 0.5);
            background: rgba(255, 255, 255, 0.03);
            color: white;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            border-radius: 8px 8px 0 0;
        }

        .input-group input::placeholder {
            color: transparent;
        }

        .input-group .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #1A73E8;
            font-size: 18px;
            transition: color 0.3s;
        }

        .input-group label {
            position: absolute;
            left: 48px;
            top: 16px;
            color: #888;
            font-size: 14px;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-group input:focus {
            border-bottom-color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.15);
        }

        .input-group input:focus ~ .input-icon {
            color: #ffffff;
        }

        .input-group input:focus ~ label,
        .input-group input:not(:placeholder-shown) ~ label {
            top: -10px;
            left: 14px;
            font-size: 12px;
            color: #ffffff;
            font-weight: 600;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #003A91, #1A73E8);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(26, 115, 232, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(26, 115, 232, 0.6);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        /* Footer */
        .footer-text {
            text-align: center;
            margin-top: 28px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.4);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                padding: 35px 25px;
            }
            .title {
                font-size: 20px;
            }
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="background-animation"></div>
    <div class="particles" id="particles"></div>

    <div class="login-container">
        <div class="logo-area">
            <div class="logo-circle">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>

        <h1 class="title">DASHBOARD SIDAK LANTAS</h1>
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
                <input type="text" name="login" id="login" placeholder="Username" value="{{ old('login') }}" required>
                <i class="fas fa-user input-icon"></i>
                <label for="login">Username / NRP</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <i class="fas fa-lock input-icon"></i>
                <label for="password">Password</label>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>

        <p class="footer-text">KORLANTAS POLRI &copy; {{ date('Y') }}</p>
    </div>

    <script>
        // Create floating particles
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (15 + Math.random() * 10) + 's';
            particlesContainer.appendChild(particle);
        }

        // Subtle glow animation on input focus
        const inputs = document.querySelectorAll("input");
        inputs.forEach(input => {
            input.addEventListener("focus", () => {
                document.body.style.backgroundColor = "#0b1225";
            });
            input.addEventListener("blur", () => {
                document.body.style.backgroundColor = "#0b1120";
            });
        });
    </script>
</body>
</html>