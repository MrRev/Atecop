<?php

/**
 * Clase Modelo: MetodoPago
 * Representa un método de pago disponible
 */
class MetodoPago {
    private ?int $idmetodopago;
    private string $nombremetodo;

    public function __construct() {
        $this->idmetodopago = null;
    }

    public function getIdmetodopago(): ?int {
        return $this->idmetodopago;
    }

    public function setIdmetodopago(?int $idmetodopago): void {
        $this->idmetodopago = $idmetodopago;
    }

    public function getNombremetodo(): string {
        return $this->nombremetodo;
    }

    public function setNombremetodo(string $nombremetodo): void {
        $this->nombremetodo = $nombremetodo;
    }
}
