<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya Admin yang boleh masuk sini
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIGA | Personal Lab & Journey (ADMIN)</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #41431B;     /* Deep Olive */
            --secondary: #AEB784;   /* Sage Green */
            --accent: #E3DBBB;      /* Sand Beige */
            --bg: #F8F3E1;          /* Creamy White */
            --dark: #2C2E14;        /* Darker Olive for Text */
            --white: #ffffff;
            --danger: #8B0000;      /* Dark Red */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg);
            color: var(--dark);
            line-height: 1.7;
        }

        .container { max-width: 1100px; margin: 0 auto; padding: 0 25px; }

        /* --- Navigation --- */
        nav {
            padding: 30px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            font-weight: 800; 
            font-size: 1.5rem; 
            color: var(--primary);
            text-decoration: none;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-status {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--primary);
            opacity: 0.7;
            letter-spacing: 1px;
        }

        .btn-logout {
            text-decoration: none;
            color: var(--danger);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 8px 18px;
            border: 2px solid var(--danger);
            border-radius: 10px;
            transition: 0.3s;
            letter-spacing: 1px;
        }

        .btn-logout:hover {
            background: var(--danger);
            color: var(--white);
            box-shadow: 0 5px 15px rgba(139, 0, 0, 0.2);
        }

        /* --- Hero Section --- */
        .hero { padding: 40px 0 80px 0; }
        .hero-content { display: flex; align-items: center; justify-content: space-between; gap: 60px; }
        .hero-text { flex: 1.2; }
        
        .badge-dev {
            background: var(--secondary);
            color: var(--primary);
            padding: 6px 14px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .hero-text h1 { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            font-size: clamp(2.5rem, 6vw, 4rem); 
            line-height: 1.1; 
            color: var(--primary);
            margin-bottom: 20px;
        }

        .hero-text p { font-size: 1.1rem; color: var(--dark); opacity: 0.8; margin-bottom: 25px; }
        .tag-row { display: flex; gap: 12px; margin-top: 20px; }
        .tag-item { color: var(--primary); font-weight: 800; font-size: 0.9rem; opacity: 0.7; }

        .hero-image-container { flex: 1; position: relative; display: flex; justify-content: flex-end; }
        .hero-big-img {
            width: 380px; height: 500px; object-fit: cover; border-radius: 24px;
            position: relative; z-index: 2; filter: sepia(0.1) contrast(1.05);
            box-shadow: 25px 25px 0px var(--secondary); border: 2px solid var(--primary);
        }
        .img-accent-box {
            position: absolute; width: 380px; height: 500px; border: 3px solid var(--accent);
            top: -25px; right: 25px; border-radius: 24px; z-index: 1;
        }

        /* --- Info Box --- */
        .info-box {
            background-color: var(--white); padding: 40px; border-radius: 24px;
            border: 2px solid var(--secondary); border-left: 10px solid var(--primary);
            margin-bottom: 60px; box-shadow: 0 10px 30px rgba(65, 67, 27, 0.05);
        }
        .info-box h2 { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--primary); margin-bottom: 10px; }

        /* --- Grid Menu --- */
        .grid-label { 
            font-weight: 800; font-size: 0.8rem; color: var(--primary); opacity: 0.5;
            text-transform: uppercase; letter-spacing: 3px; margin-bottom: 25px;
            display: flex; align-items: center; gap: 15px;
        }
        .grid-label::after { content: ""; height: 2px; flex: 1; background: var(--secondary); }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 60px; }
        .card {
            background: var(--white); padding: 35px; border-radius: 20px; text-decoration: none;
            transition: 0.4s ease; border: 2px solid transparent; box-shadow: 0 4px 15px rgba(65, 67, 27, 0.05);
        }
        .card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(65, 67, 27, 0.1); border-color: var(--secondary); background: var(--accent); }
        .card h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.4rem; margin-bottom: 10px; color: var(--primary); }
        .card p { color: var(--dark); opacity: 0.8; font-size: 0.95rem; }

        footer { text-align: center; padding: 40px 0; color: var(--primary); font-size: 0.8rem; font-weight: 600; opacity: 0.5; }

        @media (max-width: 900px) {
            .hero-content { flex-direction: column-reverse; text-align: center; }
            .hero-image-container { justify-content: center; margin-top: 40px; }
            .hero-big-img, .img-accent-box { width: 300px; height: 400px; }
            .tag-row { justify-content: center; }
            nav { padding: 20px 0; }
        }
    </style>
</head>
<body>

<div class="container">
    <nav>
        <a href="admin_dashboard.php" class="logo">GIGA LAB.</a>
        <div class="nav-right">
            <div class="admin-status">ADMIN_SESSION: <?= strtoupper($username); ?></div>
            <a href="../auth/logout.php" class="btn-logout" onclick="return confirm('Sudah beres brok?')">LOGOUT</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <div class="badge-dev">IT Network Systems Admin</div>
                <h1>They saw my failures,<br>now they’ll witness my ascent.</h1>
                <p>Saya <strong>Naufal Giga Arrasyid</strong>. Fokus pada skalabilitas, keamanan jaringan, dan efisiensi sistem. Di sini adalah pusat dokumentasi saya menuju semua mimpi yang mau saya raih.</p>
                
                <div class="tag-row">
                    <span class="tag-item">#ITNSA</span>
                    <span class="tag-item">#CISCO</span>
                    <span class="tag-item">#LINUX</span>
                    <span class="tag-item">#WINDOWS</span>
                    <span class="tag-item">#ADMIN_MODE</span>
                </div>
            </div>
            
            <div class="hero-image-container">
                <img src="../giga2.jpeg" alt="Giga Profile" class="hero-big-img">
                <div class="img-accent-box"></div>
            </div>
        </div>
    </section>

    <div class="info-box">
        <h2>Control Panel Active.</h2>
        <p>Halo, Giga. Gunakan menu di bawah untuk mengelola database materi, menambah bank soal, atau memperbarui video tutorial. Semua sistem berjalan normal.</p>
    </div>

    <div class="grid-label">Master Management</div>

    <div class="grid">
        <a href="../data_materi.php" class="card">
            <h3>01. Knowledge Base</h3>
            <p>Kelola write-up teknis, edit dokumentasi, dan hapus konfigurasi lama.</p>
        </a>

        <a href="../bank_soal.php" class="card">
            <h3>02. Challenge Room</h3>
            <p>Update bank soal simulasi dan pantau tantangan terbaru.</p>
        </a>

        <a href="../video_belajar.php" class="card">
            <h3>03. Video Gallery</h3>
            <p>Sinkronisasi tutorial visual dengan link YouTube atau Cloud Drive.</p>
        </a>

        <a href="generate_ai.php" class="card" style="border-style: dashed; border-color: var(--primary);">
            <h3 style="display: flex; align-items: center; gap: 10px;">
                04. GIGA AI <span style="font-size: 0.7rem; background: var(--primary); color: var(--bg); padding: 2px 8px; border-radius: 5px;">BETA</span>
            </h3>
            <p>Konfigurasi engine AI untuk generate soal otomatis buat para user.</p>
        </a>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> GIGA LAB. SYSTEM SECURED BY NAUFALGIGA.
    </footer>
</div>

</body>
</html>