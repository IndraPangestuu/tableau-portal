<?php
// Contoh sangat sederhana untuk proses login (nanti bisa disesuaikan)
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // HARDCODE SEMENTARA – SILAKAN GANTI DENGAN QUERY DATABASE
    if ($username === "admin" && $password === "admin") {
        $_SESSION['login'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DASHBOARD SIDAK LANTAS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="background-animation"></div>

<div class="login-container">

    <div class="logo-area">
        <!-- Placeholder Logo -->
        <div class="logo-circle">
            <span class="logo-text">LOGO</span>
        </div>
    </div>

    <h1 class="title">DASHBOARD SIDAK LANTAS</h1>
    <h2 class="subtitle">DASHBOARD SIDAK LANTAS</h2>

    <?php if (!empty($error)): ?>
        <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="login-form">

        <div class="input-group">
            <input type="text" name="username" required>
            <label>Username</label>
        </div>

        <div class="input-group">
            <input type="password" name="password" required>
            <label>Password</label>
        </div>

        <button type="submit" class="login-btn">LOGIN</button>
    </form>

</div>

<script src="script.js"></script>
</body>
</html>
