<?php

/**
 * Clase PonenteDAO
 * Data Access Object para la tabla 'ponente'
 */
class PonenteDAO {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear un nuevo ponente
     */
    public function createPonente(Ponente $ponente) {
        try {
            $sql = "INSERT INTO ponente (nombrecompleto, dni, email, telefono, estado, idprofesion) 
                    VALUES (:nombrecompleto, :dni, :email, :telefono, :estado, :idprofesion)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombrecompleto' => $ponente->getNombrecompleto(),
                ':dni' => $ponente->getDni(),
                ':email' => $ponente->getEmail(),
                ':telefono' => $ponente->getTelefono(),
                ':estado' => $ponente->getEstado(),
                ':idprofesion' => $ponente->getIdprofesion()
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en createPonente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener un ponente por ID
     */
    public function readPonente($idponente) {
        try {
            $sql = "SELECT p.*, pr.nombreprofesion 
                    FROM ponente p
                    LEFT JOIN profesion pr ON p.idprofesion = pr.idprofesion
                    WHERE p.idponente = :idponente";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idponente' => $idponente]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $ponente = new Ponente();
                $ponente->setIdponente($row['idponente']);
                $ponente->setNombrecompleto($row['nombrecompleto']);
                $ponente->setDni($row['dni']);
                $ponente->setEmail($row['email']);
                $ponente->setTelefono($row['telefono']);
                $ponente->setEstado($row['estado']);
                $ponente->setIdprofesion($row['idprofesion']);
                return $ponente;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en readPonente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar un ponente
     */
    public function updatePonente(Ponente $ponente) {
        try {
            $sql = "UPDATE ponente 
                    SET nombrecompleto = :nombrecompleto,
                        dni = :dni,
                        email = :email,
                        telefono = :telefono,
                        estado = :estado,
                        idprofesion = :idprofesion
                    WHERE idponente = :idponente";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nombrecompleto' => $ponente->getNombrecompleto(),
                ':dni' => $ponente->getDni(),
                ':email' => $ponente->getEmail(),
                ':telefono' => $ponente->getTelefono(),
                ':estado' => $ponente->getEstado(),
                ':idprofesion' => $ponente->getIdprofesion(),
                ':idponente' => $ponente->getIdponente()
            ]);
        } catch (PDOException $e) {
            error_log("Error en updatePonente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todos los ponentes
     */
    public function listarPonentes($filtros = []) {
        try {
            $sql = "SELECT p.*, pr.nombreprofesion 
                    FROM ponente p
                    LEFT JOIN profesion pr ON p.idprofesion = pr.idprofesion
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($filtros['estado'])) {
                $sql .= " AND p.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (p.nombrecompleto LIKE :busqueda OR p.dni LIKE :busqueda)";
                $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
            }
            
            $sql .= " ORDER BY p.nombrecompleto ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en listarPonentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener lista de ponentes activos (para desplegables)
     */
    public function getListaPonentesActivos() {
        try {
            $sql = "SELECT idponente, nombrecompleto 
                    FROM ponente 
                    WHERE estado = 'Activo' 
                    ORDER BY nombrecompleto ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getListaPonentesActivos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si existe un DNI (para evitar duplicados)
     */
    public function existeDni($dni, $idponente = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM ponente WHERE dni = :dni";
            
            if ($idponente) {
                $sql .= " AND idponente != :idponente";
            }
            
            $stmt = $this->db->prepare($sql);
            $params = [':dni' => $dni];
            
            if ($idponente) {
                $params[':idponente'] = $idponente;
            }
            
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en existeDni: " . $e->getMessage());
            return false;
        }
    }
}
