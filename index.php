<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Inicio';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <h1>Bienvenido a <?= e(SITE_NAME) ?></h1>
    <p><?= e(SITE_SLOGAN) ?>. Realizá tu denuncia de forma simple, rápida y segura.</p>
    <div class="hero-actions">
      <a href="<?= SITE_URL ?>/denuncias.php" class="btn btn-primary">Hacer una denuncia</a>
      <a href="<?= SITE_URL ?>/seguimiento.php" class="btn btn-outline">Seguir un reclamo</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="section-title">Servicios disponibles</h2>
    <div class="cards-grid">
      <div class="card">
        <div class="icon">&#9993;</div>
        <h3>Denuncias en línea</h3>
        <p>Presentá tu denuncia sobre seguridad, servicios públicos, medio ambiente y más, con opción de anonimato.</p>
        <a href="<?= SITE_URL ?>/denuncias.php">Ir al formulario &rarr;</a>
      </div>
      <div class="card">
        <div class="icon">&#128269;</div>
        <h3>Seguimiento de reclamos</h3>
        <p>Consultá el estado de tu denuncia ingresando el código de seguimiento que recibiste al enviarla.</p>
        <a href="<?= SITE_URL ?>/seguimiento.php">Consultar estado &rarr;</a>
      </div>
      <div class="card">
        <div class="icon">&#128274;</div>
        <h3>Datos protegidos</h3>
        <p>Tu información se almacena de forma segura. Podés optar por realizar tu denuncia de manera anónima.</p>
      </div>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <h2 class="section-title">¿Cómo funciona?</h2>
    <div class="cards-grid">
      <div class="card">
        <div class="icon">1</div>
        <h3>Completá el formulario</h3>
        <p>Elegí la categoría y describí el hecho con el mayor detalle posible.</p>
      </div>
      <div class="card">
        <div class="icon">2</div>
        <h3>Recibí un código</h3>
        <p>Al enviar tu denuncia se genera un código único de seguimiento.</p>
      </div>
      <div class="card">
        <div class="icon">3</div>
        <h3>Hacé el seguimiento</h3>
        <p>Usá el código para consultar el estado y la resolución de tu caso.</p>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
