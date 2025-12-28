<?php

namespace Modelos;

use Clases\Database;
use Modelos\Usuario;

class Cita
{
    // Propiedades mapeadas desde la consulta SQL
    public private(set) int $id_cita;
    public private(set) string $fecha_cita;    // DATETIME
    public private(set) string $nombre_estado; // Viene de la tabla estado_cita
    public private(set) ?string $servicios;    // Viene del GROUP_CONCAT

    public static function getByUser(Usuario $usuario): array
    {
        $pdo = Database::connect();

        //buscado en gemini, la consulta era muy compleja
        $sql = "SELECT 
                    c.id_cita,
                    c.fecha_cita,
                    e.estado as nombre_estado,
                    GROUP_CONCAT(s.tipo SEPARATOR ', ') as servicios
                FROM cita c
                INNER JOIN estado_cita e ON c.id_estado = e.id_estado
                LEFT JOIN cita_tiene_servicio cts ON c.id_cita = cts.id_cita
                LEFT JOIN servicio s ON cts.id_servicio = s.id_servicio
                WHERE c.id_usuario = :idu
                GROUP BY c.id_cita
                ORDER BY FIELD(e.estado, 'Pendiente', 'Confirmada', 'Rechazada'), 
                         c.fecha_cita DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([ ":idu" => $usuario->id ]);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, Cita::class);
    }



    //vamos a crear el metodo "crear" para añadir citas
    public static function create(array $datos): void
    {
        $pdo = Database::connect();

        $sqlCita = "INSERT INTO cita (id_usuario, fecha_cita) VALUES (:idu, :fecha)";

        $stmt = $pdo->prepare($sqlCita);
        $stmt->execute([
           ":idu" => $datos['id_usuario'],
           ":fecha" => $datos['fecha_cita']
        ]);

        //Con esto recupero el id de la última cita creada
        $idCitaCreada = $pdo->lastInsertId();

        //creamos la consulta y los alias y lo ejecutamos
        $sqlServicio = "INSERT INTO cita_tiene_servicio (id_cita, id_servicio) VALUES (:idc, :ids)";
        $stmtServicio = $pdo->prepare($sqlServicio);
        $stmtServicio->execute([
            ":idc" => $idCitaCreada,
            ":ids" => $datos['id_servicio']
        ]);

    }




    //funcion para borrar la cita
    public static function delete(int $idCita, int $idUsuario): void
    {
        $pdo = Database::connect();

        //le decimos a la base de dato que borre la cita si coincide el id de la cita con el del usuario
        $sql = "DELETE FROM cita WHERE id_cita = :id AND id_usuario = :idu";


        $stmt = $pdo->prepare($sql);
        $stmt->execute([
           ":id" => $idCita,
           ":idu" => $idUsuario
        ]);

    }
}