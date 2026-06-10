<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | Anuwani.net</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png"> 
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <form action="lupa_password_action.php" method="POST" class="auth-card">
            <h2>Pemulihan Password</h2>
            <p>Masukkan Email terdaftar Anda untuk menerima link reset</p>
            
            <input type="email" name="email" placeholder="Alamat Email Anda" required autofocus>

            <button type="submit" name="recover">Kirim Link Reset</button>

            <div class="auth-link">
                Kembali ke <a href="login.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>