<<<<<<< HEAD
<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['username'])){
    header("Location: ../auth/login.php");
    exit;
}
=======
<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['username'])){
    header("Location: ../auth/login.php");
    exit;
}
>>>>>>> f84e3a15b34b48a451e2d79d91178a54c44a250d
?>