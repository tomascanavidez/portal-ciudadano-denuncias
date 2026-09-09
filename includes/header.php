<?php if (!defined('SITE_NAME')) { require_once __DIR__ . '/../config/config.php'; } ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="topbar">
  <div class="container topbar-inner">
    <span>Sitio de gestión ciudadana</span>
    <span>Atención 24hs.</span>
  </div>
</div>

<header class="main-header">
  <div class="container header-inner">
    <a href="<?= SITE_URL ?>/index.php" class="brand">
      <span class="brand-text"><?= e(SITE_NAME) ?></span>
    </a>
    <nav class="main-nav" id="mainNav">
      <a href="<?= SITE_URL ?>/index.php">Inicio</a>
      <a href="<?= SITE_URL ?>/denuncias.php">Hacer una denuncia</a>
      <a href="<?= SITE_URL ?>/seguimiento.php">Seguir un reclamo</a>
      <a href="<?= SITE_URL ?>/admin/login.php">Acceso interno</a>
    </nav>
    <button class="nav-toggle" id="navToggle" aria-label="Abrir menú">&#9776;</button>
  </div>
</header>
<main>
