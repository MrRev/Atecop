<?php
$hash = '$2y$10$SAk4BUY4lGaGtNn2wgPXquwK712/o/y3hdSYu1U6h/TIK62Q8CVve';
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=atecop_db;charset=utf8mb4','root','', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare('UPDATE administrador SET clavehash = :h WHERE usuario = :u');
    $stmt->execute([':h' => $hash, ':u' => 'mimi']);
    echo "UPDATED\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>