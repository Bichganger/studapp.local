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

// Создаем таблицу работ, если не существует
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS library_works (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            work_type ENUM('lab', 'coursework', 'essay') NOT NULL,
            subject VARCHAR(100),
            student_id INT,
            student_name VARCHAR(255),
            group_name VARCHAR(50),
            file_path VARCHAR(255),
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            reviewed_by INT,
            reviewed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_type (work_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
} catch (Exception $e) {
    // Таблица может уже существовать
}

// Получение работ
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $status = $_GET['status'] ?? null;
        $type = $_GET['type'] ?? null;
        $subject = $_GET['subject'] ?? null;
        
        $sql = "SELECT * FROM library_works WHERE 1=1";
        $params = [];
        
        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }
        if ($type) {
            $sql .= " AND work_type = :type";
            $params['type'] = $type;
        }
        if ($subject) {
            $sql .= " AND subject = :subject";
            $params['subject'] = $subject;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $works = $stmt->fetchAll();
        
        // Преобразуем типы
        $typeMap = ['lab' => 'Лабораторная', 'coursework' => 'Курсовая', 'essay' => 'Реферат'];
        foreach ($works as &$work) {
            $work['type_ru'] = $typeMap[$work['work_type']] ?? $work['work_type'];
        }
        
        echo json_encode(['success' => true, 'data' => $works]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка получения работ']);
    }
    exit;
}

// Одобрение работы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'approve') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID работы не указан']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE library_works 
            SET status = 'approved', reviewed_by = :reviewer, reviewed_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            'reviewer' => $_SESSION['user_id'],
            'id' => $input['id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Работа одобрена']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка одобрения работы']);
    }
    exit;
}

// Отклонение работы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'reject') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID работы не указан']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE library_works 
            SET status = 'rejected', reviewed_by = :reviewer, reviewed_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            'reviewer' => $_SESSION['user_id'],
            'id' => $input['id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Работа отклонена']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка отклонения работы']);
    }
    exit;
}

// Удаление работы
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID работы не указан']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM library_works WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        
        echo json_encode(['success' => true, 'message' => 'Работа удалена']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка удаления работы']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
