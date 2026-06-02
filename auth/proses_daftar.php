<?php
include '../config/koneksi.php';

if (isset($_POST['daftar'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    // Enkripsi password biar Admin MariaDB pun gak bisa liat pass aslinya
    $password_aman = password_hash($pass, PASSWORD_BCRYPT);

    // Default role adalah 'user'
    $query = "INSERT INTO users (username, email, password, role) 
              VALUES ('$user', '$email', '$password_aman', 'user')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pendaftaran sukses! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>