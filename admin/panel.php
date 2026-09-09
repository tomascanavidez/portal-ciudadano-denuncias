<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();
$pdo = getDb();

$estadoFiltro = $_GET['estado'] ?? '';
$categoriaFiltro = (int) ($_GET['categoria'] ?? 0);

$where = [];
$params = [];
if ($estadoFiltro !== '' && in_array($estadoFiltro, ['pendiente', 'en_revision', 'resuelta', 'rechazada'], true)) {
    $where[] = 'd.estado = ?';
    $params[] = $estadoFiltro;
}
if ($categoriaFiltro > 0) {
    $where[] = 'd.categoria_id = ?';
    $params[] = $categoriaFiltro;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM denuncias d $whereSql");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));

$sql = "
    SELECT d.id, d.codigo_seguimiento, d.estado, d.es_anonima, d.nombre, d.creado_en, c.nombre AS categoria_nombre
    FROM denuncias d
    JOIN categorias c ON c.id = d.categoria_id
    $whereSql
    ORDER BY d.creado_en DESC
    LIMIT $perPage OFFSET $offset
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$denuncias = $stmt->fetchAll();

$categorias = $pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel de denuncias - <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="main-header">
  <div class="container header-inner">
    <a href="<?= SITE_URL ?>/index.php" class="brand">
      <span class="brand-text"><?= e(SITE_NAME) ?></span>
    </a>
    <nav class="main-nav" style="display:flex;">
      <span style="color:#fff;">Hola, <?= e($_SESSION['admin_nombre'] ?? 'Administrador') ?></span>
      <a href="logout.php">Cerrar sesión</a>
    </nav>
  </div>
</header>
<main>
<section class="section">
  <div class="container">
    <h2 class="section-title" style="text-align:left;">Panel de denuncias</h2>

    <form method="get" style="display:flex; gap:14px; margin-bottom:24px; flex-wrap:wrap; align-items:flex-end;">
      <div class="form-group" style="margin:0;">
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
          <option value="">Todos</option>
          <?php foreach (['pendiente', 'en_revision', 'resuelta', 'rechazada'] as $est): ?>
            <option value="<?= $est ?>" <?= $estadoFiltro === $est ? 'selected' : '' ?>><?= e(estadoLabel($est)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin:0;">
        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria">
          <option value="0">Todas</option>
          <?php foreach ($categorias as $cat): ?>
            <option value="<?= (int) $cat['id'] ?>" <?= $categoriaFiltro === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-solid">Filtrar</button>
    </form>

    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Código</th>
            <th>Categoría</th>
            <th>Denunciante</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($denuncias)): ?>
            <tr><td colspan="6">No hay denuncias que coincidan con el filtro.</td></tr>
          <?php endif; ?>
          <?php foreach ($denuncias as $d): ?>
            <tr>
              <td><?= e($d['codigo_seguimiento']) ?></td>
              <td><?= e($d['categoria_nombre']) ?></td>
              <td><?= $d['es_anonima'] ? 'Anónimo' : e($d['nombre'] ?: '-') ?></td>
              <td><?= e(date('d/m/Y H:i', strtotime($d['creado_en']))) ?></td>
              <td><span class="badge badge-<?= e($d['estado']) ?>"><?= e(estadoLabel($d['estado'])) ?></span></td>
              <td><a href="ver.php?id=<?= (int) $d['id'] ?>">Ver detalle</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
      <div style="margin-top:20px; display:flex; gap:10px;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=<?= $i ?>&estado=<?= e($estadoFiltro) ?>&categoria=<?= $categoriaFiltro ?>"
             style="<?= $i === $page ? 'font-weight:bold; text-decoration:underline;' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
</main>
</body>
</html>
