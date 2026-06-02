<?php include '../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bank Soal | GIGA LAB</title>
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

        .btn-upload { 
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
        .btn-upload:hover { 
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

        /* Styling upload button bawaan */
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
    <h2> Upload New Challenge</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nama Soal / Nama Modul</label>
        <input type="text" name="nama_soal" placeholder="Contoh: Modul Troubleshooting Enterprise" required>
        
        <div class="row">
            <div class="col">
                <label>Tahun</label>
                <input type="text" name="tahun" placeholder="2026" required>
            </div>
            <div class="col">
                <label>Level Kompetisi</label>
                <select name="level">
                    <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                    <option value="Provinsi">Provinsi</option>
                    <option value="Nasional">Nasional</option>
                    <option value="Latihan">Latihan Internal</option>
                </select>
            </div>
        </div>
        
        <label>Pilih File Modul</label>
        <input type="file" name="file_soal" required>
        <p class="info-text">💡 Format disarankan: PDF, ZIP, atau PKT (Max 10MB)</p>
        
        <div class="btn-group">
            <a href="../bank_soal.php" class="btn-batal">Batal</a>
            <button type="submit" name="upload" class="btn-upload">Simpan ke Bank Soal</button>
        </div>
    </form>
</div>

<?php
if(isset($_POST['upload'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_soal']);
    $thn  = mysqli_real_escape_string($conn, $_POST['tahun']);
    $lvl  = mysqli_real_escape_string($conn, $_POST['level']);
    
    // Penamaan file unik dan aman dari karakter aneh
    $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9._]/", "", $_FILES['file_soal']['name']);
    $tmp_name  = $_FILES['file_soal']['tmp_name'];
    
    if(!empty($file_name)){
        if(move_uploaded_file($tmp_name, "uploads/".$file_name)){
            $query = mysqli_query($conn, "INSERT INTO bank_soal (nama_soal, tahun, level, file_soal, tgl_upload) VALUES ('$nama', '$thn', '$lvl', '$file_name', NOW())");
            
            if($query){
                echo "<script>alert('Soal Berhasil Masuk Bank Soal!'); window.location='bank_soal.php';</script>";
            } else {
                echo "<script>alert('Gagal Simpan Database: " . mysqli_error($conn) . "');</script>";
            }
        } else {
            echo "<script>alert('Gagal Upload File ke Server.');</script>";
        }
    }
}
?>

</body>
</html>