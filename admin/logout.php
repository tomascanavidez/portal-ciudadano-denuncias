<?php
if (!file_exists(__DIR__ . '/../config/config.php')) {
    http_response_code(500);
    exit('Falta config/config.php. Copiá config/config.example.php a config/config.php y completá los datos del hosting.');
}
require_once __DIR__ . '/../config/config.php';
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
