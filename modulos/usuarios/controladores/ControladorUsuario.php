<?php
require_once __DIR__ . '/../dao/UsuarioDAO.php';
require_once __DIR__ . '/../../../util_global/ApiPeruDev.php';
require_once __DIR__ . '/../../../util_global/SessionManager.php';
require_once __DIR__ . '/../../socios/dao/SocioDAO.php';

/**
 * Controlador: ControladorUsuario
 * 
 * Maneja todas las acciones relacionadas con usuarios y validación de DNI
 */
class ControladorUsuario {
    private $usuarioDAO;
    private $socioDAO;
    private $apiPeruDev;
    
    public function __construct() {
        $this->usuarioDAO = new UsuarioDAO();
        $this->socioDAO = new SocioDAO();
        $this->apiPeruDev = new ApiPeruDev();
    }
    
    /**
     * Listar usuarios (con filtros)
     */
    public function listar() {
    // Debug: registrar llamada y estado de sesión en logs/debug_usuario.log
    $debugMsg = '[' . date('Y-m-d H:i:s') . '] ControladorUsuario::listar called. _SESSION keys: ' . json_encode(array_keys($_SESSION ?? [])) . "\n";
    @file_put_contents(__DIR__ . '/../../../logs/debug_usuario.log', $debugMsg, FILE_APPEND);
        SessionManager::checkSession();
        
        $buscar = $_GET['buscar'] ?? null;
        $estado = $_GET['estado'] ?? null;
        
        try {
            $usuarios = $this->usuarioDAO->listarFiltrado($buscar, $estado);
            require_once __DIR__ . '/../vistas/VistaListarUsuarios.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?modulo=dashboard');
            exit;
        }
    }
    
    /**
     * Mostrar formulario de usuario
     */
    public function mostrarFormulario() {
        SessionManager::checkSession();
        
        $usuario = null;
        $socios = $this->socioDAO->listAll(); // Para select de vinculación con socio
        
        if (isset($_GET['id'])) {
            $usuario = $this->usuarioDAO->read((int)$_GET['id']);
            if (!$usuario) {
                $_SESSION['error'] = 'Usuario no encontrado';
                header('Location: index.php?modulo=usuarios&accion=listar');
                exit;
            }
        }
        
        require_once __DIR__ . '/../vistas/VistaFormUsuario.php';
    }
    
    /**
     * Validar DNI con API
     */
    public function validarDNI() {
        header('Content-Type: application/json');
        SessionManager::checkSession();
        
        $dni = trim($_GET['dni'] ?? $_POST['dni'] ?? '');
        $excludeId = isset($_GET['excludeId']) ? (int)$_GET['excludeId'] : null;
        
        if (empty($dni)) {
            echo json_encode(['success' => false, 'mensaje' => 'DNI no proporcionado']);
            return;
        }
        
        // Verificar si el DNI ya existe
        if ($this->usuarioDAO->existeDNI($dni, $excludeId)) {
            echo json_encode(['success' => false, 'mensaje' => 'Este DNI ya está registrado']);
            return;
        }
        
        // Validar con API
        $resultado = $this->apiPeruDev->consultarDNI($dni);
        
        if (isset($resultado['success']) && $resultado['success']) {
            echo json_encode([
                'success' => true,
                'nombre' => $resultado['data']['nombre'] ?? '',
                'direccion' => $resultado['data']['direccion'] ?? ''
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => $resultado['message'] ?? 'Error al validar DNI'
            ]);
        }
    }
    
    /**
     * Guardar usuario (crear/actualizar)
     */
    public function guardar() {
        SessionManager::checkSession();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=usuarios&accion=listar');
            return;
        }

    error_log("DEBUG - ControladorUsuario::guardar - POST data: " . json_encode($_POST));
    // Also append to debug file so we can see sequence in same log
    $debugFile = __DIR__ . '/../../../logs/debug_usuario.log';
    @file_put_contents($debugFile, '[' . date('Y-m-d H:i:s') . '] ControladorUsuario::guardar - POST: ' . json_encode($_POST) . PHP_EOL, FILE_APPEND);
        
        try {
            $usuario = new Usuario();
            
            // En actualización
            if (!empty($_POST['idusuario'])) {
                error_log("DEBUG - ControladorUsuario::guardar - Actualizando usuario existente: " . $_POST['idusuario']);
                
                $usuario_actual = $this->usuarioDAO->read((int)$_POST['idusuario']);
                if (!$usuario_actual) {
                    throw new Exception('Usuario no encontrado');
                }
                
                // DNI y nombre completo no se actualizan (son inmutables)
                $usuario->setIdusuario($usuario_actual->getIdusuario());
                $usuario->setDni($usuario_actual->getDni());
                $usuario->setNombrecompleto($usuario_actual->getNombrecompleto());
                
            } else {
                // En creación, validar DNI
                if (empty($_POST['dni'])) {
                    throw new Exception('El DNI es requerido');
                }
                
                $resultado = $this->apiPeruDev->consultarDNI($_POST['dni']);
                if (!$resultado['success']) {
                    throw new Exception($resultado['message'] ?? 'Error al validar DNI');
                }
                
                $usuario->setDni($_POST['dni']);
                $usuario->setNombrecompleto($resultado['data']['nombre']);
            }
            
            // Campos editables
            if (!empty($_POST['nombreusuario'])) {
                error_log("DEBUG - ControladorUsuario::guardar - Usando nombre de usuario proporcionado: " . $_POST['nombreusuario']);
                $usuario->setNombreusuario($_POST['nombreusuario']);
            } else {
                error_log("DEBUG - ControladorUsuario::guardar - Generando nombre de usuario automáticamente");
                $usuario->setNombrecompleto($usuario->getNombrecompleto() ?? $resultado['data']['nombre']);
                $usuario->setNombreusuario($usuario->generarNombreUsuario());
            }
            
            $usuario->setEmail($_POST['email'] ?? null);
            $usuario->setTelefono($_POST['telefono'] ?? null);
            $usuario->setDireccion($_POST['direccion'] ?? null);
            $usuario->setRol($_POST['rol'] ?? 'administrador');
            $usuario->setIdsocio($_POST['idsocio'] ?? null);
            $usuario->setEstado($_POST['estado'] ?? 'Activo');
            
            // Password solo si es nuevo o se proporciona
            if (empty($_POST['idusuario']) || !empty($_POST['password'])) {
                if (empty($_POST['password'])) {
                    throw new Exception('La contraseña es requerida para nuevos usuarios');
                }
                error_log("DEBUG - ControladorUsuario::guardar - Hasheando contraseña");
                $usuario->setClavehash(password_hash($_POST['password'], PASSWORD_DEFAULT));
            }
            
            // Crear o actualizar
            try {
                error_log("DEBUG - ControladorUsuario::guardar - Intentando " . 
                         ($usuario->getIdusuario() ? "actualizar" : "crear") . " usuario");
                
                if ($usuario->getIdusuario()) {
                    $resultado = $this->usuarioDAO->update($usuario);
                    $mensaje = 'Usuario actualizado correctamente';
                } else {
                    $resultado = $this->usuarioDAO->create($usuario);
                    $mensaje = 'Usuario creado correctamente';
                }
                
                if ($resultado) {
                    $_SESSION['success'] = $mensaje;
                    header('Location: index.php?modulo=usuarios&accion=listar');
                    exit;
                } else {
                    throw new Exception('Error al guardar el usuario');
                }
            } catch (Exception $e) {
                $err = "ERROR - ControladorUsuario::guardar - " . $e->getMessage();
                error_log($err);
                @file_put_contents($debugFile, '[' . date('Y-m-d H:i:s') . '] ' . $err . PHP_EOL, FILE_APPEND);
                throw $e;
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $redirectUrl = 'index.php?modulo=usuarios&accion=formulario';
            if (!empty($_POST['idusuario'])) {
                $redirectUrl .= '&id=' . $_POST['idusuario'];
            }
            header("Location: $redirectUrl");
            exit;
        }
    }
    
    /**
     * Ver perfil de usuario
     */
    public function verPerfil() {
        SessionManager::checkSession();
        
        try {
            $idusuario = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['idusuario'];
            
            $usuario = $this->usuarioDAO->read($idusuario);
            if (!$usuario) {
                throw new Exception('Usuario no encontrado');
            }
            
            // Si el usuario está vinculado a un socio, obtener sus cursos
            $cursos = [];
            if ($usuario->getIdsocio()) {
                require_once __DIR__ . '/../../cursos/dao/CursoInscritoDAO.php';
                $cursoInscritoDAO = new CursoInscritoDAO();
                $cursos = $cursoInscritoDAO->getCursosPorSocio($usuario->getIdsocio());
            }
            
            require_once __DIR__ . '/../vistas/VistaPerfilUsuario.php';
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?modulo=usuarios&accion=listar');
            exit;
        }
    }
    
    /**
     * Cambiar estado (activar/desactivar)
     */
    public function cambiarEstado() {
        SessionManager::checkSession();
        header('Content-Type: application/json');
        
        try {
            if (!isset($_POST['idusuario'])) {
                throw new Exception('ID de usuario no proporcionado');
            }
            
            $usuario = $this->usuarioDAO->read((int)$_POST['idusuario']);
            if (!$usuario) {
                throw new Exception('Usuario no encontrado');
            }
            
            $nuevo_estado = $usuario->getEstado() === 'Activo' ? 'Inactivo' : 'Activo';
            $usuario->setEstado($nuevo_estado);
            
            $resultado = $this->usuarioDAO->update($usuario);
            
            echo json_encode([
                'success' => $resultado,
                'mensaje' => $resultado ? 'Estado actualizado correctamente' : 'Error al actualizar estado'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}