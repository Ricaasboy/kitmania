<?php
// conn.php - apenas conexão + idioma

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega o idioma (lang.php)
require_once __DIR__ . "/lang.php";

// ==================== CONEXÃO BD ====================
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'kitmania';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

$conn->set_charset('utf8mb4');
?>