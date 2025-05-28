<?php

class Database {
    private static $instance = null;
    private $pdo;
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $password;
    private $charset;

    private function __construct() {
        $config = require_once __DIR__ . '/../../config/database.php';

        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->dbname = $config['dbname'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->charset = $config['charset'];

        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            // In a real application, you'd log this error and show a user-friendly message.
            // For now, we'll just re-throw the exception during development.
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {}
}

?>
