<?php
// Основной API для синхронизации данных
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверка подключения к БД
try {
    require_once '../config/db.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'DB Connection: ' . $e->getMessage()]);
    exit;
}

session_start();

$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['role'] ?? null;
$userGroup = null;

// Получаем группу пользователя если это студент
if ($userId && $userRole === 'student') {
    $stmt = $pdo->prepare("SELECT group_name FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userGroup = $stmt->fetch()['group_name'] ?? null;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    // === ПОЛЬЗОВАТЕЛИ ===
    if ($action === 'get_users') {
        if ($userRole !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $stmt = $pdo->query("SELECT id, username, full_name, role, group_name, course, email, created_at FROM users ORDER BY id DESC");
        $users = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $users]);
        exit;
    }
    
    if ($action === 'add_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $username = $_POST['username'] ?? '';
        $plainPassword = $_POST['password'] ?? '';
        if (!$plainPassword) {
            echo json_encode(['success' => false, 'error' => 'Пароль обязателен']);
            exit;
        }
        $password = password_hash($plainPassword, PASSWORD_DEFAULT);
        $fullName = $_POST['full_name'] ?? '';
        $role = $_POST['role'] ?? 'student';
        $groupName = $_POST['group'] ?? $_POST['group_name'] ?? null;
        if ($groupName === '') $groupName = null;
        $course = $_POST['course'] ?? 1;
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role, group_name, course) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $fullName, $role, $groupName, $course]);
        $newId = $pdo->lastInsertId();
        // Обновить счетчик студентов в группе
        if ($groupName && $role === 'student') {
            $pdo->prepare("UPDATE groups SET student_count = (SELECT COUNT(*) FROM users WHERE group_name = ? AND role = 'student') WHERE name = ?")->execute([$groupName, $groupName]);
        }
        echo json_encode(['success' => true, 'id' => $newId]);
        exit;
    }
    
    if ($action === 'update_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID не указан']);
            exit;
        }
        $username = $_POST['username'] ?? '';
        $fullName = $_POST['full_name'] ?? '';
        $role = $_POST['role'] ?? 'student';
        $groupName = $_POST['group'] ?? $_POST['group_name'] ?? null;
        if ($groupName === '') $groupName = null;

        $fields = [];
        $params = [];
        if ($username) { $fields[] = "username = ?"; $params[] = $username; }
        if ($fullName) { $fields[] = "full_name = ?"; $params[] = $fullName; }
        if ($role) { $fields[] = "role = ?"; $params[] = $role; }
        $fields[] = "group_name = ?"; $params[] = $groupName;

        $plainPassword = $_POST['password'] ?? '';
        if ($plainPassword) {
            $fields[] = "password = ?";
            $params[] = password_hash($plainPassword, PASSWORD_DEFAULT);
        }

        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        // Обновить счетчики для старой и новой группы
        $stmt = $pdo->prepare("SELECT group_name, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $u = $stmt->fetch();
        if ($u && $u['role'] === 'student') {
            $oldGroup = $u['group_name'];
            if ($oldGroup) {
                $pdo->prepare("UPDATE groups SET student_count = (SELECT COUNT(*) FROM users WHERE group_name = ? AND role = 'student') WHERE name = ?")->execute([$oldGroup, $oldGroup]);
            }
            if ($groupName && $groupName !== $oldGroup) {
                $pdo->prepare("UPDATE groups SET student_count = (SELECT COUNT(*) FROM users WHERE group_name = ? AND role = 'student') WHERE name = ?")->execute([$groupName, $groupName]);
            }
        }
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($action === 'delete_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID не указан']);
            exit;
        }
        // Получить группу перед удалением для обновления счетчика
        $stmt = $pdo->prepare("SELECT group_name, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $u = $stmt->fetch();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        if ($u && $u['role'] === 'student' && $u['group_name']) {
            $pdo->prepare("UPDATE groups SET student_count = (SELECT COUNT(*) FROM users WHERE group_name = ? AND role = 'student') WHERE name = ?")->execute([$u['group_name'], $u['group_name']]);
        }
        echo json_encode(['success' => true]);
        exit;
    }
    
    // === ГРУППЫ ===
    if ($action === 'get_groups') {
        $stmt = $pdo->query("SELECT * FROM groups ORDER BY id DESC");
        $groups = $stmt->fetchAll();
        foreach ($groups as &$group) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE group_name = ? AND role = 'student'");
            $stmt->execute([$group['name']]);
            $group['student_count'] = $stmt->fetch()['count'];
        }
        echo json_encode(['success' => true, 'data' => $groups]);
        exit;
    }
    
    if ($action === 'add_group' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $groupName = $_POST['name'] ?? $_POST['group_name'] ?? '';
        $specialty = $_POST['specialty'] ?? '';
        $course = (int)($_POST['course'] ?? 1);

        $stmt = $pdo->prepare("INSERT INTO groups (name, specialty, course) VALUES (?, ?, ?)");
        $stmt->execute([$groupName, $specialty, $course]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
    
    if ($action === 'update_group' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID не указан']);
            exit;
        }
        $name = $_POST['name'] ?? '';
        $specialty = $_POST['specialty'] ?? '';
        $course = (int)($_POST['course'] ?? 1);

        $stmt = $pdo->prepare("UPDATE groups SET name = ?, specialty = ?, course = ? WHERE id = ?");
        $stmt->execute([$name, $specialty, $course, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($action === 'delete_group' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID не указан']);
            exit;
        }
        // Получить название группы перед удалением
        $stmt = $pdo->prepare("SELECT name FROM groups WHERE id = ?");
        $stmt->execute([$id]);
        $g = $stmt->fetch();
        if ($g) {
            // Сбросить группу у пользователей
            $pdo->prepare("UPDATE users SET group_name = NULL WHERE group_name = ?")->execute([$g['name']]);
        }
        $stmt = $pdo->prepare("DELETE FROM groups WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    // === УВЕДОМЛЕНИЯ ===
    if ($action === 'get_notifications') {
        $stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 50");
        $notifications = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $notifications]);
        exit;
    }
    
    if ($action === 'add_notification' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $title = $_POST['title'] ?? '';
        $message = $_POST['message'] ?? '';
        $targetType = $_POST['target_type'] ?? 'all';
        $targetGroup = $_POST['target_group'] ?? null;
        if ($targetGroup === '') $targetGroup = null;
        
        $stmt = $pdo->prepare("INSERT INTO notifications (title, message, target_type, target_group, sender_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $message, $targetType, $targetGroup, $userId]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
    
    if ($action === 'update_notification' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID не указан']);
            exit;
        }
        $title = $_POST['title'] ?? '';
        $message = $_POST['message'] ?? '';
        $targetType = $_POST['target_type'] ?? 'all';
        $targetGroup = $_POST['target_group'] ?? null;
        if ($targetGroup === '') $targetGroup = null;

        $stmt = $pdo->prepare("UPDATE notifications SET title = ?, message = ?, target_type = ?, target_group = ? WHERE id = ?");
        $stmt->execute([$title, $message, $targetType, $targetGroup, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($action === 'delete_notification' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID не указан']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    // === РАСПИСАНИЕ ===
    if ($action === 'get_schedule') {
        if ($userGroup) {
            $stmt = $pdo->prepare("SELECT * FROM schedule WHERE group_name = ? ORDER BY FIELD(day_of_week, 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'), start_time");
            $stmt->execute([$userGroup]);
        } else {
            $stmt = $pdo->query("SELECT * FROM schedule ORDER BY FIELD(day_of_week, 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'), start_time");
        }
        $schedule = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $schedule]);
        exit;
    }
    
    if ($action === 'add_schedule' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $day = $_POST['day_of_week'] ?? $_POST['day'] ?? '';
        $groupName = $_POST['group_name'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $teacher = $_POST['teacher_name'] ?? $_POST['teacher'] ?? '';
        $timeStart = $_POST['start_time'] ?? $_POST['time_start'] ?? '09:00:00';
        $timeEnd = $_POST['end_time'] ?? '10:30:00';
        $classroom = $_POST['classroom'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO schedule (day_of_week, group_name, subject, teacher_name, start_time, end_time, classroom) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$day, $groupName, $subject, $teacher, $timeStart, $timeEnd, $classroom]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
    
    if ($action === 'update_schedule' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID не указан']);
            exit;
        }
        $day = $_POST['day_of_week'] ?? '';
        $groupName = $_POST['group_name'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $teacher = $_POST['teacher_name'] ?? '';
        $timeStart = $_POST['start_time'] ?? '09:00:00';
        $timeEnd = $_POST['end_time'] ?? '10:30:00';
        $classroom = $_POST['classroom'] ?? '';

        $stmt = $pdo->prepare("UPDATE schedule SET day_of_week = ?, group_name = ?, subject = ?, teacher_name = ?, start_time = ?, end_time = ?, classroom = ? WHERE id = ?");
        $stmt->execute([$day, $groupName, $subject, $teacher, $timeStart, $timeEnd, $classroom, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($action === 'delete_schedule' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM schedule WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    // === ОЦЕНКИ ===
    if ($action === 'get_grades') {
        if ($userGroup) {
            $stmt = $pdo->prepare("SELECT g.*, u.full_name as teacher_name FROM grades g LEFT JOIN users u ON g.teacher_id = u.id WHERE g.student_id = ? ORDER BY g.date DESC");
            $stmt->execute([$userId]);
        } else {
            $stmt = $pdo->query("SELECT g.*, u.full_name as teacher_name FROM grades g LEFT JOIN users u ON g.teacher_id = u.id ORDER BY g.date DESC");
        }
        $grades = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $grades]);
        exit;
    }
    
    if ($action === 'add_grade' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $studentId = $_POST['student_id'] ?? 0;
        $studentName = $_POST['student_name'] ?? '';
        $groupName = $_POST['group_name'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $grade = $_POST['grade'] ?? '';
        $date = $_POST['date'] ?? date('Y-m-d');
        
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, student_name, group_name, subject, grade, date, teacher_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studentId, $studentName, $groupName, $subject, $grade, $date, $userId]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
    
    // === ЗАДАНИЯ/РАБОТЫ ===
    if ($action === 'get_assignments') {
        if ($userRole === 'student') {
            $stmt = $pdo->prepare("SELECT * FROM assignments WHERE student_id = ? ORDER BY submitted_at DESC");
            $stmt->execute([$userId]);
        } else {
            $stmt = $pdo->query("SELECT * FROM assignments ORDER BY submitted_at DESC");
        }
        $assignments = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $assignments]);
        exit;
    }
    
    if ($action === 'add_assignment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentId = $_SESSION['user_id'] ?? 0;
        $studentName = $_SESSION['full_name'] ?? '';
        $groupName = $userGroup ?? '';
        $subject = $_POST['subject'] ?? '';
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $filePath = $_POST['file_path'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO assignments (student_id, student_name, group_name, subject, title, description, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studentId, $studentName, $groupName, $subject, $title, $description, $filePath]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
    
    if ($action === 'update_assignment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? 'pending';
        $comment = $_POST['teacher_comment'] ?? $_POST['comment'] ?? '';
        $grade = $_POST['grade'] ?? null;
        
        $stmt = $pdo->prepare("UPDATE assignments SET status = ?, teacher_comment = ?, grade = ?, checked_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $comment, $grade, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    // === БИБЛИОТЕКА ===
    if ($action === 'get_library') {
        $stmt = $pdo->query("SELECT * FROM library WHERE is_public = 1 ORDER BY created_at DESC");
        $library = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $library]);
        exit;
    }
    
    if ($action === 'add_library' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $fileType = $_POST['file_type'] ?? 'document';
        $filePath = $_POST['file_path'] ?? '';
        $groupName = $_POST['group_name'] ?? null;
        if ($groupName === '') $groupName = null;
        
        $stmt = $pdo->prepare("INSERT INTO library (title, description, file_type, file_path, group_name, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $fileType, $filePath, $groupName, $userId]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
    
    if ($action === 'update_library' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID не указан']);
            exit;
        }
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $fileType = $_POST['file_type'] ?? 'document';
        $groupName = $_POST['group_name'] ?? null;
        if ($groupName === '') $groupName = null;

        $stmt = $pdo->prepare("UPDATE library SET title = ?, description = ?, file_type = ?, group_name = ? WHERE id = ?");
        $stmt->execute([$title, $description, $fileType, $groupName, $id]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($action === 'delete_library' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID не указан']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM library WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    // === ЖУРНАЛ ПОСЕЩАЕМОСТИ ===
    if ($action === 'get_journal') {
        if ($userGroup) {
            $stmt = $pdo->prepare("SELECT * FROM journal WHERE group_name = ? ORDER BY date DESC LIMIT 50");
            $stmt->execute([$userGroup]);
        } else {
            $stmt = $pdo->query("SELECT * FROM journal ORDER BY date DESC LIMIT 50");
        }
        $journal = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $journal]);
        exit;
    }
    
    if ($action === 'add_journal' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
            exit;
        }
        $studentId = $_POST['student_id'] ?? 0;
        $studentName = $_POST['student_name'] ?? '';
        $groupName = $_POST['group_name'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $date = $_POST['date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'present';
        
        $stmt = $pdo->prepare("INSERT INTO journal (student_id, student_name, group_name, subject, date, status, teacher_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studentId, $studentName, $groupName, $subject, $date, $status, $userId]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
    
    // === НАСТРОЙКИ ДОСТУПНОСТИ ===
    if ($action === 'save_accessibility' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
            exit;
        }
        $settings = $_POST['settings'] ?? '';
        if (!empty($settings)) {
            $stmt = $pdo->prepare("UPDATE users SET accessibility_settings = ? WHERE id = ?");
            $stmt->execute([$settings, $userId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Пустые настройки']);
        }
        exit;
    }
    
    if ($action === 'get_accessibility') {
        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT accessibility_settings FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        $settings = $result['accessibility_settings'] ? json_decode($result['accessibility_settings'], true) : null;
        echo json_encode(['success' => true, 'data' => $settings]);
        exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
