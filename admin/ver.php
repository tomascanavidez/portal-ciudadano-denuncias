<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();
$pdo = getDb();

$id = (int) ($_GET['id'] ?? 0);
$mensaje = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck($_POST['csrf_token'] ?? null)) {
        $mensaje = ['tipo' => 'error', 'texto' => 'Sesión inválida. Volvé a intentar.'];
    } else {
        $nuevoEstado = $_POST['estado'] ?? '';
        $notas = trim($_POST['notas_admin'] ?? '');
        if (in_array($nuevoEstado, ['pendiente', 'en_revision', 'resuelta', 'rechazada'], true)) {
            $upd = $pdo->prepare('UPDATE denuncias SET estado = ?, notas_admin = ? WHERE id = ?');
            $upd->execute([$nuevoEstado, $notas ?: null, $id]);
            $mensaje = ['tipo' => 'success', 'texto' => 'Denuncia actualizada correctamente.'];
        }
    }
}

$stmt = $pdo->prepare('
    SELECT d.*, c.nombre AS categoria_nombre
    FROM denuncias d
    JOIN categorias c ON c.id = d.categoria_id
    WHERE d.id = ?
');
$stmt->execute([$id]);
$denuncia = $stmt->fetch();

if (!$denuncia) {
    header('Location: panel.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detalle de denuncia - <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="main-header">
  <div class="container header-inner">
    <a href="<?= SITE_URL ?>/index.php" class="brand">
      <span class="brand-text"><?= e(SITE_NAME) ?></span>
    </a>
    <nav class="main-nav" style="display:flex;">
      <a href="panel.php">Volver al panel</a>
      <a href="logout.php">Cerrar sesión</a>
    </nav>
  </div>
</header>
<main>
<section class="section">
  <div class="container">
    <div class="form-wrapper">
      <h2>Denuncia <?= e($denuncia['codigo_seguimiento']) ?></h2>

      <?php if ($mensaje): ?>
        <div class="alert alert-<?= $mensaje['tipo'] === 'success' ? 'success' : 'error' ?>"><?= e($mensaje['texto']) ?></div>
      <?php endif; ?>

      <p><strong>Categoría:</strong> <?= e($denuncia['categoria_nombre']) ?></p>
      <p><strong>Fecha del hecho:</strong> <?= $denuncia['fecha_hecho'] ? e(date('d/m/Y', strtotime($denuncia['fecha_hecho']))) : '-' ?></p>
      <p><strong>Ubicación:</strong> <?= e($denuncia['ubicacion'] ?: '-') ?></p>
      <p><strong>Denunciante:</strong>
        <?= $denuncia['es_anonima'] ? 'Anónimo' : e($denuncia['nombre'] ?: '-') ?>
      </p>
      <?php if (!$denuncia['es_anonima']): ?>
        <p><strong>Email:</strong> <?= e($denuncia['email'] ?: '-') ?> &nbsp; <strong>Teléfono:</strong> <?= e($denuncia['telefono'] ?: '-') ?></p>
      <?php endif; ?>
      <p><strong>IP de origen:</strong> <?= e($denuncia['ip_origen'] ?: '-') ?></p>
      <p><strong>Descripción:</strong></p>
      <div class="alert alert-info"><?= nl2br(e($denuncia['descripcion'])) ?></div>

      <?php if ($denuncia['adjunto_path']): ?>
        <p><strong>Adjunto:</strong>
          <a href="<?= UPLOAD_URL . e($denuncia['adjunto_path']) ?>" target="_blank" rel="noopener">Ver archivo</a>
        </p>
      <?php endif; ?>

      <hr style="margin:24px 0; border:none; border-top:1px solid var(--borde);">

      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="form-group">
          <label for="estado">Estado</label>
          <select id="estado" name="estado">
            <?php foreach (['pendiente', 'en_revision', 'resuelta', 'rechazada'] as $est): ?>
              <option value="<?= $est ?>" <?= $denuncia['estado'] === $est ? 'selected' : '' ?>><?= e(estadoLabel($est)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="notas_admin">Notas / novedades (visibles para el denunciante)</label>
          <textarea id="notas_admin" name="notas_admin"><?= e($denuncia['notas_admin'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-solid">Guardar cambios</button>
      </form>
    </div>
  </div>
</section>
</main>
</body>
</html>
