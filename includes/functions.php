<?php

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfCheck(?string $token): bool
{
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function generarCodigoSeguimiento(PDO $pdo): string
{
    do {
        $codigo = 'DEN-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM denuncias WHERE codigo_seguimiento = ?');
        $stmt->execute([$codigo]);
    } while ((int) $stmt->fetchColumn() > 0);

    return $codigo;
}

function isAdminLogged(): bool
{
    return !empty($_SESSION['admin_id']);
}

function requireAdmin(): void
{
    if (!isAdminLogged()) {
        header('Location: login.php');
        exit;
    }
}

function estadoLabel(string $estado): string
{
    $labels = [
        'pendiente' => 'Pendiente',
        'en_revision' => 'En revisión',
        'resuelta' => 'Resuelta',
        'rechazada' => 'Rechazada',
    ];
    return $labels[$estado] ?? $estado;
}
