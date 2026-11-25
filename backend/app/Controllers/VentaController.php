<?php
/**
 * Ventas: historial, ticket y registro de una venta nueva.
 */

class VentaController extends Controller
{
    /** GET /api/ventas?desde=AAAA-MM-DD&hasta=AAAA-MM-DD */
    public function index(): void
    {
        $desde = $_GET['desde'] ?? date('Y-m-d', strtotime('-7 day'));
        $hasta = $_GET['hasta'] ?? date('Y-m-d');
        Response::ok(Venta::entreFechas($desde, $hasta));
    }

    /** GET /api/ventas/{id} -> ticket */
    public function show(int $id): void
    {
        $venta = Venta::buscar($id);
        if (!$venta) {
            Response::error('Venta no encontrada.', 404);
        }
        Response::ok($venta);
    }

    /** POST /api/ventas  { id_cliente, metodo_pago, productos: [{id_producto, cantidad}] } */
    public function store(): void
    {
        $d         = $this->datos();
        $productos = $d['productos'] ?? [];
        $metodo    = $d['metodo_pago'] ?? '';
        $idCliente = !empty($d['id_cliente']) ? (int) $d['id_cliente'] : null;

        $errores = [];
        if (!in_array($metodo, ['efectivo', 'tarjeta']))          $errores[] = 'Selecciona un método de pago válido.';
        if (!is_array($productos) || count($productos) === 0)     $errores[] = 'Agrega al menos un producto.';
        $this->validar($errores);

        try {
            [$idVenta, $total] = Venta::crear(Auth::usuario()['id_usuario'], $idCliente, $metodo, $productos);
        } catch (InvalidArgumentException $e) {
            Response::error($e->getMessage(), 422);   // ej. stock insuficiente
        }

        Response::ok(['id_venta' => $idVenta, 'total' => $total], "Venta #$idVenta registrada.", 201);
    }
}
