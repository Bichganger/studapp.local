<?php
session_start();
header('Content-Type: application/json');

require_once '../../protected/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit;
}

require_once '../../config/db.php';

// Создаем таблицу настроек, если не существует
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS system_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
} catch (Exception $e) {
    // Таблица может уже существовать
}

// Получение настроек
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
        $settings = $stmt->fetchAll();
        $result = [];
        foreach ($settings as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }
        echo json_encode(['success' => true, 'data' => $result]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка получения настроек']);
    }
    exit;
}

// Сохранение настроек
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['settings']) || !is_array($input['settings'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Неверный формат данных']);
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        foreach ($input['settings'] as $key => $value) {
            $key = preg_replace('/[^a-z_]/', '', $key); // Sanitize key
            
            $stmt = $pdo->prepare("
                INSERT INTO system_settings (setting_key, setting_value) 
                VALUES (:key, :value)
                ON DUPLICATE KEY UPDATE setting_value = :value
            ");
            $stmt->execute(['key' => $key, 'value' => $value]);
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Настройки сохранены']);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка сохранения настроек: ' . $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
