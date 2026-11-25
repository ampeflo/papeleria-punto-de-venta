<?php
/**
 * Respuestas JSON con el mismo formato en toda la API:
 *   { "ok": true,  "data": ..., "mensaje": "..." }
 *   { "ok": false, "mensaje": "..." }
 */

class Response
{
    public static function ok($data = null, string $mensaje = '', int $codigo = 200): void
    {
        http_response_code($codigo);
        echo json_encode(['ok' => true, 'data' => $data, 'mensaje' => $mensaje], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(string $mensaje, int $codigo = 400): void
    {
        http_response_code($codigo);
        echo json_encode(['ok' => false, 'mensaje' => $mensaje], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
