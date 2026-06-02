<?php include '../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Materi | GIGA LAB</title>
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
            padding: 40px 20px; 
            background-color: var(--bg); 
            color: var(--dark); 
        }

        .container { max-width: 800px; margin: 0 auto; }

        h2 { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--primary); 
            font-weight: 800;
            font-size: 1.8rem;
            border-left: 10px solid var(--primary); 
            padding-left: 20px; 
            margin-bottom: 35px; 
            text-transform: uppercase;
        }

        form { 
            background: var(--white); 
            padding: 45px; 
            border-radius: 24px; 
            border: 2px solid var(--secondary); 
            box-shadow: 0 15px 35px rgba(65, 67, 27, 0.08); 
        }

        label { 
            display: block; 
            margin-top: 15px;
            margin-bottom: 10px; 
            font-weight: 800; 
            color: var(--primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="text"], textarea { 
            width: 100%; 
            padding: 18px; 
            border: 2px solid var(--accent); 
            background: #fafaf8; 
            color: var(--dark); 
            border-radius: 14px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            box-sizing: border-box;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        textarea { 
            font-family: 'Consolas', 'Monaco', monospace; 
            font-size: 0.9rem; 
            line-height: 1.6;
            background: #fdfdfd;
        }

        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 5px rgba(174, 183, 132, 0.2);
        }

        .help-text {
            font-size: 0.75rem;
            color: var(--primary); 
            opacity: 0.6;
            margin-top: -5px; 
            margin-bottom: 12px; 
            display: block;
            font-style: italic;
            font-weight: 500;
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--accent);
        }

        .btn-simpan { 
            background: var(--primary); 
            color: var(--bg); 
            padding: 18px 40px; 
            border: none; 
            border-radius: 14px; 
            cursor: pointer; 
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1rem;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-simpan:hover { 
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(65, 67, 27, 0.2);
            filter: brightness(1.1);
        }

        .btn-batal { 
            color: var(--primary); 
            text-decoration: none; 
            font-weight: 700;
            font-size: 0.95rem;
            opacity: 0.7;
            transition: 0.2s;
        }
        .btn-batal:hover { opacity: 1; text-decoration: underline; }

        input[type="file"]::file-selector-button {
            background: var(--secondary);
            color: var(--primary);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            margin-right: 15px;
            cursor: pointer;
            transition: 0.3s;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Knowledge</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Judul Materi</label>
        <input type="text" name="judul" placeholder="Contoh: Konfigurasi VLAN & Trunking" required>
        
        <label>Isi Materi / Konfigurasi CLI</label>
        <textarea name="isi" rows="10" placeholder="Paste command CLI di sini..." required></textarea>
        
        <label>Upload Foto Topologi</label>
        <span class="help-text">*Bisa pilih banyak foto sekaligus</span>
        <input type="file" name="gambar[]" multiple>

        <label>Upload Dokumen Pendukung</label>
        <span class="help-text">*Format: PDF, DOCX, atau TXT</span>
        <input type="file" name="dokumen">
        
        <div class="action-group">
            <button type="submit" name="submit" class="btn-simpan"> Simpan Materi</button>
            <a href="../data_materi.php" class="btn-batal">Kembali ke Daftar</a>
        </div>
    </form>
</div>

<?php
if(isset($_POST['submit'])){
    $judul  = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi    = mysqli_real_escape_string($conn, $_POST['isi']);
    
    $string_gambar = ""; 
    $nama_dokumen  = "";

    // PROSES GAMBAR MULTIPLE
    if(!empty($_FILES['gambar']['name'][0])){
        $files_array = [];
        foreach($_FILES['gambar']['name'] as $key => $val){
            // Regex diperbarui: Menambahkan spasi " " agar nama file tidak menyambung
            $nama_file = time() . "_" . preg_replace("/[^a-zA-Z0-9._ ]/", "", $_FILES['gambar']['name'][$key]);
            $tmp_file  = $_FILES['gambar']['tmp_name'][$key];
            
            if(move_uploaded_file($tmp_file, "uploads/" . $nama_file)){
                $files_array[] = $nama_file;
            }
        }
        $string_gambar = implode(",", $files_array);
    }

    // PROSES DOKUMEN
    if(!empty($_FILES['dokumen']['name'])){
        // Regex diperbarui: Menambahkan spasi " " agar nama file tidak menyambung
        $nama_dokumen = time() . "_" . preg_replace("/[^a-zA-Z0-9._ ]/", "", $_FILES['dokumen']['name']);
        $tmp_dokumen  = $_FILES['dokumen']['tmp_name'];
        move_uploaded_file($tmp_dokumen, "uploads/" . $nama_dokumen);
    }

    $query = "INSERT INTO materi (judul, isi, gambar, dokumen) VALUES ('$judul', '$isi', '$string_gambar', '$nama_dokumen')";
    $insert = mysqli_query($conn, $query);
    
    if($insert){
        echo "<script>alert('Materi Mantap Berhasil Simpan!'); window.location='data_materi.php';</script>";
    } else {
        echo "<script>alert('Gagal Simpan Bro: " . mysqli_error($conn) . "');</script>";
    }
}
?>
</body>
</html>