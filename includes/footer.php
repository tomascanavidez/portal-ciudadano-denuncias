</main>
<footer class="main-footer">
  <div class="container footer-inner">
    <div>
      <strong><?= e(SITE_NAME) ?></strong>
      <p><?= e(SITE_SLOGAN) ?></p>
    </div>
    <div>
      <h4>Enlaces</h4>
      <a href="<?= SITE_URL ?>/index.php">Inicio</a>
      <a href="<?= SITE_URL ?>/denuncias.php">Hacer una denuncia</a>
      <a href="<?= SITE_URL ?>/seguimiento.php">Seguir un reclamo</a>
    </div>
    <div>
      <h4>Contacto</h4>
      <p>info@portalciudadano.example</p>
      <p>0800-000-0000</p>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. Todos los derechos reservados.</div>
  </div>
</footer>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
