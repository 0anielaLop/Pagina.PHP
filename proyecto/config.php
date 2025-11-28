<?php
// config.php
class Database {
    private $pdo;
    private $host = "localhost";
    private $dbname = "animelist_db";
    private $usuario = "root";
    private $password = "";

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->usuario, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Método para obtener la conexión
    public function getConnection() {
        return $this->pdo;
    }

    // Método para cerrar la conexión
    public function closeConnection() {
        $this->pdo = null;
    }

    // Método genérico para ejecutar consultas SELECT
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Error en consulta: " . $e->getMessage());
        }
    }

    // Método para INSERT, UPDATE, DELETE
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            die("Error en ejecución: " . $e->getMessage());
        }
    }
}

// Crear instancia global de la base de datos
$database = new Database();
?>