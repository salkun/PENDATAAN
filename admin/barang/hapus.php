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
    // Delete item
    $delete = mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang = $id");
    if ($delete) {
        header("Location: index.php?status=success_delete");
    } else {
        header("Location: index.php?status=error");
    }
} else {
    header("Location: index.php?status=error");
}
exit;
