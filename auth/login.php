<?php
session_start();
include '../config/koneksi.php';

$error = "";

if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    $data = mysqli_fetch_array($query);

    if ($data) {
        if (password_verify($pass, $data['password'])) {
            $_SESSION['id'] = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];

            if ($data['role'] == 'admin') {
                header("Location: ../admin/admin_dashboard.php");
                exit();
            } else {
                header("Location: ../user_dashboard.php");
                exit();
            }
        } else {
            $error = "Password salah, brok!";
        }
    } else {
        $error = "User ga ketemu!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GIGA LAB Access</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #41431B; --bg: #F8F3E1; --accent: #AEB784; --white: #ffffff; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg); 
            display: flex; justify-content: center; align-items: center; 
            height: 100vh; margin: 0;
        }
        .login-card {
            background: var(--white);
            padding: 40px;
            border-radius: 25px;
            border: 3px solid var(--primary);
            box-shadow: 15px 15px 0px var(--primary);
            width: 100%; max-width: 350px;
        }
        h2 { color: var(--primary); font-weight: 800; text-align: center; text-transform: uppercase; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        input {
            width: 100%; padding: 12px; border: 2px solid var(--accent); border-radius: 10px;
            box-sizing: border-box; outline: none;
        }
        button {
            width: 100%; padding: 15px; background: var(--primary); color: var(--bg);
            border: none; border-radius: 10px; font-weight: 800; cursor: pointer;
            text-transform: uppercase; margin-top: 10px;
        }
        .error-msg { color: red; font-size: 0.8rem; text-align: center; margin-bottom: 15px; font-weight: 600; }
        p { text-align: center; font-size: 0.85rem; margin-top: 20px; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Access GIGA LAB</h2>
    
    <?php if($error != ""): ?>
        <div class="error-msg"><?= $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="login">Login System</button>
        <p>Belum punya akses? <a href="daftar.php" style="color:var(--primary); font-weight:800; text-decoration:none;">Daftar</a></p>
    </form>
</div>

</body>
</html>