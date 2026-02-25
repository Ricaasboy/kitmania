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

// Validação da subcategoria (se enviada, verifica se existe)
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

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data']   = $_POST;
    header("Location: ../dist/create_product.php");
    exit;
}

// =============================================
// Prepara JSON multilíngue
$name_json = json_encode(['pt' => $name_pt, 'en' => $name_en], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$desc_json = json_encode(['pt' => $desc_pt, 'en' => $desc_en], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// =============================================
// Upload da thumbnail – BLOCO FINAL QUE FUNCIONA
$thumbnail_path = '0';

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
// Guardar na base de dados – FORÇA ATUALIZAÇÃO DO THUMBNAIL
if ($id > 0) {
    // UPDATE
    $sql = "UPDATE products SET 
                name            = ?,
                description     = ?,
                price           = ?,
                category_id     = ?,
                subcategory_id  = ?,
                status          = ?,
                thumbnail       = ?";  // SEMPRE atualiza thumbnail
    $types  = 'ssdisis';
    $params = [$name_json, $desc_json, $price, $category_id, $subcategory_id, $status, $thumbnail_path];

    $sql .= " WHERE id = ?";
    $params[] = $id;
    $types   .= 'i';
} else {
    // INSERT
    $sql = "INSERT INTO products 
                (name, description, price, category_id, subcategory_id, status, thumbnail) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $types  = 'ssdisis';
    $params = [$name_json, $desc_json, $price, $category_id, $subcategory_id, $status, $thumbnail_path];
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['form_errors'][] = $lang === 'en' ? "Database prepare error: " . $conn->error : "Erro ao preparar query: " . $conn->error;
    header("Location: ../dist/create_product.php");
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['success'] = $lang === 'en' ? "Product saved successfully!" : "Produto guardado com sucesso!";
} else {
    $_SESSION['form_errors'][] = $lang === 'en' ? "Database error: " . $stmt->error : "Erro na BD: " . $stmt->error;
}

$stmt->close();

header("Location: ../dist/create_product.php");
exit;