<?php 
include '../config/koneksi.php'; 

// Ambil ID dari URL
$id = mysqli_real_escape_string($conn, $_GET['id']);
$query_data = mysqli_query($conn, "SELECT * FROM bank_soal WHERE id = '$id'");
$data = mysqli_fetch_array($query_data);

// Jika ID tidak ditemukan, balikkan ke halaman utama
if(!$data) {
    header("Location: bank_soal.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bank Soal | GIGA LAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #41431B;     /* Deep Olive */
            --secondary: #AEB784;   /* Sage Green */
            --accent: #E3DBBB;      /* Sand Beige */
            --bg: #F8F3E1;          /* Creamy White */
            --dark: #2C2E14;        /* Darker Olive */
            --white: #ffffff;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            background-color: var(--bg); 
            color: var(--dark); 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .form-card { 
            background: var(--white); 
            padding: 45px; 
            border-radius: 24px; 
            box-shadow: 0 20px 40px rgba(65, 67, 27, 0.1); 
            width: 100%;
            max-width: 600px;
            border: 2px solid var(--primary); 
            box-sizing: border-box;
        }

        h2 { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--primary); 
            margin-top: 0; 
            text-align: center; 
            margin-bottom: 35px; 
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }
        
        label { 
            display: block; 
            margin-top: 22px; 
            margin-bottom: 10px; 
            font-weight: 800; 
            font-size: 0.85rem; 
            color: var(--primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        input[type="text"], select, input[type="file"] { 
            width: 100%; 
            padding: 16px; 
            border: 2px solid var(--accent); 
            background: #fafaf8; 
            color: var(--dark); 
            border-radius: 14px;
            box-sizing: border-box;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 5px rgba(174, 183, 132, 0.2);
        }

        .row { display: flex; gap: 20px; }
        .col { flex: 1; }

        .btn-group { margin-top: 40px; display: flex; gap: 15px; }

        .btn-update { 
            flex: 2;
            background: var(--primary); 
            color: var(--bg); 
            padding: 18px; 
            border: none; 
            border-radius: 14px; 
            cursor: pointer; 
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-update:hover { 
            filter: brightness(1.2);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(65, 67, 27, 0.2);
        }

        .btn-batal { 
            flex: 1;
            background: var(--white); 
            color: var(--primary); 
            text-decoration: none; 
            padding: 18px; 
            border-radius: 14px; 
            text-align: center;
            font-weight: 700;
            font-size: 0.9rem;
            border: 2px solid var(--accent);
            transition: 0.3s;
        }
        .btn-batal:hover { background: var(--accent); color: var(--primary); }

        .info-text { 
            font-size: 0.75rem; 
            color: var(--primary); 
            opacity: 0.6;
            margin-top: 12px; 
            font-style: italic;
            font-weight: 500;
        }

        input[type="file"]::file-selector-button {
            background: var(--secondary);
            color: var(--primary);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            margin-right: 15px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="form-card">
    <h2>Edit Challenge</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_soal" value="<?= $data['id']; ?>">

        <label>Nama Soal / Nama Modul</label>
        <input type="text" name="nama_soal" value="<?= $data['nama_soal']; ?>" required>
        
        <div class="row">
            <div class="col">
                <label>Tahun</label>
                <input type="text" name="tahun" value="<?= $data['tahun']; ?>" required>
            </div>
            <div class="col">
                <label>Level Kompetisi</label>
                <select name="level">
                    <option value="Kabupaten/Kota" <?= ($data['level'] == 'Kabupaten/Kota') ? 'selected' : ''; ?>>Kabupaten/Kota</option>
                    <option value="Provinsi" <?= ($data['level'] == 'Provinsi') ? 'selected' : ''; ?>>Provinsi</option>
                    <option value="Nasional" <?= ($data['level'] == 'Nasional') ? 'selected' : ''; ?>>Nasional</option>
                    <option value="Latihan" <?= ($data['level'] == 'Latihan') ? 'selected' : ''; ?>>Latihan Internal</option>
                </select>
            </div>
        </div>
        
        <label>Ganti File Modul (Opsional)</label>
        <input type="file" name="file_soal">
        <p class="info-text">💡 Biarkan kosong jika tidak ingin mengganti file.<br>File saat ini: <b><?= $data['file_soal']; ?></b></p>
        
        <div class="btn-group">
            <a href="../bank_soal.php" class="btn-batal">Batal</a>
            <button type="submit" name="update" class="btn-update">Update Data Soal</button>
        </div>
    </form>
</div>

<?php
if(isset($_POST['update'])){
    $id_up   = $_POST['id_soal'];
    $nama_up = mysqli_real_escape_string($conn, $_POST['nama_soal']);
    $thn_up  = mysqli_real_escape_string($conn, $_POST['tahun']);
    $lvl_up  = mysqli_real_escape_string($conn, $_POST['level']);
    
    // Cek apakah ada file baru yang diupload
    if($_FILES['file_soal']['name'] != ""){
        // Hapus file lama dari server
        unlink("uploads/" . $data['file_soal']);

        // Upload file baru
        $file_name_up = time() . "_" . preg_replace("/[^a-zA-Z0-9._]/", "", $_FILES['file_soal']['name']);
        $tmp_name_up  = $_FILES['file_soal']['tmp_name'];
        move_uploaded_file($tmp_name_up, "uploads/".$file_name_up);

        // Query Update dengan file baru
        $sql = "UPDATE bank_soal SET nama_soal='$nama_up', tahun='$thn_up', level='$lvl_up', file_soal='$file_name_up' WHERE id='$id_up'";
    } else {
        // Query Update tanpa ganti file
        $sql = "UPDATE bank_soal SET nama_soal='$nama_up', tahun='$thn_up', level='$lvl_up' WHERE id='$id_up'";
    }

    $update_query = mysqli_query($conn, $sql);
    
    if($update_query){
        echo "<script>alert('Data Berhasil Diperbarui!'); window.location='../bank_soal.php';</script>";
    } else {
        echo "<script>alert('Gagal Update Database.');</script>";
    }
}
?>

</body>
</html>