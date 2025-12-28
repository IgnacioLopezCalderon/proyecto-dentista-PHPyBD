<?php

namespace Modelos;

use Clases\Database;

class Servicio{

    public private(set) int $id_servicio;
    public private(set) string $tipo;
    public private(set) float $precio;


    public static function obtenerServicios()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM servicio");
        // return buscado en gemini
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}