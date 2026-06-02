<?php
include '../config/koneksi.php';

// Cek apakah ada ID yang dikirim lewat URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. Ambil nama file dulu sebelum datanya dihapus dari DB
    $ambil_data = mysqli_query($conn, "SELECT file_soal FROM bank_soal WHERE id = '$id'");
    
    if (mysqli_num_rows($ambil_data) > 0) {
        $data = mysqli_fetch_array($ambil_data);
        $nama_file = $data['file_soal'];

        // 2. Hapus file fisik di folder uploads jika filenya ada
        // Pastikan path-nya benar sesuai lokasi folder uploads lo
        if (!empty($nama_file) && file_exists("uploads/" . $nama_file)) {
            unlink("uploads/" . $nama_file); 
        }

        // 3. Baru hapus data dari database
        $query = mysqli_query($conn, "DELETE FROM bank_soal WHERE id = '$id'");

        if ($query) {
            echo "<script>
                    alert('Modul Berhasil Dihapus!');
                    window.location='../bank_soal.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menghapus data di database!');
                    window.location='../bank_soal.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Data tidak ditemukan!');
                window.location='../bank_soal.php';
              </script>";
    }
} else {
    // Kalau nggak ada ID, balikin ke halaman bank soal
    header("Location:../bank_soal.php");
}
?>