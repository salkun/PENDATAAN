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
    $where_clause = "WHERE b.nama_barang LIKE '%$search%' OR bm.supplier LIKE '%$search%' OR b.kode_barang LIKE '%$search%'";
}

// Fetch all barang masuk with joined barang details
$query_masuk = mysqli_query($koneksi, "
    SELECT bm.*, b.nama_barang, b.kode_barang, b.satuan 
    FROM barang_masuk bm
    JOIN barang b ON bm.id_barang = b.id_barang
    $where_clause
    ORDER BY bm.id_masuk DESC
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
                    <h1 class="mb-0 fw-bold text-dark">Transaksi Barang Masuk</h1>
                    <p class="text-muted small mb-0">Kelola catatan penerimaan barang/restock gudang.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-indigo">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Barang Masuk</li>
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
                        <i class="bi bi-check-circle-fill me-2"></i> Transaksi barang masuk berhasil ditambahkan! Stok barang telah disesuaikan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['status'] === 'success_edit'): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Transaksi berhasil diperbarui! Stok barang telah disesuaikan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['status'] === 'success_delete'): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Transaksi berhasil dihapus! Stok barang telah dikembalikan.
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
                        <i class="bi bi-box-arrow-in-down text-indigo fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">Catatan Barang Masuk</h5>
                    </div>
                    <div class="ms-sm-auto text-end">
                        <a href="tambah.php" class="btn btn-primary fw-semibold rounded-3 px-4 py-2">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Transaksi
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
                                       placeholder="Cari barang atau supplier..." 
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
                                    <th class="small text-uppercase fw-bold text-muted" style="width: 12%;">Tanggal</th>
                                    <th class="small text-uppercase fw-bold text-muted" style="width: 15%;">Kode Barang</th>
                                    <th class="small text-uppercase fw-bold text-muted">Nama Barang</th>
                                    <th class="small text-uppercase fw-bold text-muted">Supplier</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center" style="width: 10%;">Jumlah</th>
                                    <th class="small text-uppercase fw-bold text-muted text-end">Harga Satuan</th>
                                    <th class="small text-uppercase fw-bold text-muted text-end">Total Biaya</th>
                                    <th class="small text-uppercase fw-bold text-muted">Keterangan</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query_masuk) > 0): ?>
                                    <?php 
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($query_masuk)): 
                                    ?>
                                        <tr>
                                            <td class="text-center text-muted small"><?= $no++ ?></td>
                                            <td><span class="text-dark small fw-semibold"><?= date('d M Y', strtotime($row['tanggal_masuk'])) ?></span></td>
                                            <td><span class="badge bg-secondary-subtle text-secondary-emphasis font-monospace py-1.5 px-2.5 rounded-2"><?= htmlspecialchars($row['kode_barang']) ?></span></td>
                                            <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                            <td><span class="text-dark fw-semibold"><?= htmlspecialchars($row['supplier']) ?></span></td>
                                            <td class="text-center fw-bold text-success">
                                                +<?= number_format($row['jumlah']) ?> <span class="text-muted small fw-normal"><?= htmlspecialchars($row['satuan']) ?></span>
                                            </td>
                                            <td class="text-end fw-semibold text-secondary">
                                                <?= $row['total_biaya'] > 0 ? 'Rp ' . number_format($row['total_biaya'] / $row['jumlah'], 0, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-end fw-semibold text-dark">
                                                <?= $row['total_biaya'] > 0 ? 'Rp ' . number_format($row['total_biaya'], 0, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-muted small"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <!-- Edit Button -->
                                                    <a href="edit.php?id=<?= $row['id_masuk'] ?>" class="btn btn-outline-primary btn-sm rounded-2" title="Edit Transaksi">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <!-- Delete Button -->
                                                    <a href="hapus.php?id=<?= $row['id_masuk'] ?>" class="btn btn-outline-danger btn-sm rounded-2" title="Hapus Transaksi" onclick="return confirm('Apakah Anda yakin ingin menghapus catatan barang masuk ini? Stok barang terkait akan dikurangi.')">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">Belum ada data transaksi barang masuk.</td>
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
