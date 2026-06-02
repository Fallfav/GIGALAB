<?php 
session_start();
include '../config/koneksi.php'; // Naik 1 folder ke config

// PENGAMAN: Cek login & Role Admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$pesan = "";
$id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. TARIK DATA LAMA
$query_lama = mysqli_query($conn, "SELECT * FROM video WHERE id='$id'");
$data = mysqli_fetch_array($query_lama);

if (!$data) {
    echo "Data video tidak ditemukan, brok!";
    exit();
}

// 2. LOGIKA UPDATE DATA
if (isset($_POST['update'])) {
    $judul_video = mysqli_real_escape_string($conn, $_POST['judul_video']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $link_video = mysqli_real_escape_string($conn, $_POST['link_video']); 

    if (!empty($link_video)) {
        $query_update = "UPDATE video SET 
                         judul_video = '$judul_video', 
                         nama_file = '$link_video', 
                         keterangan = '$keterangan' 
                         WHERE id = '$id'";
        
        if (mysqli_query($conn, $query_update)) {
            $pesan = "<div class='alert success'>Video berhasil diupdate, brok! <a href='../video_belajar.php' style='color:inherit;'>Cek Gallery</a></div>";
            // Refresh data biar inputan langsung berubah
            $query_lama = mysqli_query($conn, "SELECT * FROM video WHERE id='$id'");
            $data = mysqli_fetch_array($query_lama);
        } else {
            $pesan = "<div class='alert error'>Gagal update data: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $pesan = "<div class='alert error'>Link jangan dikosongin ya!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Video | GIGA LAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #41431B; --secondary: #AEB784; --accent: #E3DBBB;
            --bg: #F8F3E1; --dark: #2C2E14; --white: #ffffff;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--dark); padding: 40px 20px; }
        
        .container { 
            max-width: 600px; margin: 0 auto; background: var(--white); 
            padding: 40px; border-radius: 25px; border: 3px solid var(--primary); 
            box-shadow: 12px 12px 0 var(--primary); 
        }
        
        h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 5px; letter-spacing: -1px; }
        .subtitle { color: var(--primary); opacity: 0.7; margin-bottom: 30px; font-weight: 600; font-size: 0.9rem; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 800; font-size: 0.85rem; margin-bottom: 8px; text-transform: uppercase; color: var(--primary); }
        
        input[type="text"], input[type="url"], textarea {
            width: 100%; padding: 14px; border: 2px solid var(--accent); 
            border-radius: 12px; font-family: 'Inter', sans-serif; box-sizing: border-box; 
            outline: none; transition: 0.3s; background: #fafafa;
        }
        
        input:focus, textarea:focus { border-color: var(--primary); background: #fff; }
        
        /* --- HOLLOW BUTTON UPDATE --- */
        .btn-update { 
            background: var(--primary); color: var(--bg); border: 2px solid var(--primary); 
            padding: 16px 30px; border-radius: 12px; font-weight: 800; cursor: pointer; 
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); width: 100%; 
            font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase; margin-top: 10px; 
        }
        
        .btn-update:hover { 
            background: transparent; color: var(--primary) !important; 
            transform: translateY(-3px); 
        }
        
        .btn-back { 
            display: inline-flex; align-items: center; justify-content: center;
            margin-top: 25px; text-decoration: none; color: var(--primary); 
            font-weight: 800; font-size: 0.85rem; transition: 0.3s; 
            padding: 10px 20px; border-radius: 10px; border: 2px solid transparent;
            text-transform: uppercase;
        }
        
        .btn-back:hover { 
            border-color: var(--primary); 
            transform: translateX(-5px);
        }
        
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; border: 2px solid transparent; }
        .success { background: #e8f5e9; color: #2e7d32; border-color: #a5d6a7; }
        .error { background: #ffebee; color: #c62828; border-color: #ef9a9a; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Lab Video</h2>
    <div class="subtitle">Update informasi video tutorial atau pembuktian lab.</div>
    
    <?= $pesan; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Judul Pembuktian</label>
            <input type="text" name="judul_video" value="<?= htmlspecialchars($data['judul_video']); ?>" required>
        </div>

        <div class="form-group">
            <label>URL / Link Video</label>
            <input type="url" name="link_video" value="<?= htmlspecialchars($data['nama_file']); ?>" required>
        </div>

        <div class="form-group">
            <label>Keterangan / Log Lab</label>
            <textarea name="keterangan" rows="4"><?= htmlspecialchars($data['keterangan']); ?></textarea>
        </div>

        <button type="submit" name="update" class="btn-update">Update Changes</button>
    </form>

    <div style="text-align: center;">
        <a href="../video_belajar.php" class="btn-back">← Batal & Kembali</a>
    </div>
</div>

</body>
</html>