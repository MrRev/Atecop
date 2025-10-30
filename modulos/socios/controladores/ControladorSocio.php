<?php

require_once __DIR__ . '/../dao/SocioDAO.php';
require_once __DIR__ . '/../dao/TipoSocioDAO.php';
require_once __DIR__ . '/../dao/ProfesionDAO.php';
require_once __DIR__ . '/../../../util_global/ApiPeruDev.php';
require_once __DIR__ . '/../../membresias/dao/PlanMembresiaDAO.php';

/**
 * Controlador: ControladorSocio
 * Maneja todas las acciones relacionadas con socios
 */
class ControladorSocio {
    private SocioDAO $socioDAO;
    private TipoSocioDAO $tipoSocioDAO;
    private ProfesionDAO $profesionDAO;
    private PlanMembresiaDAO $planDAO;
    private ApiPeruDev $apiPeruDev;

    public function __construct() {
        $this->socioDAO = new SocioDAO();
        $this->tipoSocioDAO = new TipoSocioDAO();
        $this->profesionDAO = new ProfesionDAO();
        $this->planDAO = new PlanMembresiaDAO();
        $this->apiPeruDev = new ApiPeruDev();
    }

    /**
     * Listar todos los socios
     */
    public function listar(): void {
        $socios = $this->socioDAO->listAll();
        require_once __DIR__ . '/../vistas/VistaListarSocios.php';
    }

    /**
     * Mostrar formulario para crear/editar socio
     */
    public function mostrarFormulario($id = null): void {
        $socio = null;
        $tiposSocio = $this->tipoSocioDAO->listAll();
        $profesiones = $this->profesionDAO->listAll();
        $planes = $this->planDAO->listAll();

        if ($id !== null) {
            $socio = $this->socioDAO->read((int)$id);
        } elseif (isset($_GET['id'])) {
            $socio = $this->socioDAO->read((int)$_GET['id']);
        }

        require_once __DIR__ . '/../vistas/VistaFormSocio.php';
    }

    /**
     * Validar DNI/RUC con API externa
     */
    public function validarDNI(): void {
        header('Content-Type: application/json');

        // Aceptar tanto GET como POST (las vistas usan fetch GET)
        $documento = trim($_GET['dni'] ?? $_POST['documento'] ?? '');
        $excludeId = isset($_GET['excludeId']) ? (int)$_GET['excludeId'] : (isset($_POST['excludeId']) ? (int)$_POST['excludeId'] : null);

        if (empty($documento)) {
            echo json_encode(['success' => false, 'mensaje' => 'Documento no proporcionado']);
            return;
        }
        
        // Verificar si el documento ya existe en la BD
        if ($this->socioDAO->existeDni($documento, $excludeId)) {
            echo json_encode(['success' => false, 'mensaje' => 'Este documento ya está registrado']);
            return;
        }

        // Validar con API externa
        $resultado = $this->apiPeruDev->consultarDocumento($documento);

        if (isset($resultado['success']) && $resultado['success']) {
            $d = $resultado['data'] ?? [];
            // Normalizar campos esperados por la vista
            $nombre = $d['nombre_completo'] ?? $d['nombres'] ?? $d['nombre'] ?? '';
            $direccion = $d['direccion'] ?? '';
            echo json_encode(['success' => true, 'nombre' => $nombre, 'direccion' => $direccion]);
        } else {
            $msg = $resultado['message'] ?? $resultado['mensaje'] ?? 'No se pudo validar el documento';
            echo json_encode(['success' => false, 'mensaje' => $msg]);
        }
    }

    /**
     * Guardar socio (crear o actualizar)
     */
    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=socios&accion=listar');
            return;
        }

        try {
            // Debug: Imprimir datos recibidos
            echo "<script>console.log('Datos POST recibidos:', " . json_encode($_POST) . ");</script>";
            
            $socio = new Socio();
        
        // Validar campos requeridos
        if (empty($_POST['dni']) || empty($_POST['nombrecompleto']) || 
            empty($_POST['idtiposocio']) || empty($_POST['idplan'])) {
            $_SESSION['error'] = 'Todos los campos obligatorios deben ser completados';
            header('Location: index.php?modulo=socios&accion=formulario');
            return;
        }

        // Mapear datos del formulario
        $socio->setDni(trim($_POST['dni']));
        $socio->setNombrecompleto(trim($_POST['nombrecompleto']));
        $socio->setFechanacimiento(!empty($_POST['fechanacimiento']) ? $_POST['fechanacimiento'] : null);
        $socio->setDireccion(!empty($_POST['direccion']) ? trim($_POST['direccion']) : null);
        $socio->setEmail(!empty($_POST['email']) ? trim($_POST['email']) : null);
        $socio->setTelefono(!empty($_POST['telefono']) ? trim($_POST['telefono']) : null);
        $socio->setNumcuentabancaria(!empty($_POST['numcuentabancaria']) ? trim($_POST['numcuentabancaria']) : null);
        $socio->setEstado($_POST['estado'] ?? 'Activo');
        $socio->setIdtiposocio((int)$_POST['idtiposocio']);
        $socio->setIdplan((int)$_POST['idplan']);
        $socio->setIdprofesion(!empty($_POST['idprofesion']) ? (int)$_POST['idprofesion'] : null);

        // Calcular fecha de vencimiento basada en el plan
        $plan = $this->planDAO->read((int)$_POST['idplan']);
        if ($plan) {
            $fechaVencimiento = date('Y-m-d', strtotime('+' . $plan['duracionmeses'] . ' months'));
            $socio->setFechavencimiento($fechaVencimiento);
        } else {
            $socio->setFechavencimiento(date('Y-m-d', strtotime('+1 month')));
        }

        // Crear o actualizar
        if (!empty($_POST['idsocio'])) {
            // Actualizar
            $socio->setIdsocio((int)$_POST['idsocio']);
            $resultado = $this->socioDAO->update($socio);
            $mensaje = $resultado ? 'Socio actualizado correctamente' : 'Error al actualizar socio';
        } else {
            // Crear
            $resultado = $this->socioDAO->create($socio);
            if ($resultado) {
                $_SESSION['success'] = 'Socio registrado correctamente';
                header('Location: index.php?modulo=socios&accion=listar');
                exit;
            } else {
                throw new Exception('Error al registrar el socio');
            }
        }
        
        } catch (Exception $e) {
            // Log en servidor
            error_log("Error en ControladorSocio::guardar - " . $e->getMessage());
            
            // Log en consola del navegador
            echo "<script>console.error('Error al guardar socio:', " . json_encode($e->getMessage()) . ");</script>";
            
            $_SESSION['error_socios'] = $e->getMessage(); // Cambiamos la key para evitar conflictos
            
            // Si estamos editando, incluimos el ID en la redirección
            $redirectUrl = 'index.php?modulo=socios&accion=formulario';
            if (!empty($_POST['idsocio'])) {
                $redirectUrl .= '&id=' . $_POST['idsocio'];
            }
            
            header("Location: $redirectUrl");
            exit;
        }
    }

    /**
     * Ver perfil detallado de un socio
     */
    public function verPerfil(?int $id = null): void {
        if (!isset($_GET['id'])) {
            header('Location: index.php?modulo=socios&accion=listar');
            return;
        }

        $socio = $this->socioDAO->read((int)$_GET['id']);
        
        if (!$socio) {
            $_SESSION['error'] = 'Socio no encontrado';
            header('Location: index.php?modulo=socios&accion=listar');
            return;
        }

        // Obtener historial de pagos
        require_once __DIR__ . '/../../pagos/dao/PagoDAO.php';
        $pagoDAO = new PagoDAO();
        $pagos = $pagoDAO->getPagosPorSocio($socio->getIdsocio());

        // Obtener cursos inscritos
        require_once __DIR__ . '/../../cursos/dao/CursoInscritoDAO.php';
        $cursoInscritoDAO = new CursoInscritoDAO();
        $cursos = $cursoInscritoDAO->getCursosPorSocio($socio->getIdsocio());

        // Obtener información del plan para mostrar costo/duración en la vista
        $plan = null;
        if ($socio->getIdplan()) {
            $plan = $this->planDAO->read($socio->getIdplan());
        }

        require_once __DIR__ . '/../vistas/VistaPerfilSocio.php';
    }

    /**
     * Dar de baja a un socio
     */
    public function darDeBaja(): void {
        if (!isset($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }

        $resultado = $this->socioDAO->darDeBaja((int)$_POST['id']);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $resultado,
            'message' => $resultado ? 'Socio dado de baja correctamente' : 'Error al dar de baja al socio'
        ]);
    }

    /**
     * Asignar plan a un socio
     */
    public function asignarPlan(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=socios&accion=listar');
            return;
        }

        $idsocio = (int)$_POST['idsocio'];
        $idplan = (int)$_POST['idplan'];

        // Obtener duración del plan
        $plan = $this->planDAO->read($idplan);
        if (!$plan) {
            $_SESSION['error'] = 'Plan no encontrado';
            header('Location: index.php?modulo=socios&accion=perfil&id=' . $idsocio);
            return;
        }

        // Calcular nueva fecha de vencimiento
        $fechaVencimiento = date('Y-m-d', strtotime('+' . $plan['duracionmeses'] . ' months'));

        // Actualizar socio
        $resultado = $this->socioDAO->updatePlanSocio($idsocio, $idplan, $fechaVencimiento);

        $_SESSION[$resultado ? 'success' : 'error'] = $resultado 
            ? 'Plan asignado correctamente' 
            : 'Error al asignar plan';

        header('Location: index.php?modulo=socios&accion=perfil&id=' . $idsocio);
    }
}
