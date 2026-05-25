<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Anuwani.net</title>
    <link class="icon" type="image/png" href="../assets/images/logo.png"> 
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="auth-container">
        <form action="register_action.php" method="POST" class="auth-card">
            <h2>Register</h2>
            <p>Buat akun baru</p>

            <input type="text" name="nama_customer" placeholder="Nama Lengkap" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email_customer" placeholder="Email" required>
            <input type="text" name="telepon_customer" placeholder="Nomor Telepon" required>
            <textarea name="alamat_customer" placeholder="Alamat" required></textarea>

            <div class="password-wrapper" style="margin-bottom: 20px !important;">
                <input type="password" name="password" id="passInput" placeholder="Password" required>
                <i class="bi bi-eye toggle-pass" id="togglePass"></i>
            </div>

            <button type="submit">Register</button>

            <div class="auth-link">
                Sudah punya akun? <a href="login.php">Login</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('togglePass').addEventListener('click', function () {
            const inp = document.getElementById('passInput');
            if (inp.type === 'password') {
                inp.type = 'text';
                this.className = 'bi bi-eye-slash toggle-pass';
            } else {
                inp.type = 'password';
                this.className = 'bi bi-eye toggle-pass';
            }
        });
    </script>
</body>
</html>