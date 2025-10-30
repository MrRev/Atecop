<?php
/**
 * Autoloader PSR-4 para el Sistema ATECOP
 * 
 * Carga automáticamente las clases sin necesidad de require/include manual.
 */

spl_autoload_register(function ($className) {
    // Namespace base del proyecto
    $baseNamespace = '';
    
    // Directorio base donde están las clases
    $baseDir = __DIR__ . '/';
    
    // Reemplazar namespace separators con directory separators
    $relativeClass = str_replace('\\', '/', $className);
    
    // Posibles ubicaciones de clases según la estructura modular
    $possiblePaths = [
        // Utilidades globales
        $baseDir . 'util_global/' . $relativeClass . '.php',
        
        // Módulos - Controladores
        $baseDir . 'modulos/socios/controladores/' . $relativeClass . '.php',
        $baseDir . 'modulos/membresias/controladores/' . $relativeClass . '.php',
        $baseDir . 'modulos/pagos/controladores/' . $relativeClass . '.php',
        $baseDir . 'modulos/ponentes/controladores/' . $relativeClass . '.php',
        $baseDir . 'modulos/cursos/controladores/' . $relativeClass . '.php',
        $baseDir . 'modulos/reportes/controladores/' . $relativeClass . '.php',
        $baseDir . 'modulos/seguridad/controladores/' . $relativeClass . '.php',
        
        // Módulos - DAO
        $baseDir . 'modulos/socios/dao/' . $relativeClass . '.php',
        $baseDir . 'modulos/membresias/dao/' . $relativeClass . '.php',
        $baseDir . 'modulos/pagos/dao/' . $relativeClass . '.php',
        $baseDir . 'modulos/ponentes/dao/' . $relativeClass . '.php',
        $baseDir . 'modulos/cursos/dao/' . $relativeClass . '.php',
        $baseDir . 'modulos/reportes/dao/' . $relativeClass . '.php',
        $baseDir . 'modulos/seguridad/dao/' . $relativeClass . '.php',
        
        // Módulos - Modelos
        $baseDir . 'modulos/socios/modelo/' . $relativeClass . '.php',
        $baseDir . 'modulos/membresias/modelo/' . $relativeClass . '.php',
        $baseDir . 'modulos/pagos/modelo/' . $relativeClass . '.php',
        $baseDir . 'modulos/ponentes/modelo/' . $relativeClass . '.php',
        $baseDir . 'modulos/cursos/modelo/' . $relativeClass . '.php',
        $baseDir . 'modulos/seguridad/modelo/' . $relativeClass . '.php',
        
        // Módulos - Utilidades
        $baseDir . 'modulos/socios/util/' . $relativeClass . '.php',
        $baseDir . 'modulos/membresias/util/' . $relativeClass . '.php',
        $baseDir . 'modulos/pagos/util/' . $relativeClass . '.php',
        $baseDir . 'modulos/ponentes/util/' . $relativeClass . '.php',
        $baseDir . 'modulos/cursos/util/' . $relativeClass . '.php',
        $baseDir . 'modulos/reportes/util/' . $relativeClass . '.php',
        $baseDir . 'modulos/seguridad/util/' . $relativeClass . '.php',
    ];
    
    // Intentar cargar desde cada ubicación posible
    foreach ($possiblePaths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
