<?php
require_once __DIR__ . '/../modelo/Usuario.php';
require_once __DIR__ . '/../../../util_global/Database.php';

/**
 * Clase UsuarioDAO
 * 
 * Data Access Object para la tabla usuario.
 */
class UsuarioDAO {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Crear un nuevo usuario
     */
    public function create(Usuario $usuario) {
        try {
            // Validación de campos requeridos
            if (empty($usuario->getDni())) {
                throw new Exception("El DNI es requerido");
            }
            if (empty($usuario->getNombrecompleto())) {
                throw new Exception("El nombre completo es requerido");
            }
            if (empty($usuario->getNombreusuario())) {
                throw new Exception("El nombre de usuario es requerido");
            }
            if (empty($usuario->getClavehash())) {
                throw new Exception("La contraseña es requerida");
            }

            $sql = "INSERT INTO usuario (dni, nombrecompleto, nombreusuario, email, telefono, 
                    clavehash, direccion, rol, idsocio, estado) 
                    VALUES (:dni, :nombrecompleto, :nombreusuario, :email, :telefono,
                    :clavehash, :direccion, :rol, :idsocio, :estado)";
            
            $stmt = $this->db->prepare($sql);
            
            $params = [
                ':dni' => $usuario->getDni(),
                ':nombrecompleto' => $usuario->getNombrecompleto(),
                ':nombreusuario' => $usuario->getNombreusuario(),
                ':email' => $usuario->getEmail() ?: null,
                ':telefono' => $usuario->getTelefono() ?: null,
                ':clavehash' => $usuario->getClavehash(),
                ':direccion' => $usuario->getDireccion() ?: null,
                ':rol' => $usuario->getRol() ?: 'administrador',
                ':idsocio' => $usuario->getIdsocio() ?: null,
                ':estado' => $usuario->getEstado() ?: 'Activo'
            ];

            error_log("DEBUG - UsuarioDAO::create - Params: " . json_encode($params));
            // Also write to debug log for easier debugging
            $logfile = __DIR__ . '/../../../logs/debug_usuario.log';
            $logmsg = '[' . date('Y-m-d H:i:s') . '] UsuarioDAO::create - Params: ' . json_encode($params) . PHP_EOL;
            @file_put_contents($logfile, $logmsg, FILE_APPEND);

            if (!$stmt->execute($params)) {
                $error = $stmt->errorInfo();
                $errMsg = "ERROR - UsuarioDAO::create - SQL Error: " . json_encode($error);
                error_log($errMsg);
                @file_put_contents($logfile, '[' . date('Y-m-d H:i:s') . '] ' . $errMsg . PHP_EOL, FILE_APPEND);
                throw new Exception("Error al ejecutar la consulta: " . ($error[2] ?? 'Error desconocido'));
            }

            @file_put_contents($logfile, '[' . date('Y-m-d H:i:s') . '] UsuarioDAO::create - Éxito' . PHP_EOL, FILE_APPEND);
            return true;
            
        } catch (PDOException $e) {
            $msg = "Error en UsuarioDAO::create - " . $e->getMessage();
            error_log($msg);
            // Escribir también en el log de debug para traza completa
            $logfile = __DIR__ . '/../../../logs/debug_usuario.log';
            @file_put_contents($logfile, '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL, FILE_APPEND);
            throw new Exception($this->getMensajeError($e));
        }
    }
    
    /**
     * Leer un usuario por ID
     */
    public function read($idusuario) {
        try {
            $sql = "SELECT u.*, s.nombrecompleto as nombre_socio 
                    FROM usuario u 
                    LEFT JOIN socio s ON u.idsocio = s.idsocio 
                    WHERE u.idusuario = :idusuario";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idusuario' => $idusuario]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;
            
            $usuario = new Usuario();
            $this->mapRowToUsuario($row, $usuario);
            return $usuario;
            
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::read - " . $e->getMessage());
            throw new Exception("Error al leer usuario");
        }
    }
    
    /**
     * Actualizar un usuario
     * No actualiza DNI ni nombrecompleto (son inmutables una vez validados)
     */
    public function update(Usuario $usuario) {
        try {
            $sql = "UPDATE usuario SET 
                    nombreusuario = :nombreusuario,
                    email = :email,
                    telefono = :telefono,
                    direccion = :direccion,
                    rol = :rol,
                    idsocio = :idsocio,
                    estado = :estado
                    WHERE idusuario = :idusuario";
            
            if ($usuario->getClavehash()) {
                $sql = "UPDATE usuario SET 
                        nombreusuario = :nombreusuario,
                        email = :email,
                        telefono = :telefono,
                        clavehash = :clavehash,
                        direccion = :direccion,
                        rol = :rol,
                        idsocio = :idsocio,
                        estado = :estado
                        WHERE idusuario = :idusuario";
            }
            
            $stmt = $this->db->prepare($sql);
            
            $params = [
                ':idusuario' => $usuario->getIdusuario(),
                ':nombreusuario' => $usuario->getNombreusuario(),
                ':email' => $usuario->getEmail(),
                ':telefono' => $usuario->getTelefono(),
                ':direccion' => $usuario->getDireccion(),
                ':rol' => $usuario->getRol(),
                ':idsocio' => $usuario->getIdsocio(),
                ':estado' => $usuario->getEstado()
            ];
            
            if ($usuario->getClavehash()) {
                $params[':clavehash'] = $usuario->getClavehash();
            }
            
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::update - " . $e->getMessage());
            throw new Exception($this->getMensajeError($e));
        }
    }

    /**
     * Buscar usuarios por término (DNI o nombre)
     */
    public function findUsuarios($termino) {
        try {
            $sql = "SELECT u.*, s.nombrecompleto as nombre_socio 
                    FROM usuario u 
                    LEFT JOIN socio s ON u.idsocio = s.idsocio 
                    WHERE u.dni LIKE :termino 
                    OR u.nombrecompleto LIKE :terminolike 
                    OR u.nombreusuario LIKE :terminolike 
                    ORDER BY u.nombrecompleto";
            
            $stmt = $this->db->prepare($sql);
            $terminolike = "%{$termino}%";
            $stmt->execute([
                ':termino' => $termino,
                ':terminolike' => $terminolike
            ]);
            
            $usuarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $usuario = new Usuario();
                $this->mapRowToUsuario($row, $usuario);
                $usuarios[] = $usuario;
            }
            
            return $usuarios;
            
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::findUsuarios - " . $e->getMessage());
            throw new Exception("Error al buscar usuarios");
        }
    }
    
    /**
     * Listar usuarios con filtros opcionales
     */
    public function listarFiltrado($buscar = null, $estado = null) {
        try {
            $sql = "SELECT u.*, s.nombrecompleto as nombre_socio 
                    FROM usuario u 
                    LEFT JOIN socio s ON u.idsocio = s.idsocio 
                    WHERE 1=1";
            $params = [];
            
            if ($buscar) {
                $sql .= " AND (u.dni LIKE :buscar 
                        OR u.nombrecompleto LIKE :buscarlike 
                        OR u.nombreusuario LIKE :buscarlike)";
                $params[':buscar'] = $buscar;
                $params[':buscarlike'] = "%{$buscar}%";
            }
            
            if ($estado) {
                $sql .= " AND u.estado = :estado";
                $params[':estado'] = $estado;
            }
            
            $sql .= " ORDER BY u.nombrecompleto";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $usuarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $usuario = new Usuario();
                $this->mapRowToUsuario($row, $usuario);
                $usuarios[] = $usuario;
            }
            
            return $usuarios;
            
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::listarFiltrado - " . $e->getMessage());
            throw new Exception("Error al listar usuarios");
        }
    }
    
    /**
     * Buscar usuario por nombre de usuario (para login)
     */
    public function findByUsuario($nombreusuario) {
        try {
            $sql = "SELECT u.*, s.nombrecompleto as nombre_socio 
                    FROM usuario u 
                    LEFT JOIN socio s ON u.idsocio = s.idsocio 
                    WHERE u.nombreusuario = :nombreusuario 
                    AND u.estado = 'Activo'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':nombreusuario' => $nombreusuario]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;
            
            $usuario = new Usuario();
            $this->mapRowToUsuario($row, $usuario);
            return $usuario;
            
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::findByUsuario - " . $e->getMessage());
            throw new Exception("Error al buscar usuario");
        }
    }
    
    /**
     * Buscar usuario por DNI
     */
    public function findByDNI($dni) {
        try {
            $sql = "SELECT u.*, s.nombrecompleto as nombre_socio 
                    FROM usuario u 
                    LEFT JOIN socio s ON u.idsocio = s.idsocio 
                    WHERE u.dni = :dni";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':dni' => $dni]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;
            
            $usuario = new Usuario();
            $this->mapRowToUsuario($row, $usuario);
            return $usuario;
            
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::findByDNI - " . $e->getMessage());
            throw new Exception("Error al buscar usuario por DNI");
        }
    }
    
    /**
     * Verificar si existe un DNI (excluyendo un ID opcional)
     */
    public function existeDNI($dni, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM usuario WHERE dni = :dni";
            $params = [':dni' => $dni];
            
            if ($excludeId) {
                $sql .= " AND idusuario != :idusuario";
                $params[':idusuario'] = $excludeId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::existeDNI - " . $e->getMessage());
            throw new Exception("Error al verificar DNI");
        }
    }
    
    /**
     * Verificar si existe un nombre de usuario (excluyendo un ID opcional)
     */
    public function existeNombreUsuario($nombreusuario, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM usuario WHERE nombreusuario = :nombreusuario";
            $params = [':nombreusuario' => $nombreusuario];
            
            if ($excludeId) {
                $sql .= " AND idusuario != :idusuario";
                $params[':idusuario'] = $excludeId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::existeNombreUsuario - " . $e->getMessage());
            throw new Exception("Error al verificar nombre de usuario");
        }
    }

    /**
     * Mapear una fila de la BD a un objeto Usuario
     */
    private function mapRowToUsuario($row, Usuario $usuario) {
        $usuario->setIdusuario($row['idusuario']);
        $usuario->setDni($row['dni']);
        $usuario->setNombrecompleto($row['nombrecompleto']);
        $usuario->setNombreusuario($row['nombreusuario']);
        $usuario->setEmail($row['email']);
        $usuario->setTelefono($row['telefono']);
        // No mapear clavehash por seguridad
        if (isset($row['clavehash'])) {
            $usuario->setClavehash($row['clavehash']);
        }
        $usuario->setDireccion($row['direccion']);
        $usuario->setRol($row['rol']);
        $usuario->setIdsocio($row['idsocio']);
        $usuario->setEstado($row['estado']);
        // Extras
        if (isset($row['nombre_socio'])) {
            $usuario->setNombreSocio($row['nombre_socio']);
        }
    }
    
    /**
     * Obtener mensaje de error amigable para excepciones de BD
     */
    private function getMensajeError(PDOException $e) {
        if (strpos($e->getMessage(), 'usuario.dni') !== false) {
            return "Ya existe un usuario con ese DNI";
        }
        if (strpos($e->getMessage(), 'usuario.nombreusuario') !== false) {
            return "Ya existe un usuario con ese nombre de usuario";
        }
        if (strpos($e->getMessage(), 'usuario.email') !== false) {
            return "Ya existe un usuario con ese email";
        }
        return "Error al procesar la operación";
    }
}