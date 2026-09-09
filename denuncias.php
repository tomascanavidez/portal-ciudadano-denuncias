<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Hacer una denuncia';

$pdo = getDb();
$categorias = $pdo->query('SELECT id, nombre FROM categorias WHERE activo = 1 ORDER BY nombre')->fetchAll();

$codigoGenerado = $_SESSION['ultimo_codigo'] ?? null;
unset($_SESSION['ultimo_codigo']);
$errores = $_SESSION['errores_denuncia'] ?? [];
$datosViejos = $_SESSION['datos_denuncia'] ?? [];
unset($_SESSION['errores_denuncia'], $_SESSION['datos_denuncia']);

require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="form-wrapper">
      <h2>Formulario de denuncia</h2>
      <p>Completá los siguientes datos. Los campos marcados con * son obligatorios.</p>

      <?php if ($codigoGenerado): ?>
        <div class="alert alert-success">
          Tu denuncia fue registrada correctamente. Guardá este código para hacer seguimiento:
          <strong><?= e($codigoGenerado) ?></strong>
        </div>
      <?php endif; ?>

      <?php if (!empty($errores)): ?>
        <div class="alert alert-error">
          <ul style="margin:0; padding-left:18px;">
            <?php foreach ($errores as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="actions/guardar_denuncia.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-group checkbox-row">
          <input type="checkbox" id="es_anonima" name="es_anonima" value="1"
            <?= !empty($datosViejos['es_anonima']) ? 'checked' : '' ?>>
          <label for="es_anonima" style="margin:0;">Realizar denuncia de forma anónima</label>
        </div>

        <div id="datosContacto">
          <div class="form-row">
            <div class="form-group">
              <label for="nombre">Nombre y apellido</label>
              <input type="text" id="nombre" name="nombre" value="<?= e($datosViejos['nombre'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="email">Correo electrónico</label>
              <input type="email" id="email" name="email" value="<?= e($datosViejos['email'] ?? '') ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="telefono">Teléfono de contacto</label>
            <input type="tel" id="telefono" name="telefono" value="<?= e($datosViejos['telefono'] ?? '') ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="categoria_id">Categoría *</label>
            <select id="categoria_id" name="categoria_id" required>
              <option value="">Seleccioná una opción</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>"
                  <?= (isset($datosViejos['categoria_id']) && (int)$datosViejos['categoria_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                  <?= e($cat['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="fecha_hecho">Fecha del hecho</label>
            <input type="date" id="fecha_hecho" name="fecha_hecho" value="<?= e($datosViejos['fecha_hecho'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="ubicacion">Ubicación del hecho</label>
          <input type="text" id="ubicacion" name="ubicacion" placeholder="Calle, número, localidad" value="<?= e($datosViejos['ubicacion'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción de la denuncia *</label>
          <textarea id="descripcion" name="descripcion" required minlength="20" placeholder="Describí el hecho con el mayor detalle posible"><?= e($datosViejos['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="adjunto">Adjuntar archivo (opcional)</label>
          <input type="file" id="adjunto" name="adjunto" accept=".jpg,.jpeg,.png,.pdf">
          <p class="hint">Formatos permitidos: JPG, PNG, PDF. Tamaño máximo 5MB.</p>
        </div>

        <button type="submit" class="btn btn-solid">Enviar denuncia</button>
      </form>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
