<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Определяем путь к login.php в зависимости от расположения файла
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    
    // Проверяем, находится ли файл в подпапке
    if (strpos($scriptPath, '/teacher/') !== false || 
        strpos($scriptPath, '/student/') !== false || 
        strpos($scriptPath, '/admin/') !== false) {
        header("Location: ../login.php");
    } else {
        header("Location: login.php");
    }
    exit;
}
?>