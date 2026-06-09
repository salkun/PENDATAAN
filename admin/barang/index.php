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
    $where_clause = "WHERE kode_barang LIKE '%$search%' OR nama_barang LIKE '%$search%' OR kategori LIKE '%$search%'";
}

// Fetch all barang
$query_barang = mysqli_query($koneksi, "SELECT * FROM barang $where_clause ORDER BY id_barang DESC");

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
                    <h1 class="mb-0 fw-bold text-dark">Manajemen Barang</h1>
                    <p class="text-muted small mb-0">Kelola daftar inventaris barang yang tersedia di gudang.</p>
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

            <!-- Alert notification (Optional) -->
            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] === 'success_add'): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Barang berhasil ditambahkan!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['status'] === 'success_edit'): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Data barang berhasil diperbarui!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif ($_GET['status'] === 'success_delete'): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Barang berhasil dihapus dari sistem!
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
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam-fill text-indigo fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">Daftar Inventaris Barang</h5>
                    </div>
                    <div>
                        <a href="tambah.php" class="btn btn-primary fw-semibold rounded-3 px-4 py-2">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Barang
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
                                    <th class="small text-uppercase fw-bold text-muted text-center">Stok</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center">Satuan</th>
                                    <th class="small text-uppercase fw-bold text-muted">Keterangan</th>
                                    <th class="small text-uppercase fw-bold text-muted text-center" style="width: 150px;">Aksi</th>
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
                                                <div class="d-flex justify-content-center gap-1">
                                                    <!-- Edit Button -->
                                                    <a href="edit.php?id=<?= $row['id_barang'] ?>" class="btn btn-outline-primary btn-sm rounded-2" title="Edit Barang">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <!-- Delete Button -->
                                                    <a href="hapus.php?id=<?= $row['id_barang'] ?>" class="btn btn-outline-danger btn-sm rounded-2" title="Hapus Barang" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini? Semua transaksi terkait akan terpengaruh.')">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </a>
                                                </div>
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
include '../../templates/footer.php';
include '../../templates/script.php';
?>
