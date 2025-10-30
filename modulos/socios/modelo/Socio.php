<?php

/**
 * Clase Modelo: Socio
 * Representa la entidad Socio del sistema
 */
class Socio {
    private ?int $idsocio;
    private string $dni;
    private string $nombrecompleto;
    private ?string $fechanacimiento;
    private ?string $direccion;
    private ?string $email;
    private ?string $telefono;
    private ?string $numcuentabancaria;
    private string $estado;
    private string $fechavencimiento;
    private string $fechacreacion;
    private int $idtiposocio;
    private int $idplan;
    private ?int $idprofesion;

    // Propiedades adicionales para joins
    private ?string $nombretipo;
    private ?string $nombreplan;
    private ?string $nombreprofesion;

    public function __construct() {
        $this->idsocio = null;
        $this->estado = 'Activo';
        $this->fechacreacion = date('Y-m-d H:i:s');
    }

    // Getters y Setters
    public function getIdsocio(): ?int {
        return $this->idsocio;
    }

    public function setIdsocio(?int $idsocio): void {
        $this->idsocio = $idsocio;
    }

    public function getDni(): string {
        return $this->dni;
    }

    public function setDni(string $dni): void {
        $this->dni = $dni;
    }

    public function getNombrecompleto(): string {
        return $this->nombrecompleto;
    }

    public function setNombrecompleto(string $nombrecompleto): void {
        $this->nombrecompleto = $nombrecompleto;
    }

    public function getFechanacimiento(): ?string {
        return $this->fechanacimiento;
    }

    public function setFechanacimiento(?string $fechanacimiento): void {
        $this->fechanacimiento = $fechanacimiento;
    }

    public function getDireccion(): ?string {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): void {
        $this->direccion = $direccion;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function getTelefono(): ?string {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): void {
        $this->telefono = $telefono;
    }

    public function getNumcuentabancaria(): ?string {
        return $this->numcuentabancaria;
    }

    public function setNumcuentabancaria(?string $numcuentabancaria): void {
        $this->numcuentabancaria = $numcuentabancaria;
    }

    public function getEstado(): string {
        return $this->estado;
    }

    public function setEstado(string $estado): void {
        $this->estado = $estado;
    }

    public function getFechavencimiento(): string {
        return $this->fechavencimiento;
    }

    public function setFechavencimiento(string $fechavencimiento): void {
        $this->fechavencimiento = $fechavencimiento;
    }

    public function getFechacreacion(): string {
        return $this->fechacreacion;
    }

    public function setFechacreacion(string $fechacreacion): void {
        $this->fechacreacion = $fechacreacion;
    }

    public function getIdtiposocio(): int {
        return $this->idtiposocio;
    }

    public function setIdtiposocio(int $idtiposocio): void {
        $this->idtiposocio = $idtiposocio;
    }

    public function getIdplan(): int {
        return $this->idplan;
    }

    public function setIdplan(int $idplan): void {
        $this->idplan = $idplan;
    }

    public function getIdprofesion(): ?int {
        return $this->idprofesion;
    }

    public function setIdprofesion(?int $idprofesion): void {
        $this->idprofesion = $idprofesion;
    }

    public function getNombretipo(): ?string {
        return $this->nombretipo;
    }

    public function setNombretipo(?string $nombretipo): void {
        $this->nombretipo = $nombretipo;
    }

    public function getNombreplan(): ?string {
        return $this->nombreplan;
    }

    public function setNombreplan(?string $nombreplan): void {
        $this->nombreplan = $nombreplan;
    }

    public function getNombreprofesion(): ?string {
        return $this->nombreprofesion;
    }

    public function setNombreprofesion(?string $nombreprofesion): void {
        $this->nombreprofesion = $nombreprofesion;
    }
}
