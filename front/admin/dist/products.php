<?php
// Inclui conexão + $lang
include("../../../back/conn.php");

// Força refresh sem cache
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Número de produtos por página
$per_page = 20;

// Página atual
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;

// Total de produtos
$total_stmt = $conn->query("SELECT COUNT(*) as total FROM products");
$total_row = $total_stmt->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $per_page);

// Produtos da página atual
$sql = "SELECT p.id, p.name, p.category_id, p.subcategory_id, p.thumbnail 
        FROM products p 
        ORDER BY p.id DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Cache de nomes de categorias
$categories_cache = [];
function get_category_name($id, $lang, $conn)
{
  global $categories_cache;
  if (isset($categories_cache[$id])) return $categories_cache[$id];

  $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  $name = '—';
  if ($row = $res->fetch_assoc()) {
    $name_json = json_decode($row['name'], true);
    $name = $name_json[$lang] ?? $name_json['pt'] ?? 'Sem nome';
  }
  $categories_cache[$id] = $name;
  return $name;
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>AdminLTE | Dashboard v2</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="color-scheme" content="light dark" />
  <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
  <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="./css/adminlte.css">

  <style>
    /* Botão de ação circular: fundo transparente, borda e ícone cinza claro */
    .btn-circle-action {
      background-color: transparent !important;
      border: 2px solid #adb5bd;
      color: #adb5bd;
      transition: all 0.25s ease;
    }

    .btn-circle-action:hover,
    .btn-circle-action:focus {
      border-color: #33d286 !important;
      color: #33d286 !important;
      background-color: transparent !important;
    }

    .btn-circle-action:not(:disabled):not(.disabled):active,
    .btn-circle-action:not(:disabled):not(.disabled).active {
      background-color: transparent !important;
      border-color: #33d286 !important;
      color: #33d286 !important;
    }
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

  <div class="app-wrapper">

    <?php include("header.php"); ?>

    <main class="app-main">

      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
            </div>
          </div>
        </div>
      </div>

      <div class="app-content">
        <div class="container-fluid">

          <div class="row">
            <div class="col-md-12">
              <div class="card mb-4">
                <div class="card-header">
                  <h3 class="card-title"><?php echo $lang === 'en' ? 'Products' : 'Produtos'; ?></h3>
                </div>

                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th style="width: 1px"><?php echo $lang === 'en' ? 'Thumbnail' : 'Imagem'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Product' : 'Produto'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Categories' : 'Categorias'; ?></th>
                        <th style="width: 40px"><?php echo $lang === 'en' ? 'Actions' : 'Ações'; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                          <?php
                          $name_json = json_decode($row['name'], true);
                          $product_name = $name_json[$lang] ?? $name_json['pt'] ?? 'Sem nome';

                          $cat_name = get_category_name($row['category_id'], $lang, $conn);
                          $sub_name = $row['subcategory_id'] ? get_category_name($row['subcategory_id'], $lang, $conn) : '';
                          $categories = $cat_name . ($sub_name ? ' / ' . $sub_name : '');
                          ?>
                          <tr class="align-middle">
                            <td>
                              <?php if ($row['thumbnail'] && $row['thumbnail'] !== '0'): ?>
                                <img src="<?= htmlspecialchars('../../../' . $row['thumbnail']) ?>" alt="Thumbnail" style="max-height:140px; border:1px solid #ddd; border-radius:4px;">
                              <?php else: ?>
                                <img src="/assets/img/default-product.jpg" alt="Sem imagem" style="max-height:140px; border:1px solid #ddd; border-radius:4px;">
                              <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($product_name) ?></td>
                            <td><?= htmlspecialchars($categories) ?></td>
                            <td class="text-center">
                              <div class="d-flex align-items-center justify-content-center gap-2">

                                <a href="view_product.php?id=<?= $row['id'] ?>"
                                  class="btn btn-circle-action rounded-circle d-flex align-items-center justify-content-center"
                                  style="width: 40px; height: 40px;">
                                  <i style="padding-top: 5px;" class="bi bi-eye fs-5"></i>
                                </a>

                                <a href="create_product.php?id=<?= $row['id'] ?>"
                                  class="btn btn-circle-action rounded-circle d-flex align-items-center justify-content-center"
                                  style="width: 40px; height: 40px;">
                                  <i style="padding-top: 5px;" class="bi bi-pencil fs-5"></i>
                                </a>

                                <form action="../back/delete_product.php" method="POST" style="display:inline;">
                                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                  <button type="submit"
                                    class="btn btn-circle-action rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;"
                                    onclick="return confirm('<?= $lang === 'en' ? 'Delete this product?' : 'Apagar este produto?' ?>');">
                                    <i style="padding-top: 5px;" class="bi bi-trash fs-5"></i>
                                  </button>
                                </form>

                              </div>
                            </td>
                          </tr>
                        <?php endwhile; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="4" class="text-center py-4 text-muted">
                            <?= $lang === 'en' ? 'No products found' : 'Nenhum produto encontrado' ?>
                          </td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

                <div class="card-footer clearfix">
                  <ul class="pagination pagination-sm m-0 float-end">
                    <?php if ($page > 1): ?>
                      <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a style="background-color: #33d286; border-color: #33d286;" class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                      </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                      <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a></li>
                    <?php endif; ?>
                  </ul>

                  <a href="create_product.php">
                    <button type="button" class="btn btn-circle-action rounded-circle d-flex align-items-center justify-content-center"
                      style="width: 30px; height: 30px; margin-right: 1570px">
                      <i style="padding-top: 5px;" class="bi bi-plus fs-5"></i>
                    </button>
                  </a>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>

  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
  <script src="./js/adminlte.js"></script>

  <script>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
      scrollbarTheme: 'os-theme-light',
      scrollbarAutoHide: 'leave',
      scrollbarClickScroll: true,
    };
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
      if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: {
            theme: Default.scrollbarTheme,
            autoHide: Default.scrollbarAutoHide,
            clickScroll: Default.scrollbarClickScroll,
          },
        });
      }
    });
  </script>
</body>

</html>