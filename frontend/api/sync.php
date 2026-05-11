<?php
// Основной API для синхронизации данных
header('Content-Type: application/json');
require_once '../config/db.php';

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
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $fullName = $_POST['full_name'] ?? '';
        $role = $_POST['role'] ?? 'student';
        $groupName = $_POST['group_name'] ?? null;
        $course = $_POST['course'] ?? 1;
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role, group_name, course) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $fullName, $role, $groupName, $course]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
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
        $course = $_POST['course'] ?? 1;
        
        $stmt = $pdo->prepare("INSERT INTO groups (name, specialty, course) VALUES (?, ?, ?)");
        $stmt->execute([$groupName, $specialty, $course]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
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
        
        $stmt = $pdo->prepare("INSERT INTO notifications (title, message, target_type, target_group) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $message, $targetType, $targetGroup]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
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
        
        $stmt = $pdo->prepare("INSERT INTO library (title, description, file_type, file_path, group_name, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $fileType, $filePath, $groupName, $userId]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
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
    
    echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
