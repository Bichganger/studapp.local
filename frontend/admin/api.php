<?php
session_start();
header('Content-Type: application/json');

require_once '../protected/auth_guard.php';

$role = $_SESSION['role'] ?? '';
require_once '../config/db.php';

$action = $_GET['action'] ?? '';

// === АДМИН ===
if ($role == 'admin') {
    // === ПОЛЬЗОВАТЕЛИ ===
    if ($action == 'get_users') {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
        echo json_encode(['users' => $stmt->fetchAll()]);
        exit;
    }

    if ($action == 'add_user' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $full_name = $_POST['full_name'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role_user = $_POST['role'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$full_name, $username, $password, $role_user]);
            echo json_encode(['success' => 1]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'ошибка']);
        }
        exit;
    }

    if ($action == 'delete_user' && $_GET['id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(['success' => 1]);
        exit;
    }

    // === ГРУППЫ ===
    if ($action == 'get_groups') {
        try {
            $stmt = $pdo->query("SELECT * FROM study_groups ORDER BY id DESC");
            echo json_encode(['groups' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['groups' => []]);
        }
        exit;
    }

    if ($action == 'add_group' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $stmt = $pdo->prepare("INSERT INTO study_groups (group_name, specialty, course) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['group_name'], $_POST['specialty'], $_POST['course']]);
            echo json_encode(['success' => 1]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'ошибка']);
        }
        exit;
    }

    if ($action == 'delete_group' && $_GET['id']) {
        $stmt = $pdo->prepare("DELETE FROM study_groups WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(['success' => 1]);
        exit;
    }

    // === РАСПИСАНИЕ ===
    if ($action == 'get_schedule') {
        try {
            $stmt = $pdo->query("SELECT * FROM schedule ORDER BY id DESC");
            echo json_encode(['schedule' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['schedule' => []]);
        }
        exit;
    }

    if ($action == 'add_schedule' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $stmt = $pdo->prepare("INSERT INTO schedule (day, group_name, subject, time_start, type, classroom, teacher) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['day'], $_POST['group'], $_POST['subject'], $_POST['time_start'], $_POST['type'], $_POST['classroom'], $_POST['teacher']]);
            echo json_encode(['success' => 1]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'ошибка']);
        }
        exit;
    }

    if ($action == 'delete_schedule' && $_GET['id']) {
        $stmt = $pdo->prepare("DELETE FROM schedule WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(['success' => 1]);
        exit;
    }

    // === БИБЛИОТЕКА ===
    if ($action == 'get_library') {
        try {
            $stmt = $pdo->query("SELECT * FROM library_works ORDER BY id DESC");
            echo json_encode(['works' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['works' => []]);
        }
        exit;
    }

    if ($action == 'approve_work' && $_GET['id']) {
        $stmt = $pdo->prepare("UPDATE library_works SET status = 'approved' WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(['success' => 1]);
        exit;
    }

    if ($action == 'reject_work' && $_GET['id']) {
        $stmt = $pdo->prepare("UPDATE library_works SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(['success' => 1]);
        exit;
    }

    if ($action == 'delete_work' && $_GET['id']) {
        $stmt = $pdo->prepare("DELETE FROM library_works WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(['success' => 1]);
        exit;
    }

    // === УВЕДОМЛЕНИЯ ===
    if ($action == 'send_notification' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $stmt = $pdo->prepare("INSERT INTO notifications (title, message, target_type) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['title'], $_POST['message'], $_POST['target_type']]);
            echo json_encode(['success' => 1]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'ошибка']);
        }
        exit;
    }

    if ($action == 'get_notifications') {
        try {
            $stmt = $pdo->query("SELECT * FROM notifications ORDER BY id DESC LIMIT 50");
            echo json_encode(['notifications' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['notifications' => []]);
        }
        exit;
    }

    // === НАСТРОЙКИ ===
    if ($action == 'get_settings') {
        try {
            $stmt = $pdo->query("SELECT * FROM system_settings");
            $settings = [];
            foreach ($stmt->fetchAll() as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            echo json_encode(['settings' => $settings]);
        } catch (Exception $e) {
            echo json_encode(['settings' => []]);
        }
        exit;
    }

    if ($action == 'save_settings' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            foreach ($_POST as $key => $value) {
                $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$key, $value, $value]);
            }
            echo json_encode(['success' => 1]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'ошибка']);
        }
        exit;
    }
}

// === СТУДЕНТ ===
if ($role == 'student') {
    // moje schet studenta
    if ($action == 'get_my_schedule') {
        $stmt = $pdo->query("SELECT * FROM schedule WHERE group_name IN (SELECT group_name FROM users WHERE id = " . (int)$_SESSION['user_id'] . ") ORDER BY id DESC");
        echo json_encode(['schedule' => $stmt->fetchAll()]);
        exit;
    }
    
    if ($action == 'get_my_grades') {
        try {
            $stmt = $pdo->query("SELECT * FROM grades WHERE user_id = " . (int)$_SESSION['user_id'] . " ORDER BY id DESC");
            echo json_encode(['grades' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['grades' => []]);
        }
        exit;
    }
}

// === ПРЕПОДАВАТЕЛЬ ===
if ($role == 'teacher') {
    if ($action == 'get_teacher_groups') {
        try {
            $stmt = $pdo->query("SELECT DISTINCT group_name FROM schedule WHERE teacher = '" . $_SESSION['full_name'] . "'");
            echo json_encode(['groups' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            echo json_encode(['groups' => []]);
        }
        exit;
    }
    
    if ($action == 'add_grade' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $stmt = $pdo->prepare("INSERT INTO grades (user_id, subject, grade, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['user_id'], $_POST['subject'], $_POST['grade'], $_POST['comment']]);
            echo json_encode(['success' => 1]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'ошибка']);
        }
        exit;
    }
}

echo json_encode(['error' => 'нет доступа или неизвестное действие']);
