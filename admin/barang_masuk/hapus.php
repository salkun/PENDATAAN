<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Get transaction details first
    $query = mysqli_query($koneksi, "SELECT * FROM barang_masuk WHERE id_masuk = $id");
    $transaction = mysqli_fetch_assoc($query);

    if ($transaction) {
        $id_barang = $transaction['id_barang'];
        $jumlah = $transaction['jumlah'];

        // Start transaction for atomicity
        mysqli_begin_transaction($koneksi);

        try {
            // 1. Revert stock
            $revert = mysqli_query($koneksi, "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = $id_barang");
            if (!$revert) throw new Exception("Gagal mengembalikan stok");

            // 2. Delete transaction record
            $delete = mysqli_query($koneksi, "DELETE FROM barang_masuk WHERE id_masuk = $id");
            if (!$delete) throw new Exception("Gagal menghapus transaksi");

            mysqli_commit($koneksi);
            header("Location: index.php?status=success_delete");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            header("Location: index.php?status=error");
            exit;
        }
    } else {
        header("Location: index.php?status=error");
        exit;
    }
} else {
    header("Location: index.php?status=error");
    exit;
}
