<?php
/**
 * Router: relaciona cada URL con el método de un controlador.
 *
 *   $router->get('/api/productos/{id}', [ProductoController::class, 'show'], 'login');
 *
 * El tercer parámetro es el middleware: 'login' (cualquier usuario con sesión),
 * 'admin' (solo administrador) o null (ruta pública).
 */

class Router
{
    private array $rutas = [];

    public function get(string $ruta, array $accion, ?string $middleware = null): void
    {
        $this->agregar('GET', $ruta, $accion, $middleware);
    }

    public function post(string $ruta, array $accion, ?string $middleware = null): void
    {
        $this->agregar('POST', $ruta, $accion, $middleware);
    }

    public function put(string $ruta, array $accion, ?string $middleware = null): void
    {
        $this->agregar('PUT', $ruta, $accion, $middleware);
    }

    public function delete(string $ruta, array $accion, ?string $middleware = null): void
    {
        $this->agregar('DELETE', $ruta, $accion, $middleware);
    }

    private function agregar(string $metodo, string $ruta, array $accion, ?string $middleware): void
    {
        // '/api/productos/{id}'  ->  expresión regular '#^/api/productos/(\d+)$#'
        $patron = '#^' . preg_replace('#\{id\}#', '(\d+)', $ruta) . '$#';
        $this->rutas[] = compact('metodo', 'patron', 'accion', 'middleware');
    }

    /** Busca la ruta que coincide con la petición y ejecuta su controlador. */
    public function despachar(string $metodo, string $url): void
    {
        foreach ($this->rutas as $ruta) {
            if ($ruta['metodo'] === $metodo && preg_match($ruta['patron'], $url, $coincidencias)) {

                // 1. Middleware: revisa la sesión / el rol antes de entrar al controlador
                if ($ruta['middleware'] === 'login') Auth::requiereLogin();
                if ($ruta['middleware'] === 'admin') Auth::requiereAdmin();

                // 2. Crear el controlador y llamar al método, pasando el {id} si lo hay
                [$clase, $funcion] = $ruta['accion'];
                $parametros = array_map('intval', array_slice($coincidencias, 1));
                (new $clase())->$funcion(...$parametros);
                return;
            }
        }
        Response::error('Ruta no encontrada.', 404);
    }
}
