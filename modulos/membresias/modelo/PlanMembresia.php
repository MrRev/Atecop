<?php

/**
 * Clase Modelo: PlanMembresia
 * Representa un plan de membresía
 */
class PlanMembresia {
    private ?int $idplan;
    private string $nombreplan;
    private int $duracionmeses;
    private float $costo;
    private string $estado;

    public function __construct() {
        $this->idplan = null;
        $this->estado = 'Activo';
        $this->costo = 0.0;
    }

    // Getters y Setters
    public function getIdplan(): ?int {
        return $this->idplan;
    }

    public function setIdplan(?int $idplan): void {
        $this->idplan = $idplan;
    }

    public function getNombreplan(): string {
        return $this->nombreplan;
    }

    public function setNombreplan(string $nombreplan): void {
        $this->nombreplan = $nombreplan;
    }

    public function getDuracionmeses(): int {
        return $this->duracionmeses;
    }

    public function setDuracionmeses(int $duracionmeses): void {
        $this->duracionmeses = $duracionmeses;
    }

    public function getCosto(): float {
        return $this->costo;
    }

    public function setCosto(float $costo): void {
        $this->costo = $costo;
    }

    public function getEstado(): string {
        return $this->estado;
    }

    public function setEstado(string $estado): void {
        $this->estado = $estado;
    }
}
