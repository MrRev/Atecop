<?php
/**
 * Clase Usuario
 * 
 * Representa un usuario del sistema con validación de DNI y roles.
 */
class Usuario {
    private $idusuario;
    private $dni;
    private $nombrecompleto;
    private $nombreusuario;
    private $email;
    private $telefono;
    private $clavehash;
    private $direccion;
    private $rol;
    private $idsocio;
    private $estado;
    private $fechacreacion;
    private $fechamodificacion;
    private $nombre_socio;

    // Getters
    public function getIdusuario() { return $this->idusuario; }
    public function getDni() { return $this->dni; }
    public function getNombrecompleto() { return $this->nombrecompleto; }
    public function getNombreSocio() { return $this->nombre_socio; }
    public function getNombreusuario() { return $this->nombreusuario; }
    public function getEmail() { return $this->email; }
    public function getTelefono() { return $this->telefono; }
    public function getClavehash() { return $this->clavehash; }
    public function getDireccion() { return $this->direccion; }
    public function getRol() { return $this->rol; }
    public function getIdsocio() { return $this->idsocio; }
    public function getEstado() { return $this->estado; }
    public function getFechacreacion() { return $this->fechacreacion; }
    public function getFechamodificacion() { return $this->fechamodificacion; }

    // Setters con validaciones
    public function setIdusuario($idusuario) { 
        $this->idusuario = (int)$idusuario; 
    }

    public function setDni($dni) {
        if (!preg_match('/^\d{8}$/', $dni)) {
            throw new Exception('DNI debe tener 8 dígitos');
        }
        $this->dni = $dni;
    }

    public function setNombrecompleto($nombrecompleto) {
        if (empty(trim($nombrecompleto))) {
            throw new Exception('El nombre completo no puede estar vacío');
        }
        $this->nombrecompleto = trim($nombrecompleto);
    }

    public function setNombreusuario($nombreusuario) {
        $nombreusuario = trim($nombreusuario);
        if (empty($nombreusuario)) {
            throw new Exception('El nombre de usuario no puede estar vacío');
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $nombreusuario)) {
            throw new Exception('El nombre de usuario solo puede contener letras, números y guiones bajos');
        }
        $this->nombreusuario = $nombreusuario;
    }

    public function setEmail($email) {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }
        $this->email = $email;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setClavehash($clavehash) {
        if (empty($clavehash)) {
            throw new Exception('La contraseña es requerida');
        }
        $this->clavehash = $clavehash;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    public function setRol($rol) {
        $roles_validos = ['administrador']; // Preparado para más roles en el futuro
        if (!in_array($rol, $roles_validos)) {
            throw new Exception('Rol inválido');
        }
        $this->rol = $rol;
    }

    public function setIdsocio($idsocio) {
        $this->idsocio = $idsocio ? (int)$idsocio : null;
    }

    public function setEstado($estado) {
        $estados_validos = ['Activo', 'Inactivo'];
        if (!in_array($estado, $estados_validos)) {
            throw new Exception('Estado inválido');
        }
        $this->estado = $estado;
    }

    // Método para verificar contraseña
    public function verificarPassword($password) {
        return password_verify($password, $this->clavehash);
    }

    // Método para generar nombre de usuario a partir del nombre completo
    public function generarNombreUsuario() {
        $nombres = explode(' ', $this->nombrecompleto);
        if (count($nombres) >= 2) {
            $primer_nombre = $nombres[0];
            $primer_apellido = $nombres[count($nombres) - 1];
            return strtolower($primer_nombre . '_' . $primer_apellido);
        }
        throw new Exception('El nombre completo debe tener al menos nombre y apellido');
    }

    // Setter para nombre_socio
    public function setNombreSocio($nombre_socio) {
        $this->nombre_socio = $nombre_socio;
    }
}