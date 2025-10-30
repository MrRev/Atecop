<?php
/**
 * Clase Database - Gestión de Conexión a Base de Datos
 * 
 * Implementa el patrón Singleton para manejar una única instancia
 * de conexión PDO a la base de datos MySQL.
 */

class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Constructor privado para implementar Singleton
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
    
    /**
     * Obtiene la instancia única de Database (Singleton)
     * 
     * @return Database Instancia única
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtiene la conexión PDO
     * 
     * @return PDO Objeto de conexión PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Maneja errores de conexión
     * 
     * @param PDOException $e Excepción capturada
     */
    private function handleError($e) {
        if (ENVIRONMENT === 'development') {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        } else {
            error_log("Database Error: " . $e->getMessage());
            die("Error al conectar con la base de datos. Por favor, contacte al administrador.");
        }
    }
    
    /**
     * Prevenir clonación del objeto
     */
    private function __clone() {}
    
    /**
     * Prevenir deserialización del objeto
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton.");
    }
}
