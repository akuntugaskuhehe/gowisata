<?php 
require_once __DIR__ . '/../core/session.php'; 
require_once __DIR__ . '/../core/functions.php';

$usr  = isLoggedIn() ? user() : null;
$role = $usr['peran'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= APP_NAME ?></title>

    <!-- Bootstrap, SB Admin 2, Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: "Comic Neue", cursive; }
        .navbar { background: #7fe7a1 !important; }
        .page-wrapper { min-height: 100vh; display:flex; flex-direction:column; }
    </style>
</head>

<body>

<div class="page-wrapper">

<nav class="navbar navbar-expand-lg shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= APP_URL ?>">
      <i class="fa fa-map-marked-alt"></i> <?= APP_NAME ?>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto">

            <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>">Home</a></li>

            <!-- MENU ADMIN -->
            <?php if ($role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-warning fw-bold" href="<?= APP_URL ?>/admin/index.php">
                    <i class="fa fa-tachometer-alt"></i> Admin Dashboard
                </a>
            </li>
            <?php endif; ?>

            <!-- USER LOGIN -->
            <?php if ($usr): ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/user/wishlist.php">
                        <i class="fa fa-heart text-danger"></i> Wishlist
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/user/profile.php">
                        <i class="fa fa-user"></i> Profil
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/auth/logout.php">
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </a>
                </li>

            <?php else: ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/auth/login.php">
                        <i class="fa fa-sign-in-alt"></i> Login
                    </a>
                </li>

            <?php endif; ?>

        </ul>
    </div>

  </div>
</nav>

<div class="container mt-4">
