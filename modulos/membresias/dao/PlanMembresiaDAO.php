<?php

require_once __DIR__ . '/../../../util_global/Database.php';
require_once __DIR__ . '/../modelo/PlanMembresia.php';

/**
 * Clase DAO: PlanMembresiaDAO
 * Maneja operaciones de base de datos para planes de membresía
 */
class PlanMembresiaDAO {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstance()->getConnection();
    }

    /**
     * Crear un nuevo plan
     */
    public function create(PlanMembresia $plan): bool {
        try {
            $sql = "INSERT INTO planmembresia (nombreplan, duracionmeses, costo, estado) 
                    VALUES (:nombreplan, :duracionmeses, :costo, :estado)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreplan', $plan->getNombreplan());
            $stmt->bindValue(':duracionmeses', $plan->getDuracionmeses(), PDO::PARAM_INT);
            $stmt->bindValue(':costo', $plan->getCosto());
            $stmt->bindValue(':estado', $plan->getEstado());
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en PlanMembresiaDAO::create - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Leer un plan por ID
     */
    public function read(int $idplan): ?array {
        try {
            $sql = "SELECT * FROM planmembresia WHERE idplan = :idplan";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idplan', $idplan, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error en PlanMembresiaDAO::read - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar un plan
     */
    public function update(PlanMembresia $plan): bool {
        try {
            $sql = "UPDATE planmembresia SET 
                    nombreplan = :nombreplan, 
                    duracionmeses = :duracionmeses, 
                    costo = :costo, 
                    estado = :estado 
                    WHERE idplan = :idplan";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':nombreplan', $plan->getNombreplan());
            $stmt->bindValue(':duracionmeses', $plan->getDuracionmeses(), PDO::PARAM_INT);
            $stmt->bindValue(':costo', $plan->getCosto());
            $stmt->bindValue(':estado', $plan->getEstado());
            $stmt->bindValue(':idplan', $plan->getIdplan(), PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en PlanMembresiaDAO::update - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todos los planes
     */
    public function listAll(): array {
        try {
            $sql = "SELECT * FROM planmembresia ORDER BY duracionmeses ASC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PlanMembresiaDAO::listAll - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar solo planes activos
     */
    public function listActivos(): array {
        try {
            $sql = "SELECT * FROM planmembresia WHERE estado = 'Activo' ORDER BY duracionmeses ASC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PlanMembresiaDAO::listActivos - " . $e->getMessage());
            return [];
        }
    }
}
