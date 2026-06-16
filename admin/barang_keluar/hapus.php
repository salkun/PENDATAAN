<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

$id_keluar = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_keluar > 0) {
    mysqli_begin_transaction($koneksi);
    try {
        // We just delete it, since the stock is calculated dynamically based on existing rows
        $delete = mysqli_query($koneksi, "DELETE FROM barang_keluar WHERE id_keluar = $id_keluar");
        if (!$delete) throw new Exception("Gagal menghapus data transaksi.");

        mysqli_commit($koneksi);
        header("Location: index.php?status=success_delete");
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        header("Location: index.php?status=error");
    }
} else {
    header("Location: index.php?status=error");
}
exit;
