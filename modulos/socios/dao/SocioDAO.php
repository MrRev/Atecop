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
            // Debug: Imprimir información del socio
            error_log("Intentando crear socio con DNI: " . $socio->getDni());
            
            // Validaciones adicionales
            if (empty($socio->getDni()) || empty($socio->getNombrecompleto())) {
                throw new Exception("DNI y nombre completo son obligatorios");
            }

            if (empty($socio->getIdtiposocio()) || empty($socio->getIdplan())) {
                throw new Exception("Tipo de socio y plan son obligatorios");
            }

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
            
            $result = $stmt->execute();
            
            if (!$result) {
                $error = $stmt->errorInfo();
                error_log("Error SQL en SocioDAO::create - " . json_encode($error));
                echo "<script>console.error('Error SQL:', " . json_encode($error) . ");</script>";
                throw new Exception("Error al crear el socio: " . ($error[2] ?? "Error desconocido"));
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::create - " . $e->getMessage());
            echo "<script>console.error('Error PDO:', " . json_encode($e->getMessage()) . ");</script>";
            throw new Exception("Error al crear el socio: " . $e->getMessage());
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
     * Listar socios con filtros opcionales
     */
    public function listarFiltrado(?string $buscar, ?string $estado): array {
        try {
            $sql = "SELECT s.*, ts.nombretipo, pm.nombreplan, p.nombreprofesion 
                    FROM socio s
                    LEFT JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                    LEFT JOIN planmembresia pm ON s.idplan = pm.idplan
                    LEFT JOIN profesion p ON s.idprofesion = p.idprofesion";

            $params = [];
            $whereClauses = [];

            // ✅ Filtro por nombre o DNI (usar nombres distintos para los parámetros)
            if (!empty($buscar)) {
                $whereClauses[] = "(s.dni LIKE :buscarDni OR s.nombrecompleto LIKE :buscarNombre)";
                $params[':buscarDni'] = '%' . $buscar . '%';
                $params[':buscarNombre'] = '%' . $buscar . '%';
            }

            // ✅ Filtro por estado
            if (!empty($estado)) {
                $whereClauses[] = "s.estado = :estado";
                $params[':estado'] = $estado;
            }

            // ✅ Solo añadimos WHERE si hay al menos un filtro
            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);
            }

            $sql .= " ORDER BY s.nombrecompleto ASC";

            $stmt = $this->conexion->prepare($sql);

            // ✅ Vinculamos solo si hay parámetros
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();

            $socios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $socios[] = $this->mapRowToSocio($row);
            }

            error_log("✅ listarFiltrado ejecutado correctamente. Resultados: " . count($socios));
            return $socios;

        } catch (PDOException $e) {
            error_log("❌ Error en SocioDAO::listarFiltrado - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar socios por DNI o nombre (para AJAX)
     */
    public function findSocios(string $criterio): array {
        try {
            error_log("=== DEBUG findSocios ===");
            error_log("Criterio recibido: " . $criterio);

            $sql = "SELECT s.*, ts.nombretipo, pm.nombreplan, p.nombreprofesion 
                    FROM socio s
                    LEFT JOIN tiposocio ts ON s.idtiposocio = ts.idtiposocio
                    LEFT JOIN planmembresia pm ON s.idplan = pm.idplan
                    LEFT JOIN profesion p ON s.idprofesion = p.idprofesion
                    WHERE s.dni LIKE :dni OR s.nombrecompleto LIKE :nombre
                    ORDER BY s.nombrecompleto ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':dni', '%' . $criterio . '%', PDO::PARAM_STR);
            $stmt->bindValue(':nombre', '%' . $criterio . '%', PDO::PARAM_STR);
            $stmt->execute();

            $socios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $socios[] = $this->mapRowToSocio($row);
            }

            error_log("findSocios devolvió " . count($socios) . " resultados.");

            return $socios;

        } catch (PDOException $e) {
            error_log("❌ Error en SocioDAO::findSocios - " . $e->getMessage());
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
     * Reactiva a un socio (cambia su estado a Activo)
     */
    public function reactivar(int $idsocio): bool {
        try {
            // Cambiamos el estado de 'Inactivo' a 'Activo'
            $sql = "UPDATE socio SET estado = 'Activo' WHERE idsocio = :idsocio";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idsocio', $idsocio, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::reactivar - " . $e->getMessage());
            return false;
        }
    }
    /**
     * Mapear fila de BD a objeto Socio
     */
    /**
     * Actualizar la fecha de vencimiento y estado de un socio
     */
    public function updateVencimiento(int $idsocio, string $fechaVencimiento, string $estado): bool {
        try {
            $sql = "UPDATE socio SET 
                    fechavencimiento = :fechavencimiento,
                    estado = :estado 
                    WHERE idsocio = :idsocio";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idsocio', $idsocio, PDO::PARAM_INT);
            $stmt->bindValue(':fechavencimiento', $fechaVencimiento);
            $stmt->bindValue(':estado', $estado);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en SocioDAO::updateVencimiento - " . $e->getMessage());
            return false;
        }
    }

    private function mapRowToSocio($row): Socio {
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
