<?php
/**
 * Configuración de sesiones
 * IMPORTANTE: Este archivo debe ser incluido antes de cualquier inicio de sesión
 */

// Configuración de sesión
ini_set('session.gc_maxlifetime', 7200); // 2 horas
ini_set('session.cookie_lifetime', 7200);
session_name('ATECOP_SESSION');

// Otras configuraciones de seguridad para la sesión
ini_set('session.use_strict_mode', 1);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); // Se activa solo en HTTPS

// 
// ¡ESTE ES EL CAMBIO IMPORTANTE!
// Cambiamos 'Strict' por 'Lax'. 
// 'Lax' es seguro y permite que las llamadas AJAX (fetch)
// dentro del mismo sitio envíen la cookie de sesión.
//
ini_set('session.cookie_samesite', 'Lax');