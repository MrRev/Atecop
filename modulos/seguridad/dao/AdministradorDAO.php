<?php
/**
 * Clase DAO: AdministradorDAO
 * 
 * Maneja el acceso a datos de la tabla administrador.
 */

class AdministradorDAO {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Busca un administrador por nombre de usuario
     * 
     * @param string $usuario Nombre de usuario
     * @return Administrador|null Objeto Administrador o null si no existe
     */
    public function findByUsuario($usuario) {
        try {
            $sql = "SELECT * FROM administrador WHERE usuario = :usuario AND estado = 'Activo'";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->execute();
            
            $row = $stmt->fetch();
            
            if ($row) {
                return new Administrador(
                    $row['idadmin'],
                    $row['usuario'],
                    $row['clavehash'],
                    $row['nombrecompleto'],
                    $row['estado']
                );
            }
            
            return null;
            
        } catch (PDOException $e) {
            error_log("Error en AdministradorDAO::findByUsuario - " . $e->getMessage());
            throw new Exception("Error al buscar administrador");
        }
    }
    
    /**
     * Obtiene un administrador por ID
     * 
     * @param int $idadmin ID del administrador
     * @return Administrador|null
     */
    public function findById($idadmin) {
        try {
            $sql = "SELECT * FROM administrador WHERE idadmin = :idadmin";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idadmin', $idadmin, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch();
            
            if ($row) {
                return new Administrador(
                    $row['idadmin'],
                    $row['usuario'],
                    $row['clavehash'],
                    $row['nombrecompleto'],
                    $row['estado']
                );
            }
            
            return null;
            
        } catch (PDOException $e) {
            error_log("Error en AdministradorDAO::findById - " . $e->getMessage());
            throw new Exception("Error al buscar administrador");
        }
    }
}
