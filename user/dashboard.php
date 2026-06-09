<?php
require '../config/auth.php';
require '../config/koneksi.php';

// If admin accesses, redirect to admin dashboard
if ($_SESSION['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit;
}

// Fetch stats for User
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang");
$total_barang = mysqli_fetch_assoc($query_total)['total'] ?? 0;

$query_stok = mysqli_query($koneksi, "SELECT SUM(stok) as total FROM barang");
$stok = mysqli_fetch_assoc($query_stok)['total'] ?? 0;

// Fetch Recent Items
$query_items = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY id_barang DESC LIMIT 8");

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
                    <h1 class="mb-0 fw-bold text-dark">Dashboard Petugas</h1>
                    <p class="text-muted small mb-0">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>. Berikut data stok barang terbaru.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-indigo">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Body -->
    <div class="app-content">
        <div class="container-fluid">
            
            <!-- Welcome Info Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 20px;">
                        <div class="card-body p-4 p-md-5 position-relative overflow-hidden">
                            <div class="position-absolute end-0 bottom-0 opacity-10" style="transform: translate(10%, 10%);">
                                <i class="bi bi-shield-lock" style="font-size: 15rem;"></i>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-8 position-relative z-1">
                                    <span class="badge bg-primary text-white fw-bold mb-3 px-3 py-2 text-uppercase" style="font-size: 0.75rem;">Akses Petugas</span>
                                    <h2 class="fw-extrabold text-white mb-2" style="font-size: 2.2rem; letter-spacing: -0.5px;">Monitoring Inventaris Gudang</h2>
                                    <p class="text-white-50 mb-4 fs-6">Sebagai petugas, Anda memiliki akses untuk memantau data barang, memeriksa jumlah stok yang tersedia, dan mencetak laporan inventaris.</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="<?= $base_url ?>/user/barang.php" class="btn btn-primary fw-bold px-4 py-2 rounded-3 border-0" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                                            <i class="bi bi-search me-1"></i> Cari & Lihat Barang
                                        </a>
                                        <a href="<?= $base_url ?>/user/laporan.php" class="btn btn-outline-light fw-semibold px-4 py-2 rounded-3 border-white border-opacity-30">
                                            <i class="bi bi-printer me-1"></i> Laporan Stok
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Widgets Row -->
            <div class="row g-4 mb-4">
                <!-- Total Kategori Barang -->
                <div class="col-12 col-md-6">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-tags-fill fs-2"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block fw-semibold text-uppercase font-monospace">Jenis Barang</span>
                                <h2 class="fw-bold mb-0 text-dark"><?= number_format($total_barang) ?> <span class="fs-6 text-muted fw-normal">Item terdaftar</span></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Stok Gudang -->
                <div class="col-12 col-md-6">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-archive-fill fs-2"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block fw-semibold text-uppercase font-monospace">Total Stok Tersedia</span>
                                <h2 class="fw-bold mb-0 text-dark"><?= number_format($stok) ?> <span class="fs-6 text-muted fw-normal">Total unit</span></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Barang Terbaru -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">Daftar Barang Terbaru</h5>
                            <a href="<?= $base_url ?>/user/barang.php" class="btn btn-light btn-sm text-indigo fw-semibold rounded-pill px-3">Lihat Semua Barang</a>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase fw-bold text-muted" style="width: 15%;">Kode Barang</th>
                                            <th class="small text-uppercase fw-bold text-muted">Nama Barang</th>
                                            <th class="small text-uppercase fw-bold text-muted">Kategori</th>
                                            <th class="small text-uppercase fw-bold text-muted text-center" style="width: 15%;">Stok</th>
                                            <th class="small text-uppercase fw-bold text-muted text-center" style="width: 15%;">Satuan</th>
                                            <th class="small text-uppercase fw-bold text-muted">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($query_items) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($query_items)): ?>
                                                <tr>
                                                    <td><span class="badge bg-secondary-subtle text-secondary-emphasis font-monospace py-1.5 px-2 rounded-2"><?= htmlspecialchars($row['kode_barang']) ?></span></td>
                                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                    <td><span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;"><?= htmlspecialchars($row['kategori']) ?></span></td>
                                                    <td class="text-center">
                                                        <span class="fw-bold <?= $row['stok'] > 10 ? 'text-success' : 'text-danger' ?>">
                                                            <?= number_format($row['stok']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center text-muted small"><?= htmlspecialchars($row['satuan']) ?></td>
                                                    <td class="text-muted small"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data barang.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
