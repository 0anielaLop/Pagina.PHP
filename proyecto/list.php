<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anime_id = $_POST['anime_id'];
    $status = $_POST['status']; 
    $progress = $_POST['progress'];
    $p_score = $_POST['p_score'];
    $email = $_SESSION['email'];
    
    try {
        // Verificar si el anime ya está en la lista del usuario
        $checkSql = "SELECT id_list FROM animelist_db_list WHERE email = ? AND id_anime = ?";
        $checkStmt = $database->query($checkSql, [$email, $anime_id]);
        $existing = $checkStmt->fetch();
        
        if ($existing) {
            // UPDATE si ya existe
            $updateSql = "UPDATE animelist_db_list SET status = ?, progress = ?, p_score = ?, fecha_agregado = NOW() WHERE id_list = ?";
            $database->execute($updateSql, [$status, $progress, $p_score, $existing['id_list']]);
            $message = "List updated successfully!";
        } else {
            // INSERT si es nuevo
            $insertSql = "INSERT INTO animelist_db_list (email, id_anime, status, progress, p_score, state, fecha_agregado) VALUES (?, ?, ?, ?, ?, 1, NOW())";
            $database->execute($insertSql, [$email, $anime_id, $status, $progress, $p_score]);
            $message = "Anime added to your list!";
        }
        
        $_SESSION['success_message'] = $message;
        header('Location: panel.php');
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}