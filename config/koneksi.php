<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "joki1";

$koneksi = mysqli_connect(
    $host,
    $user,
    $pass,
    $db
);

if(!$koneksi){
    die("Koneksi gagal");
}

$base_url = "";