<?php
/**
 * Controlador base: funciones que comparten todos los controladores.
 */

class Controller
{
    /** Datos JSON que envió el frontend en el cuerpo de la petición. */
    protected function datos(): array
    {
        $datos = json_decode(file_get_contents('php://input'), true);
        return is_array($datos) ? $datos : [];
    }

    /** Si hay errores de validación, responde 422 con todos los mensajes. */
    protected function validar(array $errores): void
    {
        if ($errores) {
            Response::error(implode(' ', $errores), 422);
        }
    }
}
