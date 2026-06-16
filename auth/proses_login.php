<?php

session_start();

require '../config/koneksi.php';

$username = mysqli_real_escape_string(
    $koneksi,
    $_POST['username']
);

$password = $_POST['password'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM users
     WHERE username='$username'"
);

$user = mysqli_fetch_assoc($query);

if($user)
{
    if($password === $user['password'])
    {

        $_SESSION['login'] = true;

        $_SESSION['id_user']
        = $user['id_user'];

        $_SESSION['nama_lengkap']
        = $user['nama_lengkap'];

        $_SESSION['role']
        = $user['role'];

        if($user['role'] == 'admin')
        {
            header(
                "Location: ../admin/dashboard.php"
            );
        }
        else
        {
            header(
                "Location: ../user/dashboard.php"
            );
        }

        exit;
    }
}

header("Location: login.php?error=invalid");
exit;