<?php
/**
 * Modelo Reporte: consultas de resumen que se muestran en el Panel.
 */

class Reporte
{
    /** Número de ventas y total vendido hoy. */
    public static function ventasHoy(): array
    {
        return Database::conexion()->query(
            'SELECT COUNT(*) AS num_ventas, COALESCE(SUM(total), 0) AS total
             FROM vista_ventas
             WHERE DATE(fecha) = CURDATE()'
        )->fetch();
    }


