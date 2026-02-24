<?php
include("../../../back/conn.php");
?>

<link rel="stylesheet" href="icons" />

<nav class="app-header navbar navbar-expand bg-body">
  <!--begin::Container-->
  <div class="container-fluid">
    <!--begin::Start Navbar Links-->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>
    </ul>
    <!--end::Start Navbar Links-->
    <!--begin::Start Navbar Links-->
    <ul class="navbar-nav">
      <!--begin::Navbar Search-->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="bi bi-search"></i>
        </a>
      </li>
      <!--end::Navbar Search-->
    </ul>
    <!--end::Start Navbar Links-->
    <!--begin::End Navbar Links-->
    <ul class="navbar-nav ms-auto">
      <!--begin::Notifications Dropdown Menu-->
      <li class="nav-item dropdown">
        <a class="nav-link" data-bs-toggle="dropdown" href="#">
          <i class="bi bi-bell-fill"></i>
          <span class="navbar-badge badge text-bg-warning">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="bi bi-envelope me-2"></i> 4 new messages
            <span class="float-end text-secondary fs-7">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="bi bi-people-fill me-2"></i> 8 friend requests
            <span class="float-end text-secondary fs-7">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="bi bi-file-earmark-fill me-2"></i> 3 new reports
            <span class="float-end text-secondary fs-7">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer"> See All Notifications </a>
        </div>
      </li>
      <!--end::Notifications Dropdown Menu-->
      <!--begin::User Menu Dropdown-->
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
          <span
            class="d-none d-md-inline"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Utilizador'; ?></span>
        </a>
      </li>
      <!--end::User Menu Dropdown-->
    </ul>
    <!--end::End Navbar Links-->
  </div>
  <!--end::Container-->
</nav>
<!--end::Header-->
<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="light">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <!--begin::Brand Link-->
    <a href="./index.html" class="brand-link">
      <!--begin::Brand Image-->
      <img src="./assets/img/logo_km.png" alt="AdminLTE Logo" class="brand-image opacity-75" />
      <!--end::Brand Image-->
    </a>
    <!--end::Brand Link-->
  </div>
  <!--end::Sidebar Brand-->
  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <nav class="mt-2 d-flex flex-column h-100">
      <!--begin::Sidebar Menu-->
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
        aria-label="Main navigation" data-accordion="false" id="navigation">
        <li class="nav-item menu-open">
          <a href="../../home.php" class="nav-link">
            <i class="bi bi-chevron-left"></i>
            <p style="padding-left: 15px;">
              <?php echo $lang === 'en' ? 'Back' : 'Voltar'; ?>
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="account.php" class="nav-link">
            <i class="nav-icon bi bi-person-fill"></i>
            <p><?php echo $lang === 'en' ? 'My account' : 'Minha conta'; ?></p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-box-seam-fill"></i>
            <p>
              <?php echo $lang === 'en' ? 'Orders' : 'Encomendas'; ?>
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-heart-fill"></i>
            <p>
              <?php echo $lang === 'en' ? 'Favorites' : 'Favoritos'; ?>
              <span class="nav-badge badge text-bg-secondary me-3"><!-- nº de favoritos --></span>
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="settings.php" class="nav-link">
            <i class="nav-icon bi bi-gear-fill"></i>
            <p>
              <?php echo $lang === 'en' ? 'Settings' : 'Configurações'; ?>
            </p>
          </a>
        </li>
      </ul>
      <!--end::Sidebar Menu-->

      <!-- Início do item fixado na parte inferior -->
      <ul class="nav sidebar-menu flex-column mt-auto">
        <li class="nav-item">
          <a href="../../../back/logout.php" class="nav-link">
            <i class="bi bi-box-arrow-in-left" style="padding-left: 7px;"></i>
            <p>
              <?php echo $lang === 'en' ? 'Sign out' : 'Terminar sessão'; ?>
            </p>
          </a>
        </li>
      </ul>
      <!-- Fim do item fixado na parte inferior -->
    </nav>
  </div>
  <!--end::Sidebar Wrapper-->
</aside>