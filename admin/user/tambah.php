<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Check if username already exists
        $check_user = mysqli_query($koneksi, "SELECT id_user FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check_user) > 0) {
            $error = "Username '$username' sudah digunakan, silakan pilih username lain.";
        } else {
            // Insert user
            $insert_user = mysqli_query($koneksi, "
                INSERT INTO users (nama_lengkap, username, password, role) 
                VALUES ('$nama_lengkap', '$username', '$password', '$role')
            ");

            if ($insert_user) {
                header("Location: index.php?status=success_add");
                exit;
            } else {
                $error = "Gagal mendaftarkan pengguna baru.";
            }
        }
    }
}

include '../../templates/header.php';
include '../../templates/navbar.php';
include '../../templates/sidebar.php';
?>

<main class="app-main">
    <!-- Content Header -->
    <div class="app-content-header py-4 bg-white border-bottom mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="mb-0 fw-bold text-dark">Tambah Pengguna</h1>
                    <p class="text-muted small mb-0">Daftarkan akun admin atau staf baru.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-success">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-success">Manajemen User</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Body -->
    <div class="app-content">
        <div class="container-fluid">
            
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="card border-0 shadow-sm">
                        <!-- Card Header -->
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-plus-fill text-success fs-4"></i>
                                <h5 class="fw-bold text-dark mb-0">Formulir Pengguna Baru</h5>
                            </div>
                        </div>

                        <!-- Card Body / Form -->
                        <div class="card-body p-4">
                            <form action="tambah.php" method="POST">
                                
                                <div class="bg-light p-3 rounded-3 mb-4 border border-secondary border-opacity-10">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Akun</h6>
                                    <div class="row g-3">
                                        <!-- Nama Lengkap -->
                                        <div class="col-md-12">
                                            <label for="nama_lengkap" class="form-label fw-semibold text-secondary">Nama Lengkap *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-person-lines-fill"></i></span>
                                                <input type="text" 
                                                       name="nama_lengkap" 
                                                       id="nama_lengkap" 
                                                       class="form-control" 
                                                       placeholder="Nama asli pengguna" 
                                                       value="<?= isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : '' ?>"
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Username -->
                                        <div class="col-md-6">
                                            <label for="username" class="form-label fw-semibold text-secondary">Username *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-at"></i></span>
                                                <input type="text" 
                                                       name="username" 
                                                       id="username" 
                                                       class="form-control" 
                                                       placeholder="Username unik untuk login" 
                                                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Password -->
                                        <div class="col-md-6">
                                            <label for="password" class="form-label fw-semibold text-secondary">Password *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                                                <input type="text" 
                                                       name="password" 
                                                       id="password" 
                                                       class="form-control" 
                                                       placeholder="Password untuk login" 
                                                       required>
                                            </div>
                                            <div class="form-text small text-muted">Password akan disimpan dalam bentuk plain-text.</div>
                                        </div>

                                        <!-- Role -->
                                        <div class="col-md-12">
                                            <label for="role" class="form-label fw-semibold text-secondary">Hak Akses (Role) *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-shield-lock"></i></span>
                                                <select name="role" id="role" class="form-select" required>
                                                    <option value="" disabled <?= !isset($_POST['role']) ? 'selected' : '' ?>>Pilih peran akun...</option>
                                                    <option value="user" <?= isset($_POST['role']) && $_POST['role'] === 'user' ? 'selected' : '' ?>>User / Staf (Akses Terbatas)</option>
                                                    <option value="admin" <?= isset($_POST['role']) && $_POST['role'] === 'admin' ? 'selected' : '' ?>>Admin (Akses Penuh)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                                        Daftarkan Akun
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</main>

<?php
include '../../templates/footer.php';
include '../../templates/script.php';
?>
