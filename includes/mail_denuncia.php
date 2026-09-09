<?php

function enviarMailConfirmacionDenuncia(array $denuncia, string $categoriaNombre): bool
{
    if (empty($denuncia['email'])) {
        return false;
    }

    $codigo = $denuncia['codigo_seguimiento'];
    $urlSeguimiento = SITE_URL . '/seguimiento.php?codigo=' . urlencode($codigo);
    $fecha = date('d/m/Y H:i');
    $nombre = $denuncia['nombre'] ?: 'vecino/a';

    $asunto = 'Confirmación de denuncia - Código ' . $codigo;

    $cuerpoHtml = '
<!DOCTYPE html>
<html lang="es">
<body style="margin:0; padding:0; background:#f4f5f7; font-family: Arial, Helvetica, sans-serif; color:#2B2B2B;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7; padding:24px 0;">
    <tr>
      <td align="center">
        <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #D9E2EC;">
          <tr>
            <td style="background:#232D4F; padding:20px 28px;">
              <span style="color:#ffffff; font-size:18px; font-weight:bold;">' . htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') . '</span>
            </td>
          </tr>
          <tr>
            <td style="padding:28px;">
              <p style="font-size:15px;">Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>
              <p style="font-size:15px;">Recibimos tu denuncia correctamente. A continuación encontrarás el detalle y el código para hacer seguimiento del caso.</p>

              <table width="100%" cellpadding="8" cellspacing="0" style="background:#E9EBF2; border-radius:6px; margin:20px 0; font-size:14px;">
                <tr>
                  <td style="width:40%; color:#6B7280;">Código de seguimiento</td>
                  <td style="font-weight:bold;">' . htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr>
                  <td style="color:#6B7280;">Categoría</td>
                  <td>' . htmlspecialchars($categoriaNombre, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr>
                  <td style="color:#6B7280;">Fecha de registro</td>
                  <td>' . htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr>
                  <td style="color:#6B7280;">Estado actual</td>
                  <td>Pendiente</td>
                </tr>
              </table>

              <p style="font-size:15px;">Podés consultar el estado de tu denuncia en cualquier momento desde el siguiente enlace:</p>
              <p style="text-align:center; margin:26px 0;">
                <a href="' . htmlspecialchars($urlSeguimiento, ENT_QUOTES, 'UTF-8') . '"
                   style="background:#232D4F; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:6px; font-weight:bold; display:inline-block;">
                  Consultar estado
                </a>
              </p>

              <p style="font-size:13px; color:#6B7280;">Guardá este código, ya que es necesario para realizar el seguimiento y no podrá recuperarse por otro medio.</p>
            </td>
          </tr>
          <tr>
            <td style="background:#f0f1f4; padding:18px 28px; font-size:12px; color:#6B7280;">
              Este es un mensaje automático, por favor no respondas a este correo.<br>
              ' . htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') . ' &middot; ' . htmlspecialchars(MAIL_FROM_ADDRESS, ENT_QUOTES, 'UTF-8') . '
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . ">\r\n";

    if (!function_exists('mail')) {
        error_log('[mail_denuncia] La función mail() no está disponible en este hosting.');
        return false;
    }

    $mensajeError = null;
    $puedeCapturarErrores = function_exists('set_error_handler') && function_exists('restore_error_handler');

    if ($puedeCapturarErrores) {
        set_error_handler(function (int $errno, string $errstr) use (&$mensajeError): bool {
            $mensajeError = $errstr;
            return true;
        });
    }

    $enviado = @mail(
        $denuncia['email'],
        '=?UTF-8?B?' . base64_encode($asunto) . '?=',
        $cuerpoHtml,
        $headers
    );

    if ($puedeCapturarErrores) {
        restore_error_handler();
    }

    if (!$enviado) {
        error_log('[mail_denuncia] Fallo al enviar a ' . $denuncia['email'] . ': ' . ($mensajeError ?? 'mail() devolvió false sin detalle adicional'));
    }

    return $enviado;
}
