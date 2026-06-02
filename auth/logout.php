<?php
session_start();

// Hapus semua data di dalam session
$_SESSION = array();

// Kalau mau bener-bener bersih, hapus juga cookie session-nya
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Tendang balik ke halaman login dengan pesan sukses (optional)
header("Location: login.php?pesan=logout_berhasil");
exit();
?>