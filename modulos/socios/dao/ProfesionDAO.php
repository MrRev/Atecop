<?php

require_once __DIR__ . '/../../../util_global/Database.php';

/**
 * Clase DAO: ProfesionDAO
 * Maneja operaciones de base de datos para profesiones
 */
class ProfesionDAO {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstance()->getConnection();
    }

    /**
     * Listar todas las profesiones
     */
    public function listAll(): array {
        try {
            $sql = "SELECT * FROM profesion ORDER BY nombreprofesion ASC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en ProfesionDAO::listAll - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crear una nueva profesión
     */
    public function create(string $nombreprofesion): bool {
        try {
            $sql = "INSERT INTO profesion (nombreprofesion) VALUES (:nombreprofesion)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreprofesion', $nombreprofesion);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en ProfesionDAO::create - " . $e->getMessage());
            return false;
        }
    }
}
