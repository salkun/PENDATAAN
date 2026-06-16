<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

// Set up filter parameters
$jenis_laporan = isset($_GET['jenis']) ? $_GET['jenis'] : 'stok'; // stok, masuk, keluar
$id_barang = isset($_GET['id_barang']) ? (int)$_GET['id_barang'] : 0;
$bulan_tahun = isset($_GET['bulan_tahun']) ? $_GET['bulan_tahun'] : ''; // Default to empty (all time)

// Prepare base where clauses
$where_masuk = "WHERE 1=1";
$where_keluar = "WHERE 1=1";

if (!empty($bulan_tahun)) {
    $year = date('Y', strtotime($bulan_tahun . '-01'));
    $month = date('m', strtotime($bulan_tahun . '-01'));
    $where_masuk = "WHERE YEAR(bm.tanggal_masuk) = '$year' AND MONTH(bm.tanggal_masuk) = '$month'";
    $where_keluar = "WHERE YEAR(bk.tanggal_keluar) = '$year' AND MONTH(bk.tanggal_keluar) = '$month'";
}

// Fetch items for the dropdown
$query_dropdown = mysqli_query($koneksi, "SELECT id_barang, kode_barang, nama_barang FROM barang ORDER BY nama_barang ASC");

// Prepare queries based on the selected report type
$query_result = null;

if ($jenis_laporan === 'stok') {
    $where_stok = "";
    if ($id_barang > 0) {
        $where_stok = "WHERE b.id_barang = $id_barang";
    }
    
    // For stock, month filter might not apply directly since it's current stock, but we can just show current stock.
    $query_result = mysqli_query($koneksi, "
        SELECT b.kode_barang, b.nama_barang, b.kategori, b.satuan, b.keterangan,
               (COALESCE((SELECT SUM(jumlah) FROM barang_masuk WHERE id_barang = b.id_barang), 0) - 
                COALESCE((SELECT SUM(jumlah) FROM barang_keluar WHERE id_barang = b.id_barang), 0)) AS stok
        FROM barang b
        $where_stok
        ORDER BY b.nama_barang ASC
    ");
} elseif ($jenis_laporan === 'masuk') {
    if ($id_barang > 0) {
        $where_masuk .= " AND bm.id_barang = $id_barang";
    }
    
    $query_result = mysqli_query($koneksi, "
        SELECT bm.tanggal_masuk AS tanggal, b.kode_barang, b.nama_barang, bm.jumlah, b.satuan, bm.supplier AS pihak_terkait, bm.total_biaya, bm.keterangan
        FROM barang_masuk bm
        JOIN barang b ON bm.id_barang = b.id_barang
        $where_masuk
        ORDER BY bm.tanggal_masuk DESC, bm.id_masuk DESC
    ");
} elseif ($jenis_laporan === 'keluar') {
    if ($id_barang > 0) {
        $where_keluar .= " AND bk.id_barang = $id_barang";
    }
    
    $query_result = mysqli_query($koneksi, "
        SELECT bk.tanggal_keluar AS tanggal, b.kode_barang, b.nama_barang, bk.jumlah, b.satuan, bk.tujuan AS pihak_terkait, bk.keterangan
        FROM barang_keluar bk
        JOIN barang b ON bk.id_barang = b.id_barang
        $where_keluar
        ORDER BY bk.tanggal_keluar DESC, bk.id_keluar DESC
    ");
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
                    <h1 class="mb-0 fw-bold text-dark">Laporan Inventaris</h1>
                    <p class="text-muted small mb-0">Cetak riwayat transaksi dan data persediaan barang.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-success">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-funnel-fill text-success fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">Filter Laporan</h5>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="index.php" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="jenis" class="form-label fw-semibold text-secondary small">Jenis Laporan</label>
                                <select name="jenis" id="jenis" class="form-select">
                                    <option value="stok" <?= $jenis_laporan === 'stok' ? 'selected' : '' ?>>Data Barang / Stok (Semua Waktu)</option>
                                    <option value="masuk" <?= $jenis_laporan === 'masuk' ? 'selected' : '' ?>>Barang Masuk (Riwayat)</option>
                                    <option value="keluar" <?= $jenis_laporan === 'keluar' ? 'selected' : '' ?>>Barang Keluar (Riwayat)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="id_barang" class="form-label fw-semibold text-secondary small">Spesifik Barang (Opsional)</label>
                                <select name="id_barang" id="id_barang" class="form-select">
                                    <option value="0">Semua Barang</option>
                                    <?php 
                                    mysqli_data_seek($query_dropdown, 0);
                                    while ($row_dd = mysqli_fetch_assoc($query_dropdown)): 
                                    ?>
                                        <option value="<?= $row_dd['id_barang'] ?>" <?= $id_barang == $row_dd['id_barang'] ? 'selected' : '' ?>>
                                            [<?= $row_dd['kode_barang'] ?>] <?= htmlspecialchars($row_dd['nama_barang']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="bulan_tahun" class="form-label fw-semibold text-secondary small">Bulan & Tahun (B. Masuk/Keluar)</label>
                                <input type="month" name="bulan_tahun" id="bulan_tahun" class="form-control" value="<?= htmlspecialchars($bulan_tahun) ?>" <?= $jenis_laporan === 'stok' ? 'disabled' : '' ?>>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-3">
                                    <i class="bi bi-funnel me-1"></i> Terapkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom border-light pt-4 px-4 pb-3 d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-text-fill text-indigo fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">Preview Data</h5>
                    </div>
                    <div class="ms-sm-auto text-end">
                        <!-- Print Button that goes to cetak.php -->
                        <a href="cetak.php?jenis=<?= urlencode($jenis_laporan) ?>&id_barang=<?= urlencode($id_barang) ?>&bulan_tahun=<?= urlencode($bulan_tahun) ?>" 
                           target="_blank" 
                           class="btn btn-success fw-semibold rounded-3 px-4 py-2">
                            <i class="bi bi-printer-fill me-1"></i> Cetak / Export PDF
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <?php if ($jenis_laporan === 'stok'): ?>
                                    <tr>
                                        <th class="small text-uppercase fw-bold text-muted text-center py-3" style="width: 50px;">No</th>
                                        <th class="small text-uppercase fw-bold text-muted py-3">Kode</th>
                                        <th class="small text-uppercase fw-bold text-muted py-3">Nama Barang</th>
                                        <th class="small text-uppercase fw-bold text-muted py-3">Kategori</th>
                                        <th class="small text-uppercase fw-bold text-muted text-center py-3">Stok Saat Ini</th>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <th class="small text-uppercase fw-bold text-muted text-center py-3" style="width: 50px;">No</th>
                                        <th class="small text-uppercase fw-bold text-muted py-3">Tanggal</th>
                                        <th class="small text-uppercase fw-bold text-muted py-3">Kode</th>
                                        <th class="small text-uppercase fw-bold text-muted py-3">Nama Barang</th>
                                        <th class="small text-uppercase fw-bold text-muted text-center py-3">Jumlah</th>
                                        <?php if ($jenis_laporan === 'masuk'): ?>
                                            <th class="small text-uppercase fw-bold text-muted text-end py-3">Harga Satuan</th>
                                            <th class="small text-uppercase fw-bold text-muted text-end py-3">Total Biaya</th>
                                        <?php endif; ?>
                                        <th class="small text-uppercase fw-bold text-muted py-3"><?= $jenis_laporan === 'masuk' ? 'Supplier' : 'Tujuan' ?></th>
                                    </tr>
                                <?php endif; ?>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query_result) > 0): ?>
                                    <?php 
                                    $no = 1;
                                    $total_jumlah = 0;
                                    $total_biaya = 0;
                                    while ($row = mysqli_fetch_assoc($query_result)): 
                                        if ($jenis_laporan === 'masuk') {
                                            $total_jumlah += $row['jumlah'];
                                            $total_biaya += $row['total_biaya'];
                                        } elseif ($jenis_laporan === 'keluar') {
                                            $total_jumlah += $row['jumlah'];
                                        }
                                    ?>
                                        <?php if ($jenis_laporan === 'stok'): ?>
                                            <tr>
                                                <td class="text-center text-muted small"><?= $no++ ?></td>
                                                <td><span class="font-monospace text-secondary"><?= htmlspecialchars($row['kode_barang']) ?></span></td>
                                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                <td class="text-muted small"><?= htmlspecialchars($row['kategori']) ?></td>
                                                <td class="text-center fw-bold <?= $row['stok'] > 0 ? 'text-success' : 'text-danger' ?>">
                                                    <?= number_format($row['stok']) ?> <span class="text-muted fw-normal small"><?= htmlspecialchars($row['satuan']) ?></span>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td class="text-center text-muted small"><?= $no++ ?></td>
                                                <td class="text-dark small fw-semibold"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                                <td><span class="font-monospace text-secondary"><?= htmlspecialchars($row['kode_barang']) ?></span></td>
                                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                <td class="text-center fw-bold <?= $jenis_laporan === 'masuk' ? 'text-success' : 'text-danger' ?>">
                                                    <?= $jenis_laporan === 'masuk' ? '+' : '-' ?><?= number_format($row['jumlah']) ?> 
                                                    <span class="text-muted fw-normal small"><?= htmlspecialchars($row['satuan']) ?></span>
                                                </td>
                                                <?php if ($jenis_laporan === 'masuk'): ?>
                                                    <td class="text-end fw-semibold text-secondary">
                                                        <?= isset($row['total_biaya']) && $row['total_biaya'] > 0 ? 'Rp ' . number_format($row['total_biaya'] / $row['jumlah'], 0, ',', '.') : '-' ?>
                                                    </td>
                                                    <td class="text-end fw-semibold text-dark">
                                                        <?= isset($row['total_biaya']) && $row['total_biaya'] > 0 ? 'Rp ' . number_format($row['total_biaya'], 0, ',', '.') : '-' ?>
                                                    </td>
                                                <?php endif; ?>
                                                <td class="text-dark small"><?= htmlspecialchars($row['pihak_terkait']) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endwhile; ?>

                                    <?php if ($jenis_laporan === 'masuk'): ?>
                                        <tr class="table-light fw-bold border-top border-dark-subtle">
                                            <td colspan="4" class="text-end py-3 text-uppercase small text-muted">Total:</td>
                                            <td class="text-center py-3 text-success font-monospace">+<?= number_format($total_jumlah) ?></td>
                                            <td></td>
                                            <td class="text-end py-3 text-primary">Rp <?= number_format($total_biaya, 0, ',', '.') ?></td>
                                            <td></td>
                                        </tr>
                                    <?php elseif ($jenis_laporan === 'keluar'): ?>
                                        <tr class="table-light fw-bold border-top border-dark-subtle">
                                            <td colspan="4" class="text-end py-3 text-uppercase small text-muted">Total:</td>
                                            <td class="text-center py-3 text-danger font-monospace">-<?= number_format($total_jumlah) ?></td>
                                            <td></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?= $jenis_laporan === 'stok' ? '5' : ($jenis_laporan === 'masuk' ? '8' : '6') ?>" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x fs-1 d-block mb-2 opacity-50"></i>
                                            Tidak ada data untuk filter yang dipilih.
                                        </td>
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

<script>
// Disable month input if showing stock
document.getElementById('jenis').addEventListener('change', function() {
    var monthInput = document.getElementById('bulan_tahun');
    if (this.value === 'stok') {
        monthInput.disabled = true;
    } else {
        monthInput.disabled = false;
    }
});
</script>

<?php
include '../../templates/footer.php';
include '../../templates/script.php';
?>
