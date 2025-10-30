<?php

require_once __DIR__ . '/../dao/PlanMembresiaDAO.php';

/**
 * Controlador: ControladorPlan
 * Maneja todas las acciones relacionadas con planes de membresía
 */
class ControladorPlan {
    private PlanMembresiaDAO $planDAO;

    public function __construct() {
        $this->planDAO = new PlanMembresiaDAO();
    }

    /**
     * Listar todos los planes
     */
    public function listar(): void {
        $planes = $this->planDAO->listAll();
        require_once __DIR__ . '/../vistas/VistaGestionPlanes.php';
    }

    /**
     * Mostrar formulario para crear/editar plan
     */
    public function formulario(): void {
        $plan = null;
        
        if (isset($_GET['id'])) {
            $plan = $this->planDAO->read((int)$_GET['id']);
        }
        
        require_once __DIR__ . '/../vistas/VistaFormPlan.php';
    }

    /**
     * Guardar plan (crear o actualizar)
     */
    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=membresias&accion=listar');
            return;
        }

        // Validar campos requeridos
        if (empty($_POST['nombreplan']) || empty($_POST['duracionmeses']) || !isset($_POST['costo'])) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: index.php?modulo=membresias&accion=formulario');
            return;
        }

        $plan = new PlanMembresia();
        $plan->setNombreplan(trim($_POST['nombreplan']));
        $plan->setDuracionmeses((int)$_POST['duracionmeses']);
        $plan->setCosto((float)$_POST['costo']);
        $plan->setEstado($_POST['estado'] ?? 'Activo');

        // Crear o actualizar
        if (!empty($_POST['idplan'])) {
            $plan->setIdplan((int)$_POST['idplan']);
            $resultado = $this->planDAO->update($plan);
            $mensaje = $resultado ? 'Plan actualizado correctamente' : 'Error al actualizar plan';
        } else {
            $resultado = $this->planDAO->create($plan);
            $mensaje = $resultado ? 'Plan creado correctamente' : 'Error al crear plan';
        }

        $_SESSION[$resultado ? 'success' : 'error'] = $mensaje;
        header('Location: index.php?modulo=membresias&accion=listar');
    }
}
