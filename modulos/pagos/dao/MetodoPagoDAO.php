<?php

require_once __DIR__ . '/../../../util_global/Database.php';

/**
 * Clase DAO: MetodoPagoDAO
 * Maneja operaciones de base de datos para métodos de pago
 */
class MetodoPagoDAO {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstance()->getConnection();
    }

    /**
     * Listar todos los métodos de pago
     */
    public function listAll(): array {
        try {
            $sql = "SELECT * FROM metodopago ORDER BY nombremetodo ASC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en MetodoPagoDAO::listAll - " . $e->getMessage());
            return [];
        }
    }
}
