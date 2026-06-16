<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

$id_keluar = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch existing transaction
$query_current = mysqli_query($koneksi, "SELECT * FROM barang_keluar WHERE id_keluar = $id_keluar");
if (mysqli_num_rows($query_current) === 0) {
    header("Location: index.php?status=error");
    exit;
}
$current = mysqli_fetch_assoc($query_current);
$current_id_barang = $current['id_barang'];
$current_jumlah = $current['jumlah'];

// Fetch all barang for the dropdown, including calculated stock
$query_barang = mysqli_query($koneksi, "
    SELECT b.id_barang, b.kode_barang, b.nama_barang, b.satuan,
           (COALESCE((SELECT SUM(jumlah) FROM barang_masuk WHERE id_barang = b.id_barang), 0) - 
            COALESCE((SELECT SUM(jumlah) FROM barang_keluar WHERE id_barang = b.id_barang), 0)) AS stok
    FROM barang b
    ORDER BY b.nama_barang ASC
");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = isset($_POST['id_barang']) ? (int)$_POST['id_barang'] : 0;
    $tanggal_keluar = mysqli_real_escape_string($koneksi, $_POST['tanggal_keluar']);
    $jumlah = (int)$_POST['jumlah'];
    $tujuan = mysqli_real_escape_string($koneksi, trim($_POST['tujuan']));
    $keterangan = mysqli_real_escape_string($koneksi, trim($_POST['keterangan']));

    if ($id_barang === 0 || $jumlah <= 0 || empty($tanggal_keluar) || empty($tujuan)) {
        $error = "Semua field yang bertanda bintang (*) wajib diisi dengan benar!";
    } else {
        // Validate stock: calculated current stock PLUS the old quantity (since we're editing this transaction)
        $query_cek = mysqli_query($koneksi, "
            SELECT (COALESCE((SELECT SUM(jumlah) FROM barang_masuk WHERE id_barang = $id_barang), 0) - 
                    COALESCE((SELECT SUM(jumlah) FROM barang_keluar WHERE id_barang = $id_barang), 0)) AS stok 
            FROM barang WHERE id_barang = $id_barang
        ");
        
        $row_cek = mysqli_fetch_assoc($query_cek);
        $stok_tersedia = $row_cek ? $row_cek['stok'] : 0;
        
        // If the item hasn't changed, we can safely add the current_jumlah back into the calculation
        if ($id_barang === (int)$current_id_barang) {
            $max_allowed = $stok_tersedia + $current_jumlah;
        } else {
            $max_allowed = $stok_tersedia;
        }
        
        if ($jumlah > $max_allowed) {
            $error = "Jumlah pengeluaran ($jumlah) melebihi stok yang tersedia ($max_allowed)!";
        } else {
            // Start database transaction
            mysqli_begin_transaction($koneksi);

            try {
                $update_keluar = mysqli_query($koneksi, "
                    UPDATE barang_keluar 
                    SET id_barang = $id_barang,
                        tanggal_keluar = '$tanggal_keluar',
                        jumlah = $jumlah,
                        tujuan = '$tujuan',
                        keterangan = '$keterangan'
                    WHERE id_keluar = $id_keluar
                ");
                if (!$update_keluar) throw new Exception("Gagal memperbarui transaksi");

                mysqli_commit($koneksi);
                header("Location: index.php?status=success_edit");
                exit;
            } catch (Exception $e) {
                mysqli_rollback($koneksi);
                $error = "Gagal memproses transaksi: " . $e->getMessage();
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
                    <h1 class="mb-0 fw-bold text-dark">Edit Barang Keluar</h1>
                    <p class="text-muted small mb-0">Ubah detail transaksi barang keluar.</p>
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
                                <h5 class="fw-bold text-dark mb-0">Formulir Edit Transaksi</h5>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <form action="edit.php?id=<?= $id_keluar ?>" method="POST">
                                <div class="bg-light p-3 rounded-3 mb-4 border border-secondary border-opacity-10">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-box-seam me-2"></i>Pilih Barang</h6>
                                    <div class="col-md-12">
                                        <label for="id_barang" class="form-label fw-semibold text-secondary">Pilih Barang yang Dikeluarkan *</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="bi bi-box2"></i></span>
                                            <select name="id_barang" id="id_barang" class="form-select" required onchange="updateMaxStok()">
                                                <?php while ($row = mysqli_fetch_assoc($query_barang)): 
                                                    // hitung max stok
                                                    $max_stok_show = $row['stok'];
                                                    if($row['id_barang'] == $current_id_barang) {
                                                        $max_stok_show += $current_jumlah;
                                                    }
                                                ?>
                                                    <option value="<?= $row['id_barang'] ?>" data-stok="<?= $max_stok_show ?>" <?= $row['id_barang'] == $current['id_barang'] ? 'selected' : '' ?>>
                                                        [<?= $row['kode_barang'] ?>] <?= htmlspecialchars($row['nama_barang']) ?> - Max Stok: <?= number_format($max_stok_show) ?> <?= $row['satuan'] ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded-3 mb-4 border border-secondary border-opacity-10">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-arrow-up-right-square me-2"></i>Detail Pengeluaran</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="tanggal_keluar" class="form-label fw-semibold text-secondary">Tanggal Keluar *</label>
                                            <input type="date" name="tanggal_keluar" class="form-control" value="<?= htmlspecialchars($current['tanggal_keluar']) ?>" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="jumlah" class="form-label fw-semibold text-secondary">Jumlah Keluar *</label>
                                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="<?= htmlspecialchars($current['jumlah']) ?>" required>
                                            <div class="form-text small text-danger" id="stokWarning" style="display: none;"></div>
                                        </div>

                                        <div class="col-md-12">
                                            <label for="tujuan" class="form-label fw-semibold text-secondary">Tujuan / Pemakai *</label>
                                            <input type="text" name="tujuan" class="form-control" value="<?= htmlspecialchars($current['tujuan']) ?>" required>
                                        </div>

                                        <div class="col-md-12">
                                            <label for="keterangan" class="form-label fw-semibold text-secondary">Keterangan / Alasan</label>
                                            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($current['keterangan']) ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<script>
function updateMaxStok() {
    const select = document.getElementById('id_barang');
    const inputJumlah = document.getElementById('jumlah');
    const warning = document.getElementById('stokWarning');
    
    if(select.selectedIndex > -1) {
        const selectedOption = select.options[select.selectedIndex];
        const stokMax = parseInt(selectedOption.getAttribute('data-stok'));
        
        inputJumlah.setAttribute('max', stokMax);
        warning.style.display = 'block';
        warning.innerHTML = 'Maksimal jumlah yang bisa dikeluarkan: <strong>' + stokMax + '</strong>';
    }
}
// Run once on load
document.addEventListener("DOMContentLoaded", updateMaxStok);
</script>

<?php
include '../../templates/footer.php';
include '../../templates/script.php';
?>
