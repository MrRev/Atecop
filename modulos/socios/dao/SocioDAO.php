<?php

require_once __DIR__ . '/../../../util_global/Database.php';
require_once __DIR__ . '/../modelo/Socio.php';

/**
 * Clase DAO: SocioDAO
 * Maneja todas las operaciones de base de datos para la entidad Socio
 */
class SocioDAO {
    private PDO $conexion;

    public function __construct() {
        $this->conexion = Database::getInstance()->getConnection();
    }

    /**
     * Crear un nuevo socio
     */
    public function create(Socio $socio): bool {
        try {
            $sql = "INSERT INTO socio (dni, nombrecompleto, fechanacimiento, direccion, email, 
                    telefono, numcuentabancaria, estado, fechavencimiento, idtiposocio, idplan, idprofesion) 
                    VALUES (:dni, :nombrecompleto, :fechanacimiento, :direccion, :email, 
                    :telefono, :numcuentabancaria, :estado, :fechavencimiento, :idtiposocio, :idplan, :idprofesion)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':dni', $socio->getDni());
            $stmt->bindValue(':nombrecompleto', $socio->getNombrecompleto());
            $stmt->bindValue(':fechanacimiento', $socio->getFechanacimiento());
            $stmt->bindValue(':direccion', $socio->getDireccion());
            $stmt->bindValue(':email', $socio->getEmail());
            $stmt->bindValue(':telefono', $socio->getTelefono());
            $stmt->bindValue(':numcuentabancaria', $socio->getNumcuentabancaria());
            $stmt->bindValue(':estado', $socio->getEstado());
            $stmt->bindValue(':fechavencimiento', $socio->getFechavencimiento());
            $stmt->bindValue(':idtiposocio', $socio->getIdtiposocio());
            $stmt->bindValue(':idplan', $socio->getIdplan());
            $stmt->bindValue(':idprofesion', $socio->getIdprofesion(), PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::create - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Leer un socio por ID
     */
    public function read(int $idsocio): ?Socio {
        try {
            $sql = "SELECT s.*, ts.nombretipo, pm.nombreplan, p.nombreprofesion 
                    FROM socio s
                    LEFT JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                    LEFT JOIN planmembresia pm ON s.idplan = pm.idplan
                    LEFT JOIN profesion p ON s.idprofesion = p.idprofesion
                    WHERE s.idsocio = :idsocio";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idsocio', $idsocio, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                return $this->mapRowToSocio($row);
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::read - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar un socio existente
     */
    public function update(Socio $socio): bool {
        try {
            $sql = "UPDATE socio SET 
                    dni = :dni, 
                    nombrecompleto = :nombrecompleto, 
                    fechanacimiento = :fechanacimiento, 
                    direccion = :direccion, 
                    email = :email, 
                    telefono = :telefono, 
                    numcuentabancaria = :numcuentabancaria, 
                    estado = :estado, 
                    fechavencimiento = :fechavencimiento, 
                    idtiposocio = :idtiposocio, 
                    idplan = :idplan, 
                    idprofesion = :idprofesion
                    WHERE idsocio = :idsocio";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':dni', $socio->getDni());
            $stmt->bindValue(':nombrecompleto', $socio->getNombrecompleto());
            $stmt->bindValue(':fechanacimiento', $socio->getFechanacimiento());
            $stmt->bindValue(':direccion', $socio->getDireccion());
            $stmt->bindValue(':email', $socio->getEmail());
            $stmt->bindValue(':telefono', $socio->getTelefono());
            $stmt->bindValue(':numcuentabancaria', $socio->getNumcuentabancaria());
            $stmt->bindValue(':estado', $socio->getEstado());
            $stmt->bindValue(':fechavencimiento', $socio->getFechavencimiento());
            $stmt->bindValue(':idtiposocio', $socio->getIdtiposocio());
            $stmt->bindValue(':idplan', $socio->getIdplan());
            $stmt->bindValue(':idprofesion', $socio->getIdprofesion(), PDO::PARAM_INT);
            $stmt->bindValue(':idsocio', $socio->getIdsocio(), PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::update - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todos los socios con información relacionada
     */
    public function listAll(): array {
        try {
            $sql = "SELECT s.*, ts.nombretipo, pm.nombreplan, p.nombreprofesion 
                    FROM socio s
                    LEFT JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                    LEFT JOIN planmembresia pm ON s.idplan = pm.idplan
                    LEFT JOIN profesion p ON s.idprofesion = p.idprofesion
                    ORDER BY s.nombrecompleto ASC";
            
            $stmt = $this->conexion->query($sql);
            $socios = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $socios[] = $this->mapRowToSocio($row);
            }
            
            return $socios;
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::listAll - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar socios por criterios (DNI o nombre)
     */
    public function findSocios(string $criterio): array {
        try {
            $sql = "SELECT s.*, ts.nombretipo, pm.nombreplan, p.nombreprofesion 
                    FROM socio s
                    LEFT JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                    LEFT JOIN planmembresia pm ON s.idplan = pm.idplan
                    LEFT JOIN profesion p ON s.idprofesion = p.idprofesion
                    WHERE s.dni LIKE :criterio OR s.nombrecompleto LIKE :criterio
                    ORDER BY s.nombrecompleto ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':criterio', '%' . $criterio . '%');
            $stmt->execute();
            
            $socios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $socios[] = $this->mapRowToSocio($row);
            }
            
            return $socios;
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::findSocios - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si un DNI ya existe
     */
    public function existeDni(string $dni, ?int $excludeId = null): bool {
        try {
            $sql = "SELECT COUNT(*) FROM socio WHERE dni = :dni";
            if ($excludeId !== null) {
                $sql .= " AND idsocio != :excludeId";
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':dni', $dni);
            if ($excludeId !== null) {
                $stmt->bindValue(':excludeId', $excludeId, PDO::PARAM_INT);
            }
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::existeDni - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar plan y fecha de vencimiento de un socio
     */
    public function updatePlanSocio(int $idsocio, int $idplan, string $fechavencimiento): bool {
        try {
            $sql = "UPDATE socio SET idplan = :idplan, fechavencimiento = :fechavencimiento 
                    WHERE idsocio = :idsocio";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idplan', $idplan, PDO::PARAM_INT);
            $stmt->bindValue(':fechavencimiento', $fechavencimiento);
            $stmt->bindValue(':idsocio', $idsocio, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::updatePlanSocio - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar vencimiento y estado de un socio
     */
    public function updateVencimientoEstadoSocio(int $idsocio, string $fechavencimiento, string $estado): bool {
        try {
            $sql = "UPDATE socio SET fechavencimiento = :fechavencimiento, estado = :estado 
                    WHERE idsocio = :idsocio";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':fechavencimiento', $fechavencimiento);
            $stmt->bindValue(':estado', $estado);
            $stmt->bindValue(':idsocio', $idsocio, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::updateVencimientoEstadoSocio - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Dar de baja a un socio (cambiar estado a Inactivo)
     */
    public function darDeBaja(int $idsocio): bool {
        try {
            $sql = "UPDATE socio SET estado = 'Inactivo' WHERE idsocio = :idsocio";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idsocio', $idsocio, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::darDeBaja - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mapear fila de BD a objeto Socio
     */
    private function mapRowToSocio(array $row): Socio {
        $socio = new Socio();
        $socio->setIdsocio($row['idsocio']);
        $socio->setDni($row['dni']);
        $socio->setNombrecompleto($row['nombrecompleto']);
        $socio->setFechanacimiento($row['fechanacimiento']);
        $socio->setDireccion($row['direccion']);
        $socio->setEmail($row['email']);
        $socio->setTelefono($row['telefono']);
        $socio->setNumcuentabancaria($row['numcuentabancaria']);
        $socio->setEstado($row['estado']);
        $socio->setFechavencimiento($row['fechavencimiento']);
        $socio->setFechacreacion($row['fechacreacion']);
        $socio->setIdtiposocio($row['idtiposocio']);
        $socio->setIdplan($row['idplan']);
        $socio->setIdprofesion($row['idprofesion']);
        
        // Datos de joins
        if (isset($row['nombretipo'])) {
            $socio->setNombretipo($row['nombretipo']);
        }
        if (isset($row['nombreplan'])) {
            $socio->setNombreplan($row['nombreplan']);
        }
        if (isset($row['nombreprofesion'])) {
            $socio->setNombreprofesion($row['nombreprofesion']);
        }
        
        return $socio;
    }
}
