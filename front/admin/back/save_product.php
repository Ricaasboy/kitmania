<?php
// kitmania/front/admin/back/save_product.php

session_start();
include("../../../back/conn.php");  // sobe 3 níveis corretamente

$lang = $_SESSION['lang'] ?? 'en';

// =============================================
// Recolhe dados do formulário
$id              = intval($_POST['id'] ?? 0);
$category_id     = intval($_POST['category_id'] ?? 0);
$subcategory_raw = $_POST['subcategory_id'] ?? '';
$subcategory_id  = ($subcategory_raw !== '' && is_numeric($subcategory_raw)) ? intval($subcategory_raw) : null;

$name_pt = trim($_POST['name']['pt'] ?? '');
$name_en = trim($_POST['name']['en'] ?? '');
$desc_pt = trim($_POST['description']['pt'] ?? '');
$desc_en = trim($_POST['description']['en'] ?? '');

$price  = floatval($_POST['price'] ?? 0);
$status = in_array($_POST['status'] ?? 'active', ['active', 'inactive']) ? $_POST['status'] : 'active';

// Validações básicas
$errors = [];

if (empty($name_pt)) $errors[] = $lang === 'en' ? "Product name (PT) is required" : "Nome do produto (PT) é obrigatório";
if (empty($name_en)) $errors[] = $lang === 'en' ? "Product name (EN) is required" : "Nome do produto (EN) é obrigatório";
if ($price <= 0) $errors[] = $lang === 'en' ? "Price must be greater than 0" : "O preço deve ser maior que 0";
if ($category_id <= 0) $errors[] = $lang === 'en' ? "Please select a category" : "Selecione uma categoria";

// Validação da subcategoria
if ($subcategory_id !== null && $subcategory_id > 0) {
    $check = $conn->prepare("SELECT 1 FROM categories WHERE id = ? AND status = 'active'");
    $check->bind_param("i", $subcategory_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $errors[] = $lang === 'en' ? "Invalid or inactive subcategory selected" : "Subcategoria inválida ou inativa";
        $subcategory_id = null;
    }
    $check->close();
}

// Validação do status (já é ENUM, só garantimos que é válido)
if (!in_array($status, ['active', 'inactive'])) {
    $status = 'active'; // fallback seguro
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data']   = $_POST;
    header("Location: ../dist/create_product.php" . ($id > 0 ? "?id=" . $id : ""));
    exit;
}

// =============================================
// Prepara JSON multilíngue
$name_json = json_encode(['pt' => $name_pt, 'en' => $name_en], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$desc_json = json_encode(['pt' => $desc_pt, 'en' => $desc_en], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// =============================================
// Upload da thumbnail
$thumbnail_path = '0';  // default

// Se for edição e não enviou nova imagem, manter a antiga
if ($id > 0 && (!isset($_FILES['thumbnail']) || $_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK || empty($_FILES['thumbnail']['name']))) {
    // Buscar thumbnail atual
    $stmt = $conn->prepare("SELECT thumbnail FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $thumbnail_path = $row['thumbnail'];
    }
    $stmt->close();
}

// Processar novo upload se existir
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK && !empty($_FILES['thumbnail']['name'])) {
    $upload_dir_relative = 'assets/img/products/';
    $upload_dir_absolute = __DIR__ . '/../../../' . $upload_dir_relative;

    if (!is_dir($upload_dir_absolute)) {
        mkdir($upload_dir_absolute, 0755, true);
    }

    $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
    $filename = uniqid('prod_') . '.' . $ext;
    $target = $upload_dir_absolute . $filename;

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $allowed) && $_FILES['thumbnail']['size'] <= 5 * 1024 * 1024) {
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target)) {
            $thumbnail_path = $upload_dir_relative . $filename;
        }
    }
}

// =============================================
// Guardar na base de dados
if ($id > 0) {
    // UPDATE – modo edição
    $sql = "UPDATE products SET 
                name            = ?,
                description     = ?,
                price           = ?,
                category_id     = ?,
                subcategory_id  = ?,
                status          = ?";
    $types  = 'ssdiss';
    $params = [$name_json, $desc_json, $price, $category_id, $subcategory_id, $status];

    // Só atualiza thumbnail se nova imagem foi enviada
    if ($thumbnail_path !== '0' && isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $sql .= ", thumbnail = ?";
        $params[] = $thumbnail_path;
        $types   .= 's';
    }

    $sql .= " WHERE id = ?";
    $params[] = $id;
    $types   .= 'i';
} else {
    // INSERT – criação nova
    $sql = "INSERT INTO products 
                (name, description, price, category_id, subcategory_id, status, thumbnail) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $types  = 'ssdissi';
    $params = [$name_json, $desc_json, $price, $category_id, $subcategory_id, $status, $thumbnail_path];
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['form_errors'][] = $lang === 'en' ? "Database prepare error: " . $conn->error : "Erro ao preparar query: " . $conn->error;
    header("Location: ../dist/create_product.php" . ($id > 0 ? "?id=" . $id : ""));
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['success'] = $lang === 'en' ? "Product saved successfully!" : "Produto guardado com sucesso!";

    // Se foi inserção nova, pegar o ID gerado
    if ($id == 0) {
        $id = $stmt->insert_id;
    }

    // Redirect com id para recarregar dados frescos em edição
    header("Location: ../dist/create_product.php?id=" . $id . "&success=1");
} else {
    $_SESSION['form_errors'][] = $lang === 'en' ? "Database error: " . $stmt->error : "Erro na BD: " . $stmt->error;
    header("Location: ../dist/create_product.php" . ($id > 0 ? "?id=" . $id : ""));
}

$stmt->close();
$conn->close();
exit;
