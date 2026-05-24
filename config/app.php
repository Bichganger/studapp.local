<?php
/**
 * Конфигурация приложения
 * Автоматически определяет базовый URL
 */

// Автоматическое определение базового URL
if (!function_exists('getBaseUrl')) {
    function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
                     (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Получаем путь до каталога проекта
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $path = dirname($scriptName);
        
        // Убираем начальный и конечный слэш для правильного пути
        $path = rtrim($path, '/');
        if ($path === '' || $path === '/') {
            $path = '';
        }
        
        return $protocol . $host . $path;
    }
}

// Константа базового URL - проверяем, не определена ли уже
if (!defined('BASE_URL')) {
    define('BASE_URL', getBaseUrl());
}

// Путь к корню проекта
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', __DIR__ . '/..');
}

// Путь к uploads
if (!defined('UPLOADS_PATH')) {
    define('UPLOADS_PATH', PROJECT_ROOT . '/uploads');
}
if (!defined('UPLOADS_URL')) {
    define('UPLOADS_URL', BASE_URL . '/uploads');
}

// Настройки сессии - ТОЛЬКО ОДИН РАЗ!
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
