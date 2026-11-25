<?php
/**
 * CRUD de categorías.
 */

class CategoriaController extends Controller
{
    /** GET /api/categorias */
    public function index(): void
    {
        Response::ok(Categoria::todas());
    }

    /** POST /api/categorias */
    public function store(): void
    {
        $nombre = $this->validarNombre();
        Categoria::crear($nombre);
        Response::ok(null, 'Categoría registrada.', 201);
    }

    /** PUT /api/categorias/{id} */
    public function update(int $id): void
    {
        $nombre = $this->validarNombre();
        Categoria::actualizar($id, $nombre);
        Response::ok(null, 'Categoría actualizada.');
    }

    /** DELETE /api/categorias/{id} */
    public function destroy(int $id): void
    {
        Categoria::eliminar($id);
        Response::ok(null, 'Categoría eliminada.');
    }

    private function validarNombre(): string
    {
        $nombre = trim($this->datos()['nombre'] ?? '');
        if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
            Response::error('El nombre debe tener entre 3 y 50 caracteres.', 422);
        }
        return $nombre;
    }
}
