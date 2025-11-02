<?php

require_once __DIR__ . '/../dao/SocioDAO.php';
require_once __DIR__ . '/../dao/TipoSocioDAO.php';
require_once __DIR__ . '/../dao/ProfesionDAO.php';
require_once __DIR__ . '/../../../util_global/ApiPeruDev.php';
require_once __DIR__ . '/../../membresias/dao/PlanMembresiaDAO.php';
require_once __DIR__ . '/../../../util_global/SessionManager.php';

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
     * Listar todos los socios (CON FILTROS)
     */
    public function listar(): void {
        // 1. Lee los filtros de la URL (del formulario GET)
        $buscar = $_GET['buscar'] ?? null;
        $estado = $_GET['estado'] ?? null;

        // 2. ¡Llama a la nueva función "listarFiltrado"!
        //    (Aquí estaba el error, antes decía listAll())
        $socios = $this->socioDAO->listarFiltrado($buscar, $estado);

        // 3. Carga la vista
        require_once __DIR__ . '/../vistas/VistaListarSocios.php';
    }
    /**
     * Validar DNI/RUC con API
     */
    public function validarDocumento(): void {
        header('Content-Type: application/json');
        
        SessionManager::checkSession();
        
        if (!isset($_GET['documento']) || !isset($_GET['tipo'])) {
            echo json_encode(['success' => false, 'mensaje' => 'Faltan parámetros requeridos']);
            exit;
        }

        $documento = trim($_GET['documento']);
        $tipo = $_GET['tipo'];
        
        try {
            if (!defined('API_PERUDEV_TOKEN') || empty(API_PERUDEV_TOKEN)) {
                throw new Exception('Token de API no configurado');
            }
            
            $resultado = null;
            
            if ($tipo === 'dni') {
                $resultado = $this->apiPeruDev->consultarDNI($documento);
            } else if ($tipo === 'ruc') {
                $resultado = $this->apiPeruDev->consultarRUC($documento);
            } else {
                throw new Exception('Tipo de documento no válido');
            }
            
            if ($resultado && $resultado['success'] && isset($resultado['data'])) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'nombre'    => $resultado['data']['nombre'] ?? $resultado['data']['razonSocial'] ?? '',
                        'direccion' => $resultado['data']['direccion'] ?? ''
                    ]
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => false,
                'mensaje' => $resultado['message'] ?? 'No se encontraron datos'
            ]);
            exit;

        } catch (Exception $e) {
            error_log("Error en validación de documento: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'mensaje' => 'Error al validar el documento: ' . $e->getMessage()
            ]);
            exit; 
        }
    }

    /**
     * Mostrar formulario para crear/editar socio
     */
    public function mostrarFormulario($id = null): void {
        $socio = null;
        $tiposSocio = $this->tipoSocioDAO->listAll();
        $profesiones = $this->profesionDAO->listAll();
        $planes = $this->planDAO->listActivos();

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
            if ($resultado) {
                $_SESSION['success'] = 'Socio actualizado correctamente';
                header('Location: index.php?modulo=socios&accion=listar');
            exit;
            } else {
                throw new Exception('Error al actualizar el socio');
            }
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
        $planes = $this->planDAO->listActivos();
        
        require_once __DIR__ . '/../vistas/VistaPerfilSocio.php';
    }

    /**
     * Dar de baja a un socio
     * --- VERSIÓN CORREGIDA ---
     */
    public function darDeBaja(?int $id = null): void {
        // Establecer el header al inicio para todas las respuestas
        header('Content-Type: application/json');

        // Validamos el ID que recibimos como argumento
        if ($id === null || $id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado o inválido']);
            exit; // Usamos exit; para detener la ejecución
        }

        // Usamos el $id del argumento, no $_POST
        $resultado = $this->socioDAO->darDeBaja($id);
        
        echo json_encode([
            'success' => $resultado,
            'message' => $resultado ? 'Socio dado de baja correctamente' : 'Error al dar de baja al socio'
        ]);
        exit; // Usamos exit;
    }

    /**
     * Reactivar a un socio
     */
    public function reactivar(?int $id = null): void {
        header('Content-Type: application/json');

        if ($id === null || $id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado o inválido']);
            exit;
        }

        // Usamos la nueva función del DAO que ya creaste
        $resultado = $this->socioDAO->reactivar($id); 
        
        echo json_encode([
            'success' => $resultado,
            'message' => $resultado ? 'Socio reactivado correctamente' : 'Error al reactivar al socio'
        ]);
        exit;
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
    /**
     * Busca socios por DNI o nombre (para AJAX de inscripciones)
     */
    public function buscar(): void {
        error_log("=== DEBUG buscarSocios ===");
        error_log("GET termino = " . ($_GET['termino'] ?? 'NO_LLEGA'));

        header('Content-Type: application/json');
        SessionManager::checkSession(); // Importante para AJAX

        $termino = $_GET['termino'] ?? '';

        if (strlen($termino) < 3) {
            echo json_encode([]); // Devuelve array vacío
            exit;
        }

        try {
            // 1. Llama a la función del DAO (que ya está correcta)
            $socios = $this->socioDAO->findSocios($termino); 
            
            // 2. Convierte los OBJETOS a ARRAYS para el JSON
            $sociosArray = [];
            foreach ($socios as $socio) {
                $sociosArray[] = [
                    'idsocio' => $socio->getIdsocio(),
                    'dni' => $socio->getDni(),
                    'nombrecompleto' => $socio->getNombrecompleto(),
                    'email' => $socio->getEmail(),
                    'estado' => $socio->getEstado()
                ];
            }
            
            echo json_encode($sociosArray);
            
        } catch (Exception $e) {
            error_log("Error en ControladorSocio::buscar - " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}
