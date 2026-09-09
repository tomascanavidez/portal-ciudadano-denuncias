<?php
// Script de línea de comandos para crear o actualizar un usuario administrador.
// Uso: php crear_admin.php usuario contraseña "Nombre Completo"

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

if (php_sapi_name() !== 'cli') {
    die("Este script solo puede ejecutarse por línea de comandos.\n");
}

if ($argc < 3) {
    die("Uso: php crear_admin.php usuario contraseña \"Nombre Completo\"\n");
}

[$_, $usuario, $password, $nombreCompleto] = array_pad($argv, 4, null);

$hash = password_hash($password, PASSWORD_DEFAULT);
$pdo = getDb();

$stmt = $pdo->prepare('
    INSERT INTO admins (usuario, password_hash, nombre_completo)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), nombre_completo = VALUES(nombre_completo)
');
$stmt->execute([$usuario, $hash, $nombreCompleto]);

echo "Administrador '$usuario' creado/actualizado correctamente.\n";
