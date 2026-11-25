<?php
/**
 * CRUD de productos.
 */

class ProductoController extends Controller
{
    /** GET /api/productos?buscar=&categoria= */
    public function index(): void
    {
        $buscar    = trim($_GET['buscar'] ?? '');
        $categoria = (int) ($_GET['categoria'] ?? 0);
        Response::ok(Producto::todos($buscar, $categoria));
    }

    /** POST /api/productos */
    public function store(): void
    {
        Producto::crear($this->datosValidados());
        Response::ok(null, 'Producto registrado.', 201);
    }

    /** PUT /api/productos/{id} */
    public function update(int $id): void
    {
        Producto::actualizar($id, $this->datosValidados());
        Response::ok(null, 'Producto actualizado.');
    }

    /** DELETE /api/productos/{id} */
    public function destroy(int $id): void
    {
        Producto::eliminar($id);
        Response::ok(null, 'Producto eliminado.');
    }

    /** Valida el formulario y devuelve los datos limpios. */
    private function datosValidados(): array
    {
        $d = $this->datos();
        $errores = [];

        if (trim($d['codigo'] ?? '') === '')                         $errores[] = 'El código es obligatorio.';
        if (mb_strlen(trim($d['nombre'] ?? '')) < 3)                 $errores[] = 'El nombre debe tener al menos 3 caracteres.';
        if ((int) ($d['id_categoria'] ?? 0) <= 0)                    $errores[] = 'Selecciona una categoría.';
        if (!is_numeric($d['precio'] ?? null) || $d['precio'] <= 0)  $errores[] = 'El precio debe ser mayor a 0.';
        if (!is_numeric($d['stock'] ?? null) || $d['stock'] < 0)     $errores[] = 'El stock no puede ser negativo.';
        if (!is_numeric($d['stock_minimo'] ?? null) || $d['stock_minimo'] < 0) $errores[] = 'El stock mínimo no puede ser negativo.';

        $this->validar($errores);

        return [
            'codigo'       => strtoupper(trim($d['codigo'])),
            'nombre'       => trim($d['nombre']),
            'id_categoria' => (int) $d['id_categoria'],
            'precio'       => (float) $d['precio'],
            'stock'        => (int) $d['stock'],
            'stock_minimo' => (int) $d['stock_minimo'],
        ];
    }
}
