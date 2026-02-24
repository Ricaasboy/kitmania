<?php
// Inclui conexão + $lang
include("../../../back/conn.php");

// =============================================
// Línguas para as tabs
$languages = [
  ['code' => 'pt', 'name' => 'Português'],
  ['code' => 'en', 'name' => 'English'],
];

// =============================================
// Produto (valores padrão ou em edição)
$product = $product ?? [
  'id'             => null,
  'name'           => [],
  'description'    => [],
  'price'          => '',
  'category_id'    => '',
  'subcategory_id' => '',
  'status'         => 'active',
  'thumbnail'      => ''
];

// =============================================
// Carrega TODAS as categorias ativas
$all_categories = [];

try {
  $result = $conn->query("
      SELECT id, name, parent_id 
      FROM categories 
      WHERE status = 'active' 
      ORDER BY parent_id IS NULL DESC, id ASC
  ");

  while ($row = $result->fetch_assoc()) {
    $name = json_decode($row['name'], true) ?: ['pt' => $row['name'] ?? 'Sem nome'];
    $all_categories[] = [
      'id'        => $row['id'],
      'name'      => $name,
      'parent_id' => $row['parent_id']
    ];
  }
} catch (Exception $e) {
  error_log("Erro ao carregar categorias: " . $e->getMessage());
  // Fallback básico
  $all_categories = [
    ['id' => 1, 'name' => ['pt' => 'Chuteiras'], 'parent_id' => null],
    ['id' => 2, 'name' => ['pt' => 'Camisolas'], 'parent_id' => null],
    ['id' => 4, 'name' => ['pt' => 'Camisolas de Jogo'], 'parent_id' => 2],
    ['id' => 5, 'name' => ['pt' => 'Camisolas de Treino'], 'parent_id' => 2],
  ];
}

// Filtra apenas as principais
$main_categories = array_filter($all_categories, fn($c) => $c['parent_id'] === null);
?>

<!doctype html>
<html lang="<?= $lang ?>">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $lang === 'en' ? 'Create Product' : 'Criar Produto' ?> | Admin</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="css/adminlte.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css">
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

  <div class="app-wrapper">

    <?php include("header.php"); ?>

    <main class="app-main">

      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
          </div>
        </div>
      </div>

      <div class="app-content">
        <div class="container-fluid">

          <!-- Mensagens -->
          <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= $lang === 'en' ? 'Product saved successfully!' : 'Produto guardado com sucesso!' ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (isset($_SESSION['form_errors'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <ul class="mb-0">
                <?php foreach ($_SESSION['form_errors'] as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['form_errors']); ?>
          <?php endif; ?>

          <!-- FORMULÁRIO -->
          <form method="POST" action="../back/save_product.php" enctype="multipart/form-data">

            <!-- CARD 1: CAMPOS TRADUZÍVEIS -->
            <?php if (!empty($languages)): ?>
              <div class="card">
                <div class="card-header p-2">
                  <ul class="nav nav-tabs">
                    <?php foreach ($languages as $idx => $lang_item): ?>
                      <li class="nav-item">
                        <b><a class="nav-link <?= $idx === 0 ? 'active' : '' ?>"
                            href="#lang-<?= htmlspecialchars($lang_item['code']) ?>"
                            data-bs-toggle="tab" style="color: #33d286; ;">
                            <?= strtoupper($lang_item['code']) ?>
                          </a></b>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>

                <div class="card-body">
                  <div class="tab-content">
                    <?php foreach ($languages as $idx => $lang_item):
                      $code = $lang_item['code'];
                      $langName = $lang_item['name'] ?? strtoupper($code);
                      $nameValue = $product['name'][$code] ?? '';
                      $descValue = $product['description'][$code] ?? '';
                    ?>
                      <div class="tab-pane fade <?= $idx === 0 ? 'show active' : '' ?>"
                        id="lang-<?= htmlspecialchars($code) ?>">

                        <div class="form-group">
                          <label>
                            <?= $lang === 'en' ? 'Product name' : 'Nome do produto' ?>
                            <small class="text-muted">(<?= htmlspecialchars($langName) ?>)</small>
                            <span class="text-danger">*</span>
                          </label>
                          <input type="text"
                            name="name[<?= htmlspecialchars($code) ?>]"
                            value="<?= htmlspecialchars($nameValue) ?>"
                            class="form-control"
                            required
                            placeholder="<?= $lang === 'en' ? 'Ex: FC Porto Jersey' : 'Ex: Camisola FC Porto' ?>">
                        </div>

                        <div class="form-group">
                          <label>
                            <?= $lang === 'en' ? 'Full description' : 'Descrição completa' ?>
                            <small class="text-muted">(<?= htmlspecialchars($langName) ?>)</small>
                          </label>
                          <textarea name="description[<?= htmlspecialchars($code) ?>]"
                            class="form-control"
                            rows="5"
                            placeholder="<?= $lang === 'en' ? 'Describe the product...' : 'Descreva o produto em detalhe...' ?>"><?= htmlspecialchars($descValue) ?></textarea>
                        </div>

                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <!-- CARD 2: DADOS GERAIS -->
            <div class="card mt-4">
              <div class="card-header">
                <h3 class="card-title"><?= $lang === 'en' ? 'General product information' : 'Informações gerais do produto' ?></h3>
              </div>

              <div class="card-body">

                <div class="row">
                  <!-- Preço (col-md-4) -->
                  <div class="col-md-2">
                    <div class="form-group">
                      <label><?= $lang === 'en' ? 'Price (€)' : 'Preço (€)' ?> <span class="text-danger">*</span></label>
                      <input type="number" step="0.01" min="0"
                        name="price"
                        value="<?= htmlspecialchars($product['price'] ?? '') ?>"
                        class="form-control"
                        required>
                    </div>
                  </div>

                  <!-- Categoria Principal (col-md-4) -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label><?= $lang === 'en' ? 'Category' : 'Categoria' ?> <span class="text-danger">*</span></label>
                      <select name="category_id" id="main_category" class="form-control" required>
                        <option value=""><?= $lang === 'en' ? '— Select category —' : '— Selecionar categoria —' ?></option>
                        <?php foreach ($main_categories as $cat):
                          $catName = $cat['name'][$lang] ?? $cat['name']['pt'] ?? 'Sem nome';
                          $selected = ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '';
                        ?>
                          <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($catName) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <!-- Subcategoria (col-md-4) -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label><?= $lang === 'en' ? 'Subcategory' : 'Subcategoria' ?></label>
                      <select name="subcategory_id" id="sub_category" class="form-control">
                        <option value=""><?= $lang === 'en' ? '— Select subcategory —' : '— Selecionar subcategoria —' ?></option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2"> <!-- offset para alinhar com a coluna da direita -->
                    <div class="form-group">
                      <label><?= $lang === 'en' ? 'Status' : 'Estado' ?></label>
                      <select name="status" class="form-control">
                        <?php $current = $product['status'] ?? 'active'; ?>
                        <option value="active" <?= $current === 'active'   ? 'selected' : '' ?>>
                          <?= $lang === 'en' ? 'Active' : 'Ativo' ?>
                        </option>
                        <option value="inactive" <?= $current === 'inactive' ? 'selected' : '' ?>>
                          <?= $lang === 'en' ? 'Inactive' : 'Inativo' ?>
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Thumbnail -->
                <div class="form-group mt-4">
                  <label><?= $lang === 'en' ? 'Main image (thumbnail)' : 'Imagem principal (thumbnail)' ?></label>

                  <!-- Novo input bonito com AdminLTE/Bootstrap 5 -->
                  <div class="input-group">
                    <input type="file" name="thumbnail" class="form-control" id="thumbnail" accept="image/*">
                    <label class="input-group-text text-white" for="thumbnail" style="background-color: #33d286;">
                      <i class="bi bi-upload me-2"></i>
                      <?= $lang === 'en' ? 'Choose file' : 'Escolher ficheiro' ?>
                    </label>
                  </div>

                  <!-- Nome do ficheiro selecionado (opcional, mas fica bonito) -->
                  <div class="mt-2 small text-muted" id="file-name-display">
                    <?= $lang === 'en' ? 'No file chosen' : 'Nenhum ficheiro selecionado' ?>
                  </div>

                  <!-- Pré-visualização da imagem atual (mantido) -->
                  <?php if (!empty($product['thumbnail'])): ?>
                    <div class="mt-3">
                      <img src="<?= htmlspecialchars('/' . $product['thumbnail']) ?>"
                        alt="<?= $lang === 'en' ? 'Current thumbnail' : 'Thumbnail atual' ?>"
                        class="img-thumbnail"
                        style="max-height:140px; object-fit: cover;">
                      <small class="text-muted d-block mt-1">
                        <?= $lang === 'en' ? 'Current image' : 'Imagem atual' ?>
                      </small>
                    </div>
                  <?php endif; ?>
                </div>
              </div>

              <div class="card-footer text-right">
                <button type="submit" class="btn" style="background-color: #33d286; color: white;">
                  <i class="fas fa-save"></i>
                  <?= !empty($product['id'])
                    ? ($lang === 'en' ? 'Update product' : 'Atualizar produto')
                    : ($lang === 'en' ? 'Create product' : 'Criar produto') ?>
                </button>
              </div>
            </div>

          </form>

        </div>
      </div>
    </main>

  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
  <script src="./js/adminlte.js"></script>

  <!-- Script dinâmico para subcategorias -->
  <script>
    const allCategories = <?= json_encode($all_categories) ?>;

    document.getElementById('main_category').addEventListener('change', function() {
      const parentId = this.value;
      const subSelect = document.getElementById('sub_category');

      // Sempre mostra o select (não desativa)
      subSelect.disabled = false;
      subSelect.innerHTML = '<option value=""><?= $lang === 'en' ? '— Select subcategory (optional) —' : '— Selecionar subcategoria (opcional) —' ?></option>';

      if (!parentId) {
        subSelect.innerHTML += '<option value="" disabled><?= $lang === 'en' ? '— Select subcategory (optional) —' : '— Selecione uma categoria primeiro —' ?></option>';
        return;
      }

      const subs = allCategories.filter(cat => cat.parent_id == parentId);

      if (subs.length > 0) {
        subs.forEach(sub => {
          const option = document.createElement('option');
          option.value = sub.id;
          option.textContent = sub.name['<?= $lang ?>'] || sub.name['pt'] || 'Sem nome';
          if (<?= json_encode($product['subcategory_id'] ?? '') ?> == sub.id) {
            option.selected = true;
          }
          subSelect.appendChild(option);
        });
      } else {
        subSelect.innerHTML += '<option value="" disabled selected><?= $lang === 'en' ? '— This category has no subcategories —' : '— Esta categoria não tem subcategorias —' ?></option>';
      }
    });

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
      const mainSelect = document.getElementById('main_category');
      if (mainSelect.value) {
        mainSelect.dispatchEvent(new Event('change'));
      } else {
        document.getElementById('sub_category').innerHTML = '<option value=""><?= $lang === 'en' ? '— Select a category first —' : '— Selecione uma categoria primeiro —' ?></option>';
      }
    });

    // Carrega subcategorias se estiver em modo edição
    document.addEventListener('DOMContentLoaded', function() {
      const mainSelect = document.getElementById('main_category');
      if (mainSelect.value) {
        mainSelect.dispatchEvent(new Event('change'));
      }
    });
  </script>

  <script>
    document.querySelectorAll('.custom-file-input').forEach(input => {
      input.addEventListener('change', function(e) {
        let fileName = e.target.files[0]?.name || '<?= $lang === 'en' ? 'Choose image...' : 'Escolher imagem...' ?>';
        this.nextElementSibling.textContent = fileName;
      });
    });
  </script>

  <!-- Script para mostrar o nome do ficheiro selecionado -->
  <script>
    document.getElementById('thumbnail').addEventListener('change', function(e) {
      const fileName = e.target.files.length > 0 ? e.target.files[0].name : '<?= $lang === 'en' ? 'No file chosen' : 'Nenhum ficheiro selecionado' ?>';
      document.getElementById('file-name-display').textContent = fileName;
    });
  </script>

</body>

</html>