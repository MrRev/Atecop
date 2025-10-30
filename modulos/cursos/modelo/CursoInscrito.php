<?php

/**
 * Clase CursoInscrito
 * Modelo/Entidad que representa la inscripción de un socio a un curso
 */
class CursoInscrito {
    private $idsocio;
    private $idcurso;
    private $fechainscripcion;
    private $estadopagocurso;

    // Getters y Setters
    public function getIdsocio() {
        return $this->idsocio;
    }

    public function setIdsocio($idsocio) {
        $this->idsocio = $idsocio;
    }

    public function getIdcurso() {
        return $this->idcurso;
    }

    public function setIdcurso($idcurso) {
        $this->idcurso = $idcurso;
    }

    public function getFechainscripcion() {
        return $this->fechainscripcion;
    }

    public function setFechainscripcion($fechainscripcion) {
        $this->fechainscripcion = $fechainscripcion;
    }

    public function getEstadopagocurso() {
        return $this->estadopagocurso;
    }

    public function setEstadopagocurso($estadopagocurso) {
        $this->estadopagocurso = $estadopagocurso;
    }
}
