<?php
// Script de prueba para verificar la conexión a la base de datos usando config/config.php
require_once __DIR__ . '/config/config.php';

echo "Usando configuración:\n";
echo "DB_HOST=" . DB_HOST . "\n";
echo "DB_NAME=" . DB_NAME . "\n";
echo "DB_USER=" . DB_USER . "\n";

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Conexión a la base de datos: OK\n";
} catch (Exception $e) {
    echo "Conexión a la base de datos: FALLÓ\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
