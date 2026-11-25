<?php
/**
 * Middleware de autenticación.
 * Se ejecuta ANTES del controlador (ver Router::despachar).
 * Los permisos se revisan en el servidor: ocultar un botón en Vue no es seguridad.
 */

class Auth
{
    /** Usuario con sesión iniciada (o null). */
    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function requiereLogin(): void
    {
        if (!self::usuario()) {
            Response::error('Debes iniciar sesión.', 401);
        }
    }

    public static function requiereAdmin(): void
    {
        self::requiereLogin();
        if (self::usuario()['rol'] !== 'Administrador') {
            Response::error('Acceso denegado: solo el administrador puede hacer esto.', 403);
        }
    }
}
