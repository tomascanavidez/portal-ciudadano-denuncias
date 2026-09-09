<?php
if (!file_exists(__DIR__ . '/config/config.php')) {
    http_response_code(500);
    exit('Falta config/config.php. Copiá config/config.example.php a config/config.php y completá los datos del hosting.');
}
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Seguir un reclamo';
$pdo = getDb();

$denuncia = null;
$buscado = false;
$codigo = trim($_GET['codigo'] ?? '');

if ($codigo !== '') {
    $buscado = true;
    $stmt = $pdo->prepare('
        SELECT d.*, c.nombre AS categoria_nombre
        FROM denuncias d
        JOIN categorias c ON c.id = d.categoria_id
        WHERE d.codigo_seguimiento = ?
    ');
    $stmt->execute([$codigo]);
    $denuncia = $stmt->fetch();
}

require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="form-wrapper">
      <h2>Seguimiento de denuncia</h2>
      <p>Ingresá el código que recibiste al enviar tu denuncia (por ejemplo: DEN-2026-A1B2C3).</p>

      <form action="seguimiento.php" method="get">
        <div class="form-group">
          <label for="codigo">Código de seguimiento</label>
          <input type="text" id="codigo" name="codigo" value="<?= e($codigo) ?>" placeholder="DEN-2026-XXXXXX" required>
        </div>
        <button type="submit" class="btn btn-solid">Consultar</button>
      </form>

      <?php if ($buscado): ?>
        <hr style="margin:28px 0; border:none; border-top:1px solid var(--borde);">
        <?php if ($denuncia): ?>
          <p><strong>Categoría:</strong> <?= e($denuncia['categoria_nombre']) ?></p>
          <p><strong>Estado:</strong>
            <span class="badge badge-<?= e($denuncia['estado']) ?>"><?= e(estadoLabel($denuncia['estado'])) ?></span>
          </p>
          <p><strong>Fecha de registro:</strong> <?= e(date('d/m/Y H:i', strtotime($denuncia['creado_en']))) ?></p>
          <?php if (!empty($denuncia['notas_admin'])): ?>
            <p><strong>Novedades:</strong></p>
            <div class="alert alert-info"><?= nl2br(e($denuncia['notas_admin'])) ?></div>
          <?php endif; ?>
        <?php else: ?>
          <div class="alert alert-error">No se encontró ninguna denuncia con ese código.</div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
