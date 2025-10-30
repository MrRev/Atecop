<?php

/**
 * Clase Modelo: Pago
 * Representa un pago realizado por un socio
 */
class Pago {
    private ?int $idpago;
    private float $monto;
    private string $fechapago;
    private ?string $concepto;
    private ?string $urlcomprobante;
    private string $estado;
    private int $idsocio;
    private int $idmetodopago;

    // Propiedades adicionales para joins
    private ?string $nombresocio;
    private ?string $nombremetodo;

    public function __construct() {
        $this->idpago = null;
        $this->estado = 'Registrado';
        $this->fechapago = date('Y-m-d');
    }

    // Getters y Setters
    public function getIdpago(): ?int {
        return $this->idpago;
    }

    public function setIdpago(?int $idpago): void {
        $this->idpago = $idpago;
    }

    public function getMonto(): float {
        return $this->monto;
    }

    public function setMonto(float $monto): void {
        $this->monto = $monto;
    }

    public function getFechapago(): string {
        return $this->fechapago;
    }

    public function setFechapago(string $fechapago): void {
        $this->fechapago = $fechapago;
    }

    public function getConcepto(): ?string {
        return $this->concepto;
    }

    public function setConcepto(?string $concepto): void {
        $this->concepto = $concepto;
    }

    public function getUrlcomprobante(): ?string {
        return $this->urlcomprobante;
    }

    public function setUrlcomprobante(?string $urlcomprobante): void {
        $this->urlcomprobante = $urlcomprobante;
    }

    public function getEstado(): string {
        return $this->estado;
    }

    public function setEstado(string $estado): void {
        $this->estado = $estado;
    }

    public function getIdsocio(): int {
        return $this->idsocio;
    }

    public function setIdsocio(int $idsocio): void {
        $this->idsocio = $idsocio;
    }

    public function getIdmetodopago(): int {
        return $this->idmetodopago;
    }

    public function setIdmetodopago(int $idmetodopago): void {
        $this->idmetodopago = $idmetodopago;
    }

    public function getNombresocio(): ?string {
        return $this->nombresocio;
    }

    public function setNombresocio(?string $nombresocio): void {
        $this->nombresocio = $nombresocio;
    }

    public function getNombremetodo(): ?string {
        return $this->nombremetodo;
    }

    public function setNombremetodo(?string $nombremetodo): void {
        $this->nombremetodo = $nombremetodo;
    }
}
