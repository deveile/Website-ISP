<?php
session_start();

include '../koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM tb_user 
    WHERE username='$username'"
);

$data = mysqli_fetch_assoc($query);

if ($data) {

    if (password_verify($password, $data['password'])) {

        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'admin') {

            header("Location: /isp_projek/admin/index.php");
            exit;

        } else {

            header("Location: /isp_projek/customer/index.php");
            exit;

        }

    } else {

        echo "
        <script>
            alert('Password salah');
            window.location='login.php';
        </script>
        ";

    }

} else {

    echo "
    <script>
        alert('Username tidak ditemukan');
        window.location='login.php';
    </script>
    ";

}
?>