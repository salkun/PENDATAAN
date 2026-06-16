<?php
require '../../config/auth.php';
require '../../config/koneksi.php';

// Check role
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../user/dashboard.php");
    exit;
}

$id_user = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_user > 0) {
    // Prevent self-deletion
    if ($id_user == $_SESSION['id_user']) {
        header("Location: index.php?status=error_self_delete");
        exit;
    }

    $delete = mysqli_query($koneksi, "DELETE FROM users WHERE id_user = $id_user");
    if ($delete) {
        header("Location: index.php?status=success_delete");
    } else {
        header("Location: index.php?status=error");
    }
} else {
    header("Location: index.php?status=error");
}
exit;
