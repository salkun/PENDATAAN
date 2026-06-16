<?php
session_start();

if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/dashboard.php");
    }
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pendataan Barang | Pengadilan Agama</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            background: #f0f2f5;
        }

        /* ========== LEFT PANEL (Hero) ========== */
        .login-hero {
            flex: 1;
            background: linear-gradient(160deg, #0a2e11 0%, #14532d 35%, #166534 65%, #15803d 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 60px;
            overflow: hidden;
        }

        /* Decorative mesh / grid pattern overlay */
        .login-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255,255,255,0.04) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255,255,255,0.03) 0%, transparent 50%);
            z-index: 1;
        }

        /* Floating orbs */
        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.25;
            z-index: 0;
        }
        .orb-1 {
            width: 350px; height: 350px;
            background: #4ade80;
            top: -80px; left: -100px;
            animation: orbFloat 12s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 250px; height: 250px;
            background: #22c55e;
            bottom: -60px; right: -40px;
            animation: orbFloat 10s ease-in-out infinite alternate-reverse;
        }
        .orb-3 {
            width: 180px; height: 180px;
            background: #86efac;
            top: 40%; left: 60%;
            animation: orbFloat 14s ease-in-out infinite alternate;
        }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, -30px) scale(1.08); }
            100% { transform: translate(-10px, 20px) scale(0.95); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 420px;
        }

        .hero-logo {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.15);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            backdrop-filter: blur(10px);
            transition: transform 0.4s ease;
        }
        .hero-logo:hover {
            transform: scale(1.05) rotate(-2deg);
        }
        .hero-logo img {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }

        .hero-title {
            font-size: 2.2rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: -1px;
            line-height: 1.15;
            margin-bottom: 12px;
        }
        .hero-title span {
            background: linear-gradient(90deg, #86efac, #4ade80);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.6);
            line-height: 1.65;
            font-weight: 400;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 32px;
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #4ade80;
        }
        .stat-label {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* ========== RIGHT PANEL (Form) ========== */
        .login-form-panel {
            width: 520px;
            min-width: 420px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px 55px;
            background: #ffffff;
            position: relative;
        }

        .form-header {
            margin-bottom: 36px;
        }
        .form-greeting {
            font-size: 0.8rem;
            font-weight: 600;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .form-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .form-desc {
            font-size: 0.88rem;
            color: #6b7280;
            margin-top: 8px;
            line-height: 1.5;
        }

        /* Input Styles */
        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }
        .input-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            pointer-events: none;
            z-index: 2;
            transition: color 0.25s ease;
        }
        .input-field {
            width: 100%;
            padding: 14px 16px 14px 48px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            color: #111827;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            outline: none;
            transition: all 0.25s ease;
        }
        .input-field::placeholder {
            color: #c0c5ce;
            font-weight: 400;
        }
        .input-field:focus {
            border-color: #22c55e;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        }
        .input-field:focus ~ .input-icon {
            color: #16a34a;
        }

        .password-toggle-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 4px;
            z-index: 2;
            transition: color 0.2s ease;
        }
        .password-toggle-btn:hover {
            color: #16a34a;
        }

        /* Login Button */
        .btn-sign-in {
            width: 100%;
            padding: 15px;
            font-size: 0.92rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
        }
        .btn-sign-in::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .btn-sign-in:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
        }
        .btn-sign-in:hover::after {
            opacity: 1;
        }
        .btn-sign-in:active {
            transform: translateY(0);
        }

        /* Error Alert */
        .alert-login-error {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 14px;
            color: #991b1b;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 24px;
            animation: shakeAlert 0.45s ease;
        }
        .alert-login-error i {
            font-size: 1.25rem;
            color: #dc2626;
            flex-shrink: 0;
        }

        @keyframes shakeAlert {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }

        /* Remember & Extras */
        .login-extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .remember-check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .remember-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #16a34a;
            cursor: pointer;
        }
        .remember-check label {
            font-size: 0.82rem;
            color: #6b7280;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        /* Footer */
        .login-footer {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid #f3f4f6;
            text-align: center;
        }
        .login-footer span {
            font-size: 0.75rem;
            color: #9ca3af;
            font-weight: 500;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 992px) {
            body {
                flex-direction: column;
                overflow-y: auto;
            }
            .login-hero {
                min-height: 300px;
                padding: 40px 30px;
            }
            .hero-title { font-size: 1.6rem; }
            .hero-stats { gap: 20px; margin-top: 24px; padding-top: 20px; }
            .login-form-panel {
                width: 100%;
                min-width: unset;
                padding: 36px 28px;
            }
        }

        @media (max-width: 576px) {
            .login-hero {
                min-height: 240px;
                padding: 30px 20px;
            }
            .hero-logo {
                width: 70px; height: 70px;
                border-radius: 18px;
                margin-bottom: 18px;
            }
            .hero-logo img { width: 48px; height: 48px; }
            .hero-title { font-size: 1.35rem; }
            .hero-subtitle { font-size: 0.82rem; }
            .hero-stats { display: none; }
            .login-form-panel { padding: 28px 20px; }
            .form-title { font-size: 1.45rem; }
        }
    </style>
</head>
<body>

    <!-- ===== LEFT: Hero Panel ===== -->
    <div class="login-hero">
        <div class="hero-orb orb-1"></div>
        <div class="hero-orb orb-2"></div>
        <div class="hero-orb orb-3"></div>

        <div class="hero-content">
            <div class="hero-logo">
                <img src="../assets/img/logo-pa.png" alt="Logo Pengadilan Agama">
            </div>
            <h1 class="hero-title">Sistem <span>Pendataan</span> Barang</h1>
            <p class="hero-subtitle">
                Platform pencatatan inventaris terpadu untuk mengelola aset dan barang operasional Pengadilan Agama secara efisien.
            </p>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-value"><i class="bi bi-shield-check"></i></div>
                    <div class="stat-label">Aman</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><i class="bi bi-speedometer"></i></div>
                    <div class="stat-label">Cepat</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="stat-label">Terstruktur</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== RIGHT: Form Panel ===== -->
    <div class="login-form-panel">
        <div class="form-header">
            <div class="form-greeting">Selamat Datang</div>
            <h2 class="form-title">Masuk ke Akun Anda</h2>
            <p class="form-desc">Silakan masukkan kredensial Anda untuk mengakses sistem pendataan.</p>
        </div>

        <!-- Error Alert -->
        <?php if ($error === 'invalid'): ?>
            <div class="alert-login-error" role="alert">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <div>Username atau password salah! Silakan periksa kembali.</div>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="proses_login.php" method="POST">
            <!-- Username -->
            <div class="input-group-custom">
                <label class="input-label" for="username">Username</label>
                <div class="input-wrapper">
                    <input type="text" 
                           name="username" 
                           id="username" 
                           class="input-field" 
                           placeholder="Masukkan username Anda" 
                           required 
                           autocomplete="username">
                    <i class="bi bi-person-fill input-icon"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="input-group-custom">
                <label class="input-label" for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" 
                           name="password" 
                           id="password" 
                           class="input-field" 
                           placeholder="Masukkan password Anda"
                           style="padding-right: 48px;"
                           required 
                           autocomplete="current-password">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <button type="button" class="password-toggle-btn" id="passwordToggle" aria-label="Tampilkan Password">
                        <i class="bi bi-eye-fill" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="login-extras">
                <div class="remember-check">
                    <input type="checkbox" id="rememberMe">
                    <label for="rememberMe">Ingat saya</label>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-sign-in">
                Masuk Sekarang <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </form>

        <!-- Footer -->
        <div class="login-footer">
            <span>&copy; 2026 Pendataan Barang — Pengadilan Agama. All rights reserved.</span>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Password Visibility Toggle -->
    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');
        const toggleIcon = document.getElementById('toggleIcon');

        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'text') {
                toggleIcon.classList.remove('bi-eye-fill');
                toggleIcon.classList.add('bi-eye-slash-fill');
            } else {
                toggleIcon.classList.remove('bi-eye-slash-fill');
                toggleIcon.classList.add('bi-eye-fill');
            }
        });
    </script>
</body>
</html>