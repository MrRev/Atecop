<?php

/**
 * Clase CursoDAO
 * Data Access Object para la tabla 'curso'
 */
class CursoDAO {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear un nuevo curso
     */
    public function createCurso(Curso $curso) {
        try {
            $sql = "INSERT INTO curso (nombrecurso, descripcion, cupostotales, costoinscripcion, 
                    fechainicio, fechafin, urlenlacevirtual, estado, idponente) 
                    VALUES (:nombrecurso, :descripcion, :cupostotales, :costoinscripcion, 
                    :fechainicio, :fechafin, :urlenlacevirtual, :estado, :idponente)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombrecurso' => $curso->getNombrecurso(),
                ':descripcion' => $curso->getDescripcion(),
                ':cupostotales' => $curso->getCupostotales(),
                ':costoinscripcion' => $curso->getCostoinscripcion(),
                ':fechainicio' => $curso->getFechainicio(),
                ':fechafin' => $curso->getFechafin(),
                ':urlenlacevirtual' => $curso->getUrlenlacevirtual(),
                ':estado' => $curso->getEstado(),
                ':idponente' => $curso->getIdponente()
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en createCurso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener un curso por ID
     */
    public function readCurso($idcurso) {
        try {
            $sql = "SELECT c.*, p.nombrecompleto as nombre_ponente 
                    FROM curso c
                    LEFT JOIN ponente p ON c.idponente = p.idponente
                    WHERE c.idcurso = :idcurso";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idcurso' => $idcurso]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $curso = new Curso();
                $curso->setIdcurso($row['idcurso']);
                $curso->setNombrecurso($row['nombrecurso']);
                $curso->setDescripcion($row['descripcion']);
                $curso->setCupostotales($row['cupostotales']);
                $curso->setCostoinscripcion($row['costoinscripcion']);
                $curso->setFechainicio($row['fechainicio']);
                $curso->setFechafin($row['fechafin']);
                $curso->setUrlenlacevirtual($row['urlenlacevirtual']);
                $curso->setEstado($row['estado']);
                $curso->setIdponente($row['idponente']);
                return $curso;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en readCurso: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar un curso
     */
    public function updateCurso(Curso $curso) {
        try {
            $sql = "UPDATE curso 
                    SET nombrecurso = :nombrecurso,
                        descripcion = :descripcion,
                        cupostotales = :cupostotales,
                        costoinscripcion = :costoinscripcion,
                        fechainicio = :fechainicio,
                        fechafin = :fechafin,
                        urlenlacevirtual = :urlenlacevirtual,
                        estado = :estado,
                        idponente = :idponente
                    WHERE idcurso = :idcurso";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nombrecurso' => $curso->getNombrecurso(),
                ':descripcion' => $curso->getDescripcion(),
                ':cupostotales' => $curso->getCupostotales(),
                ':costoinscripcion' => $curso->getCostoinscripcion(),
                ':fechainicio' => $curso->getFechainicio(),
                ':fechafin' => $curso->getFechafin(),
                ':urlenlacevirtual' => $curso->getUrlenlacevirtual(),
                ':estado' => $curso->getEstado(),
                ':idponente' => $curso->getIdponente(),
                ':idcurso' => $curso->getIdcurso()
            ]);
        } catch (PDOException $e) {
            error_log("Error en updateCurso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todos los cursos
     */
    public function listarCursos($filtros = []) {
        try {
            $sql = "SELECT c.*, p.nombrecompleto as nombreponente,
                    (SELECT COUNT(*) FROM cursoinscrito ci WHERE ci.idcurso = c.idcurso) as inscritos
                    FROM curso c
                    LEFT JOIN ponente p ON c.idponente = p.idponente
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($filtros['estado'])) {
                $sql .= " AND c.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (!empty($filtros['busqueda'])) {
                $sql .= " AND c.nombrecurso LIKE :busqueda";
                $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
            }
            
            $sql .= " ORDER BY c.fechainicio DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en listarCursos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener cupos disponibles de un curso
     */
    public function getCuposDisponibles($idcurso) {
        try {
            $sql = "SELECT c.cupostotales - COUNT(ci.idsocio) as disponibles
                    FROM curso c
                    LEFT JOIN cursoinscrito ci ON c.idcurso = ci.idcurso
                    WHERE c.idcurso = :idcurso
                    GROUP BY c.idcurso, c.cupostotales";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idcurso' => $idcurso]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['disponibles'] : 0;
        } catch (PDOException $e) {
            error_log("Error en getCuposDisponibles: " . $e->getMessage());
            return 0;
        }
    }
}
