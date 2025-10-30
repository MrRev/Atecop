<?php
// Script temporal para verificar password '123' contra el hash del usuario 'mimi'
try {
    $dsn = "mysql:host=127.0.0.1;dbname=atecop_db;charset=utf8mb4";
    $user = 'root';
    $pass = '';
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $pdo->prepare('SELECT clavehash FROM administrador WHERE usuario = :u LIMIT 1');
    $stmt->execute([':u' => 'mimi']);
    $hash = $stmt->fetchColumn();
    if ($hash === false) {
        echo "USUARIO_NO_ENCONTRADO\n";
        exit(1);
    }

    $ok = password_verify('123', $hash);
    echo $ok ? "VERIFICACION_OK\n" : "VERIFICACION_FALLA\n";
    echo "HASH:" . $hash . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>