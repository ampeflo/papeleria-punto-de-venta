<?php
/**
 * Panel de inicio: junta los reportes en una sola respuesta.
 */

class PanelController extends Controller
{
    /** GET /api/panel */
    public function index(): void
    {
        Response::ok([
            'hoy'        => Reporte::ventasHoy(),
            'top'        => Reporte::topProductos(),
            'stock_bajo' => Reporte::stockBajo(),
            'sin_ventas' => Reporte::sinVentas(),
        ]);
    }
}
