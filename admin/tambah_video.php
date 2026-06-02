<?php 
include '../config/koneksi.php'; 

$pesan = "";
// Logika simpan data link
if (isset($_POST['submit'])) {
    $judul_video = mysqli_real_escape_string($conn, $_POST['judul_video']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    // Sekarang kita ambil link dari input text, bukan file
    $link_video = mysqli_real_escape_string($conn, $_POST['link_video']); 

    if (!empty($link_video)) {
        // Query tetep masuk ke kolom 'nama_file' (biar lo gak perlu ubah struktur tabel)
        $query = "INSERT INTO video (judul_video, nama_file, keterangan) VALUES ('$judul_video', '$link_video', '$keterangan')";
        
        if (mysqli_query($conn, $query)) {
            $pesan = "<div class='alert success'>Link berhasil disimpan, brok! Siap diakses publik.</div>";
        } else {
            $pesan = "<div class='alert error'>Gagal simpan ke database: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $pesan = "<div class='alert error'>Link jangan dikosongin, brok!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Video Link | GIGA LAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #41431B; --secondary: #AEB784; --accent: #E3DBBB;
            --bg: #F8F3E1; --dark: #2C2E14; --white: #ffffff;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--dark); padding: 40px 20px; }
        .container { max-width: 700px; margin: 0 auto; background: var(--white); padding: 40px; border-radius: 25px; border: 3px solid var(--primary); box-shadow: 15px 15px 0 var(--primary); }
        
        h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 10px; }
        .subtitle { color: var(--primary); opacity: 0.7; margin-bottom: 30px; font-weight: 600; font-size: 0.9rem; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 800; font-size: 0.85rem; margin-bottom: 8px; text-transform: uppercase; color: var(--primary); }
        input[type="text"], input[type="url"], textarea {
            width: 100%; padding: 12px; border: 2px solid var(--accent); border-radius: 12px; font-family: 'Inter', sans-serif; box-sizing: border-box; outline: none; transition: 0.3s;
        }
        input:focus, textarea:focus { border-color: var(--primary); background: #fffdf7; }
        
        .btn-submit { background: var(--primary); color: var(--bg); border: none; padding: 15px 30px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; width: 100%; font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase; margin-top: 10px; }
        .btn-submit:hover { background: var(--dark); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        .btn-back { display: inline-block; margin-top: 20px; text-decoration: none; color: var(--primary); font-weight: 700; font-size: 0.9rem; transition: 0.2s; }
        .btn-back:hover { opacity: 0.7; }
        
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .platform-hint { display: flex; gap: 10px; margin-top: 8px; }
        .badge { font-size: 0.7rem; padding: 4px 8px; border-radius: 5px; background: var(--accent); color: var(--primary); font-weight: 800; }
    </style>
</head>
<body>

<div class="container">
    <h2>Link Your Lab Evidence</h2>
    <div class="subtitle">Halo Naufal, silakan masukkan URL video dari platform luar.</div>
    
    <?= $pesan; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Judul Pembuktian / Tutorial</label>
            <input type="text" name="judul_video" placeholder="Misal: Monitoring Server with Prometheus" required>
        </div>

        <div class="form-group">
            <label>URL / Link Video</label>
            <input type="url" name="link_video" placeholder="https://www.youtube.com/watch?v=..." required>
            <div class="platform-hint">
                <span class="badge">YouTube</span>
                <span class="badge">Google Drive</span>
                <span class="badge">TikTok</span>
                <span class="badge">IG / Others</span>
            </div>
        </div>

        <div class="form-group">
            <label>Keterangan / Log Lab</label>
            <textarea name="keterangan" rows="4" placeholder="Jelaskan ringkasan konfigurasi lo di sini..."></textarea>
        </div>

        <button type="submit" name="submit" class="btn-submit">Publish ke GIGA LAB</button>
    </form>

    <a href="../video_belajar.php" class="btn-back">← Kembali ke Video Gallery</a>
</div>

</body>
</html>