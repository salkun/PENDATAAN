<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

$id_user = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch existing user
$query_current = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = $id_user");
if (mysqli_num_rows($query_current) === 0) {
    header("Location: index.php?status=error");
    exit;
}
$current = mysqli_fetch_assoc($query_current);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Check if username already exists for OTHER users
        $check_user = mysqli_query($koneksi, "SELECT id_user FROM users WHERE username = '$username' AND id_user != $id_user");
        if (mysqli_num_rows($check_user) > 0) {
            $error = "Username '$username' sudah digunakan oleh akun lain.";
        } else {
            // Check self-demotion logic if editing self
            if ($id_user == $_SESSION['id_user'] && $role !== 'admin') {
                $error = "Anda tidak dapat mengubah hak akses Anda sendiri menjadi User.";
            } else {
                // Update user
                $update_user = mysqli_query($koneksi, "
                    UPDATE users 
                    SET nama_lengkap = '$nama_lengkap', 
                        username = '$username', 
                        password = '$password', 
                        role = '$role'
                    WHERE id_user = $id_user
                ");

                if ($update_user) {
                    // if self edit, update session
                    if ($id_user == $_SESSION['id_user']) {
                        $_SESSION['nama_lengkap'] = $nama_lengkap;
                    }
                    header("Location: index.php?status=success_edit");
                    exit;
                } else {
                    $error = "Gagal memperbarui data pengguna.";
                }
            }
        }
    }
}

include '../../templates/header.php';
include '../../templates/navbar.php';
include '../../templates/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header py-4 bg-white border-bottom mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="mb-0 fw-bold text-dark">Edit Pengguna</h1>
                    <p class="text-muted small mb-0">Ubah detail profil atau peran pengguna.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-success">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-success">Manajemen User</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

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
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-pencil-square text-primary fs-4"></i>
                                <h5 class="fw-bold text-dark mb-0">Formulir Edit Akun</h5>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <form action="edit.php?id=<?= $id_user ?>" method="POST">
                                
                                <div class="bg-light p-3 rounded-3 mb-4 border border-secondary border-opacity-10">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Akun</h6>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="nama_lengkap" class="form-label fw-semibold text-secondary">Nama Lengkap *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-person-lines-fill"></i></span>
                                                <input type="text" 
                                                       name="nama_lengkap" 
                                                       id="nama_lengkap" 
                                                       class="form-control" 
                                                       value="<?= isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : htmlspecialchars($current['nama_lengkap']) ?>"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="username" class="form-label fw-semibold text-secondary">Username *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-at"></i></span>
                                                <input type="text" 
                                                       name="username" 
                                                       id="username" 
                                                       class="form-control" 
                                                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : htmlspecialchars($current['username']) ?>"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="password" class="form-label fw-semibold text-secondary">Password *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                                                <input type="text" 
                                                       name="password" 
                                                       id="password" 
                                                       class="form-control" 
                                                       value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password']) : htmlspecialchars($current['password']) ?>"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <label for="role" class="form-label fw-semibold text-secondary">Hak Akses (Role) *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-shield-lock"></i></span>
                                                <select name="role" id="role" class="form-select" required <?= $id_user == $_SESSION['id_user'] ? 'style="pointer-events:none; background-color:#e9ecef;" readonly' : '' ?>>
                                                    <?php $sel_role = isset($_POST['role']) ? $_POST['role'] : $current['role']; ?>
                                                    <option value="user" <?= $sel_role === 'user' ? 'selected' : '' ?>>User / Staf (Akses Terbatas)</option>
                                                    <option value="admin" <?= $sel_role === 'admin' ? 'selected' : '' ?>>Admin (Akses Penuh)</option>
                                                </select>
                                            </div>
                                            <?php if($id_user == $_SESSION['id_user']): ?>
                                                <div class="form-text small text-warning"><i class="bi bi-exclamation-triangle"></i> Anda tidak dapat mengubah peran Anda sendiri.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                                        Simpan Perubahan
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
