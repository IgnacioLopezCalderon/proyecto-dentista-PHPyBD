<?php


namespace Modelos;

use Clases\Database;

class Estado
{
    // Constantes públicas para usar en todo el proyecto
    public const PENDIENTE = 'Pendiente';
    public const CONFIRMADA = 'Confirmada';
    public const RECHAZADA = 'Rechazada';
    public const FINALIZADA = 'Finalizada';

    /**
     * Busca el ID de un estado por su nombre exacto.
     * * @param string $nombreEstado
     * @return int
     */
    public static function getId(string $nombreEstado): int
    {
        $pdo = Database::connect();

        $sql = "SELECT id_estado FROM estado_cita WHERE estado = :nombre LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nombre' => $nombreEstado]);


        return (int)$stmt->fetchColumn();
    }
}