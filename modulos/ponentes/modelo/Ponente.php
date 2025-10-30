<?php

/**
 * Clase Ponente
 * Modelo/Entidad que representa un ponente (tutor de cursos)
 */
class Ponente {
    private $idponente;
    private $nombrecompleto;
    private $dni;
    private $email;
    private $telefono;
    private $estado;
    private $idprofesion;

    // Getters y Setters
    public function getIdponente() {
        return $this->idponente;
    }

    public function setIdponente($idponente) {
        $this->idponente = $idponente;
    }

    public function getNombrecompleto() {
        return $this->nombrecompleto;
    }

    public function setNombrecompleto($nombrecompleto) {
        $this->nombrecompleto = $nombrecompleto;
    }

    public function getDni() {
        return $this->dni;
    }

    public function setDni($dni) {
        $this->dni = $dni;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getIdprofesion() {
        return $this->idprofesion;
    }

    public function setIdprofesion($idprofesion) {
        $this->idprofesion = $idprofesion;
    }
}
