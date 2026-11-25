<?php
/**
 * CRUD de clientes.
 */

class ClienteController extends Controller
{
    /** GET /api/clientes?buscar= */
    public function index(): void
    {
        Response::ok(Cliente::todos(trim($_GET['buscar'] ?? '')));
    }

    /** POST /api/clientes */
    public function store(): void
    {
        Cliente::crear($this->datosValidados());
        Response::ok(null, 'Cliente registrado.', 201);
    }

    /** PUT /api/clientes/{id} */
    public function update(int $id): void
    {
        Cliente::actualizar($id, $this->datosValidados());
        Response::ok(null, 'Cliente actualizado.');
    }

    /** DELETE /api/clientes/{id} */
    public function destroy(int $id): void
    {
        Cliente::eliminar($id);
        Response::ok(null, 'Cliente eliminado.');
    }

    private function datosValidados(): array
    {
        $d = $this->datos();
        $nombre   = trim($d['nombre'] ?? '');
        $telefono = trim($d['telefono'] ?? '');
        $email    = trim($d['email'] ?? '');
        $errores  = [];

        if (mb_strlen($nombre) < 3)                                       $errores[] = 'El nombre debe tener al menos 3 caracteres.';
        if ($telefono !== '' && !preg_match('/^\d{10}$/', $telefono))     $errores[] = 'El teléfono debe tener 10 dígitos.';
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))  $errores[] = 'El correo no es válido.';

        $this->validar($errores);

        // Los campos opcionales vacíos se guardan como NULL
        return [
            'nombre'   => $nombre,
            'telefono' => $telefono ?: null,
            'email'    => $email ?: null,
        ];
    }
}
