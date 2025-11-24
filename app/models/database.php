<?php
class Database {
    public $pdo;


    public function __construct(){
        $cfg = require __DIR__ . '/../../config/config.php';
        $db = $cfg['db'];

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $db['host'], $db['port'], $db['dbname']);

        try {
            $this->pdo = new PDO($dsn, $db['user'], $db['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        } catch (PDOException $e) {
            // En producción no muestres $e->getMessage(); lo logueas
            die('DB Connection failed: ' . $e->getMessage());
        }
    }
}