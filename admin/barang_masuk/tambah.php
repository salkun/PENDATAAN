<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

// Generate auto-code suggestions for convenience (optional)
$query_code = mysqli_query($koneksi, "SELECT MAX(id_barang) as max_id FROM barang");
$row_code = mysqli_fetch_assoc($query_code);
$next_id = ($row_code['max_id'] ?? 0) + 1;
$auto_code = "BRG-" . str_pad($next_id, 4, "0", STR_PAD_LEFT);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = mysqli_real_escape_string($koneksi, trim($_POST['kode_barang']));
    $nama_barang = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $tanggal_masuk = mysqli_real_escape_string($koneksi, $_POST['tanggal_masuk']);
    $jumlah = (int)$_POST['jumlah'];
    $supplier = mysqli_real_escape_string($koneksi, trim($_POST['supplier']));
    $keterangan = mysqli_real_escape_string($koneksi, trim($_POST['keterangan']));

    if (empty($kode_barang) || empty($nama_barang) || empty($kategori) || empty($satuan) || $jumlah <= 0 || empty($tanggal_masuk) || empty($supplier)) {
        $error = "Semua field yang bertanda bintang (*) wajib diisi dengan benar!";
    } else {
        // Start database transaction
        mysqli_begin_transaction($koneksi);

        try {
            // 1. Check if the item already exists in the barang table by its code
            $check_barang = mysqli_query($koneksi, "SELECT id_barang FROM barang WHERE kode_barang = '$kode_barang'");
            $barang_row = mysqli_fetch_assoc($check_barang);

            if ($barang_row) {
                // If exists, get the id_barang
                $id_barang = $barang_row['id_barang'];
                
                // Optionally update name, category, and unit to match current input
                $update_barang = mysqli_query($koneksi, "
                    UPDATE barang 
                    SET nama_barang = '$nama_barang', 
                        kategori = '$kategori', 
                        satuan = '$satuan' 
                    WHERE id_barang = $id_barang
                ");
                if (!$update_barang) throw new Exception("Gagal memperbarui data barang");
            } else {
                // If not exists, insert it as a new item
                $insert_barang = mysqli_query($koneksi, "
                    INSERT INTO barang (kode_barang, nama_barang, kategori, satuan, stok) 
                    VALUES ('$kode_barang', '$nama_barang', '$kategori', '$satuan', 0)
                ");
                if (!$insert_barang) throw new Exception("Gagal mendaftarkan barang baru");
                
                $id_barang = mysqli_insert_id($koneksi);
            }

            // 2. Insert into barang_masuk
            $insert_masuk = mysqli_query($koneksi, "
                INSERT INTO barang_masuk (id_barang, tanggal_masuk, jumlah, supplier, keterangan) 
                VALUES ($id_barang, '$tanggal_masuk', $jumlah, '$supplier', '$keterangan')
            ");
            if (!$insert_masuk) throw new Exception("Gagal mencatat transaksi masuk");

            mysqli_commit($koneksi);
            header("Location: index.php?status=success_add");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $error = "Gagal memproses transaksi: " . $e->getMessage();
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
                    <h1 class="mb-0 fw-bold text-dark">Tambah Barang Masuk</h1>
                    <p class="text-muted small mb-0">Catat penerimaan stok dengan mengisi detail barang dan transaksi.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-indigo">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-indigo">Barang Masuk</a></li>
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
                                <i class="bi bi-plus-circle-fill text-indigo fs-4"></i>
                                <h5 class="fw-bold text-dark mb-0">Formulir Barang Masuk</h5>
                            </div>
                        </div>

                        <!-- Card Body / Form -->
                        <div class="card-body p-4">
                            <form action="tambah.php" method="POST">
                                
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
                                                       placeholder="Contoh: <?= htmlspecialchars($auto_code) ?>" 
                                                       required>
                                            </div>
                                            <div class="form-text small text-muted">Jika kode sudah ada di database, data barang akan otomatis terhubung.</div>
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
                                                    <option value="" disabled selected>Pilih Kategori...</option>
                                                    <option value="Alat Tulis Kantor">Alat Tulis Kantor (ATK)</option>
                                                    <option value="Elektronik">Elektronik</option>
                                                    <option value="Furnitur">Furnitur</option>
                                                    <option value="Medis">Peralatan Medis</option>
                                                    <option value="Pakaian">Pakaian / Tekstil</option>
                                                    <option value="Lain-lain">Lain-lain</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Satuan -->
                                        <div class="col-md-6">
                                            <label for="satuan" class="form-label fw-semibold text-secondary">Satuan *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-calculator"></i></span>
                                                <select name="satuan" id="satuan" class="form-select" required>
                                                    <option value="" disabled selected>Pilih Satuan...</option>
                                                    <option value="Pcs">Pcs / Buah</option>
                                                    <option value="Box">Box / Dus</option>
                                                    <option value="Rim">Rim</option>
                                                    <option value="Lusin">Lusin</option>
                                                    <option value="Meter">Meter</option>
                                                    <option value="Unit">Unit</option>
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
                                                       value="<?= date('Y-m-d') ?>" 
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
                                                       placeholder="Jumlah barang masuk" 
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Supplier -->
                                        <div class="col-md-12">
                                            <label for="supplier" class="form-label fw-semibold text-secondary">Nama Supplier *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-building"></i></span>
                                                <input type="text" 
                                                       name="supplier" 
                                                       id="supplier" 
                                                       class="form-control" 
                                                       placeholder="Contoh: PT. Sumber Agung, CV. Jaya Abadi" 
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Keterangan -->
                                        <div class="col-md-12">
                                            <label for="keterangan" class="form-label fw-semibold text-secondary">Keterangan / Catatan</label>
                                            <textarea name="keterangan" 
                                                      id="keterangan" 
                                                      class="form-control" 
                                                      rows="3" 
                                                      placeholder="Catatan tambahan mengenai pengiriman (opsional)"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                                        Simpan Transaksi
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
