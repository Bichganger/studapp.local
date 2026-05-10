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

// Создаем таблицу групп, если не существует
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS study_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            group_name VARCHAR(50) UNIQUE NOT NULL,
            specialty VARCHAR(100),
            course INT DEFAULT 1,
            student_count INT DEFAULT 0,
            head_of_department VARCHAR(200),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_group_name (group_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
} catch (Exception $e) {
    // Таблица может уже существовать
}

// Получение групп
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM study_groups ORDER BY group_name");
        $groups = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $groups]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка получения групп']);
    }
    exit;
}

// Создание группы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['group_name'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Название группы обязательно']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO study_groups (group_name, specialty, course, student_count, head_of_department)
            VALUES (:name, :specialty, :course, :count, :head)
        ");
        
        $stmt->execute([
            'name' => $input['group_name'],
            'specialty' => $input['specialty'] ?? null,
            'course' => $input['course'] ?? 1,
            'count' => $input['student_count'] ?? 0,
            'head' => $input['head_of_department'] ?? null
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Группа создана', 'id' => $pdo->lastInsertId()]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка создания группы: ' . $e->getMessage()]);
    }
    exit;
}

// Обновление группы
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID группы не указан']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE study_groups SET
                group_name = COALESCE(:name, group_name),
                specialty = COALESCE(:specialty, specialty),
                course = COALESCE(:course, course),
                student_count = COALESCE(:count, student_count),
                head_of_department = COALESCE(:head, head_of_department)
            WHERE id = :id
        ");
        
        $stmt->execute([
            'id' => $input['id'],
            'name' => $input['group_name'] ?? null,
            'specialty' => $input['specialty'] ?? null,
            'course' => $input['course'] ?? null,
            'count' => $input['student_count'] ?? null,
            'head' => $input['head_of_department'] ?? null
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Группа обновлена']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка обновления группы']);
    }
    exit;
}

// Удаление группы
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID группы не указан']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM study_groups WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        
        echo json_encode(['success' => true, 'message' => 'Группа удалена']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка удаления группы']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
