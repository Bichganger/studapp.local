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

// Создаем таблицу уведомлений, если не существует
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            target_type ENUM('all', 'students', 'teachers', 'group') DEFAULT 'all',
            target_group VARCHAR(50),
            send_email TINYINT(1) DEFAULT 0,
            status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
            sent_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
} catch (Exception $e) {
    // Таблица может уже существовать
}

// Получение истории уведомлений
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 100");
        $notifications = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $notifications]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка получения уведомлений']);
    }
    exit;
}

// Отправка уведомления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required = ['title', 'message', 'target_type'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Заполните обязательные поля']);
            exit;
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (title, message, target_type, target_group, send_email)
            VALUES (:title, :message, :target_type, :target_group, :send_email)
        ");
        
        $stmt->execute([
            'title' => $input['title'],
            'message' => $input['message'],
            'target_type' => $input['target_type'],
            'target_group' => $input['target_type'] === 'group' ? ($input['target_group'] ?? null) : null,
            'send_email' => !empty($input['send_email']) ? 1 : 0
        ]);
        
        $notificationId = $pdo->lastInsertId();
        
        // Обновляем статус на отправленный (симуляция)
        $stmt = $pdo->prepare("
            UPDATE notifications SET status = 'sent', sent_at = NOW() WHERE id = :id
        ");
        $stmt->execute(['id' => $notificationId]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Уведомление отправлено', 
            'id' => $notificationId
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка отправки уведомления']);
    }
    exit;
}

// Просмотр уведомления
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'view') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID уведомления не указан']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        $notification = $stmt->fetch();
        
        if (!$notification) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Уведомление не найдено']);
            exit;
        }
        
        echo json_encode(['success' => true, 'data' => $notification]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка получения уведомления']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
