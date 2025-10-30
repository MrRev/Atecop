<?php
/**
 * Controlador: ControladorLogin
 * 
 * Maneja la autenticación de administradores en el sistema.
 */

class ControladorLogin {
    private $adminDAO;
    
    public function __construct() {
        $this->adminDAO = new AdministradorDAO();
    }
    
    /**
     * Muestra el formulario de login
     */
    public function mostrarLogin() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['idadmin'])) {
            header('Location: ' . BASE_URL . '/index.php?modulo=dashboard');
            exit;
        }
        
        require_once __DIR__ . '/../vistas/VistaLogin.php';
    }
    
    /**
     * Procesa el intento de login
     */
    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
            exit;
        }
        
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validaciones básicas
        if (empty($usuario) || empty($password)) {
            $_SESSION['error_login'] = 'Por favor, complete todos los campos.';
            header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
            exit;
        }
        
        try {
            // Buscar administrador
            $admin = $this->adminDAO->findByUsuario($usuario);
            
            if ($admin === null) {
                $_SESSION['error_login'] = 'Usuario o contraseña incorrectos.';
                header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
                exit;
            }
            
            // Verificar contraseña
            if (!$admin->verificarPassword($password)) {
                $_SESSION['error_login'] = 'Usuario o contraseña incorrectos.';
                header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
                exit;
            }
            
            // Login exitoso - Crear sesión
            $_SESSION['idadmin'] = $admin->getIdAdmin();
            $_SESSION['usuario'] = $admin->getUsuario();
            $_SESSION['nombrecompleto'] = $admin->getNombreCompleto();
            $_SESSION['login_time'] = time();
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);
            
            // Redirigir al dashboard
            header('Location: ' . BASE_URL . '/index.php?modulo=dashboard');
            exit;
            
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            $_SESSION['error_login'] = 'Error al procesar el login. Intente nuevamente.';
            header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
            exit;
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir al login
        header('Location: ' . BASE_URL . '/index.php?modulo=seguridad&accion=login');
        exit;
    }
}
