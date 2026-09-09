<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (isAdminLogged()) {
    header('Location: panel.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck($_POST['csrf_token'] ?? null)) {
        $error = 'Sesión inválida. Recargá la página e intentá de nuevo.';
    } else {
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        $pdo = getDb();
        $stmt = $pdo->prepare('SELECT id, password_hash, nombre_completo FROM admins WHERE usuario = ?');
        $stmt->execute([$usuario]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nombre'] = $admin['nombre_completo'];
            header('Location: panel.php');
            exit;
        }
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso interno - <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="main-header">
  <div class="container header-inner">
    <a href="<?= SITE_URL ?>/index.php" class="brand">
      <span class="brand-text"><?= e(SITE_NAME) ?></span>
    </a>
  </div>
</header>
<main>
<section class="section">
  <div class="container">
    <div class="form-wrapper" style="max-width:420px;">
      <h2>Acceso interno</h2>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="form-group">
          <label for="usuario">Usuario</label>
          <input type="text" id="usuario" name="usuario" required autofocus>
        </div>
        <div class="form-group">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-solid">Ingresar</button>
      </form>
    </div>
  </div>
</section>
</main>
</body>
</html>
