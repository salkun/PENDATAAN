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
    $where_clause = "WHERE b.kode_barang LIKE '%$search%' OR b.nama_barang LIKE '%$search%' OR b.kategori LIKE '%$search%'";
}

// Fetch all barang with dynamically calculated stock (Total Masuk - Total Keluar)
$query_barang = mysqli_query($koneksi, "
    SELECT b.id_barang, b.kode_barang, b.nama_barang, b.kategori, b.satuan, b.keterangan,
           (COALESCE((SELECT SUM(jumlah) FROM barang_masuk WHERE id_barang = b.id_barang), 0) - 
            COALESCE((SELECT SUM(jumlah) FROM barang_keluar WHERE id_barang = b.id_barang), 0)) AS stok
    FROM barang b
    $where_clause
    ORDER BY b.id_barang DESC
");

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
                    <h1 class="mb-0 fw-bold text-dark">Data Barang</h1>
                    <p class="text-muted small mb-0">Daftar inventaris barang. Jumlah stok diperbarui otomatis dari transaksi masuk dan keluar.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-indigo">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Data Barang</li>
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
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam-fill text-indigo fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">Status Inventaris Barang</h5>
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
                                       placeholder="Cari kode, nama, atau kategori..." 
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
                                    <th class="small text-uppercase fw-bold text-muted">Kode Barang</th>
                                    <th class="small text-uppercase fw-bold text-muted">Nama Barang</th>
                                    <th class="small text-uppercase fw-bold text-muted">Kategori</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center">Stok Tersedia</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center">Satuan</th>
                                    <th class="small text-uppercase fw-bold text-muted">Keterangan</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center" style="width: 150px;">Status</th>
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
                                            <td class="text-center fw-bold <?= $row['stok'] > 0 ? 'text-success' : 'text-danger' ?>">
                                                <?= number_format($row['stok']) ?>
                                            </td>
                                            <td class="text-center text-muted small"><?= htmlspecialchars($row['satuan']) ?></td>
                                            <td class="text-muted small"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                                            <td class="text-center">
                                                <?php if ($row['stok'] > 0): ?>
                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Tersedia
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger border-opacity-25 px-2.5 py-1.5 rounded-pill" style="font-size: 0.7rem;">
                                                        <i class="bi bi-x-circle-fill me-1"></i> Tidak Tersedia
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data barang.</td>
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
