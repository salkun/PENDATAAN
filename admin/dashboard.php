<?php
require '../config/auth.php';
require '../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

// Fetch stats
// 1. Total Barang
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang");
$total_barang = mysqli_fetch_assoc($query_total)['total'] ?? 0;

// 2. Barang Masuk
$query_masuk = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM barang_masuk");
$barang_masuk = mysqli_fetch_assoc($query_masuk)['total'] ?? 0;

// 3. Barang Keluar
$query_keluar = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM barang_keluar");
$barang_keluar = mysqli_fetch_assoc($query_keluar)['total'] ?? 0;

// 4. Stok Tersedia
$query_stok = mysqli_query($koneksi, "SELECT SUM(stok) as total FROM barang");
$stok = mysqli_fetch_assoc($query_stok)['total'] ?? 0;

// Fetch Recent Items
$query_recent_items = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY id_barang DESC LIMIT 5");

// Fetch Recent Barang Masuk
$query_recent_masuk = mysqli_query($koneksi, "
    SELECT bm.*, b.nama_barang 
    FROM barang_masuk bm 
    JOIN barang b ON bm.id_barang = b.id_barang 
    ORDER BY bm.id_masuk DESC LIMIT 5
");

// Fetch Recent Barang Keluar
$query_recent_keluar = mysqli_query($koneksi, "
    SELECT bk.*, b.nama_barang 
    FROM barang_keluar bk 
    JOIN barang b ON bk.id_barang = b.id_barang 
    ORDER BY bk.id_keluar DESC LIMIT 5
");

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
                    <h1 class="mb-0 fw-bold text-dark">Dashboard Admin</h1>
                    <p class="text-muted small mb-0">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>. Berikut ringkasan inventaris Anda.</p>
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
                    <div class="card border-0 text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 20px;">
                        <div class="card-body p-4 p-md-5 position-relative overflow-hidden">
                            <div class="position-absolute end-0 bottom-0 opacity-10" style="transform: translate(10%, 10%);">
                                <i class="bi bi-box-seam" style="font-size: 15rem;"></i>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-8 position-relative z-1">
                                    <span class="badge bg-white text-indigo fw-bold mb-3 px-3 py-2 text-uppercase" style="font-size: 0.75rem;">Sistem Informasi Inventaris</span>
                                    <h2 class="fw-extrabold text-white mb-2" style="font-size: 2.2rem; letter-spacing: -0.5px;">Manajemen Barang & Inventarisasi Modern</h2>
                                    <p class="text-white-50 mb-4 fs-6">Kelola stok barang masuk, barang keluar, laporan periodik, dan hak akses pengguna secara cepat dan terintegrasi.</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="<?= $base_url ?>/admin/barang/tambah.php" class="btn btn-light text-indigo fw-bold px-4 py-2 rounded-3 border-0">
                                            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Barang
                                        </a>
                                        <a href="<?= $base_url ?>/admin/laporan/index.php" class="btn btn-outline-light fw-semibold px-4 py-2 rounded-3 border-white border-opacity-50">
                                            <i class="bi bi-file-earmark-bar-graph me-1"></i> Lihat Laporan
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
                <!-- Total Barang -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 h-100 position-relative">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-box fs-2"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block fw-semibold text-uppercase">Total Barang</span>
                                <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_barang) ?></h3>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-2 px-4 rounded-bottom-4 d-flex justify-content-between align-items-center">
                            <a href="<?= $base_url ?>/admin/barang/index.php" class="text-primary text-decoration-none small fw-semibold">Kelola Barang</a>
                            <i class="bi bi-chevron-right text-primary small"></i>
                        </div>
                    </div>
                </div>

                <!-- Barang Masuk -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 h-100 position-relative">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-box-arrow-in-down fs-2"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block fw-semibold text-uppercase">Barang Masuk</span>
                                <h3 class="fw-bold mb-0 text-dark"><?= number_format($barang_masuk) ?></h3>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-2 px-4 rounded-bottom-4 d-flex justify-content-between align-items-center">
                            <a href="<?= $base_url ?>/admin/barang_masuk/index.php" class="text-success text-decoration-none small fw-semibold">Detail Masuk</a>
                            <i class="bi bi-chevron-right text-success small"></i>
                        </div>
                    </div>
                </div>

                <!-- Barang Keluar -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 h-100 position-relative">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-4 p-3 bg-danger bg-opacity-10 text-danger me-3">
                                <i class="bi bi-box-arrow-up fs-2"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block fw-semibold text-uppercase">Barang Keluar</span>
                                <h3 class="fw-bold mb-0 text-dark"><?= number_format($barang_keluar) ?></h3>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-2 px-4 rounded-bottom-4 d-flex justify-content-between align-items-center">
                            <a href="<?= $base_url ?>/admin/barang_keluar/index.php" class="text-danger text-decoration-none small fw-semibold">Detail Keluar</a>
                            <i class="bi bi-chevron-right text-danger small"></i>
                        </div>
                    </div>
                </div>

                <!-- Stok Tersedia -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 h-100 position-relative">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-4 p-3 bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-boxes fs-2"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block fw-semibold text-uppercase">Total Stok</span>
                                <h3 class="fw-bold mb-0 text-dark"><?= number_format($stok) ?></h3>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-2 px-4 rounded-bottom-4 d-flex justify-content-between align-items-center">
                            <span class="text-warning small fw-semibold">Dalam Gudang</span>
                            <i class="bi bi-info-circle text-warning small"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Lists -->
            <div class="row g-4">
                <!-- Recent Items -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">Barang Baru Ditambahkan</h5>
                            <a href="<?= $base_url ?>/admin/barang/index.php" class="btn btn-light btn-sm text-indigo fw-semibold rounded-pill px-3">Lihat Semua</a>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase fw-bold text-muted">Kode</th>
                                            <th class="small text-uppercase fw-bold text-muted">Nama Barang</th>
                                            <th class="small text-uppercase fw-bold text-muted">Kategori</th>
                                            <th class="small text-uppercase fw-bold text-muted text-end">Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($query_recent_items) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($query_recent_items)): ?>
                                                <tr>
                                                    <td><span class="badge bg-secondary-subtle text-secondary-emphasis font-monospace py-1.5 px-2 rounded-2"><?= htmlspecialchars($row['kode_barang']) ?></span></td>
                                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                    <td><span class="badge bg-info bg-opacity-10 text-info px-2.5 py-1.5 rounded-pill" style="font-size: 0.75rem;"><?= htmlspecialchars($row['kategori']) ?></span></td>
                                                    <td class="text-end fw-bold text-dark"><?= number_format($row['stok']) ?> <span class="text-muted small fw-normal"><?= htmlspecialchars($row['satuan']) ?></span></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data barang.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions (Masuk & Keluar) -->
                <div class="col-12 col-lg-6">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold text-dark mb-0">Aktivitas Terakhir</h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Nav tabs -->
                            <ul class="nav nav-pills mb-3 gap-2" id="transactionTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active rounded-pill px-3 py-1.5 fw-semibold btn-sm" id="masuk-tab" data-bs-toggle="tab" data-bs-target="#masuk-pane" type="button" role="tab" aria-controls="masuk-pane" aria-selected="true">
                                        <i class="bi bi-box-arrow-in-down me-1"></i> Barang Masuk
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold btn-sm" id="keluar-tab" data-bs-toggle="tab" data-bs-target="#keluar-pane" type="button" role="tab" aria-controls="keluar-pane" aria-selected="false">
                                        <i class="bi bi-box-arrow-up me-1"></i> Barang Keluar
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tab Content -->
                            <div class="tab-content" id="transactionTabsContent">
                                <!-- Barang Masuk Tab -->
                                <div class="tab-pane fade show active" id="masuk-pane" role="tabpanel" aria-labelledby="masuk-tab" tabindex="0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="small text-uppercase fw-bold text-muted">Tanggal</th>
                                                    <th class="small text-uppercase fw-bold text-muted">Barang</th>
                                                    <th class="small text-uppercase fw-bold text-muted">Supplier</th>
                                                    <th class="small text-uppercase fw-bold text-muted text-end">Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (mysqli_num_rows($query_recent_masuk) > 0): ?>
                                                    <?php while ($row = mysqli_fetch_assoc($query_recent_masuk)): ?>
                                                        <tr>
                                                            <td class="small text-muted"><?= date('d/m/Y', strtotime($row['tanggal_masuk'])) ?></td>
                                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                            <td class="small text-muted"><?= htmlspecialchars($row['supplier']) ?></td>
                                                            <td class="text-end fw-bold text-success">+<?= number_format($row['jumlah']) ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada barang masuk.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Barang Keluar Tab -->
                                <div class="tab-pane fade" id="keluar-pane" role="tabpanel" aria-labelledby="keluar-tab" tabindex="0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="small text-uppercase fw-bold text-muted">Tanggal</th>
                                                    <th class="small text-uppercase fw-bold text-muted">Barang</th>
                                                    <th class="small text-uppercase fw-bold text-muted">Tujuan</th>
                                                    <th class="small text-uppercase fw-bold text-muted text-end">Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (mysqli_num_rows($query_recent_keluar) > 0): ?>
                                                    <?php while ($row = mysqli_fetch_assoc($query_recent_keluar)): ?>
                                                        <tr>
                                                            <td class="small text-muted"><?= date('d/m/Y', strtotime($row['tanggal_keluar'])) ?></td>
                                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                            <td class="small text-muted"><?= htmlspecialchars($row['tujuan']) ?></td>
                                                            <td class="text-end fw-bold text-danger">-<?= number_format($row['jumlah']) ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada barang keluar.</td>
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

        </div>
    </div>
</main>

<?php
include '../templates/footer.php';
include '../templates/script.php';
?>