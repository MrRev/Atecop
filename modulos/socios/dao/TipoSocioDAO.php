<?php

require_once __DIR__ . '/../../../util_global/Database.php';

/**
 * Clase DAO: TipoSocioDAO
 * Maneja operaciones de base de datos para tipos de socio
 */
class TipoSocioDAO {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstance()->getConnection();
    }

    /**
     * Listar todos los tipos de socio
     */
    public function listAll(): array {
        try {
            $sql = "SELECT * FROM tiposocio ORDER BY nombretipo ASC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en TipoSocioDAO::listAll - " . $e->getMessage());
            return [];
        }
    }
}
