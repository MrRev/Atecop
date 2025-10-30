<?php

/**
 * Clase ControladorLogout
 * Maneja el cierre de sesión del administrador
 */
class ControladorLogout {
    
    public function cerrarSesion() {
        session_start();
        
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión si existe
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir al login
        header('Location: index.php?modulo=seguridad&accion=login&mensaje=sesion_cerrada');
        exit;
    }
}
