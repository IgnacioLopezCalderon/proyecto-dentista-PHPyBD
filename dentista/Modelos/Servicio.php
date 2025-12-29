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




//RFunciones para administrador



    //Obtiene los servicios ya asignados a una cita específica.
    public static function obtenerPorCita(int $idCita): array
    {
        $pdo = Database::connect();

        $sql = "SELECT s.* FROM servicio s
                JOIN cita_tiene_servicio cts ON s.id_servicio = cts.id_servicio
                WHERE cts.id_cita = :idCita";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':idCita' => $idCita]);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    }






}