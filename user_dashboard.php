<?php
session_start();
include 'config/koneksi.php';

// Cek status login
$is_logged_in = isset($_SESSION['username']);
$username = $is_logged_in ? $_SESSION['username'] : 'Guest';
$role = $is_logged_in ? $_SESSION['role'] : 'guest';

// Keamanan: Kalau admin nyasar ke sini, balikin ke dashboard admin
if ($role == 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIGA LAB | Command Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #41431B; --secondary: #AEB784; --accent: #E3DBBB;
            --bg: #F2F0E4; --dark: #1A1B0B; --white: #ffffff;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            margin: 0; background: var(--bg); color: var(--dark);
            background-image: radial-gradient(var(--secondary) 0.5px, transparent 0.5px);
            background-size: 20px 20px;
        }
        
        nav { 
            background: rgba(65, 67, 27, 0.95); backdrop-filter: blur(10px);
            padding: 15px 50px; display: flex; justify-content: space-between; 
            align-items: center; color: var(--bg); position: sticky; top: 0; z-index: 1000;
            border-bottom: 3px solid var(--secondary);
        }
        .logo { font-weight: 800; font-size: 1.4rem; letter-spacing: -1px; }
        .user-profile { display: flex; align-items: center; gap: 15px; }
        
        .logout-btn { background: #8B0000; color: white; text-decoration: none; padding: 8px 20px; border-radius: 8px; font-weight: 800; font-size: 0.8rem; transition: 0.3s; border: 2px solid #8B0000; }
        .login-btn { background: var(--secondary); color: var(--primary); text-decoration: none; padding: 8px 25px; border-radius: 8px; font-weight: 800; font-size: 0.8rem; transition: 0.3s; border: 2px solid var(--primary); }
        .login-btn:hover { background: var(--primary); color: var(--white); }

        .container { max-width: 1200px; margin: 50px auto; padding: 0 20px; }
        
        .hero { 
            background: var(--white); padding: 50px; border-radius: 30px; border: 3px solid var(--primary); 
            box-shadow: 15px 15px 0px var(--primary); margin-bottom: 50px; position: relative; overflow: hidden;
        }
        .hero::after { content: "SECURED_SYSTEM"; position: absolute; right: -20px; bottom: -10px; font-family: 'JetBrains Mono'; font-size: 5rem; opacity: 0.03; font-weight: 800; }
        .hero h1 { font-size: 3rem; margin: 0; line-height: 1; color: var(--primary); }
        .hero p { font-size: 1.1rem; opacity: 0.8; margin-top: 15px; max-width: 600px; }

        /* Grid System */
        .menu-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
            gap: 30px; 
        }

        .menu-card { 
            background: var(--white); border-radius: 25px; border: 3px solid var(--primary);
            padding: 40px; text-decoration: none; color: inherit; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex; flex-direction: column; justify-content: space-between; position: relative;
        }

        .menu-card:hover { 
            transform: scale(1.02); 
            box-shadow: 0 20px 40px rgba(65, 67, 27, 0.15);
            background: var(--accent);
        }

        .card-icon { font-size: 3.5rem; margin-bottom: 20px; }
        .menu-card h3 { font-size: 1.5rem; margin: 0; color: var(--primary); font-weight: 800; }
        .menu-card p { font-size: 0.95rem; line-height: 1.6; opacity: 0.7; margin: 15px 0; }
        .card-footer { font-family: 'JetBrains Mono'; font-size: 0.75rem; font-weight: 700; color: var(--primary); display: flex; align-items: center; justify-content: space-between; }

        .status-dot { width: 8px; height: 8px; background: #2ecc71; border-radius: 50%; display: inline-block; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); } }

        footer { text-align: center; padding: 50px; opacity: 0.4; font-weight: 700; font-size: 0.8rem; }
    </style>
</head>
<body>

<nav>
    <div class="logo">GIGA LAB // COMMAND_CENTER</div>
    <div class="user-profile">
        <span style="font-family: 'JetBrains Mono'; font-size: 0.8rem;">STATUS: <?= strtoupper($username); ?></span>
        <?php if ($is_logged_in) : ?>
            <a href="auth/logout.php" class="logout-btn">TERMINATE_SESSION</a>
        <?php else : ?>
            <a href="auth/login.php" class="login-btn">ACCESS_LOGIN</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <div class="hero">
        <h1>Welcome, <?= $is_logged_in ? 'Commander.' : 'Guest.'; ?></h1>
        <p>
            <?= $is_logged_in 
                ? "Sistem GIGA LAB siap diakses sepenuhnya. Semua modul pelatihan dan simulasi jaringan terbuka untuk profil Anda." 
                : "Anda sedang dalam mode Guest. Silakan login untuk membuka akses ke seluruh modul simulasi dan dokumentasi."; 
            ?>
        </p>
    </div>

    <div class="menu-grid">
        <a href="data_materi.php" class="menu-card">
            <div>
                <div class="card-icon">📂</div>
                <h3>Knowledge Base</h3>
                <p>Kumpulan dokumentasi teknis, topologi lab, dan konfigurasi server CentOS & Cisco.</p>
            </div>
            <div class="card-footer">
                <span><?= $is_logged_in ? 'GO_TO_MODULE' : 'LOCKED'; ?></span>
                <span><span class="status-dot"></span> LIVE</span>
            </div>
        </a>

        <a href="video_belajar.php" class="menu-card">
            <div>
                <div class="card-icon">🎬</div>
                <h3>Video Tutorial</h3>
                <p>Panduan visual step-by-step untuk konfigurasi Network Systems Administration.</p>
            </div>
            <div class="card-footer">
                <span><?= $is_logged_in ? 'STREAM_VIDEO' : 'LOCKED'; ?></span>
                <span><span class="status-dot"></span> LIVE</span>
            </div>
        </a>

        <a href="bank_soal.php" class="menu-card">
            <div>
                <div class="card-icon">🎯</div>
                <h3>Challenge Room</h3>
                <p>Uji kesiapan lo menggunakan soal tentang ITNSA dengan berbagai macam level kesulitan.</p>
            </div>
            <div class="card-footer">
                <span><?= $is_logged_in ? 'GO_TO_CHALLENGE' : 'LOCKED'; ?></span>
                <span><span class="status-dot"></span> LIVE</span>
            </div>
        </a>

        <a href="giga_ai.php" class="menu-card">
            <div>
                <div class="card-icon">🧠</div>
                <h3>AI Lab Assistant</h3>
                <p>Gunakan kecerdasan buatan untuk generate soal latihan LKS sesuai level kemampuanmu.</p>
            </div>
            <div class="card-footer">
                <span><?= $is_logged_in ? 'START_ENGINE' : 'LOCKED'; ?></span>
                <span><span class="status-dot" style="background: #f1c40f;"></span> AI_READY</span>
            </div>
        </a>
    </div>

    <footer>
        &copy; 2026 GIGA LAB. BUILT BY NAUFAL GIGA. ALL SYSTEMS OPERATIONAL.
    </footer>
</div>

</body>
</html>