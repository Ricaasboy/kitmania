<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>AdminLTE | Dashboard v2</title>
  <!--begin::Accessibility Meta Tags-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="color-scheme" content="light dark" />
  <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
  <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
  <!--end::Accessibility Meta Tags-->
  <!--begin::Primary Meta Tags-->
  <meta name="title" content="AdminLTE | Dashboard v2" />
  <meta name="author" content="ColorlibHQ" />
  <meta
    name="description"
    content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
  <meta
    name="keywords"
    content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
  <!--end::Primary Meta Tags-->
  <!--begin::Accessibility Features-->
  <!-- Skip links will be dynamically added by accessibility.js -->
  <meta name="supported-color-schemes" content="light dark" />
  <link rel="preload" href="./css/adminlte.css" as="style" />
  <!--end::Accessibility Features-->
  <!--begin::Fonts-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
    integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
    crossorigin="anonymous"
    media="print"
    onload="this.media='all'" />
  <!--end::Fonts-->
  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
    crossorigin="anonymous" />
  <!--end::Third Party Plugin(OverlayScrollbars)-->
  <!--begin::Third Party Plugin(Bootstrap Icons)-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    crossorigin="anonymous" />
  <!--end::Third Party Plugin(Bootstrap Icons)-->
  <!--begin::Required Plugin(AdminLTE)-->
  <link rel="stylesheet" href="./css/adminlte.css" />
  <!--end::Required Plugin(AdminLTE)-->
  <!-- apexcharts -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
    integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
    crossorigin="anonymous" />

  <style>
    /* Botão de ação circular: fundo transparente, borda e ícone cinza claro */
    .btn-circle-action {
      background-color: transparent !important;
      /* fundo 100% transparente */
      border: 2px solid #adb5bd;
      /* borda cinza claro (secondary) */
      color: #adb5bd;
      /* cor do ícone */
      transition: all 0.25s ease;
      /* animação suave */
    }

    /* Hover: muda borda e ícone para #33d286 */
    .btn-circle-action:hover,
    .btn-circle-action:focus {
      border-color: #33d286 !important;
      color: #33d286 !important;
      background-color: transparent !important;
    }

    /* Evita que o Bootstrap sobrescreva em alguns estados */
    .btn-circle-action:not(:disabled):not(.disabled):active,
    .btn-circle-action:not(:disabled):not(.disabled).active {
      background-color: transparent !important;
      border-color: #33d286 !important;
      color: #33d286 !important;
    }
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
  <!--begin::App Wrapper-->
  <div class="app-wrapper">
    <!--begin::Header-->
    <?php include("header.php"); ?>
    <!--end::Header-->
    <!--begin::App Main-->
    <main class="app-main">
      <!--begin::App Content Header-->
      <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Row-->
          <div class="row">
            <div class="col-sm-6">
            </div>
          </div>
          <!--begin::content-header-->

          <!--end::content-header-->
        </div>
        <!--end::Container-->
      </div>
      <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::content-->
          <div class="row">
            <div class="col-md-12">
              <div class="card mb-4">
                <div class="card-header">
                  <h3 class="card-title"><?php echo $lang === 'en' ? 'Products' : 'Produtos'; ?></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th style="width: 10px">#</th>
                        <th><?php echo $lang === 'en' ? 'Product' : 'Produto'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Categories' : 'Categorias'; ?></th>
                        <th style="width: 40px"><?php echo $lang === 'en' ? 'Actions' : 'Ações'; ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="align-middle">
                        <td>1.</td>
                        <td>Name</td>
                        <td>Categorie</td>
                        <td class="text-center"> <!-- text-center centraliza o grupo todo -->
                          <div class="d-flex align-items-center justify-content-center gap-2"> <!-- gap-2 = espaçamento de ~0.5rem entre botões -->

                            <button type="button"
                              class="btn btn-circle-action rounded-circle d-flex align-items-center justify-content-center"
                              style="width: 40px; height: 40px;">
                              <i style="padding-top: 5px;" class="bi bi-trash fs-5"></i> <!-- removi o padding-top: 5px; desnecessário com d-flex -->
                            </button>

                            <button type="button"
                              class="btn btn-circle-action rounded-circle d-flex align-items-center justify-content-center"
                              style="width: 40px; height: 40px;">
                              <i style="padding-top: 5px;" class="bi bi-pencil fs-5"></i> <!-- exemplo: ícone diferente para editar -->
                            </button>

                            <button type="button"
                              class="btn btn-circle-action rounded-circle d-flex align-items-center justify-content-center"
                              style="width: 40px; height: 40px;">
                              <i style="padding-top: 5px;" class="bi bi-eye fs-5"></i> <!-- exemplo: visualizar -->
                            </button>

                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                  <ul class="pagination pagination-sm m-0 float-end">
                    <a href="create_product.php">
                      <button type="button" class="btn btn-circle-action rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 30px; height: 30px; margin-right: 1340px">
                        <i style="padding-top: 5px;" class="bi bi-plus fs-5"></i>
                      </button>
                    </a>
                    <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <!--end::content-->
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content-->
    </main>
    <!--end::App Main-->
  </div>
  <!--end::App Wrapper-->
  <!--begin::Script-->
  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
    crossorigin="anonymous"></script>
  <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
  <script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
  <script src="./js/adminlte.js"></script>
  <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
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
  <!--end::Script-->
</body>
<!--end::Body-->

</html>