<?php
$host = "localhost";
$user = "root"; // default user phpMyAdmin
$pass = "";     // default kosong jika pakai XAMPP
$db   = "serverhosting_db";

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
