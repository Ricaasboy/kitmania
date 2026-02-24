<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// back/conn.php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'kitmania';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

require_once __DIR__ . "/lang.php";

// Define charset para UTF-8 (acentos e emojis corretos)
$conn->set_charset('utf8mb4');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/animate.css" />
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css" />
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="../assets/css/meanmenu.min.css" />
    <link rel="stylesheet" href="../assets/css/venobox.css" />
    <link rel="stylesheet" href="../assets/css/font-awesome.css" />
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/responsive.css" />
    <link rel="stylesheet" href="../assets/icons/bootstrap-icons.css" />
</head>

</html>