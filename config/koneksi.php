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

// Auto-detect base URL
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
// Check if we're running inside a subfolder like /Pendataan/
if (preg_match('#^(/[^/]+)/#', $script_name, $matches)) {
    $base_url = $matches[1]; // e.g. "/Pendataan"
} else {
    $base_url = ""; // root domain (e.g. pendataan.test)
}
