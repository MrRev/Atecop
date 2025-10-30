<?php

require_once __DIR__ . '/../../../util_global/Database.php';
require_once __DIR__ . '/../modelo/Pago.php';

/**
 * Clase DAO: PagoDAO
 * Maneja operaciones de base de datos para pagos
 */
class PagoDAO {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstance()->getConnection();
    }

    /**
     * Crear un nuevo pago
     */
    public function create($datos): bool {
        try {
            $sql = "INSERT INTO pago (monto, fechapago, concepto, urlcomprobante, estado, idsocio, idmetodopago) 
                    VALUES (:monto, :fechapago, :concepto, :urlcomprobante, :estado, :idsocio, :idmetodopago)";
            
            $stmt = $this->conexion->prepare($sql);
            
            // Acepta tanto objeto Pago como array
            if ($datos instanceof Pago) {
                $stmt->bindValue(':monto', $datos->getMonto());
                $stmt->bindValue(':fechapago', $datos->getFechapago());
                $stmt->bindValue(':concepto', $datos->getConcepto());
                $stmt->bindValue(':urlcomprobante', $datos->getUrlcomprobante());
                $stmt->bindValue(':estado', $datos->getEstado());
                $stmt->bindValue(':idsocio', $datos->getIdsocio());
                $stmt->bindValue(':idmetodopago', $datos->getIdmetodopago());
            } else {
                $stmt->bindValue(':monto', $datos['monto']);
                $stmt->bindValue(':fechapago', $datos['fechapago']);
                $stmt->bindValue(':concepto', $datos['concepto']);
                $stmt->bindValue(':urlcomprobante', $datos['urlcomprobante']);
                $stmt->bindValue(':estado', $datos['estado']);
                $stmt->bindValue(':idsocio', $datos['idsocio']);
                $stmt->bindValue(':idmetodopago', $datos['idmetodopago']);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en PagoDAO::create - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener pagos de un socio específico
     */
    public function getPagosPorSocio(int $idsocio): array {
        try {
            $sql = "SELECT p.*, mp.nombremetodo 
                    FROM pago p
                    LEFT JOIN metodopago mp ON p.idmetodopago = mp.idmetodopago
                    WHERE p.idsocio = :idsocio
                    ORDER BY p.fechapago DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idsocio', $idsocio, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PagoDAO::getPagosPorSocio - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Leer un pago por ID
     */
    public function read(int $idpago): ?array {
        try {
            $sql = "SELECT p.*, s.nombrecompleto as nombresocio, mp.nombremetodo 
                    FROM pago p
                    LEFT JOIN socio s ON p.idsocio = s.idsocio
                    LEFT JOIN metodopago mp ON p.idmetodopago = mp.idmetodopago
                    WHERE p.idpago = :idpago";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idpago', $idpago, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error en PagoDAO::read - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Anular un pago
     */
    public function anular(int $idpago): bool {
        try {
            $sql = "UPDATE pago SET estado = 'Anulado' WHERE idpago = :idpago";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idpago', $idpago, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en PagoDAO::anular - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todos los pagos
     */
    public function listAll(): array {
        try {
            $sql = "SELECT p.*, s.nombrecompleto as nombresocio, mp.nombremetodo 
                    FROM pago p
                    LEFT JOIN socio s ON p.idsocio = s.idsocio
                    LEFT JOIN metodopago mp ON p.idmetodopago = mp.idmetodopago
                    ORDER BY p.fechapago DESC";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PagoDAO::listAll - " . $e->getMessage());
            return [];
        }
    }
}
