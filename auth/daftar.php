<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join GIGA LAB | System Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #41431B; --secondary: #AEB784; --accent: #E3DBBB;
            --bg: #F8F3E1; --white: #ffffff;
        }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg); 
            display: flex; justify-content: center; align-items: center; 
            height: 100vh; margin: 0;
        }
        .register-container {
            background: var(--white);
            padding: 40px;
            border-radius: 25px;
            border: 3px solid var(--primary);
            box-shadow: 15px 15px 0px var(--primary);
            width: 100%;
            max-width: 400px;
        }
        h2 { color: var(--primary); font-weight: 800; text-transform: uppercase; margin-bottom: 25px; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: 600; font-size: 0.8rem; margin-bottom: 5px; color: var(--primary); }
        input {
            width: 100%; padding: 12px; border: 2px solid var(--accent); border-radius: 10px;
            box-sizing: border-box; outline: none; transition: 0.3s;
        }
        input:focus { border-color: var(--primary); background: #fafafa; }
        button {
            width: 100%; padding: 15px; background: var(--primary); color: var(--bg);
            border: none; border-radius: 10px; font-weight: 800; cursor: pointer;
            text-transform: uppercase; margin-top: 10px; transition: 0.3s;
        }
        button:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        p { text-align: center; font-size: 0.85rem; margin-top: 20px; }
        a { color: var(--primary); font-weight: 800; text-decoration: none; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Join GIGA LAB</h2>
    <form action="proses_daftar.php" method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username unik" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="contoh@gigalab.id" required>
        </div>
        <div class="form-group">
            <label>Security Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" name="daftar">Request Access</button>
        <p>Sudah punya akses? <a href="login.php">Login di sini</a></p>
    </form>
</div>

</body>
</html>