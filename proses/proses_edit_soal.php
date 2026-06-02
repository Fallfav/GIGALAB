<?php
include '../config/koneksi.php';

$id        = $_POST['id'];
$nama_soal = $_POST['nama_soal'];
$level     = $_POST['level'];
$tahun     = $_POST['tahun'];

// Cek apakah user upload file baru
if ($_FILES['file_soal']['name'] != "") {
    // Ambil data lama buat hapus file lama
    $data = mysqli_query($conn, "SELECT file_soal FROM bank_soal WHERE id='$id'");
    $s = mysqli_fetch_array($data);
    unlink("uploads/" . $s['file_soal']);

    // Upload file baru
    $file_name = $_FILES['file_soal']['name'];
    $tmp_name  = $_FILES['file_soal']['tmp_name'];
    move_uploaded_file($tmp_name, "uploads/" . $file_name);

    // Update dengan file baru
    $query = mysqli_query($conn, "UPDATE bank_soal SET nama_soal='$nama_soal', level='$level', tahun='$tahun', file_soal='$file_name' WHERE id='$id'");
} else {
    // Update tanpa ganti file
    $query = mysqli_query($conn, "UPDATE bank_soal SET nama_soal='$nama_soal', level='$level', tahun='$tahun' WHERE id='$id'");
}

if ($query) {
    echo "<script>alert('Data Berhasil Diperbarui!'); window.location='../bank_soal.php';</script>";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>