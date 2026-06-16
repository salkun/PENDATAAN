<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

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
        // Validate stock
        $query_cek = mysqli_query($koneksi, "
            SELECT (COALESCE((SELECT SUM(jumlah) FROM barang_masuk WHERE id_barang = $id_barang), 0) - 
                    COALESCE((SELECT SUM(jumlah) FROM barang_keluar WHERE id_barang = $id_barang), 0)) AS stok 
            FROM barang WHERE id_barang = $id_barang
        ");
        
        $row_cek = mysqli_fetch_assoc($query_cek);
        $stok_tersedia = $row_cek ? $row_cek['stok'] : 0;
        
        if ($jumlah > $stok_tersedia) {
            $error = "Jumlah pengeluaran ($jumlah) melebihi stok yang tersedia ($stok_tersedia)!";
        } else {
            // Start database transaction
            mysqli_begin_transaction($koneksi);

            try {
                // Insert into barang_keluar
                $insert_keluar = mysqli_query($koneksi, "
                    INSERT INTO barang_keluar (id_barang, tanggal_keluar, jumlah, tujuan, keterangan) 
                    VALUES ($id_barang, '$tanggal_keluar', $jumlah, '$tujuan', '$keterangan')
                ");
                if (!$insert_keluar) throw new Exception("Gagal mencatat transaksi keluar");

                mysqli_commit($koneksi);
                header("Location: index.php?status=success_add");
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
    <!-- Content Header -->
    <div class="app-content-header py-4 bg-white border-bottom mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="mb-0 fw-bold text-dark">Tambah Barang Keluar</h1>
                    <p class="text-muted small mb-0">Catat pengeluaran barang dengan memilih barang yang tersedia.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-success">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-success">Barang Keluar</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Body -->
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
                        <!-- Card Header -->
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-dash-circle-fill text-danger fs-4"></i>
                                <h5 class="fw-bold text-dark mb-0">Formulir Barang Keluar</h5>
                            </div>
                        </div>

                        <!-- Card Body / Form -->
                        <div class="card-body p-4">
                            <form action="tambah.php" method="POST">
                                
                                <div class="bg-light p-3 rounded-3 mb-4 border border-secondary border-opacity-10">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-box-seam me-2"></i>Pilih Barang</h6>
                                    <div class="row g-3">
                                        <!-- Barang Dropdown -->
                                        <div class="col-md-12">
                                            <label for="id_barang" class="form-label fw-semibold text-secondary">Pilih Barang yang Dikeluarkan *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-box2"></i></span>
                                                <select name="id_barang" id="id_barang" class="form-select" required onchange="updateMaxStok()">
                                                    <option value="" disabled selected>-- Cari dan Pilih Barang --</option>
                                                    <?php while ($row = mysqli_fetch_assoc($query_barang)): ?>
                                                        <option value="<?= $row['id_barang'] ?>" data-stok="<?= $row['stok'] ?>">
                                                            [<?= $row['kode_barang'] ?>] <?= htmlspecialchars($row['nama_barang']) ?> - Stok: <?= number_format($row['stok']) ?> <?= $row['satuan'] ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded-3 mb-4 border border-secondary border-opacity-10">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-arrow-up-right-square me-2"></i>Detail Pengeluaran</h6>
                                    <div class="row g-3">
                                        <!-- Tanggal Keluar -->
                                        <div class="col-md-6">
                                            <label for="tanggal_keluar" class="form-label fw-semibold text-secondary">Tanggal Keluar *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-calendar-event"></i></span>
                                                <input type="date" 
                                                       name="tanggal_keluar" 
                                                       id="tanggal_keluar" 
                                                       class="form-control" 
                                                       value="<?= date('Y-m-d') ?>" 
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Jumlah -->
                                        <div class="col-md-6">
                                            <label for="jumlah" class="form-label fw-semibold text-secondary">Jumlah Keluar *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-boxes"></i></span>
                                                <input type="number" 
                                                       name="jumlah" 
                                                       id="jumlah" 
                                                       class="form-control" 
                                                       min="1" 
                                                       placeholder="Jumlah pengeluaran" 
                                                       required>
                                            </div>
                                            <div class="form-text small text-danger" id="stokWarning" style="display: none;"></div>
                                        </div>

                                        <!-- Tujuan / Pemakai -->
                                        <div class="col-md-12">
                                            <label for="tujuan" class="form-label fw-semibold text-secondary">Tujuan / Pemakai *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-person-badge"></i></span>
                                                <input type="text" 
                                                       name="tujuan" 
                                                       id="tujuan" 
                                                       class="form-control" 
                                                       placeholder="Contoh: Ruang Rapat, Staf IT, Kegiatan A" 
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Keterangan -->
                                        <div class="col-md-12">
                                            <label for="keterangan" class="form-label fw-semibold text-secondary">Keterangan / Alasan</label>
                                            <textarea name="keterangan" 
                                                      id="keterangan" 
                                                      class="form-control" 
                                                      rows="3" 
                                                      placeholder="Catatan tambahan mengenai penggunaan (opsional)"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                                        Catat Pengeluaran
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

<script>
function updateMaxStok() {
    const select = document.getElementById('id_barang');
    const inputJumlah = document.getElementById('jumlah');
    const warning = document.getElementById('stokWarning');
    
    if(select.selectedIndex > 0) {
        const selectedOption = select.options[select.selectedIndex];
        const stokMax = parseInt(selectedOption.getAttribute('data-stok'));
        
        inputJumlah.setAttribute('max', stokMax);
        warning.style.display = 'block';
        warning.innerHTML = 'Maksimal jumlah yang bisa dikeluarkan: <strong>' + stokMax + '</strong>';
    } else {
        inputJumlah.removeAttribute('max');
        warning.style.display = 'none';
    }
}
</script>

<?php
include '../../templates/footer.php';
include '../../templates/script.php';
?>
