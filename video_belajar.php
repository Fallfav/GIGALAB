<?php 
session_start();
include 'config/koneksi.php'; 

// PENGAMAN: Cek login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil Role
$role_sekarang = $_SESSION['role'] ?? 'user';

$query = mysqli_query($conn, "SELECT * FROM video ORDER BY id DESC");

function getEmbedUrl($url) {
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return "https://www.youtube.com/embed/" . ($match[1] ?? '');
    }
    if (strpos($url, 'drive.google.com') !== false) {
        return str_replace('/view', '/preview', $url);
    }
    return $url;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Tutorial | GIGA LAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #41431B; --secondary: #AEB784; --accent: #E3DBBB;
            --bg: #F8F3E1; --dark: #2C2E14; --white: #ffffff;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; padding: 40px 20px; background-color: var(--bg); color: var(--dark); }
        .container { max-width: 1200px; margin: 0 auto; }
        
        h2 { 
            font-family: 'Plus Jakarta Sans', sans-serif; color: var(--primary); 
            font-weight: 800; font-size: 2.2rem; border-left: 10px solid var(--primary); 
            padding-left: 20px; margin-bottom: 35px; text-transform: uppercase; 
        }

        /* --- Navigation Style (Hollow Effect) --- */
        .nav-container { margin-bottom: 35px; display: flex; gap: 12px; flex-wrap: wrap; }
        
        .btn-common { 
            display: inline-flex; align-items: center; gap: 10px;
            padding: 12px 28px; border-radius: 12px; font-weight: 800; 
            font-size: 0.9rem; text-decoration: none; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            border: 2px solid var(--secondary);
            background: var(--accent);
            color: var(--primary) !important;
        }

        /* Hover: Transparan/Hollow */
        .btn-common:hover { 
            background: transparent; 
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(65, 67, 27, 0.1);
        }

        /* Khusus Dashboard (Army Green) */
        .btn-dash { background: var(--primary); color: var(--bg) !important; border-color: var(--primary); }
        .btn-dash:hover { color: var(--primary) !important; }

        /* Khusus Tambah Video (Hollow Style) */
        .btn-tambah { background: var(--white); border-color: var(--primary); }
        .btn-tambah:hover { background: var(--primary); color: var(--white) !important; }

        /* --- Video Grid --- */
        .video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
        
        .video-card { 
            background: var(--white); border-radius: 24px; overflow: hidden; 
            border: 2px solid var(--secondary); transition: 0.4s ease;
            display: flex; flex-direction: column; 
            position: relative; /* WAJIB: Landasan posisi tombol absolute admin */
        }
        .video-card:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: 0 15px 35px rgba(65, 67, 27, 0.1); }
        
        /* CSS Tombol Admin Melayang Atas Video */
        .admin-actions {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .btn-mini {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.95rem;
            transition: 0.3s;
            border: 2px solid transparent;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); /* Bayangan biar kontras di atas video */
        }

        .btn-edit-mini { background: #f0f7ff; color: #0056b3; border-color: #d0e3ff; }
        .btn-edit-mini:hover { background: #0056b3; color: white; transform: scale(1.1); }

        .btn-delete-mini { background: #fff5f5; color: #c53030; border-color: #feb2b2; }
        .btn-delete-mini:hover { background: #c53030; color: white; transform: scale(1.1); }

        .video-container { position: relative; width: 100%; padding-bottom: 56.25%; background: #000; }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }

        .video-info { padding: 25px; flex-grow: 1; }
        .video-title { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: var(--primary); font-size: 1.2rem; margin-bottom: 10px; display: block; }
        .video-desc { font-size: 0.9rem; color: var(--dark); opacity: 0.7; line-height: 1.6; }
        
        .empty-state { text-align: center; padding: 100px; color: var(--primary); opacity: 0.5; font-weight: 800; grid-column: 1/-1; }
    </style>
</head>
<body>

<div class="container">
    <h2>External Video Lab</h2>

    <div class="nav-container">
        <a href="<?= ($role_sekarang == 'admin') ? 'admin/admin_dashboard.php' : 'user_dashboard.php'; ?>" class="btn-common btn-dash">🏠 Dashboard</a>
        <a href="data_materi.php" class="btn-common">📂 Knowledge Base</a>
        <a href="bank_soal.php" class="btn-common">🎯 Challenge Room</a>

        <?php if ($role_sekarang == 'admin') : ?>
            <a href="admin/tambah_video.php" class="btn-common btn-tambah">➕ Add New Video</a>
        <?php endif; ?>
    </div>

    <div class="video-grid">
        <?php
        if(mysqli_num_rows($query) > 0){
            while($v = mysqli_fetch_array($query)){
                $embedUrl = getEmbedUrl($v['nama_file']);
        ?>
            <div class="video-card">
                <?php if ($role_sekarang == 'admin') : ?>
                    <div class="admin-actions">
                        <a href="admin/edit_video.php?id=<?= $v['id']; ?>" class="btn-mini btn-edit-mini" title="Edit Video">✏️</a>
                        <a href="proses/proses_hapus_video.php?id=<?= $v['id']; ?>" class="btn-mini btn-delete-mini" title="Hapus Video" onclick="return confirm('Yakin mau hapus video tutorial ini, brok?')">🗑️</a>
                    </div>
                <?php endif; ?>

                <div class="video-container">
                    <?php if(strpos($v['nama_file'], 'tiktok.com') !== false): ?>
                        <iframe src="https://www.tiktok.com/embed/v2/<?= basename(parse_url($v['nama_file'], PHP_URL_PATH)) ?>" allowfullscreen></iframe>
                    <?php else: ?>
                        <iframe src="<?= $embedUrl ?>" allow="autoplay; encrypted-media" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true"></iframe>
                    <?php endif; ?>
                </div>
                <div class="video-info">
                    <span class="video-title"><?= htmlspecialchars($v['judul_video']); ?></span>
                    <p class="video-desc"><?= htmlspecialchars($v['keterangan']); ?></p>
                </div>
            </div>
        <?php 
            }
        } else {
            echo "<div class='empty-state'>
                    <span style='font-size: 3rem;'>🎬</span><br><br>
                    Belum ada video tutorial yang diunggah.
                  </div>";
        }
        ?>
    </div>
</div>

</body>
</html>