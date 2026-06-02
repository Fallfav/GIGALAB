<?php
$host = "localhost";
$user = "root";
$pass = ""; // Jika di CentOS lo setting password, isi di sini
$db   = "web_gigalks"; // Sesuaikan dengan database yang ada di MariaDB lo

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>