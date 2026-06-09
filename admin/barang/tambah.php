<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

// Generate auto-code
$query_code = mysqli_query($koneksi, "SELECT MAX(id_barang) as max_id FROM barang");
$row_code = mysqli_fetch_assoc($query_code);
$next_id = ($row_code['max_id'] ?? 0) + 1;
$auto_code = "BRG-" . str_pad($next_id, 4, "0", STR_PAD_LEFT);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $stok = (int)$_POST['stok'];
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

    // Check if code already exists
    $check_code = mysqli_query($koneksi, "SELECT * FROM barang WHERE kode_barang = '$kode_barang'");
    if (mysqli_num_rows($check_code) > 0) {
        $error = "Kode barang sudah terdaftar!";
    } else {
        $insert = mysqli_query($koneksi, "
            INSERT INTO barang (kode_barang, nama_barang, kategori, satuan, stok, keterangan) 
            VALUES ('$kode_barang', '$nama_barang', '$kategori', '$satuan', $stok, '$keterangan')
        ");
        if ($insert) {
            header("Location: index.php?status=success_add");
            exit;
        } else {
            header("Location: index.php?status=error");
            exit;
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
                    <h1 class="mb-0 fw-bold text-dark">Tambah Barang</h1>
                    <p class="text-muted small mb-0">Tambahkan produk atau item inventaris baru ke sistem gudang.</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>/admin/dashboard.php" class="text-decoration-none text-indigo">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-indigo">Data Barang</a></li>
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
                                <h5 class="fw-bold text-dark mb-0">Formulir Tambah Barang</h5>
                            </div>
                        </div>

                        <!-- Card Body / Form -->
                        <div class="card-body p-4">
                            <form action="tambah.php" method="POST">
                                <div class="row g-3">
                                    <!-- Kode Barang -->
                                    <div class="col-md-6">
                                        <label for="kode_barang" class="form-label fw-semibold text-secondary">Kode Barang</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-qr-code"></i></span>
                                            <input type="text" 
                                                   name="kode_barang" 
                                                   id="kode_barang" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($auto_code) ?>" 
                                                   required>
                                        </div>
                                        <div class="form-text small text-muted">Kode barang unik otomatis terisi, bisa disunting manual.</div>
                                    </div>

                                    <!-- Nama Barang -->
                                    <div class="col-md-6">
                                        <label for="nama_barang" class="form-label fw-semibold text-secondary">Nama Barang</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                                            <input type="text" 
                                                   name="nama_barang" 
                                                   id="nama_barang" 
                                                   class="form-control" 
                                                   placeholder="Contoh: Kertas A4, Laptop, Kursi" 
                                                   required>
                                        </div>
                                    </div>

                                    <!-- Kategori -->
                                    <div class="col-md-6">
                                        <label for="kategori" class="form-label fw-semibold text-secondary">Kategori</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-grid-fill"></i></span>
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
                                        <label for="satuan" class="form-label fw-semibold text-secondary">Satuan</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-calculator"></i></span>
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

                                    <!-- Stok Awal -->
                                    <div class="col-md-12">
                                        <label for="stok" class="form-label fw-semibold text-secondary">Stok Awal</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-boxes"></i></span>
                                            <input type="number" 
                                                   name="stok" 
                                                   id="stok" 
                                                   class="form-control" 
                                                   value="0" 
                                                   min="0" 
                                                   required>
                                        </div>
                                        <div class="form-text small text-muted">Jumlah stok awal yang tersedia saat barang didaftarkan.</div>
                                    </div>

                                    <!-- Keterangan -->
                                    <div class="col-md-12">
                                        <label for="keterangan" class="form-label fw-semibold text-secondary">Keterangan / Catatan</label>
                                        <textarea name="keterangan" 
                                                  id="keterangan" 
                                                  class="form-control" 
                                                  rows="3" 
                                                  placeholder="Spesifikasi barang, lokasi penyimpanan, dsb (opsional)"></textarea>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                                        Simpan Barang
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
