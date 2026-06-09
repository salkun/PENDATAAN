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
    <title>Login - Pendataan Barang</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --secondary-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-dark: #1e1b4b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgba(243, 244, 246, 1) 0%, rgba(224, 231, 255, 1) 50%, rgba(238, 242, 255, 1) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Abstract shapes for premium background */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.6;
        }
        .shape-1 {
            width: 300px;
            height: 300px;
            background: #818cf8;
            top: -50px;
            left: -50px;
            animation: float 8s ease-in-out infinite alternate;
        }
        .shape-2 {
            width: 400px;
            height: 400px;
            background: #c084fc;
            bottom: -100px;
            right: -50px;
            animation: float 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(30px) scale(1.1); }
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .card-login {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card-login:hover {
            box-shadow: 0 30px 60px rgba(79, 70, 229, 0.15);
            transform: translateY(-5px);
        }

        .brand-logo-wrapper {
            width: 70px;
            height: 70px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
            margin: 0 auto 20px;
            animation: pulse 3s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }

        .brand-name {
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -1px;
            font-size: 1.75rem;
        }

        .form-floating > .form-control {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding-left: 45px;
            background-color: rgba(255, 255, 255, 0.6);
            transition: all 0.2s ease-in-out;
        }

        .form-floating > .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            background-color: #ffffff;
        }

        .form-floating > label {
            left: 35px;
            color: #64748b;
            transition: all 0.2s ease-in-out;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            left: 10px;
            color: #6366f1;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #94a3b8;
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-control:focus ~ .input-icon {
            color: #6366f1;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #94a3b8;
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            font-size: 1.1rem;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #4f46e5;
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            color: #ffffff;
            box-shadow: 0 8px 16px rgba(79, 70, 229, 0.25);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.35);
            transform: translateY(-2px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert-error {
            background-color: rgba(254, 226, 226, 0.9);
            border: 1px solid rgba(252, 165, 165, 0.5);
            border-radius: 12px;
            color: #991b1b;
            font-size: 0.875rem;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }

        .app-footer {
            margin-top: 25px;
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <!-- Background decorative shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-container">
        <div class="card card-login p-4 p-sm-5">
            <!-- Brand -->
            <div class="text-center mb-4">
                <div class="brand-logo-wrapper">
                    <i class="bi bi-box-seam-fill text-white fs-2"></i>
                </div>
                <h1 class="brand-name mb-1">Pendataan</h1>
                <p class="text-muted small">Sistem Inventarisasi & Manajemen Barang</p>
            </div>

            <!-- Error Notification -->
            <?php if ($error === 'invalid'): ?>
                <div class="alert alert-error mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>Username atau password salah! Silakan coba lagi.</div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="proses_login.php" method="POST">
                <!-- Username -->
                <div class="mb-3 position-relative">
                    <div class="form-floating">
                        <input type="text" 
                               name="username" 
                               class="form-control" 
                               id="username" 
                               placeholder="Username" 
                               required 
                               autocomplete="username">
                        <label for="username">Username</label>
                        <i class="bi bi-person-fill input-icon"></i>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4 position-relative">
                    <div class="form-floating">
                        <input type="password" 
                               name="password" 
                               class="form-control" 
                               id="password" 
                               placeholder="Password" 
                               required 
                               autocomplete="current-password">
                        <label for="password">Password</label>
                        <i class="bi bi-lock-fill input-icon"></i>
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="Tampilkan Password">
                            <i class="bi bi-eye-fill" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password (Optional UI) -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" style="cursor:pointer;">
                        <label class="form-check-label text-muted small" for="rememberMe" style="cursor:pointer; user-select: none;">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login w-100 mb-3">
                    Masuk Sekarang <i class="bi bi-arrow-right-short ms-1 fs-5 align-middle"></i>
                </button>
            </form>
        </div>

        <!-- Copyright -->
        <div class="text-center app-footer">
            <span>&copy; 2026 Pendataan Barang. All rights reserved.</span>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Password Visibility Toggle Script -->
    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');
        const toggleIcon = document.getElementById('toggleIcon');

        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle eye icon
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