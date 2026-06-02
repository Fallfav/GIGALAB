<?php
include '../config/koneksi.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM materi WHERE id=$id");
header("location:../data_materi.php");
?>