<?php
// authenticate.php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = getPost('identifier');
    $password = getPost('password');
    
    try {
        // APERTURA - Conexión ya abierta
        
        // CONSULTA: Buscar usuario por email o username
        $sql = "SELECT * FROM animelist_db_users WHERE email = ? OR username = ?";
        $stmt = $database->query($sql, [$identifier, $identifier]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login exitoso
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_id'] = $user['email'];
            
            // CIERRE implícito
            header('Location: panel.php');
            exit;
        } else {
            echo "<p style='color:red;'>Credenciales incorrectas.</p>";
            echo '<p><a href="login.php">Volver a login</a></p>';
        }
        
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error de conexión: " . $e->getMessage() . "</p>";
    }
} else {
    header('Location: login.php');
    exit;
}

// Función helper
function getPost($key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}
?>