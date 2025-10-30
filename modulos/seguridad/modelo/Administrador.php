<?php
/**
 * Clase Modelo: Administrador
 * 
 * Representa la entidad Administrador del sistema.
 */

class Administrador {
    private $idadmin;
    private $usuario;
    private $clavehash;
    private $nombrecompleto;
    private $estado;
    
    // Constructor
    public function __construct($idadmin = null, $usuario = null, $clavehash = null, 
                                $nombrecompleto = null, $estado = 'Activo') {
        $this->idadmin = $idadmin;
        $this->usuario = $usuario;
        $this->clavehash = $clavehash;
        $this->nombrecompleto = $nombrecompleto;
        $this->estado = $estado;
    }
    
    // Getters
    public function getIdAdmin() { return $this->idadmin; }
    public function getUsuario() { return $this->usuario; }
    public function getClaveHash() { return $this->clavehash; }
    public function getNombreCompleto() { return $this->nombrecompleto; }
    public function getEstado() { return $this->estado; }
    
    // Setters
    public function setIdAdmin($idadmin) { $this->idadmin = $idadmin; }
    public function setUsuario($usuario) { $this->usuario = $usuario; }
    public function setClaveHash($clavehash) { $this->clavehash = $clavehash; }
    public function setNombreCompleto($nombrecompleto) { $this->nombrecompleto = $nombrecompleto; }
    public function setEstado($estado) { $this->estado = $estado; }
    
    /**
     * Verifica si la contraseña proporcionada coincide con el hash almacenado
     * 
     * @param string $password Contraseña en texto plano
     * @return bool True si coincide
     */
    public function verificarPassword($password) {
        return password_verify($password, $this->clavehash);
    }
    
    /**
     * Genera un hash de contraseña
     * 
     * @param string $password Contraseña en texto plano
     * @return string Hash de la contraseña
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_HASH_ALGO);
    }
}
