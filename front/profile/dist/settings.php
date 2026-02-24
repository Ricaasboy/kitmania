<?php
session_start(); // obrigatório no topo

// =============================================
// Inicialização do idioma (o teu código adaptado)
$supported = ['pt', 'en'];
$lang = 'en'; // fallback

if (isset($_GET['lang']) && in_array($_GET['lang'], $supported)) {
  $lang = $_GET['lang'];
  $_SESSION['lang'] = $lang;
} elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $supported)) {
  $lang = $_SESSION['lang'];
}

// Se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['preferred_lang'])) {
  $newLang = $_POST['preferred_lang'] ?? 'en';
  if (in_array($newLang, $supported)) {
    $_SESSION['lang'] = $newLang;
    $lang = $newLang;

    // Redireciona para evitar reenvio do form (PRG pattern)
    header("Location: settings.php?updated=1");
    exit;
  }
}

// =============================================
// Restante do teu header / conexão se necessário
include("../../../back/conn.php"); // se precisares de BD mais tarde (ex: guardar no user)
?>

<!doctype html>
<html lang="<?= $lang ?>" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= t('page_title') ?> | AdminLTE v4</title>

  <!-- teus links normais do AdminLTE v4 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="./css/adminlte.css">
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

  <div class="app-wrapper">

    <?php include("header.php"); ?> <!-- teu header com menu, user dropdown, etc. -->

    <main class="app-main">

      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <h1 class="m-0"><?= t('page_title') ?></h1>
            </div>
          </div>
        </div>
      </div>

      <div class="app-content">
        <div class="container-fluid">

          <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= t('success_message') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title"><?= t('language') ?></h3>
            </div>

            <form method="POST" action="">
              <div class="card-body">

                <div class="form-group">
                  <label for="preferred_lang"><?= t('select_language') ?></label>
                  <select name="preferred_lang" id="preferred_lang" class="form-control w-50">
                    <option value="pt" <?= $lang === 'pt' ? 'selected' : '' ?>>
                      <?= t('portuguese') ?> (PT)
                    </option>
                    <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>
                      <?= t('english') ?> (EN)
                    </option>
                  </select>
                </div>

              </div>

              <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-save"></i> <?= t('save_changes') ?>
                </button>
              </div>
            </form>
          </div>

        </div>
      </div>

    </main>

  </div>

  <!-- Scripts do AdminLTE v4 -->
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
  <script src="./js/adminlte.js"></script>

</body>

</html>