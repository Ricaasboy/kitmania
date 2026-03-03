<?php
session_start();
include("../../../back/conn.php");

$lang = $_SESSION['lang'] ?? 'en';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);

    // Verifica se o produto existe
    $check = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $_SESSION['form_errors'][] = $lang === 'en' ? "Product not found" : "Produto não encontrado";
    } else {
        // Apaga o produto
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = $lang === 'en' ? "Product deleted successfully!" : "Produto apagado com sucesso!";
        } else {
            $_SESSION['form_errors'][] = $lang === 'en' ? "Error deleting product: " . $stmt->error : "Erro ao apagar produto: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
} else {
    $_SESSION['form_errors'][] = $lang === 'en' ? "Invalid request" : "Pedido inválido";
}

header("Location: ../dist/products.php");  // ou o nome do teu ficheiro de lista
exit;
?>