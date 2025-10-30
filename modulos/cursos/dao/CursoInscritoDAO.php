<?php

/**
 * Clase CursoInscritoDAO
 * Data Access Object para la tabla 'cursoinscrito'
 */
class CursoInscritoDAO {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear una nueva inscripción
     */
    public function createInscripcion(CursoInscrito $inscripcion) {
        try {
            $sql = "INSERT INTO cursoinscrito (idsocio, idcurso, fechainscripcion, estadopagocurso) 
                    VALUES (:idsocio, :idcurso, :fechainscripcion, :estadopagocurso)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':idsocio' => $inscripcion->getIdsocio(),
                ':idcurso' => $inscripcion->getIdcurso(),
                ':fechainscripcion' => $inscripcion->getFechainscripcion(),
                ':estadopagocurso' => $inscripcion->getEstadopagocurso()
            ]);
        } catch (PDOException $e) {
            error_log("Error en createInscripcion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un socio ya está inscrito en un curso
     */
    public function checkInscripcion($idsocio, $idcurso) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM cursoinscrito 
                    WHERE idsocio = :idsocio AND idcurso = :idcurso";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':idsocio' => $idsocio,
                ':idcurso' => $idcurso
            ]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en checkInscripcion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener lista de inscritos en un curso
     */
    public function getInscritosPorCurso($idcurso) {
        try {
            $sql = "SELECT ci.*, s.nombrecompleto, s.dni, s.email, s.telefono
                    FROM cursoinscrito ci
                    INNER JOIN socio s ON ci.idsocio = s.idsocio
                    WHERE ci.idcurso = :idcurso
                    ORDER BY ci.fechainscripcion ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idcurso' => $idcurso]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getInscritosPorCurso: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener cursos en los que está inscrito un socio
     */
    public function getCursosPorSocio($idsocio) {
        try {
            $sql = "SELECT ci.*, c.nombrecurso, c.fechainicio, c.fechafin, c.estado as estado_curso
                    FROM cursoinscrito ci
                    INNER JOIN curso c ON ci.idcurso = c.idcurso
                    WHERE ci.idsocio = :idsocio
                    ORDER BY c.fechainicio DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idsocio' => $idsocio]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getCursosPorSocio: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualizar estado de pago de inscripción
     */
    public function updateEstadoPago($idsocio, $idcurso, $estadopago) {
        try {
            $sql = "UPDATE cursoinscrito 
                    SET estadopagocurso = :estadopago 
                    WHERE idsocio = :idsocio AND idcurso = :idcurso";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':estadopago' => $estadopago,
                ':idsocio' => $idsocio,
                ':idcurso' => $idcurso
            ]);
        } catch (PDOException $e) {
            error_log("Error en updateEstadoPago: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar inscripción
     */
    public function deleteInscripcion($idsocio, $idcurso) {
        try {
            $sql = "DELETE FROM cursoinscrito 
                    WHERE idsocio = :idsocio AND idcurso = :idcurso";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':idsocio' => $idsocio,
                ':idcurso' => $idcurso
            ]);
        } catch (PDOException $e) {
            error_log("Error en deleteInscripcion: " . $e->getMessage());
            return false;
        }
    }
}
