<?php
/**
 * Inicio y cierre de sesión.
 */

class AuthController extends Controller
{
    /** POST /api/login */
    public function login(): void
    {
        $d = $this->datos();
        $usuario  = trim($d['usuario'] ?? '');
        $password = $d['password'] ?? '';

        if ($usuario === '' || $password === '') {
            Response::error('Escribe tu usuario y contraseña.', 422);
        }

        $u = Usuario::buscarPorUsuario($usuario);

        // password_verify compara la contraseña escrita con la cifrada en la BD
        if (!$u || !password_verify($password, $u['password_hash'])) {
            Response::error('Usuario o contraseña incorrectos.', 401);
        }

        unset($u['password_hash']);   // el hash nunca sale del servidor
        session_regenerate_id(true);
        $_SESSION['usuario'] = $u;
        Response::ok($u, 'Bienvenido(a), ' . $u['nombre']);
    }

    /** POST /api/logout */
    public function logout(): void
    {
        session_destroy();
        Response::ok(null, 'Sesión cerrada.');
    }

    /** GET /api/sesion -> usuario que tiene la sesión iniciada */
    public function sesion(): void
    {
        Response::ok(Auth::usuario());
    }
}
