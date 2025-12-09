<?php
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../includes/db.php';

requireLogin();

if (user()["role"] !== "admin") {
    die("<h3 style='color:red'>Akses ditolak!</h3>");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin — <?= APP_NAME ?></title>

    <!-- SBAdmin2 + Bootstrap + Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: "Comic Neue", cursive; }
        .bg-admin { background-color: #28a745; }
        .sidebar { min-height: 100vh; }
        .sidebar .nav-link.active { background:#34ce57; color:white; }
        .sidebar .nav-link:hover { background:#34ce57; color:white; }
    </style>
</head>

<body id="page-top">

<div id="wrapper">

<!-- ===================== SIDEBAR ===================== -->
<ul class="navbar-nav bg-admin sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" 
       href="<?= APP_URL ?>/admin/dashboard.php">
        <div class="sidebar-brand-icon">
            <i class="fa fa-user-shield"></i>
        </div>
        <div class="sidebar-brand-text mx-3"><?= APP_NAME ?></div>
    </a>

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'dashboard') ? 'active':'' ?>"
           href="<?= APP_URL ?>/admin">
           <i class="fa fa-chart-line"></i> <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'destinasi') ? 'active':'' ?>"
           href="<?= APP_URL ?>/admin/destinasi-index.php">
           <i class="fa fa-map"></i> <span>Destinasi</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="<?= APP_URL ?>">
           <i class="fa fa-home"></i> <span>Ke Website</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link" href="<?= APP_URL ?>/auth/logout.php">
           <i class="fa fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </li>

</ul>
<!-- ===================== END SIDEBAR ===================== -->

<!-- ===================== CONTENT WRAPPER ===================== -->
<div id="content-wrapper" class="d-flex flex-column">

    <div id="content">

        <!-- TOPBAR -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            <?= user()["nama"] ?>
                        </span>
                        <img class="img-profile rounded-circle"
                             src="<?= APP_URL ?>/assets/img/user/<?= user()['foto'] ?? 'default.png' ?>"
                             width="35" height="35" style="object-fit:cover;">
                    </a>

                    <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in">
                        <a class="dropdown-item" href="<?= APP_URL ?>/user/profile.php">
                            <i class="fa fa-user fa-sm fa-fw mr-2"></i> Profil
                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item" href="<?= APP_URL ?>/auth/logout.php">
                            <i class="fa fa-sign-out-alt fa-sm fa-fw mr-2"></i> Logout
                        </a>
                    </div>

                </li>

            </ul>

        </nav>

        <div class="container-fluid">
