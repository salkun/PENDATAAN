<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php?status=error");
    exit;
}

// Fetch transaction details joined with barang info
$query_transaction = mysqli_query($koneksi, "
    SELECT bm.*, b.kode_barang, b.nama_barang, b.kategori, b.satuan 
    FROM barang_masuk bm
    JOIN barang b ON bm.id_barang = b.id_barang
    WHERE bm.id_masuk = $id
");
$transaction = mysqli_fetch_assoc($query_transaction);

if (!$transaction) {
    header("Location: index.php?status=error");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = mysqli_real_escape_string($koneksi, trim($_POST['kode_barang']));
    $nama_barang = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $new_tanggal_masuk = mysqli_real_escape_string($koneksi, $_POST['tanggal_masuk']);
    $new_jumlah = (int)$_POST['jumlah'];
    $new_supplier = mysqli_real_escape_string($koneksi, trim($_POST['supplier']));
    $new_total_biaya = isset($_POST['total_biaya']) ? (int)preg_replace('/[^0-9]/', '', $_POST['total_biaya']) : 0;
    $new_keterangan = mysqli_real_escape_string($koneksi, trim($_POST['keterangan']));

    if (empty($kode_barang) || empty($nama_barang) || empty($kategori) || empty($satuan) || $new_jumlah <= 0 || empty($new_tanggal_masuk) || empty($new_supplier)) {
        $error = "Semua field yang bertanda bintang (*) wajib diisi dengan benar!";
    } else {
        // Start database transaction
        mysqli_begin_transaction($koneksi);

        try {
            // 1. Revert old stock of old item before doing anything
            $old_id_barang = $transaction['id_barang'];
            $old_jumlah = $transaction['jumlah'];
            
            $revert_query = "UPDATE barang SET stok = stok - $old_jumlah WHERE id_barang = $old_id_barang";
            $revert = mysqli_query($koneksi, $revert_query);
            if (!$revert) throw new Exception("Gagal mengembalikan stok lama");

            // 2. Check if the newly entered kode_barang exists
            $check_barang = mysqli_query($koneksi, "SELECT id_barang FROM barang WHERE kode_barang = '$kode_barang'");
            $barang_row = mysqli_fetch_assoc($check_barang);

            if ($barang_row) {
                // If exists, use it and update details
                $new_id_barang = $barang_row['id_barang'];
                $update_barang = mysqli_query($koneksi, "
                    UPDATE barang 
                    SET nama_barang = '$nama_barang', 
                        kategori = '$kategori', 
                        satuan = '$satuan' 
                    WHERE id_barang = $new_id_barang
                ");
                if (!$update_barang) throw new Exception("Gagal memperbarui data barang");
            } else {
                // If not exists, insert as a new item
                $insert_barang = mysqli_query($koneksi, "
                    INSERT INTO barang (kode_barang, nama_barang, kategori, satuan, stok) 
                    VALUES ('$kode_barang', '$nama_barang', '$kategori', '$satuan', 0)
                ");
                if (!$insert_barang) throw new Exception("Gagal mendaftarkan barang baru");
                
                $new_id_barang = mysqli_insert_id($koneksi);
            }

            // 3. Update the transaction in barang_masuk
            $update_masuk = mysqli_query($koneksi, "
                UPDATE barang_masuk 
                SET id_barang = $new_id_barang, 
                    tanggal_masuk = '$new_tanggal_masuk', 
                    jumlah = $new_jumlah, 
                    supplier = '$new_supplier', 
                    total_biaya = $new_total_biaya,
                    keterangan = '$new_keterangan' 
                WHERE id_masuk = $id
            ");
            if (!$update_masuk) throw new Exception("Gagal memperbarui transaksi masuk");

            // 4. Apply new stock of the new item
            $apply_query = "UPDATE barang SET stok = stok + $new_jumlah WHERE id_barang = $new_id_barang";
            $apply = mysqli_query($koneksi, $apply_query);
            if (!$apply) throw new Exception("Gagal menerapkan stok baru");

            mysqli_commit($koneksi);
            header("Location: index.php?status=success_edit");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $error = "Gagal menyimpan perubahan: " . $e->getMessage();
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
                    <h1 class="mb-0 fw-bold text-dark">Sunting Barang Masuk</h1>
                    <p class="text-muted small mb-0">Ubah data barang dan detail transaksi penerimaan barang masuk.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-indigo">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-indigo">Barang Masuk</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sunting</li>
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
                                <i class="bi bi-pencil-square text-indigo fs-4"></i>
                                <h5 class="fw-bold text-dark mb-0">Formulir Sunting Barang Masuk</h5>
                            </div>
                        </div>

                        <!-- Card Body / Form -->
                        <div class="card-body p-4">
                            <form action="edit.php?id=<?= $id ?>" method="POST">
                                
                                <div class="bg-light p-3 rounded-3 mb-4 border border-secondary border-opacity-10">
                                    <h6 class="fw-bold text-indigo mb-3"><i class="bi bi-box-seam me-2"></i>Informasi Barang</h6>
                                    <div class="row g-3">
                                        <!-- Kode Barang -->
                                        <div class="col-md-6">
                                            <label for="kode_barang" class="form-label fw-semibold text-secondary">Kode Barang *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-qr-code"></i></span>
                                                <input type="text" 
                                                       name="kode_barang" 
                                                       id="kode_barang" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($transaction['kode_barang']) ?>" 
                                                       required>
                                            </div>
                                            <div class="form-text small text-muted">Jika diubah ke kode lain yang terdaftar, transaksi dialihkan ke barang tersebut.</div>
                                        </div>

                                        <!-- Nama Barang -->
                                        <div class="col-md-6">
                                            <label for="nama_barang" class="form-label fw-semibold text-secondary">Nama Barang *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-tag"></i></span>
                                                <input type="text" 
                                                       name="nama_barang" 
                                                       id="nama_barang" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($transaction['nama_barang']) ?>" 
                                                       placeholder="Nama barang / produk" 
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Kategori -->
                                        <div class="col-md-6">
                                            <label for="kategori" class="form-label fw-semibold text-secondary">Kategori *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-grid-fill"></i></span>
                                                <select name="kategori" id="kategori" class="form-select" required>
                                                    <option value="" disabled>Pilih Kategori...</option>
                                                    <option value="Alat Tulis Kantor" <?= $transaction['kategori'] === 'Alat Tulis Kantor' ? 'selected' : '' ?>>Alat Tulis Kantor (ATK)</option>
                                                    <option value="Elektronik" <?= $transaction['kategori'] === 'Elektronik' ? 'selected' : '' ?>>Elektronik</option>
                                                    <option value="Furnitur" <?= $transaction['kategori'] === 'Furnitur' ? 'selected' : '' ?>>Furnitur</option>
                                                    <option value="Medis" <?= $transaction['kategori'] === 'Medis' ? 'selected' : '' ?>>Peralatan Medis</option>
                                                    <option value="Pakaian" <?= $transaction['kategori'] === 'Pakaian' ? 'selected' : '' ?>>Pakaian / Tekstil</option>
                                                    <option value="Lain-lain" <?= $transaction['kategori'] === 'Lain-lain' ? 'selected' : '' ?>>Lain-lain</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Satuan -->
                                        <div class="col-md-6">
                                            <label for="satuan" class="form-label fw-semibold text-secondary">Satuan *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-calculator"></i></span>
                                                <select name="satuan" id="satuan" class="form-select" required>
                                                    <option value="" disabled>Pilih Satuan...</option>
                                                    <option value="Pcs" <?= $transaction['satuan'] === 'Pcs' ? 'selected' : '' ?>>Pcs / Buah</option>
                                                    <option value="Box" <?= $transaction['satuan'] === 'Box' ? 'selected' : '' ?>>Box / Dus</option>
                                                    <option value="Rim" <?= $transaction['satuan'] === 'Rim' ? 'selected' : '' ?>>Rim</option>
                                                    <option value="Lusin" <?= $transaction['satuan'] === 'Lusin' ? 'selected' : '' ?>>Lusin</option>
                                                    <option value="Meter" <?= $transaction['satuan'] === 'Meter' ? 'selected' : '' ?>>Meter</option>
                                                    <option value="Unit" <?= $transaction['satuan'] === 'Unit' ? 'selected' : '' ?>>Unit</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded-3 mb-4 border border-secondary border-opacity-10">
                                    <h6 class="fw-bold text-indigo mb-3"><i class="bi bi-truck-flatbed me-2"></i>Informasi Transaksi Masuk</h6>
                                    <div class="row g-3">
                                        <!-- Tanggal Masuk -->
                                        <div class="col-md-6">
                                            <label for="tanggal_masuk" class="form-label fw-semibold text-secondary">Tanggal Masuk *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-calendar-event"></i></span>
                                                <input type="date" 
                                                       name="tanggal_masuk" 
                                                       id="tanggal_masuk" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($transaction['tanggal_masuk']) ?>" 
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Jumlah -->
                                        <div class="col-md-6">
                                            <label for="jumlah" class="form-label fw-semibold text-secondary">Jumlah Masuk *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-boxes"></i></span>
                                                <input type="number" 
                                                       name="jumlah" 
                                                       id="jumlah" 
                                                       class="form-control" 
                                                       min="1" 
                                                       value="<?= htmlspecialchars($transaction['jumlah']) ?>" 
                                                       placeholder="Jumlah barang masuk" 
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Supplier -->
                                        <div class="col-md-6">
                                            <label for="supplier" class="form-label fw-semibold text-secondary">Nama Supplier *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-building"></i></span>
                                                <input type="text" 
                                                       name="supplier" 
                                                       id="supplier" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($transaction['supplier']) ?>" 
                                                       placeholder="Contoh: PT. Sumber Agung" 
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Total Biaya -->
                                        <div class="col-md-6">
                                            <label for="total_biaya" class="form-label fw-semibold text-secondary">Total Biaya Pembelian (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white fw-bold">Rp</span>
                                                <input type="text" 
                                                       name="total_biaya" 
                                                       id="total_biaya" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($transaction['total_biaya'] ?? 0) ?>" 
                                                       placeholder="Contoh: 500000" 
                                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            </div>
                                            <div class="form-text small text-muted">Kosongkan/0 jika bukan pembelian.</div>
                                        </div>

                                        <!-- Keterangan -->
                                        <div class="col-md-12">
                                            <label for="keterangan" class="form-label fw-semibold text-secondary">Keterangan / Catatan</label>
                                            <textarea name="keterangan" 
                                                      id="keterangan" 
                                                      class="form-control" 
                                                      rows="3" 
                                                      placeholder="Catatan tambahan..."><?= htmlspecialchars($transaction['keterangan']) ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                                        Simpan Perubahan
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

<?php
include '../../templates/footer.php';
include '../../templates/script.php';
?>
