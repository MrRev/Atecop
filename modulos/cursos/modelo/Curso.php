<?php

/**
 * Clase Curso
 * Modelo/Entidad que representa un curso
 */
class Curso {
    private $idcurso;
    private $nombrecurso;
    private $descripcion;
    private $cupostotales;
    private $costoinscripcion;
    private $fechainicio;
    private $fechafin;
    private $urlenlacevirtual;
    private $estado;
    private $idponente;

    // Getters y Setters
    public function getIdcurso() {
        return $this->idcurso;
    }

    public function setIdcurso($idcurso) {
        $this->idcurso = $idcurso;
    }

    public function getNombrecurso() {
        return $this->nombrecurso;
    }

    public function setNombrecurso($nombrecurso) {
        $this->nombrecurso = $nombrecurso;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function getCupostotales() {
        return $this->cupostotales;
    }

    public function setCupostotales($cupostotales) {
        $this->cupostotales = $cupostotales;
    }

    public function getCostoinscripcion() {
        return $this->costoinscripcion;
    }

    public function setCostoinscripcion($costoinscripcion) {
        $this->costoinscripcion = $costoinscripcion;
    }

    public function getFechainicio() {
        return $this->fechainicio;
    }

    public function setFechainicio($fechainicio) {
        $this->fechainicio = $fechainicio;
    }

    public function getFechafin() {
        return $this->fechafin;
    }

    public function setFechafin($fechafin) {
        $this->fechafin = $fechafin;
    }

    public function getUrlenlacevirtual() {
        return $this->urlenlacevirtual;
    }

    public function setUrlenlacevirtual($urlenlacevirtual) {
        $this->urlenlacevirtual = $urlenlacevirtual;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getIdponente() {
        return $this->idponente;
    }

    public function setIdponente($idponente) {
        $this->idponente = $idponente;
    }
}
