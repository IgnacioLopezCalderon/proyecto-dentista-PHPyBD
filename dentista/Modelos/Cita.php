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


    public private(set) ?string $nombre_paciente;
    public private(set) ?string $apellido_paciente;
    public private(set) ?string $email;

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






//Funciones para el administrador

    public static function obtenerPendientes(): array
    {
        $pdo = Database::connect();

        $sql = "SELECT 
                        c.id_cita,
                        c.fecha_cita,
                        e.estado as nombre_estado,
                        u.nombre as nombre_paciente,
                        u.apellido1 as apellido_paciente,
                        u.email,
                        GROUP_CONCAT(s.tipo SEPARATOR ', ') as servicios
                    FROM cita c
                    INNER JOIN estado_cita e ON c.id_estado = e.id_estado
                    INNER JOIN usuario u ON c.id_usuario = u.id_usuario
                    LEFT JOIN cita_tiene_servicio cts ON c.id_cita = cts.id_cita
                    LEFT JOIN servicio s ON cts.id_servicio = s.id_servicio
                    WHERE e.estado = 'Pendiente'
                    GROUP BY c.id_cita
                    ORDER BY c.fecha_cita ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS, Cita::class);
    }




    public static function obtenerPorId(int $idCita): ?Cita
    {
        $pdo = Database::connect();

        $sql = "SELECT 
                    c.id_cita,
                    c.fecha_cita,
                    e.estado as nombre_estado,
                    u.nombre as nombre_paciente,
                    u.apellido1 as apellido_paciente,
                    u.email
                FROM cita c
                INNER JOIN estado_cita e ON c.id_estado = e.id_estado
                INNER JOIN usuario u ON c.id_usuario = u.id_usuario
                WHERE c.id_cita = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $idCita]);

        $resultado = $stmt->fetchObject(Cita::class);

        return $resultado ?: null;
    }



    public static function cambiarEstado(int $idCita, int $idEstado): bool
    {
        $pdo = Database::connect();

        $sql = "UPDATE cita SET id_estado = :estado WHERE id_cita = :id";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':estado' => $idEstado,
            ':id'     => $idCita
        ]);
    }



    public static function obtenerPorEstadoYFecha(int|array $estados, string $fecha): array
    {
        $pdo = Database::connect();

        if (is_int($estados)) {
            $estados = [$estados];
        }

        // Crea los signos de interrogación dinámicos (?,?,?)
        $placeholders = implode(',', array_fill(0, count($estados), '?'));

        //Este select lo he tenido que hacer con gemini
        $sql = "SELECT 
                    c.id_cita,
                    c.fecha_cita,
                    e.estado as nombre_estado,
                    u.nombre as nombre_paciente,
                    u.apellido1 as apellido_paciente,
                    u.email,
                    GROUP_CONCAT(s.tipo SEPARATOR ', ') as servicios
                FROM cita c
                INNER JOIN estado_cita e ON c.id_estado = e.id_estado
                INNER JOIN usuario u ON c.id_usuario = u.id_usuario
                LEFT JOIN cita_tiene_servicio cts ON c.id_cita = cts.id_cita
                LEFT JOIN servicio s ON cts.id_servicio = s.id_servicio
                WHERE c.id_estado IN ($placeholders) 
                  AND DATE(c.fecha_cita) = ?
                GROUP BY c.id_cita
                ORDER BY c.fecha_cita ASC";

        $stmt = $pdo->prepare($sql);

        // Une los estados con la fecha para pasarlos a la consulta
        $params = array_merge($estados, [$fecha]);

        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, Cita::class);
    }


}
