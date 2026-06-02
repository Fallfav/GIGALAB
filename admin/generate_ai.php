<?php
session_start();
include '../config/koneksi.php';

// PENGAMAN: Cek admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$hasil_soal = null;
$error = "";

if (isset($_POST['generate'])) {
    $topik = mysqli_real_escape_string($conn, $_POST['topik']);
    $jumlah = (int)$_POST['jumlah_soal'];
    
    // API Key Gemini lo
    $api_key = "AIzaSyBbKpelbwz2qRyoCrP7xE_2ljS1uYKZCUc"; 
    
    // PERBAIKAN UTAMA: Menggunakan model produksi massal yang stabil dan pasti didukung
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $api_key;

    // Prompt diperketat agar Sidzy konsisten membuat kuis ITNSA berbentuk JSON
    $prompt = "Kamu adalah Sidzy, asisten AI dari GIGA LAB. Buatkan $jumlah soal pilihan ganda tentang materi ITNSA dengan topik spesifik: $topik. "
            . "Format JSON harus berupa array objek dengan struktur wajib: "
            . "[{\"soal\": \"teks soal\", \"a\": \"opsi A\", \"b\": \"opsi B\", \"c\": \"opsi C\", \"d\": \"opsi D\", \"kunci\": \"A/B/C/D\", \"pembahasan\": \"teks pembahasan\"}]";

    // Payload dikunci agar menghasilkan data bertipe JSON murni
    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "responseMimeType" => "application/json"
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    // Bypass SSL untuk kelancaran localhost XAMPP
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error = "Sidzy gagal tersambung ke server: " . curl_error($ch);
    } else {
        $response_data = json_decode($response, true);
        
        // Cek jika server Google mengembalikan pesan error internal
        if (isset($response_data['error'])) {
            $error = "API Error: " . $response_data['error']['message'];
        } else {
            $text_output = $response_data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Pengaman ekstra: potong teks jika ada karakter liar di luar array JSON
            $start = strpos($text_output, '[');
            $end = strrpos($text_output, ']');
            
            if ($start !== false && $end !== false) {
                $clean_json = substr($text_output, $start, $end - $start + 1);
                $hasil_soal = json_decode($clean_json, true);
            } else {
                $hasil_soal = json_decode(trim($text_output), true);
            }
            
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($hasil_soal)) {
                $error = "Sidzy ngantuk nih brok, gagal memproses struktur kuis. Coba generate ulang ya!";
            }
        }
    }
    curl_close($ch);
}

if (isset($_POST['simpan_db'])) {
    $soal_list = json_decode($_POST['json_soal'], true);
    $topik_save = mysqli_real_escape_string($conn, $_POST['topik_save']);
    
    if (is_array($soal_list)) {
        foreach ($soal_list as $s) {
            $q_soal = mysqli_real_escape_string($conn, $s['soal']);
            $qa = mysqli_real_escape_string($conn, $s['a']);
            $qb = mysqli_real_escape_string($conn, $s['b']);
            $qc = mysqli_real_escape_string($conn, $s['c']);
            $qd = mysqli_real_escape_string($conn, $s['d']);
            $qkunci = mysqli_real_escape_string($conn, $s['kunci']);
            $pembahasan = mysqli_real_escape_string($conn, $s['pembahasan']);
            
            mysqli_query($conn, "INSERT INTO bank_soal (topik, soal, opsi_a, opsi_b, opsi_c, opsi_d, kunci_jawaban, pembahasan) 
                                 VALUES ('$topik_save', '$q_soal', '$qa', '$qb', '$qc', '$qd', '$qkunci', '$pembahasan')");
        }
        echo "<script>alert('Soal racikan Sidzy sukses disimpan ke Challenge Room!'); window.location='../bank_soal.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidzy AI Generator | GIGA LAB</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap">
    <style>
        :root {
            --primary: #41431B; --secondary: #AEB784; --accent: #E3DBBB;
            --bg: #F8F3E1; --dark: #2C2E14; --white: #ffffff;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--dark); padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        
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
        }

        .btn-dash { background: var(--primary); color: var(--bg) !important; border-color: var(--primary); }
        .btn-dash:hover { background: transparent; color: var(--primary) !important; }

        .card { 
            background: var(--white); padding: 40px; border-radius: 25px; 
            border: 3px solid var(--primary); box-shadow: 12px 12px 0 var(--primary); margin-bottom: 30px;
        }
        
        h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: var(--primary); text-transform: uppercase; margin: 0 0 10px 0; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 800; font-size: 0.85rem; margin-bottom: 8px; text-transform: uppercase; color: var(--primary); }
        
        input[type="text"], select {
            width: 100%; padding: 14px; border: 2px solid var(--accent); 
            border-radius: 12px; box-sizing: border-box; outline: none; background: #fafafa; font-weight: 600;
        }
        input:focus, select:focus { border-color: var(--primary); background: #fff; }
        
        .btn-gen { 
            background: var(--primary); color: var(--bg); border: 2px solid var(--primary); 
            padding: 16px 30px; border-radius: 12px; font-weight: 800; cursor: pointer; 
            transition: 0.3s; width: 100%; font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase; 
        }
        .btn-gen:hover { background: transparent; color: var(--primary); transform: translateY(-3px); }
        
        .btn-save { background: #2e7d32; border-color: #2e7d32; margin-top: 15px; }
        .btn-save:hover { background: transparent; color: #2e7d32; }

        .soal-item { background: #fdfdfd; padding: 20px; border-radius: 15px; border: 2px solid var(--accent); margin-bottom: 15px; }
        .kunci-badge { display: inline-block; background: var(--accent); color: var(--primary); padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 0.8rem; }
        .pembahasan { font-size: 0.9rem; background: #f5f5f5; padding: 10px; border-left: 4px solid var(--secondary); margin-top: 10px; }
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; background: #ffebee; color: #c62828; border: 2px solid #ef9a9a; }
        
        .ai-avatar {
            display: inline-flex; align-items: center; gap: 10px; background: var(--accent);
            padding: 6px 15px; border-radius: 30px; font-weight: 800; font-size: 0.85rem; color: var(--primary);
            margin-bottom: 15px; border: 2px solid var(--primary);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-container">
        <a href="../index.php" class="btn-common btn-dash">🏠 Dashboard</a>
        <a href="../video_belajar.php" class="btn-common">🎬 Video Tutorial</a>
        <a href="../data_materi.php" class="btn-common">📂 Knowledge Base</a>
        <a href="../bank_soal.php" class="btn-common">🎯 Challenge Room</a>
    </div>

    <div class="card">
        <div class="ai-avatar">🤖 SIDZY AI ASSISTANT</div>
        <h2>Racik Soal Kuis ITNSA</h2>
        <p style="margin-top: 0; opacity: 0.7;">Tulis topik server atau jaringan yang lo mau, sisanya biar Sidzy yang beresin.</p>
        
        <?php if($error != ""): ?>
            <div class="alert"><?= $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" onsubmit="return startLoading(this);">
            <div class="form-group">
                <label>Topik Lab ITNSA</label>
                <input type="text" name="topik" placeholder="Contoh: Troubleshooting Web Server Nginx / VLAN Cisco Switch" required>
            </div>
            <div class="form-group">
                <label>Jumlah Soal</label>
                <select name="jumlah_soal">
                    <option value="3">3 Soal</option>
                    <option value="5">5 Soal</option>
                    <option value="10">10 Soal</option>
                </select>
            </div>
            <button type="submit" id="btn-submit-ai" name="generate" class="btn-gen">⚡ Suruh Sidzy Bikin Soal</button>
        </form>
    </div>

    <?php if ($hasil_soal !== null && is_array($hasil_soal)): ?>
        <div class="card">
            <h2>👀 Hasil Racikan Sidzy</h2>
            <p style="opacity: 0.7;">Berikut adalah soal-soal ITNSA yang berhasil dibuat oleh Sidzy.</p>
            <hr style="border: 1px dashed var(--accent); margin-bottom: 20px;">

            <?php foreach ($hasil_soal as $index => $s): ?>
                <div class="soal-item">
                    <strong><?= ($index+1) . ". " . htmlspecialchars($s['soal']); ?></strong>
                    <ul style="list-style: none; padding-left: 0; margin-top: 10px;">
                        <li>A. <?= htmlspecialchars($s['a']); ?></li>
                        <li>B. <?= htmlspecialchars($s['b']); ?></li>
                        <li>C. <?= htmlspecialchars($s['c']); ?></li>
                        <li>D. <?= htmlspecialchars($s['d']); ?></li>
                    </ul>
                    <span class="kunci-badge">Kunci Jawaban: <?= htmlspecialchars($s['kunci']); ?></span>
                    <div class="pembahasan">💡 <strong>Analisis Sidzy:</strong> <?= htmlspecialchars($s['pembahasan']); ?></div>
                </div>
            <?php endforeach; ?>

            <form action="" method="POST">
                <input type="hidden" name="topik_save" value="<?= htmlspecialchars($_POST['topik']); ?>">
                <input type="hidden" name="json_soal" value="<?= htmlspecialchars(json_encode($hasil_soal)); ?>">
                <button type="submit" name="simpan_db" class="btn-gen btn-save">📥 Terima & Simpan ke Challenge Room</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
function startLoading(form) {
    const btn = document.getElementById('btn-submit-ai');
    btn.innerHTML = "⏳ Sidzy sedang meracik soal... 🧪";
    btn.style.opacity = "0.7";
    btn.style.pointerEvents = "none";
    return true;
}
</script>
</body>
</html>