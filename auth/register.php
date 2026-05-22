<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | Anuwani.net</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png"> 
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">

        <form action="register_action.php" method="POST" class="auth-card">

            <h2>Register</h2>
            <p>Buat akun baru</p>

            <input 
                type="text" 
                name="nama_customer" 
                placeholder="Nama Lengkap" 
                required
            >

            <input 
                type="text" 
                name="username" 
                placeholder="Username" 
                required
            >

            <input 
                type="email" 
                name="email_customer" 
                placeholder="Email" 
                required
            >

            <input 
                type="text" 
                name="telepon_customer" 
                placeholder="Nomor Telepon" 
                required
            >

            <textarea 
                type="text"
                name="alamat_customer" 
                placeholder="Alamat" 
                required
            ></textarea>

            <input 
                type="password" 
                name="password" 
                placeholder="Password" 
                required
            >

            <button type="submit">
                Register
            </button>

            <div class="auth-link">
                Sudah punya akun?
                <a href="login.php">Login</a>
            </div>

        </form>

    </div>

</body>
</html>