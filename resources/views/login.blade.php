<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Portal Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

<style>
    body {
        font-family: Arial, sans-serif;
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, #0a1628, #6ec7ff);
        overflow: hidden;
    }

    /* ───────────────────────────────────────
       Floating Animated Blobs
    ─────────────────────────────────────── */
    .blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(70px);
        opacity: 0.55;
        animation: float 8s ease-in-out infinite alternate;
    }

    .blob.blob1 {
        width: 450px;
        height: 450px;
        background: #ffffff55;
        top: -80px;
        left: -100px;
    }

    .blob.blob2 {
        width: 350px;
        height: 350px;
        background: #ffffff33;
        bottom: -80px;
        right: -100px;
        animation-delay: 2s;
    }

    @keyframes float {
        0%   { transform: translateY(0) translateX(0); }
        50%  { transform: translateY(-25px) translateX(20px); }
        100% { transform: translateY(0) translateX(0); }
    }

    /* ───────────────────────────────────────
       Glassmorphism Login Box
    ─────────────────────────────────────── */
    .login-box {
        width: 380px;
        padding: 35px;
        border-radius: 16px;
        text-align: center;
        position: relative;
        z-index: 10;

        /* glass effect */
        background: rgba(255, 255, 255, 0.20);
        border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);

        /* animation */
        animation: fadeIn 0.8s ease-out forwards;
        transform: translateY(20px);
        opacity: 0;
        box-shadow: 0 15px 45px rgba(0,0,0,0.25);
    }

    @keyframes fadeIn {
        to { opacity: 1; transform: translateY(0); }
    }

    .logo {
        margin-bottom: 15px;
        font-size: 32px;
        font-weight: bold;
        color: white;
        text-shadow: 0 3px 10px rgba(0,0,0,0.3);
        animation: logoPop 0.7s ease forwards;
        opacity: 0;
        transform: scale(0.7);
    }

    @keyframes logoPop {
        to { opacity: 1; transform: scale(1); }
    }

    /* Inputs */
    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 8px;
        border: none;
        outline: none;
        background: rgba(255,255,255,0.75);
        transition: all .25s ease;
    }

    input:focus {
        background: rgba(255,255,255,1);
        box-shadow: 0 0 10px rgba(255,255,255,0.6);
        transform: scale(1.03);
    }

    /* ───────────────────────────────────────
       Ripple Button
    ─────────────────────────────────────── */
    .btn-login {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border-radius: 8px;
        border: none;
        color: white;
        cursor: pointer;
        margin-top: 15px;
        background: #1d60d6;
        position: relative;
        overflow: hidden;
        transition: transform .2s ease, background .2s ease;
    }

    .btn-login:hover {
        background: #174aad;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.25);
    }

    /* Ripple Effect */
    .btn-login .ripple {
        position: absolute;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 50%;
        transform: scale(0);
        animation: rippleAnim 0.6s linear;
    }

    @keyframes rippleAnim {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Error message */
    .error-box {
        background: #e53935;
        color: white;
        padding: 10px;
        margin-bottom: 12px;
        border-radius: 6px;
        animation: shake 0.4s ease;
    }

    @keyframes shake {
        25% { transform: translateX(-5px); }
        50% { transform: translateX(5px); }
        75% { transform: translateX(-3px); }
        100% { transform: translateX(0); }
    }
</style>

</head>
<body>

<!-- Blurred Background Blobs -->
<div class="blob blob1"></div>
<div class="blob blob2"></div>

<div class="login-box">

    <div class="logo">Portal Dashboard</div>
    <h2 style="color:white; text-shadow:0 1px 5px rgba(0,0,0,0.3);">Login</h2>

    @if($errors->has('loginError'))
        <div class="error-box">
            {{ $errors->first('loginError') }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <input 
            type="text" 
            name="login" 
            placeholder="Username / NRP"
            value="{{ old('login') }}"
            required
        >

        <input 
            type="password" 
            name="password" 
            placeholder="Password"
            required
        >

        <button class="btn-login" id="btnLogin">Masuk</button>
    </form>

</div>

<!-- Ripple Script -->
<script>
document.getElementById("btnLogin").addEventListener("click", function (e) {
    const btn = this;
    const rect = btn.getBoundingClientRect();
    const ripple = document.createElement("span");

    const size = Math.max(btn.clientWidth, btn.clientHeight);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;

    ripple.style.width = ripple.style.height = size + "px";
    ripple.style.left = x + "px";
    ripple.style.top = y + "px";
    ripple.classList.add("ripple");

    btn.appendChild(ripple);

    setTimeout(() => ripple.remove(), 600);
});
</script>

</body>
</html>
