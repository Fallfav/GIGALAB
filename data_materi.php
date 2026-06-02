<?php 
session_start(); 
// Jalur disesuaikan: koneksi sekarang ada di dalam folder config/
include 'config/koneksi.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: auth/login.php");
    exit();
}

$role_sekarang = $_SESSION['role'] ?? 'user';

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'DESC';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'id';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$allowed_columns = ['judul', 'id'];
if (!in_array($orderby, $allowed_columns)) { $orderby = 'id'; }
$allowed_sort = ['ASC', 'DESC'];
if (!in_array($sort, $allowed_sort)) { $sort = 'DESC'; }

$query_str = "SELECT * FROM materi WHERE 1=1";
if($search != '') {
    $query_str .= " AND (judul LIKE '%$search%' OR isi LIKE '%$search%')";
}
$query_str .= " ORDER BY $orderby $sort";

$query = mysqli_query($conn, $query_str);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Base | GIGA LAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #41431B; --secondary: #AEB784; --accent: #E3DBBB;
            --bg: #F8F3E1; --dark: #2C2E14; --white: #ffffff;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; padding: 40px 20px; background-color: var(--bg); color: var(--dark); }
        .container { max-width: 1200px; margin: 0 auto; }
        h2 { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--primary); font-weight: 800; font-size: 2.2rem; border-left: 10px solid var(--primary); padding-left: 20px; margin-bottom: 35px; text-transform: uppercase; letter-spacing: -1px; }

        /* --- Navigasi & Button Style (Hollow Effect) --- */
        .nav-container { margin-bottom: 25px; display: flex; gap: 15px; flex-wrap: wrap; }
        
        .btn-common { 
            display: inline-flex; align-items: center; gap: 10px;
            padding: 14px 28px; border-radius: 12px; font-weight: 800; 
            font-size: 0.9rem; text-decoration: none; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            font-family: 'Plus Jakarta Sans', sans-serif; border: 2px solid transparent; 
        }

        /* Dashboard Button */
        .btn-home { background: var(--primary); color: var(--bg) !important; border-color: var(--primary); }
        .btn-home:hover { background: transparent; color: var(--primary) !important; transform: translateY(-3px); }

        /* Tutorial & Challenge Button */
        .btn-soal { background: var(--accent); color: var(--primary) !important; border-color: var(--secondary); }
        .btn-soal:hover { background: transparent; border-color: var(--primary); transform: translateY(-3px); }

        /* Add New Button */
        .btn-tambah { background: var(--white); color: var(--primary) !important; border-color: var(--primary); }
        .btn-tambah:hover { background: var(--primary); color: var(--white) !important; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(65, 67, 27, 0.2); }

        /* Reset Button */
        .btn-reset { 
            text-decoration: none; font-size: 0.75rem; font-weight: 800; color: #8B0000; 
            border: 2px solid #8B0000; padding: 12px 18px; border-radius: 12px; 
            transition: 0.3s; background: transparent; 
        }
        .btn-reset:hover { background: #8B0000; color: white; transform: scale(1.05); }

        /* --- Toolbar & Table --- */
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
        .search-container { flex-grow: 1; max-width: 450px; display: flex; background: var(--white); padding: 5px 15px; border-radius: 15px; border: 2px solid var(--primary); align-items: center; }
        .search-container input { border: none; outline: none; padding: 10px; width: 100%; font-weight: 600; font-family: 'Inter', sans-serif; background: transparent; }
        
        .sort-area { display: flex; align-items: center; gap: 12px; }
        .sort-container { display: flex; align-items: center; gap: 12px; background: var(--white); padding: 12px 20px; border-radius: 15px; border: 2px solid var(--secondary); }
        .sort-container label { font-weight: 800; font-size: 0.75rem; color: var(--primary); text-transform: uppercase; }
        .sort-container select { padding: 8px; border-radius: 8px; border: 1.5px solid var(--accent); font-weight: 600; color: var(--primary); cursor: pointer; outline: none; background: transparent; }

        .table-container { background: var(--white); border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(65, 67, 27, 0.08); border: 2px solid var(--primary); }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: var(--primary); color: var(--bg); padding: 22px; text-align: left; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1.5px; }
        td { padding: 25px; border: 1px solid var(--accent); vertical-align: top; }
        tr td:first-child { background: #faf8f0; text-align: center; font-weight: 800; color: var(--primary); width: 60px; }
        
        .materi-title { color: var(--primary); font-size: 1.2rem; font-weight: 800; line-height: 1.3; }
        pre { background: var(--dark); padding: 18px; border-radius: 12px; color: var(--secondary); white-space: pre-wrap; font-family: 'Consolas', monospace; font-size: 0.85rem; line-height: 1.6; }
        .img-topologi { max-width: 250px; border: 3px solid var(--accent); margin: 10px 5px; border-radius: 10px; transition: 0.3s; }
        .doc-link { display: inline-flex; align-items: center; gap: 10px; background: #faf8f0; padding: 10px 18px; border-radius: 10px; border: 1.5px dashed var(--secondary); margin-top: 10px; }
        .doc-link a { color: var(--primary); font-weight: 700; text-decoration: none; font-size: 0.85rem; }
        
        /* Aksi Buttons */
        .btn-group-aksi { display: flex; flex-direction: column; gap: 8px; }
        .btn-aksi { padding: 10px; border-radius: 8px; text-decoration: none; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; text-align: center; transition: 0.2s; border: 1.5px solid transparent; }
        .btn-edit { background: var(--accent); color: var(--primary); border-color: var(--secondary); }
        .btn-edit:hover { background: transparent; border-color: var(--primary); }
        .btn-hapus { background: #8B0000; color: #fff; border-color: #8B0000; }
        .btn-hapus:hover { background: transparent; color: #8B0000; }
        
        .empty-state { text-align: center; padding: 50px; color: var(--primary); opacity: 0.5; font-weight: 800; }
    </style>
</head>
<body>

<div class="container">
    <h2>Knowledge Base</h2>

    <div class="nav-container">
        <a href="<?= ($role_sekarang == 'admin') ? 'admin/admin_dashboard.php' : 'user_dashboard.php'; ?>" class="btn-common btn-home">🏠 Dashboard</a>
        <a href="video_belajar.php" class="btn-common btn-soal">🎥 Video Tutorial</a>
        <a href="bank_soal.php" class="btn-common btn-soal">🎯 Challenge Room</a>
        
        <?php if ($role_sekarang == 'admin') : ?>
            <a href="admin/tambah_materi.php" class="btn-common btn-tambah">➕ Add New Knowledge</a>
        <?php endif; ?>
    </div>

    <form method="GET" action="">
        <div class="toolbar">
            <div class="search-container">
                <span>🔍</span>
                <input type="text" name="search" placeholder="Cari judul atau konfigurasi..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="sort-area">
                <?php if($search != '' || $orderby != 'id' || $sort != 'DESC'): ?>
                    <a href="data_materi.php" class="btn-reset">✕ RESET</a>
                <?php endif; ?>

                <div class="sort-container">
                    <label>Urutkan:</label>
                    <select name="orderby" onchange="this.form.submit()">
                        <option value="id" <?= $orderby == 'id' ? 'selected' : '' ?>>Waktu Upload</option>
                        <option value="judul" <?= $orderby == 'judul' ? 'selected' : '' ?>>Judul Materi</option>
                    </select>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="DESC" <?= $sort == 'DESC' ? 'selected' : '' ?>>Terbaru / Z-A</option>
                        <option value="ASC" <?= $sort == 'ASC' ? 'selected' : '' ?>>Terlama / A-Z</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th width="280">Materi Utama</th>
                    <th>Detail Konfigurasi & Lab</th>
                    <?php if ($role_sekarang == 'admin') : ?>
                        <th width="110">Opsi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($query) > 0){
                    $no = 1;
                    while($data = mysqli_fetch_array($query)){
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><span class="materi-title"><?= htmlspecialchars($data['judul']); ?></span></td>
                        <td>
                            <pre><?= htmlspecialchars($data['isi']); ?></pre>
                            
                            <?php 
                            if(!empty($data['gambar'])){
                                $images = explode(",", $data['gambar']);
                                foreach($images as $img){
                                    echo '<img src="uploads/'.trim($img).'" class="img-topologi" alt="Topologi">';
                                }
                            }
                            ?>

                            <?php if(!empty($data['dokumen'])): 
                                $raw_doc = $data['dokumen'];
                                $clean_doc = explode("_", $raw_doc, 2);
                                $display_name = (isset($clean_doc[1])) ? $clean_doc[1] : $raw_doc;
                            ?>
                                <div class="doc-link">
                                    📁 <a href="uploads/<?= rawurlencode($raw_doc); ?>" target="_blank">
                                        <?= htmlspecialchars($display_name); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </td>

                        <?php if ($role_sekarang == 'admin') : ?>
                        <td style="background: #faf8f0; vertical-align: middle;">
                            <div class="btn-group-aksi">
                                <a href="admin/edit_materi.php?id=<?= $data['id']; ?>" class="btn-aksi btn-edit">Edit</a>
                                <a href="proses/hapus_materi.php?id=<?= $data['id']; ?>" class="btn-aksi btn-hapus" onclick="return confirm('Yakin mau hapus materi ini, brok?')">Hapus</a>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php } 
                } else {
                    $colspan = ($role_sekarang == 'admin') ? 4 : 3;
                    echo "<tr><td colspan='$colspan' class='empty-state'>Materi tidak ditemukan, brok! Cari keyword lain coba.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>