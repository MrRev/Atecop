<?php
require_once "../config/config.php";
require_once "../util_global/Database.php";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Verificar tabla administrador
    $stmt = $conn->query("SELECT usuario, nombrecompleto FROM administrador LIMIT 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "Conexión exitosa. Usuario admin encontrado: " . htmlspecialchars($admin['nombrecompleto']);
    } else {
        echo "Conexión exitosa pero no se encontró el usuario admin";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>