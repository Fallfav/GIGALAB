<?php 
include '../config/koneksi.php'; 
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM materi WHERE id = $id");
$data = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Materi | GIGA LAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #41431B;     /* Deep Olive */
            --secondary: #AEB784;   /* Sage Green */
            --accent: #E3DBBB;      /* Sand Beige */
            --bg: #F8F3E1;          /* Creamy White */
            --dark: #2C2E14;        /* Darker Olive */
            --white: #ffffff;
            --danger: #8B0000;      /* Dark Red */
        }

        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            padding: 40px 20px; 
            background-color: var(--bg); 
            color: var(--dark); 
        }

        .container { max-width: 900px; margin: 0 auto; }

        h2 { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--primary); 
            font-weight: 800;
            border-left: 10px solid var(--primary); 
            padding-left: 20px; 
            margin-bottom: 30px; 
            text-transform: uppercase;
        }

        form { 
            background: var(--white); 
            padding: 40px; 
            border-radius: 20px; 
            border: 2px solid var(--primary);
            box-shadow: 0 10px 30px rgba(65, 67, 27, 0.1); 
        }

        label { 
            display: block; 
            margin-top: 20px; 
            margin-bottom: 8px; 
            font-weight: 800; 
            color: var(--primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        input[type="text"], textarea { 
            width: 100%; 
            padding: 15px; 
            border: 2px solid var(--accent); 
            background: #fafafa; 
            color: var(--dark); 
            border-radius: 12px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        textarea { font-family: 'Consolas', monospace; font-size: 0.9rem; line-height: 1.5; }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--secondary);
            background: #fff;
        }

        /* Preview Section */
        .img-preview-container { 
            display: flex; 
            gap: 15px; 
            flex-wrap: wrap; 
            margin: 15px 0; 
            padding: 20px; 
            background: var(--bg); 
            border-radius: 15px;
            border: 1px solid var(--accent);
        }

        .img-preview { 
            width: 120px; 
            height: 80px; 
            border: 3px solid var(--primary); 
            border-radius: 8px; 
            object-fit: cover; 
            transition: 0.3s;
        }

        .img-preview:hover { transform: scale(1.1); }

        .hapus-box { 
            background: #fff5f5; 
            padding: 15px; 
            border-radius: 10px; 
            border: 1.5px dashed var(--danger); 
            margin-top: 10px;
            color: var(--danger);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .doc-box { 
            background: #f0f4f0; 
            padding: 20px; 
            border-radius: 15px; 
            border-left: 6px solid var(--secondary); 
            margin-top: 25px; 
        }

        .btn-update { 
            background: var(--primary); 
            color: var(--bg); 
            padding: 15px 35px; 
            border: none; 
            border-radius: 12px; 
            cursor: pointer; 
            font-weight: 800; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-top: 30px;
            transition: 0.3s;
        }

        .btn-update:hover { 
            filter: brightness(1.2); 
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(65, 67, 27, 0.2);
        }

        .btn-batal { 
            color: var(--primary); 
            text-decoration: none; 
            margin-left: 20px; 
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Materi & File</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Judul Materi</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($data['judul']); ?>" required>
        
        <label>Isi Materi / Konfigurasi</label>
        <textarea name="isi" rows="10" required><?= htmlspecialchars($data['isi']); ?></textarea>
        
        <label>Foto Saat Ini:</label>
        <div class="img-preview-container">
            <?php 
            if(!empty($data['gambar'])){
                $files = explode(",", $data['gambar']);
                foreach($files as $f){
                    $nama_file = trim($f);
                    if($nama_file != "") {
                        echo '<img src="uploads/'.$nama_file.'" class="img-preview" title="'.$nama_file.'">';
                    }
                }
                echo '</div>'; 
                echo '<div class="hapus-box">';
                echo '<input type="checkbox" name="hapus_foto" value="ya"> <b>Hapus SEMUA foto lama (Akan diganti jika upload foto baru)</b>';
                echo '</div>';
            } else { 
                echo "<p style='color:#666;'>Belum ada foto yang diupload.</p></div>"; 
            }
            ?>
        
        <label>Tambah/Ganti Foto Baru:</label>
        <input type="file" name="gambar[]" multiple>
        <small style="display:block; margin-top:5px; color:#666;">*Kosongkan jika tidak ingin mengubah foto</small>

        <div class="doc-box">
            <label style="margin-top:0;">Dokumen Write-up:</label>
            <?php if(!empty($data['dokumen'])): ?>
                <p>📄 File aktif: <a href="uploads/<?= $data['dokumen']; ?>" target="_blank" style="color: var(--primary); font-weight:bold;"><?= $data['dokumen']; ?></a></p>
                <div class="hapus-box" style="margin-bottom:15px; border-color: #ccc; background: #fafafa;">
                    <input type="checkbox" name="hapus_dokumen" value="ya"> <span>Hapus dokumen ini</span>
                </div>
            <?php else: ?>
                <p style="font-size:0.9rem; color:#666;">Belum ada dokumen pendukung.</p>
            <?php endif; ?>
            
            <label>Upload Dokumen Baru:</label>
            <input type="file" name="dokumen">
        </div>
        
        <button type="submit" name="update" class="btn-update"> Simpan Perubahan</button>
        <a href="../data_materi.php" class="btn-batal">Batal</a>
    </form>
</div>

<?php
if(isset($_POST['update'])){
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $hapus_foto = isset($_POST['hapus_foto']) ? $_POST['hapus_foto'] : '';
    $hapus_dokumen = isset($_POST['hapus_dokumen']) ? $_POST['hapus_dokumen'] : '';
    
    $string_gambar = $data['gambar']; 
    $nama_dokumen = $data['dokumen']; 

    // Logic Update Foto
    if($hapus_foto == 'ya'){
        $string_gambar = "";
    }
    
    if(!empty($_FILES['gambar']['name'][0])){
        $files_array = [];
        foreach($_FILES['gambar']['name'] as $key => $val){
            $name = time() . "_" . preg_replace("/[^a-zA-Z0-9._]/", "", $_FILES['gambar']['name'][$key]);
            if(move_uploaded_file($_FILES['gambar']['tmp_name'][$key], "uploads/" . $name)){
                $files_array[] = $name;
            }
        }
        $string_gambar = implode(",", $files_array);
    }

    // Logic Update Dokumen
    if($hapus_dokumen == 'ya'){
        $nama_dokumen = "";
    }
    
    if(!empty($_FILES['dokumen']['name'])){
        $doc_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._]/", "", $_FILES['dokumen']['name']);
        if(move_uploaded_file($_FILES['dokumen']['tmp_name'], "uploads/" . $doc_name)){
            $nama_dokumen = $doc_name;
        }
    }

    $sql = "UPDATE materi SET judul='$judul', isi='$isi', gambar='$string_gambar', dokumen='$nama_dokumen' WHERE id=$id";

    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Update Berhasil!'); window.location='../data_materi.php';</script>";
    } else {
        echo "<script>alert('Gagal Update: " . mysqli_error($conn) . "');</script>";
    }
}
?>

</body>
</html>