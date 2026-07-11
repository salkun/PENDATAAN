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
    <title>Login | Sistem Pendataan Barang</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>

<div class="background-circle circle-1"></div>
<div class="background-circle circle-2"></div>
<div class="background-circle circle-3"></div>

<div class="login-container">

    <div class="login-card">

        <!-- Logo -->
        <div class="logo-area">

            <div class="logo-box">
                <img src="../assets/img/logo-pa.png" alt="Logo Pengadilan Agama">
            </div>

            <h2>Sistem Pendataan Barang</h2>

            <p>
                Pengadilan Agama Purwakarta
            </p>

        </div>

        <!-- Error -->
        <?php if($error=='invalid'): ?>

        <div class="alert-login">

            <i class="bi bi-exclamation-circle-fill"></i>

            <span>
                Username atau Password salah.
            </span>

        </div>

        <?php endif; ?>


        <!-- Form -->
        <form action="proses_login.php" method="POST">

            <!-- Username -->

            <div class="form-group">

                <label>Username</label>

                <div class="input-group-custom">

                    <i class="bi bi-person-fill input-icon"></i>

                    <input
                        type="text"
                        name="username"
                        class="form-control-custom"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        required>

                </div>

            </div>


            <!-- Password -->

            <div class="form-group">

                <label>Password</label>

                <div class="input-group-custom">

                    <i class="bi bi-lock-fill input-icon"></i>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control-custom password-input"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required>

                    <button
                        type="button"
                        class="toggle-password"
                        id="togglePassword">

                        <i class="bi bi-eye-fill" id="toggleIcon"></i>

                    </button>

                </div>

            </div>


            <!-- Remember -->

            <div class="remember-area">

                <input type="checkbox" id="remember">

                <label for="remember">
                    Ingat Saya
                </label>

            </div>


            <!-- Button -->

            <button class="btn-login" type="submit">

                Masuk

                <i class="bi bi-arrow-right ms-2"></i>

            </button>

        </form>


        <div class="footer">

            © 2026 Pendataan Barang<br>
            Pengadilan Agama

        </div>

    </div>

</div>


<script>

const password = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");
const toggleIcon = document.getElementById("toggleIcon");

togglePassword.addEventListener("click",function(){

    if(password.type==="password"){

        password.type="text";

        toggleIcon.classList.remove("bi-eye-fill");
        toggleIcon.classList.add("bi-eye-slash-fill");

    }else{

        password.type="password";

        toggleIcon.classList.remove("bi-eye-slash-fill");
        toggleIcon.classList.add("bi-eye-fill");

    }

});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>