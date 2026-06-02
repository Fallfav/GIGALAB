<?php
session_start();
include '../config/koneksi.php'; // Naik 1 folder keluar dari folder process/

// PENGAMAN: Cek login & pastikan yang hapus adalah Admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Cek apakah ada ID video yang dilempar di URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Jalankan query hapus data berdasarkan ID
    $query_hapus = mysqli_query($conn, "DELETE FROM video WHERE id = '$id'");

    if ($query_hapus) {
        // Kalau berhasil, langsung balik ke halaman video_belajar.php di luar folder
        header("Location: ../video_belajar.php");
        exit();
    } else {
        // Kalau gagal query, tampilin errornya
        echo "Gagal menghapus video tutorial, brok! Error: " . mysqli_error($conn);
    }
} else {
    // Kalau gak ada ID di URL, balikin paksa ke gallery video
    header("Location: ../video_belajar.php");
    exit();
}
?>