<?php
if (!file_exists(__DIR__ . '/../config/config.php')) {
    http_response_code(500);
    exit('Falta config/config.php. Copiá config/config.example.php a config/config.php y completá los datos del hosting.');
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/denuncias.php');
    exit;
}

if (!csrfCheck($_POST['csrf_token'] ?? null)) {
    $_SESSION['errores_denuncia'] = ['Sesión inválida o expirada. Volvé a intentar.'];
    header('Location: ' . SITE_URL . '/denuncias.php');
    exit;
}

$errores = [];

$esAnonima = isset($_POST['es_anonima']) ? 1 : 0;
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$categoriaId = (int) ($_POST['categoria_id'] ?? 0);
$fechaHecho = trim($_POST['fecha_hecho'] ?? '');
$ubicacion = trim($_POST['ubicacion'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if ($categoriaId <= 0) {
    $errores[] = 'Debés seleccionar una categoría.';
}
if (mb_strlen($descripcion) < 20) {
    $errores[] = 'La descripción debe tener al menos 20 caracteres.';
}
if (!$esAnonima && $email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo electrónico no es válido.';
}
if ($fechaHecho !== '') {
    $fecha = DateTime::createFromFormat('Y-m-d', $fechaHecho);
    if (!$fecha || $fecha > new DateTime()) {
        $errores[] = 'La fecha del hecho no es válida.';
    }
}

$pdo = getDb();
$catCheck = $pdo->prepare('SELECT COUNT(*) FROM categorias WHERE id = ? AND activo = 1');
$catCheck->execute([$categoriaId]);
if ($categoriaId > 0 && (int) $catCheck->fetchColumn() === 0) {
    $errores[] = 'La categoría seleccionada no es válida.';
}

$adjuntoPath = null;
if (!empty($_FILES['adjunto']['name'])) {
    $file = $_FILES['adjunto'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errores[] = 'Ocurrió un error al subir el archivo.';
    } elseif ($file['size'] > MAX_UPLOAD_SIZE) {
        $errores[] = 'El archivo supera el tamaño máximo permitido (5MB).';
    } else {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ALLOWED_UPLOAD_TYPES, true)) {
            $errores[] = 'Tipo de archivo no permitido. Solo JPG, PNG o PDF.';
        } else {
            $ext = match ($mime) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'application/pdf' => 'pdf',
                default => 'bin',
            };
            $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $ext;
            $destino = UPLOAD_DIR . $nombreArchivo;

            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $destino)) {
                $adjuntoPath = $nombreArchivo;
            } else {
                $errores[] = 'No se pudo guardar el archivo adjunto.';
            }
        }
    }
}

if (!empty($errores)) {
    $_SESSION['errores_denuncia'] = $errores;
    $_SESSION['datos_denuncia'] = [
        'es_anonima' => $esAnonima,
        'nombre' => $nombre,
        'email' => $email,
        'telefono' => $telefono,
        'categoria_id' => $categoriaId,
        'fecha_hecho' => $fechaHecho,
        'ubicacion' => $ubicacion,
        'descripcion' => $descripcion,
    ];
    header('Location: ' . SITE_URL . '/denuncias.php');
    exit;
}

$codigo = generarCodigoSeguimiento($pdo);

$stmt = $pdo->prepare('
    INSERT INTO denuncias
        (codigo_seguimiento, categoria_id, es_anonima, nombre, email, telefono, ubicacion, fecha_hecho, descripcion, adjunto_path, ip_origen)
    VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
');

$stmt->execute([
    $codigo,
    $categoriaId,
    $esAnonima,
    $esAnonima ? null : ($nombre ?: null),
    $esAnonima ? null : ($email ?: null),
    $esAnonima ? null : ($telefono ?: null),
    $ubicacion ?: null,
    $fechaHecho ?: null,
    $descripcion,
    $adjuntoPath,
    $_SERVER['REMOTE_ADDR'] ?? null,
]);

if (!$esAnonima && $email !== '') {
    require_once __DIR__ . '/../includes/mail_denuncia.php';
    $catStmt = $pdo->prepare('SELECT nombre FROM categorias WHERE id = ?');
    $catStmt->execute([$categoriaId]);
    $categoriaNombre = (string) $catStmt->fetchColumn();

    enviarMailConfirmacionDenuncia([
        'codigo_seguimiento' => $codigo,
        'nombre' => $nombre,
        'email' => $email,
    ], $categoriaNombre);
}

$_SESSION['ultimo_codigo'] = $codigo;
header('Location: ' . SITE_URL . '/denuncias.php');
exit;
