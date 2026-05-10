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

// Создаем таблицу расписания, если не существует
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS schedule (
            id INT AUTO_INCREMENT PRIMARY KEY,
            day_of_week VARCHAR(20) NOT NULL,
            group_name VARCHAR(50) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME,
            type ENUM('lecture', 'practice', 'lab', 'seminar') NOT NULL,
            classroom VARCHAR(50),
            teacher VARCHAR(200),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_day (day_of_week),
            INDEX idx_group (group_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
} catch (Exception $e) {
    // Таблица может уже существовать
}

// Получение расписания
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $day = $_GET['day'] ?? null;
        $group = $_GET['group'] ?? null;
        
        $sql = "SELECT * FROM schedule WHERE 1=1";
        $params = [];
        
        if ($day) {
            $sql .= " AND day_of_week = :day";
            $params['day'] = $day;
        }
        if ($group) {
            $sql .= " AND group_name = :group";
            $params['group'] = $group;
        }
        
        $sql .= " ORDER BY day_of_week, start_time";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $schedule = $stmt->fetchAll();
        
        // Преобразуем тип в русский
        $typeMap = ['lecture' => 'Лекция', 'practice' => 'Практика', 'lab' => 'Лабораторная', 'seminar' => 'Семинар'];
        foreach ($schedule as &$item) {
            $item['type_ru'] = $typeMap[$item['type']] ?? $item['type'];
        }
        
        echo json_encode(['success' => true, 'data' => $schedule]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка получения расписания']);
    }
    exit;
}

// Добавление пары
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required = ['day_of_week', 'group_name', 'subject', 'start_time', 'type'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Заполните обязательные поля']);
            exit;
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO schedule (day_of_week, group_name, subject, start_time, end_time, type, classroom, teacher)
            VALUES (:day, :group, :subject, :start, :end, :type, :classroom, :teacher)
        ");
        
        $stmt->execute([
            'day' => $input['day_of_week'],
            'group' => $input['group_name'],
            'subject' => $input['subject'],
            'start' => $input['start_time'],
            'end' => $input['end_time'] ?? null,
            'type' => $input['type'],
            'classroom' => $input['classroom'] ?? null,
            'teacher' => $input['teacher'] ?? null
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Пара добавлена', 'id' => $pdo->lastInsertId()]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка добавления пары']);
    }
    exit;
}

// Обновление пары
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID пары не указан']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE schedule SET
                day_of_week = COALESCE(:day, day_of_week),
                group_name = COALESCE(:group, group_name),
                subject = COALESCE(:subject, subject),
                start_time = COALESCE(:start, start_time),
                end_time = COALESCE(:end, end_time),
                type = COALESCE(:type, type),
                classroom = COALESCE(:classroom, classroom),
                teacher = COALESCE(:teacher, teacher)
            WHERE id = :id
        ");
        
        $stmt->execute([
            'id' => $input['id'],
            'day' => $input['day_of_week'] ?? null,
            'group' => $input['group_name'] ?? null,
            'subject' => $input['subject'] ?? null,
            'start' => $input['start_time'] ?? null,
            'end' => $input['end_time'] ?? null,
            'type' => $input['type'] ?? null,
            'classroom' => $input['classroom'] ?? null,
            'teacher' => $input['teacher'] ?? null
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Пара обновлена']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка обновления пары']);
    }
    exit;
}

// Удаление пары
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID пары не указан']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM schedule WHERE id = :id");
        $stmt->execute(['id' => $input['id']]);
        
        echo json_encode(['success' => true, 'message' => 'Пара удалена']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Ошибка удаления пары']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
