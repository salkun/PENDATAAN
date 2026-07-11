<?php
require '../config/auth.php';
require '../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'user') {
    header("Location: ../admin/dashboard.php");
    exit;
}

// Retrieve filter parameters
$jenis_laporan = isset($_GET['jenis']) ? $_GET['jenis'] : 'stok'; 
$id_barang = isset($_GET['id_barang']) ? (int)$_GET['id_barang'] : 0;
$bulan_tahun = isset($_GET['bulan_tahun']) ? $_GET['bulan_tahun'] : ''; 

// Build title based on filter
$title = "Laporan ";
$subtitle = "";

// Fetch specific item name if filtered
$nama_barang_filter = "Semua Barang";
if ($id_barang > 0) {
    $q_brg = mysqli_query($koneksi, "SELECT nama_barang FROM barang WHERE id_barang = $id_barang");
    if($r_brg = mysqli_fetch_assoc($q_brg)) {
        $nama_barang_filter = $r_brg['nama_barang'];
    }
}

$query_result = null;

if ($jenis_laporan === 'stok') {
    $title .= "Data & Stok Barang";
    $subtitle = "Barang: $nama_barang_filter";
    
    $where_stok = "";
    if ($id_barang > 0) {
        $where_stok = "WHERE b.id_barang = $id_barang";
    }
    
    $query_result = mysqli_query($koneksi, "
        SELECT b.kode_barang, b.nama_barang, b.kategori, b.satuan, b.keterangan,
               (COALESCE((SELECT SUM(jumlah) FROM barang_masuk WHERE id_barang = b.id_barang), 0) - 
                COALESCE((SELECT SUM(jumlah) FROM barang_keluar WHERE id_barang = b.id_barang), 0)) AS stok,
               (SELECT COALESCE(total_biaya / jumlah, 0) FROM barang_masuk WHERE id_barang = b.id_barang ORDER BY id_masuk DESC LIMIT 1) AS harga_beli
        FROM barang b
        $where_stok
        ORDER BY b.nama_barang ASC
    ");
} elseif ($jenis_laporan === 'masuk') {
    $title .= "Riwayat Barang Masuk";
    if (!empty($bulan_tahun)) {
        $subtitle = "Periode: " . date('F Y', strtotime($bulan_tahun . '-01')) . " | Barang: $nama_barang_filter";
        $year = date('Y', strtotime($bulan_tahun . '-01'));
        $month = date('m', strtotime($bulan_tahun . '-01'));
        $where_masuk = "WHERE YEAR(bm.tanggal_masuk) = '$year' AND MONTH(bm.tanggal_masuk) = '$month'";
    } else {
        $subtitle = "Periode: Semua Waktu | Barang: $nama_barang_filter";
        $where_masuk = "WHERE 1=1";
    }
    if ($id_barang > 0) {
        $where_masuk .= " AND bm.id_barang = $id_barang";
    }
    
    $query_result = mysqli_query($koneksi, "
        SELECT bm.tanggal_masuk AS tanggal, b.kode_barang, b.nama_barang, bm.jumlah, b.satuan, bm.supplier AS pihak_terkait, bm.total_biaya, bm.keterangan
        FROM barang_masuk bm
        JOIN barang b ON bm.id_barang = b.id_barang
        $where_masuk
        ORDER BY bm.tanggal_masuk ASC
    ");
} elseif ($jenis_laporan === 'keluar') {
    $title .= "Riwayat Barang Keluar";
    if (!empty($bulan_tahun)) {
        $subtitle = "Periode: " . date('F Y', strtotime($bulan_tahun . '-01')) . " | Barang: $nama_barang_filter";
        $year = date('Y', strtotime($bulan_tahun . '-01'));
        $month = date('m', strtotime($bulan_tahun . '-01'));
        $where_keluar = "WHERE YEAR(bk.tanggal_keluar) = '$year' AND MONTH(bk.tanggal_keluar) = '$month'";
    } else {
        $subtitle = "Periode: Semua Waktu | Barang: $nama_barang_filter";
        $where_keluar = "WHERE 1=1";
    }
    if ($id_barang > 0) {
        $where_keluar .= " AND bk.id_barang = $id_barang";
    }
    
    $query_result = mysqli_query($koneksi, "
        SELECT bk.tanggal_keluar AS tanggal, b.kode_barang, b.nama_barang, bk.jumlah, b.satuan, bk.tujuan AS pihak_terkait, bk.keterangan
        FROM barang_keluar bk
        JOIN barang b ON bk.id_barang = b.id_barang
        $where_keluar
        ORDER BY bk.tanggal_keluar ASC
    ");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Cetak Laporan</title>
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat img {
            width: 80px;
            height: auto;
            margin-right: 20px;
        }
        .kop-text {
            text-align: center;
            flex-grow: 1;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
        }
        .kop-text h2 {
            margin: 5px 0 0;
            font-size: 18px;
            font-weight: normal;
        }
        .kop-text p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        .laporan-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .laporan-title h3 {
            margin: 0 0 5px;
            font-family: 'Inter', sans-serif;
            text-transform: uppercase;
        }
        .laporan-title p {
            margin: 0;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .ttd {
            width: 250px;
            float: right;
            text-align: center;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            margin-top: 30px;
        }
        .ttd p {
            margin: 5px 0;
        }
        .ttd .nama {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        .print-btn {
            display: block;
            margin: 0 auto 20px;
            padding: 10px 20px;
            background-color: #1b5e20;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }
        .print-btn:hover {
            background-color: #144d18;
        }
    </style>
</head>
<body>

    <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>

    <div class="kop-surat">
        <img src="../assets/img/logo-pa.png" alt="Logo PA">
        <div class="kop-text">
            <h1>PENGADILAN AGAMA</h1>
            <h2>Sistem Inventarisasi & Manajemen Barang</h2>
            <p>Jalan Pengadilan Agama No. 1, Kota, Provinsi, Kode Pos: 12345 | Telp: (021) 1234567</p>
        </div>
    </div>

    <div class="laporan-title">
        <h3><?= $title ?></h3>
        <p><?= $subtitle ?></p>
    </div>

    <table>
        <thead>
            <?php if ($jenis_laporan === 'stok'): ?>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Kode Barang</th>
                    <th style="width: 25%;">Nama Barang</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 10%;">Stok Saat Ini</th>
                    <th style="width: 10%;">Satuan</th>
                    <th style="width: 10%;">Harga Beli</th>
                    <th style="width: 10%;">Total Harga</th>
                </tr>
            <?php else: ?>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 15%;">Kode Barang</th>
                    <th style="width: 20%;">Nama Barang</th>
                    <th style="width: 10%;">Jumlah</th>
                    <th style="width: 10%;">Satuan</th>
                    <?php if ($jenis_laporan === 'masuk'): ?>
                        <th style="width: 12%;">Harga Satuan</th>
                        <th style="width: 15%;">Total Biaya</th>
                    <?php endif; ?>
                    <th style="width: 20%;"><?= $jenis_laporan === 'masuk' ? 'Supplier' : 'Tujuan' ?></th>
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
                    } elseif ($jenis_laporan === 'stok') {
                        $total_jumlah += $row['stok'];
                        $total_biaya += ($row['stok'] * $row['harga_beli']);
                    }
                ?>
                    <?php if ($jenis_laporan === 'stok'): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['kode_barang']) ?></td>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <td class="text-center"><b><?= number_format($row['stok']) ?></b></td>
                            <td class="text-center"><?= htmlspecialchars($row['satuan']) ?></td>
                            <td class="text-right"><?= $row['harga_beli'] > 0 ? 'Rp ' . number_format($row['harga_beli'], 0, ',', '.') : '-' ?></td>
                            <td class="text-right"><?= ($row['stok'] * $row['harga_beli']) > 0 ? 'Rp ' . number_format($row['stok'] * $row['harga_beli'], 0, ',', '.') : '-' ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['kode_barang']) ?></td>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td class="text-center"><b><?= number_format($row['jumlah']) ?></b></td>
                            <td class="text-center"><?= htmlspecialchars($row['satuan']) ?></td>
                            <?php if ($jenis_laporan === 'masuk'): ?>
                                <td class="text-right"><?= isset($row['total_biaya']) && $row['total_biaya'] > 0 ? 'Rp ' . number_format($row['total_biaya'] / $row['jumlah'], 0, ',', '.') : '-' ?></td>
                                <td class="text-right"><?= isset($row['total_biaya']) && $row['total_biaya'] > 0 ? 'Rp ' . number_format($row['total_biaya'], 0, ',', '.') : '-' ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($row['pihak_terkait']) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endwhile; ?>

                <?php if ($jenis_laporan === 'stok'): ?>
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                        <td colspan="4" class="text-right">Total:</td>
                        <td class="text-center"><?= number_format($total_jumlah) ?></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">Rp <?= number_format($total_biaya, 0, ',', '.') ?></td>
                    </tr>
                <?php elseif ($jenis_laporan === 'masuk'): ?>
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                        <td colspan="4" class="text-right">Total:</td>
                        <td class="text-center"><?= number_format($total_jumlah) ?></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">Rp <?= number_format($total_biaya, 0, ',', '.') ?></td>
                        <td></td>
                    </tr>
                <?php elseif ($jenis_laporan === 'keluar'): ?>
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                        <td colspan="4" class="text-right">Total:</td>
                        <td class="text-center"><?= number_format($total_jumlah) ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $jenis_laporan === 'stok' ? '8' : ($jenis_laporan === 'masuk' ? '9' : '7') ?>" class="text-center">Tidak ada data untuk periode/filter ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd">
        <p>Mengetahui,</p>
        <p><b>Petugas</b></p>
        <p class="nama"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></p>
        <p>NIP. -</p>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
