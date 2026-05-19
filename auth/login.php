<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Anuwani.net</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="auth-container">

        <form action="login_action.php" method="POST" class="auth-card">

            <h2>Login</h2>
            <p>Masuk ke akun Anda</p>

            <input 
                type="text" 
                name="username" 
                placeholder="Username" 
                required
            >

            <input 
                type="password" 
                name="password" 
                placeholder="Password" 
                required
            >

            <button type="submit">
                Login
            </button>

            <div class="auth-link">
                Belum punya akun?
                <a href="register.php">Register</a>
            </div>

        </form>

    </div>

</body>

</html>