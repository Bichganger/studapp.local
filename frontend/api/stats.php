<?php
// API для получения статистики с главной страницы
header('Content-Type: application/json');
require_once '../config/db.php';

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'stats':
            // Подсчет пользователей
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
            $users = $stmt->fetch()['total'];
            
            // Подсчет групп (из users по group_name)
            $stmt = $pdo->query("SELECT COUNT(DISTINCT group_name) as total FROM users WHERE group_name IS NOT NULL AND group_name != ''");
            $groups = $stmt->fetch()['total'];
            
            // Подсчет работ (из library_works)
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM library_works");
            $assignments = $stmt->fetch()['total'];
            
            // Подсчет уведомлений
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM notifications");
            $notifications = $stmt->fetch()['total'];
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'users' => $users,
                    'groups' => $groups,
                    'assignments' => $assignments,
                    'notifications' => $notifications
                ]
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
