<?php
/**
 * Front Controller - Punto de Entrada Principal del Sistema ATECOP
 * 
 * Maneja el enrutamiento de todas las peticiones y carga los controladores apropiados.
 */

// Iniciar sesión
session_start();

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Cargar autoloader
require_once __DIR__ . '/autoload.php';

// Función helper para verificar autenticación
function verificarAutenticacion() {
    if (!isset($_SESSION['idadmin']) || !isset($_SESSION['usuario'])) {
        header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
        exit;
    }
}

// Obtener parámetros de la URL
$modulo = $_GET['modulo'] ?? 'seguridad';
$accion = $_GET['accion'] ?? 'login';

// Rutas públicas (no requieren autenticación)
$rutasPublicas = [
    'seguridad' => ['login', 'procesarLogin']
];

// Verificar si la ruta requiere autenticación
$requiereAuth = true;
if (isset($rutasPublicas[$modulo]) && in_array($accion, $rutasPublicas[$modulo])) {
    $requiereAuth = false;
}

// Si requiere autenticación y no está logueado, redirigir al login
if ($requiereAuth) {
    verificarAutenticacion();
}

// Enrutamiento según módulo y acción
try {
    switch ($modulo) {
        case 'seguridad':
            require_once __DIR__ . '/modulos/seguridad/controladores/ControladorLogin.php';
            $controlador = new ControladorLogin();
            
            switch ($accion) {
                case 'login':
                    $controlador->mostrarLogin();
                    break;
                case 'procesarLogin':
                    $controlador->procesarLogin();
                    break;
                case 'logout':
                    $controlador->logout();
                    break;
                default:
                    $controlador->mostrarLogin();
            }
            break;
            
        case 'dashboard':
            require_once __DIR__ . '/modulos/seguridad/vistas/VistaDashboard.php';
            break;
            
        case 'socios':
            require_once __DIR__ . '/modulos/socios/controladores/ControladorSocio.php';
            $controlador = new ControladorSocio();
            
            switch ($accion) {
                case 'listar':
                    $controlador->listar();
                    break;
                case 'crear':
                    $controlador->mostrarFormulario();
                    break;
                case 'guardar':
                    $controlador->guardar();
                    break;
                case 'editar':
                    $id = $_GET['id'] ?? null;
                    $controlador->mostrarFormulario($id);
                    break;
                case 'perfil':
                    $id = $_GET['id'] ?? null;
                    $controlador->verPerfil($id);
                    break;
                case 'baja':
                    $id = $_GET['id'] ?? null;
                    $controlador->darDeBaja($id);
                    break;
                case 'reactivar':
                    $id = $_GET['id'] ?? null;
                    $controlador->reactivar($id);
                break;
                case 'validarDocumento':
                    $controlador->validarDocumento(); 
                    break;
                case 'asignarPlan':
                    $controlador->asignarPlan();
                break;
                case 'buscar':
                    $controlador->buscar();
                break;
                default:
                // ...
                        $controlador->listar();
                }
            break;
            
        case 'membresias':
            require_once __DIR__ . '/modulos/membresias/controladores/ControladorPlan.php';
            $controlador = new ControladorPlan();
            
            switch ($accion) {
                case 'listar':
                    $controlador->listar();
                    break;
                case 'crear':
                    $controlador->mostrarFormulario();
                    break;
                case 'guardar':
                    $controlador->guardar();
                    break;
                case 'editar':
                    $id = $_GET['id'] ?? null;
                    $controlador->mostrarFormulario($id);
                    break;
                case 'cambiarEstado':
                    $controlador->cambiarEstado();
                break;
                default:
                    $controlador->listar();
            }
            break;
            
        case 'pagos':
            require_once __DIR__ . '/modulos/pagos/controladores/ControladorPago.php';
            $controlador = new ControladorPago();
            
            switch ($accion) {
                case 'listar':
                    $controlador->listar();
                    break;
                case 'registrar':
                    $controlador->mostrarFormulario();
                    break;
                case 'guardar':
                    $controlador->guardar();
                    break;
                case 'anular':
                    $id = $_GET['id'] ?? null;
                    $controlador->anular($id);
                    break;
                default:
                    $controlador->mostrarFormulario();
            }
            break;
            
        case 'ponentes':
            require_once __DIR__ . '/modulos/ponentes/controladores/ControladorPonente.php';
            $controlador = new ControladorPonente();
            
            switch ($accion) {
                case 'listar':
                    $controlador->listar();
                    break;
                case 'crear':
                    $controlador->mostrarFormulario();
                    break;
                case 'guardar':
                    $controlador->guardar();
                    break;
                case 'editar':
                    $id = $_GET['id'] ?? null;
                    $controlador->mostrarFormulario($id);
                    break;
                case 'validarDNI':
                    $controlador->validarDNI();
                    break;
                default:
                    $controlador->listar();
            }
            break;
            
        case 'cursos':
            require_once __DIR__ . '/modulos/cursos/controladores/ControladorCurso.php';
            $controlador = new ControladorCurso();
            
            switch ($accion) {
                case 'listar':
                    $controlador->listar();
                    break;
                case 'crear':
                    $controlador->mostrarFormulario();
                    break;
                case 'guardar':
                    $controlador->guardar();
                    break;
                case 'editar':
                    $id = $_GET['id'] ?? null;
                    $controlador->mostrarFormulario($id);
                    break;
                case 'inscripciones':
                    $id = $_GET['id'] ?? null;
                    $controlador->gestionarInscripciones($id);
                    break;
                case 'inscribir':
                    $controlador->inscribir();
                    break;
                case 'exportarInscritos':
                    $id = $_GET['id'] ?? null;
                    $controlador->exportarInscritos($id);
                    break;
                default:
                    $controlador->listar();
            }
            break;
            
        case 'reportes':
            require_once __DIR__ . '/modulos/reportes/controladores/ControladorReporte.php';
            $controlador = new ControladorReporte();
            
            switch ($accion) {
                case 'menu':
                    $controlador->mostrarMenu();
                    break;
                case 'morosos':
                    $controlador->reporteMorosos();
                    break;
                case 'vencimientos':
                    $controlador->reporteVencimientos();
                    break;
                case 'socio':
                    // Ahora aceptamos 'dni' como parámetro preferente; mantenemos compatibilidad con 'id'
                    $dni = $_GET['dni'] ?? null;
                    $controlador->reporteSocio($dni);
                    break;
                case 'inhabilitar':
                    $controlador->reporteInhabilitar();
                    break;
                default:
                    $controlador->mostrarMenu();
            }
            break;
            
        default:
            // Si el usuario está autenticado, ir al dashboard
            if (isset($_SESSION['idadmin'])) {
                header('Location: ' . BASE_URL . '/index.php?modulo=dashboard');
            } else {
                header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
            }
            exit;
    }
    
} catch (Exception $e) {
    if (ENVIRONMENT === 'development') {
        die("Error: " . $e->getMessage() . "<br>Archivo: " . $e->getFile() . "<br>Línea: " . $e->getLine());
    } else {
        error_log("Error en index.php: " . $e->getMessage());
        die("Ha ocurrido un error. Por favor, contacte al administrador.");
    }
}
