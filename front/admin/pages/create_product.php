<?php
include("../../../back/conn.php");

// =============================================
// 1. Línguas como array simples (sem objetos)
$languages = [
  ['code' => 'pt', 'name' => 'Português'],
  ['code' => 'en', 'name' => 'English'],
  // ['code' => 'es', 'name' => 'Español'],
  // adiciona mais se precisares
];

// =============================================
// 2. Produto como array (não objeto)
$product = $product ?? [
  'id'          => null,
  'name'        => [],           // ex: ['pt' => 'Nome', 'en' => 'Name']
  'description' => [],           // ex: ['pt' => '...', 'en' => '...']
  'price'       => '',
  'category_id' => '',
  'status'      => 'active',
  'thumbnail'   => ''
];

// =============================================
// 3. Categorias (carrega da BD ou usa exemplo)
// Ajusta esta parte à tua tabela real
$categories = [];
try {
  // Exemplo com PDO – altera conforme a tua conexão
  $stmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name ASC");
  $stmt->execute();
  $categories_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($categories_result as $row) {
    // Assume que 'name' na BD já é JSON ou string → converte para array se for JSON
    $name = json_decode($row['name'], true) ?: ['pt' => $row['name'] ?? 'Sem nome'];
    $categories[] = [
      'id'   => $row['id'],
      'name' => $name
    ];
  }
} catch (Exception $e) {
  // Fallback para testes
  $categories = [
    ['id' => 1, 'name' => ['pt' => 'Eletrónica']],
    ['id' => 2, 'name' => ['pt' => 'Moda']],
    ['id' => 3, 'name' => ['pt' => 'Casa']],
  ];
}
?>

<!doctype html>
<html lang="pt">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Criar Produto | Admin</title>

<link rel="stylesheet" href="../dist/css/adminlte.min.css">
<link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

  <div class="app-wrapper">

    <?php include("header.php"); ?>

    <main class="app-main">

      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <h1 class="m-0">Criar / Editar Produto</h1>
            </div>
          </div>
        </div>
      </div>

      <div class="app-content">
        <div class="container-fluid">

          <!-- FORMULÁRIO -->
          <form method="POST" action="save_product.php" enctype="multipart/form-data">

            <!-- CARD 1: CAMPOS TRADUZÍVEIS -->
            <?php if (!empty($languages)): ?>
              <div class="card card-primary card-outline">
                <div class="card-header p-2">
                  <ul class="nav nav-pills">
                    <?php foreach ($languages as $idx => $lang): ?>
                      <li class="nav-item">
                        <a class="nav-link <?= $idx === 0 ? 'active' : '' ?>"
                          href="#lang-<?= htmlspecialchars($lang['code']) ?>"
                          data-toggle="tab"> <!-- ← Mantém data-toggle (Bootstrap 4) -->
                          <?= strtoupper($lang['code']) ?>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>

                <div class="card-body">
                  <div class="tab-content">
                    <?php foreach ($languages as $idx => $lang):
                      $code = $lang['code'];
                      $langName = $lang['name'] ?? strtoupper($code);

                      $nameValue = $product['name'][$code] ?? '';
                      $descValue = $product['description'][$code] ?? '';
                    ?>
                      <div class="tab-pane <?= $idx === 0 ? 'active' : '' ?>" <!-- Sem 'fade show' aqui -->
                        id="lang-<?= htmlspecialchars($code) ?>">

                        <div class="form-group">
                          <label>
                            Nome do produto
                            <small class="text-muted">(<?= htmlspecialchars($langName) ?>)</small>
                            <span class="text-danger">*</span>
                          </label>
                          <input type="text"
                            name="name[<?= htmlspecialchars($code) ?>]"
                            value="<?= htmlspecialchars($nameValue) ?>"
                            class="form-control"
                            required
                            placeholder="Ex: iPhone 15 Pro Max">
                        </div>

                        <div class="form-group">
                          <label>
                            Descrição completa
                            <small class="text-muted">(<?= htmlspecialchars($langName) ?>)</small>
                          </label>
                          <textarea name="description[<?= htmlspecialchars($code) ?>]"
                            class="form-control"
                            rows="10"
                            placeholder="Descreva o produto em detalhe..."><?= htmlspecialchars($descValue) ?></textarea>
                        </div>

                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <!-- CARD 2: DADOS GERAIS -->
            <div class="card card-secondary card-outline mt-4">
              <div class="card-header">
                <h3 class="card-title">Informações gerais do produto</h3>
              </div>

              <div class="card-body">

                <div class="row">
                  <!-- Preço -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Preço (€) <span class="text-danger">*</span></label>
                      <input type="number" step="0.01" min="0"
                        name="price"
                        value="<?= htmlspecialchars($product['price'] ?? '') ?>"
                        class="form-control"
                        required>
                    </div>
                  </div>

                  <!-- Categoria -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Categoria <span class="text-danger">*</span></label>
                      <select name="category_id" class="form-control" required>
                        <option value="">— Selecionar categoria —</option>
                        <?php foreach ($categories as $cat):
                          $catName = $cat['name']['pt'] ?? $cat['name'] ?? 'Sem nome';
                          $selected = ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '';
                        ?>
                          <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($catName) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <!-- Status -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Estado</label>
                      <select name="status" class="form-control">
                        <?php $current = $product['status'] ?? 'active'; ?>
                        <option value="active" <?= $current === 'active'   ? 'selected' : '' ?>>Ativo</option>
                        <option value="inactive" <?= $current === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Thumbnail -->
                <div class="form-group">
                  <label>Imagem principal (thumbnail)</label>
                  <div class="custom-file">
                    <input type="file" name="thumbnail" class="custom-file-input" accept="image/*" id="thumbnail">
                    <label class="custom-file-label" for="thumbnail">Escolher imagem...</label>
                  </div>

                  <?php if (!empty($product['thumbnail'])): ?>
                    <div class="mt-3">
                      <img src="<?= htmlspecialchars('/storage/' . $product['thumbnail']) ?>"
                        alt="Thumbnail atual"
                        style="max-height:140px; border:1px solid #ddd; border-radius:4px;">
                    </div>
                  <?php endif; ?>
                </div>

              </div>

              <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i>
                  <?= !empty($product['id']) ? 'Atualizar produto' : 'Criar produto' ?>
                </button>
              </div>
            </div>

          </form>

        </div>
      </div>
    </main>

  </div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>

  <script>
    document.querySelectorAll('.custom-file-input').forEach(input => {
      input.addEventListener('change', function(e) {
        let fileName = e.target.files[0]?.name || 'Escolher imagem...';
        this.nextElementSibling.textContent = fileName;
      });
    });
  </script>

</body>

</html>