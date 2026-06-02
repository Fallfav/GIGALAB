<?php 
session_start();
include 'config/koneksi.php'; 

// PENGAMAN: Cek login
if (!isset($_SESSION['username'])) {
    header("Location: auth/login.php");
    exit();
}

// Ambil Role untuk filter tombol
$role_sekarang = $_SESSION['role'] ?? 'user';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_tahun = isset($_GET['tahun']) ? mysqli_real_escape_string($conn, $_GET['tahun']) : '';
$filter_level = isset($_GET['level']) ? mysqli_real_escape_string($conn, $_GET['level']) : '';

$query_str = "SELECT * FROM bank_soal WHERE 1=1";

if($search != '') {
    $query_str .= " AND (nama_soal LIKE '%$search%' OR level LIKE '%$search%')";
}
if($filter_tahun != '') {
    $query_str .= " AND tahun = '$filter_tahun'";
}
if($filter_level != '') {
    $query_str .= " AND level = '$filter_level'";
}

$query_str .= " ORDER BY tahun DESC, id DESC";
$query = mysqli_query($conn, $query_str);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge Room | GIGA LAB</title>
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

        /* --- Navigation Style (Hollow Interaction) --- */
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

        .btn-common:hover { 
            background: transparent; 
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(65, 67, 27, 0.1);
        }

        .btn-dash { background: var(--primary); color: var(--bg) !important; border-color: var(--primary); }
        .btn-dash:hover { background: transparent; color: var(--primary) !important; }

        .btn-tambah { background: var(--white); border-color: var(--primary); }
        .btn-tambah:hover { background: var(--primary); color: var(--white) !important; }

        /* --- Filter & Search --- */
        .filter-wrapper { display: flex; align-items: center; gap: 15px; margin-bottom: 35px; flex-wrap: wrap; }
        .search-box { background: var(--white); padding: 8px 15px; border-radius: 15px; border: 2px solid var(--primary); display: flex; align-items: center; gap: 10px; flex-grow: 1; max-width: 400px; }
        .search-box input { border: none; outline: none; width: 100%; padding: 8px; font-weight: 600; background: transparent; }

        .filter-box { background: var(--white); padding: 5px 15px; border-radius: 15px; border: 2px solid var(--secondary); display: flex; align-items: center; gap: 10px; }
        .filter-box select { border: none; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; color: var(--primary); background: transparent; outline: none; }
        
        .btn-reset { text-decoration: none; font-size: 0.75rem; font-weight: 800; color: #8B0000; border: 2px solid #8B0000; padding: 12px 18px; border-radius: 12px; transition: 0.3s; }
        .btn-reset:hover { background: #8B0000; color: white; }

        /* --- Cards UI --- */
        .card-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 30px; }
        .card { 
            background: var(--white); padding: 35px; border-radius: 24px; border: 2px solid var(--secondary); 
            transition: 0.4s ease; display: flex; flex-direction: column; justify-content: space-between;
            position: relative; /* Penting untuk tombol melayang */
        }
        .card:hover { transform: translateY(-12px); border-color: var(--primary); box-shadow: 0 20px 40px rgba(65, 67, 27, 0.1); }
        
        /* Tombol Edit & Hapus Melayang */
        .admin-actions {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .btn-mini {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            text-decoration: none;
            font-size: 1rem;
            transition: 0.3s;
            border: 2px solid transparent;
        }

        .btn-edit-mini { background: #f0f7ff; color: #0056b3; border-color: #d0e3ff; }
        .btn-edit-mini:hover { background: #0056b3; color: white; transform: scale(1.1); }

        .btn-delete-mini { background: #fff5f5; color: #c53030; border-color: #feb2b2; }
        .btn-delete-mini:hover { background: #c53030; color: white; transform: scale(1.1); }

        .badge { display: inline-block; background: var(--accent); padding: 8px 15px; border-radius: 10px; font-size: 0.7rem; color: var(--primary); font-weight: 800; text-transform: uppercase; margin-bottom: 20px; border: 1px solid var(--secondary); }
        .card h3 { margin: 0 0 12px 0; color: var(--primary); font-size: 1.4rem; font-weight: 800; padding-right: 80px; /* Biar gak tabrakan sama tombol edit */ }
        .meta-info { font-size: 0.85rem; color: var(--dark); opacity: 0.7; margin-bottom: 25px; }

        /* Action Group Buttons */
        .action-group { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .btn-preview, .btn-download { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; border-radius: 12px; font-weight: 800; text-decoration: none; font-size: 0.8rem; transition: 0.2s; border: 2px solid transparent; }
        
        .btn-preview { background: var(--white); color: var(--primary); border-color: var(--primary); }
        .btn-preview:hover { background: var(--accent); }
        
        .btn-download { background: var(--primary); color: var(--bg) !important; text-transform: uppercase; border-color: var(--primary); }
        .btn-download:hover { background: transparent; color: var(--primary) !important; }

        .empty-state { grid-column: 1/-1; text-align: center; padding: 100px 0; color: var(--primary); opacity: 0.5; font-weight: 700; }
    </style>
</head>
<body>

<div class="container">
    <h2>Challenge Room</h2>

    <div class="nav-container">
        <a href="<?= ($role_sekarang == 'admin') ? 'admin/admin_dashboard.php' : 'user_dashboard.php'; ?>" class="btn-common btn-dash">🏠 Dashboard</a>
        <a href="video_belajar.php" class="btn-common">🎬 Video Tutorial</a>
        <a href="data_materi.php" class="btn-common">📂 Knowledge Base</a>
        
        <?php if ($role_sekarang == 'admin') : ?>
            <a href="admin/tambah_soal.php" class="btn-common btn-tambah">➕ Add New Challenge</a>
        <?php endif; ?>
    </div>

    <form method="GET" action="">
        <div class="filter-wrapper">
            <div class="search-box">
                <span>🔍</span>
                <input type="text" name="search" placeholder="Cari tantangan..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="filter-box">
                <select name="tahun" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    <?php
                    $tahun_query = mysqli_query($conn, "SELECT DISTINCT tahun FROM bank_soal ORDER BY tahun DESC");
                    while($t = mysqli_fetch_array($tahun_query)){
                        $selected = ($filter_tahun == $t['tahun']) ? 'selected' : '';
                        echo "<option value='".$t['tahun']."' $selected>".$t['tahun']."</option>";
                    }
                    ?>
                </select>

                <select name="level" onchange="this.form.submit()">
                    <option value="">Semua Level</option>
                    <?php
                    $level_query = mysqli_query($conn, "SELECT DISTINCT level FROM bank_soal ORDER BY level ASC");
                    while($l = mysqli_fetch_array($level_query)){
                        $selected = ($filter_level == $l['level']) ? 'selected' : '';
                        echo "<option value='".$l['level']."' $selected>".$l['level']."</option>";
                    }
                    ?>
                </select>
            </div>

            <?php if($search != '' || $filter_tahun != '' || $filter_level != ''): ?>
                <a href="bank_soal.php" class="btn-reset">✕ RESET</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="card-container">
        <?php
        if(mysqli_num_rows($query) > 0){
            while($s = mysqli_fetch_array($query)){
            ?>
            <div class="card">
                <?php if ($role_sekarang == 'admin') : ?>
                    <div class="admin-actions">
                        <a href="admin/edit_soal.php?id=<?= $s['id']; ?>" class="btn-mini btn-edit-mini" title="Edit Soal">✏️</a>
                        <a href="proses/proses_hapus_soal.php?id=<?= $s['id']; ?>" class="btn-mini btn-delete-mini" title="Hapus Soal" onclick="return confirm('Yakin mau hapus tantangan ini, brok?')">🗑️</a>
                    </div>
                <?php endif; ?>

                <div>
                    <span class="badge"><?= htmlspecialchars($s['level']); ?> • <?= htmlspecialchars($s['tahun']); ?></span>
                    <h3><?= htmlspecialchars($s['nama_soal']); ?></h3>
                    <div class="meta-info">
                        <span>🗓️</span> Diunggah: <?= date('d M Y', strtotime($s['tgl_upload'])); ?>
                    </div>
                </div>
                
                <div class="action-group">
                    <a href="uploads/<?= rawurlencode($s['file_soal']); ?>" class="btn-preview" target="_blank">📄 LIHAT</a>
                    <a href="uploads/<?= rawurlencode($s['file_soal']); ?>" class="btn-download" download>📥 DOWNLOAD</a>
                </div>
            </div>
            <?php 
            }
        } else {
            echo "<div class='empty-state'>
                    <span style='font-size: 3rem;'>📭</span><br><br>
                    Belum ada tantangan yang tersedia.
                  </div>";
        }
        ?>
    </div>
</div>

</body>
</html>