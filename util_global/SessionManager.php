<?php

class SessionManager {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            require_once __DIR__ . '/session_config.php';
            session_start();
        }
    }

    public static function checkSession(): bool {
        self::init();
        
        if (!isset($_SESSION['idadmin'])) {
            
            // Si la sesión no es válida, comprobamos si es AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                
                // ES AJAX: Enviamos un error 401 (No Autorizado) y JSON
                header('Content-Type: application/json');
                http_response_code(401); // <-- AÑADIR ESTA LÍNEA
                
                echo json_encode(['success' => false, 'mensaje' => 'Sesión expirada. Por favor, inicie sesión.']);
                exit;
            }
            
            // NO ES AJAX: Redirigimos al login
            header('Location: index.php?modulo=seguridad&accion=login');
            exit;
        }
        
        // Renovar la sesión
        $_SESSION['last_activity'] = time();
        return true;
    }

    public static function destroy(): void {
        self::init();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }
}