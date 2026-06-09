<?php
require '../config/auth.php';
require '../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'user') {
    header("Location: ../admin/dashboard.php");
    exit;
}

// Search feature
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$where_clause = "";
if ($search !== '') {
    $where_clause = "WHERE kode_barang LIKE '%$search%' OR nama_barang LIKE '%$search%' OR kategori LIKE '%$search%'";
}

// Fetch all barang
$query_barang = mysqli_query($koneksi, "SELECT * FROM barang $where_clause ORDER BY id_barang DESC");

include '../templates/header.php';
include '../templates/navbar.php';
include '../templates/sidebar.php';
?>

<main class="app-main">
    <!-- Content Header -->
    <div class="app-content-header py-4 bg-white border-bottom mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="mb-0 fw-bold text-dark">Daftar Barang</h1>
                    <p class="text-muted small mb-0">Cari dan lihat status persediaan barang saat ini.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/user/dashboard.php" class="text-decoration-none text-indigo">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lihat Barang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Body -->
    <div class="app-content">
        <div class="container-fluid">

            <div class="card border-0 shadow-sm">
                <!-- Card Header -->
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-search text-indigo fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">Pencarian & Informasi Barang</h5>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <!-- Search Form -->
                    <form action="barang.php" method="GET" class="row g-2 mb-4">
                        <div class="col-12 col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-filter"></i></span>
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Ketik kode, nama, atau kategori barang..." 
                                       value="<?= htmlspecialchars($search) ?>">
                                <button class="btn btn-primary fw-semibold px-4" type="submit">
                                    Cari
                                </button>
                                <?php if ($search !== ''): ?>
                                    <a href="barang.php" class="btn btn-outline-danger" title="Reset">
                                        Reset
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
                                    <th class="small text-uppercase fw-bold text-muted" style="width: 15%;">Kode Barang</th>
                                    <th class="small text-uppercase fw-bold text-muted">Nama Barang</th>
                                    <th class="small text-uppercase fw-bold text-muted">Kategori</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center" style="width: 12%;">Stok</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center" style="width: 12%;">Satuan</th>
                                    <th class="small text-uppercase fw-bold text-muted">Keterangan</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center" style="width: 12%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query_barang) > 0): ?>
                                    <?php 
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($query_barang)): 
                                    ?>
                                        <tr>
                                            <td class="text-center text-muted small"><?= $no++ ?></td>
                                            <td><span class="badge bg-secondary-subtle text-secondary-emphasis font-monospace py-1.5 px-2.5 rounded-2"><?= htmlspecialchars($row['kode_barang']) ?></span></td>
                                            <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                            <td><span class="badge bg-indigo-subtle text-indigo px-3 py-1.5 rounded-pill" style="font-size: 0.75rem; background-color: rgba(99, 102, 241, 0.1); color: #4f46e5;"><?= htmlspecialchars($row['kategori']) ?></span></td>
                                            <td class="text-center fw-bold <?= $row['stok'] > 10 ? 'text-success' : 'text-danger' ?>">
                                                <?= number_format($row['stok']) ?>
                                            </td>
                                            <td class="text-center text-muted small"><?= htmlspecialchars($row['satuan']) ?></td>
                                            <td class="text-muted small"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                                            <td class="text-center">
                                                <?php if ($row['stok'] > 10): ?>
                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Aman
                                                    </span>
                                                <?php elseif ($row['stok'] > 0): ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25 px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Menipis
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger border-opacity-25 px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                                                        <i class="bi bi-x-circle-fill me-1"></i> Habis
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data barang yang ditemukan.</td>
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
include '../templates/footer.php';
include '../templates/script.php';
?>
