<?php
// register.php
session_start();
require_once 'config.php';

function getPost($key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = getPost('username');
    $email = getPost('email');
    $password = getPost('password');
    
    $errors = [];
    
    // Validaciones básicas
    if (empty($username)) $errors[] = 'El nombre de usuario es obligatorio.';
    if (empty($email)) $errors[] = 'El correo es obligatorio.';
    if (empty($password)) $errors[] = 'La contraseña es obligatoria.';
    
    if (empty($errors)) {
        try {
            // APERTURA - La conexión ya está abierta en config.php
            
            // CONSULTA 1: Verificar si el usuario ya existe
            $sqlCheck = "SELECT email, username FROM animelist_db_users WHERE email = ? OR username = ?";
            $stmtCheck = $database->query($sqlCheck, [$email, $username]);
            $existingUser = $stmtCheck->fetch();
            
            if ($existingUser) {
                if ($existingUser['username'] === $username) {
                    $errors[] = 'Este usuario ya existe.';
                }
                if ($existingUser['email'] === $email) {
                    $errors[] = 'Este correo ya está registrado.';
                }
            }
            
            // CONSULTA 2: Insertar nuevo usuario si no hay errores
            if (empty($errors)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sqlInsert = "INSERT INTO animelist_db_users (email, username, password, state) VALUES (?, ?, ?, 1)";
                $success = $database->execute($sqlInsert, [$email, $username, $hashedPassword]);
                
                if ($success) {
                    // CIERRE - Se cierra automáticamente al terminar el script
                    header('Location: login.php');
                    exit;
                } else {
                    $errors[] = 'Error al registrar el usuario.';
                }
            }
            
        } catch (PDOException $e) {
            $errors[] = 'Error de base de datos: ' . $e->getMessage();
        }
    }
    
    // Mostrar errores si los hay
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        echo '<p><a href="index.php">Volver al registro</a></p>';
        exit;
    }
}

// Si no es POST, mostrar el formulario (redirigir a index)
header('Location: index.php');
exit;
?>