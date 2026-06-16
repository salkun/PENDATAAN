<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

// Search feature
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$where_clause = "";
if ($search !== '') {
    $where_clause = "WHERE nama_lengkap LIKE '%$search%' OR username LIKE '%$search%' OR role LIKE '%$search%'";
}

// Fetch all users
$query_users = mysqli_query($koneksi, "SELECT * FROM users $where_clause ORDER BY role ASC, id_user DESC");

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
                    <h1 class="mb-0 fw-bold text-dark">Manajemen User</h1>
                    <p class="text-muted small mb-0">Kelola akun admin dan staf yang dapat mengakses sistem.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-success">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manajemen User</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Body -->
    <div class="app-content">
        <div class="container-fluid">

            <!-- Alert Notifications -->
            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] === 'success_add'): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Akun pengguna baru berhasil didaftarkan!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['status'] === 'success_edit'): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Profil pengguna berhasil diperbarui!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['status'] === 'success_delete'): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Akun pengguna telah berhasil dihapus dari sistem!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['status'] === 'error_self_delete'): ?>
                    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Tindakan ditolak! Anda tidak dapat menghapus akun Anda sendiri.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['status'] === 'error'): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi kesalahan dalam memproses data!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <!-- Card Header -->
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-people-fill text-success fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">Daftar Akun Pengguna</h5>
                    </div>
                    <div class="ms-sm-auto text-end">
                        <a href="tambah.php" class="btn btn-primary fw-semibold rounded-3 px-4 py-2">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
                        </a>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <!-- Search Form -->
                    <form action="index.php" method="GET" class="row g-2 mb-4">
                        <div class="col-12 col-md-4 ms-auto">
                            <div class="input-group">
                                <input type="text" 
                                       name="search" 
                                       class="form-control rounded-start-3" 
                                       placeholder="Cari nama, username, role..." 
                                       value="<?= htmlspecialchars($search) ?>">
                                <button class="btn btn-outline-secondary rounded-end-3" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                                <?php if ($search !== ''): ?>
                                    <a href="index.php" class="btn btn-outline-danger ms-1 rounded-3" title="Clear Search">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="small text-uppercase fw-bold text-muted text-center" style="width: 50px;">No</th>
                                    <th class="small text-uppercase fw-bold text-muted">Nama Lengkap</th>
                                    <th class="small text-uppercase fw-bold text-muted">Username</th>
                                    <th class="small text-uppercase fw-bold text-muted">Password</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center">Hak Akses (Role)</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query_users) > 0): ?>
                                    <?php 
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($query_users)): 
                                    ?>
                                        <tr>
                                            <td class="text-center text-muted small"><?= $no++ ?></td>
                                            <td class="fw-bold text-dark">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person-fill"></i>
                                                    </div>
                                                    <?= htmlspecialchars($row['nama_lengkap']) ?>
                                                    <?php if($row['id_user'] == $_SESSION['id_user']): ?>
                                                        <span class="badge bg-success small py-1 px-2 ms-2" style="font-size:0.6rem;">ANDA</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><span class="text-secondary font-monospace"><?= htmlspecialchars($row['username']) ?></span></td>
                                            <td>
                                                <span class="text-muted small">
                                                    <?= str_repeat('&bull;', min(strlen($row['password']), 8)) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($row['role'] === 'admin'): ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill">
                                                        <i class="bi bi-shield-lock-fill me-1"></i> Admin
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary border-opacity-25 px-3 py-1.5 rounded-pill">
                                                        <i class="bi bi-person-workspace me-1"></i> User
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <!-- Edit Button -->
                                                    <a href="edit.php?id=<?= $row['id_user'] ?>" class="btn btn-outline-primary btn-sm rounded-2" title="Edit Akun">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <!-- Delete Button -->
                                                    <?php if($row['id_user'] != $_SESSION['id_user']): ?>
                                                        <a href="hapus.php?id=<?= $row['id_user'] ?>" class="btn btn-outline-danger btn-sm rounded-2" title="Hapus Akun" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini secara permanen?')">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-outline-secondary btn-sm rounded-2" disabled title="Tidak bisa menghapus akun sendiri">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
