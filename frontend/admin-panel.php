<?php
session_start();
require_once 'protected/auth_guard.php';

// Проверяем, что пользователь — администратор
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

require_once '../frontend/config/db.php'; // Подключаем БД

// Получаем всех пользователей из базы
try {
    $stmt = $pdo->query("SELECT id, username, full_name, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $users = [];
    $error = "Ошибка загрузки пользователей: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка — StudApp</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Стили -->
    <style>
        body {
            background: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            padding: 20px 0;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #34495e;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        footer {
            margin-left: 250px;
            padding: 20px;
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-shield-lock"></i> Админка</h5>
    </div>
    <div class="list-group list-group-flush mx-3">
        <a href="admin-panel.php" class="list-group-item bg-transparent active"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="#" class="list-group-item bg-transparent disabled"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="#" class="list-group-item bg-transparent disabled"><i class="bi bi-graph-up me-2"></i>Аналитика</a>
        <a href="dashboard.php" class="list-group-item bg-transparent text-danger"><i class="bi bi-arrow-left me-2"></i>Назад в кабинет</a>
    </div>
</div>

<!-- Основной контент -->
<main class="main-content">
    <div class="container-fluid">
        <h2><i class="bi bi-people"></i> Управление пользователями</h2>
        <p class="text-muted">Всего пользователей: <?= count($users) ?></p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (empty($users)): ?>
            <div class="alert alert-info">Пока нет ни одного пользователя.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th>Логин</th>
                            <th>Роль</th>
                            <th>Дата регистрации</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $user['role'] === 'admin' ? 'bg-danger' : 
                                           ($user['role'] === 'teacher' ? 'bg-success' : 'bg-primary') ?>">
                                        <?= ['student' => 'Студент', 'teacher' => 'Преподаватель', 'admin' => 'Администратор'][$user['role']] ?>
                                    </span>
                                </td>
                                <td><?= date("d.m.Y", strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <a href="delete_user.php?id=<?= $user['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Удалить пользователя <?= htmlspecialchars($user['full_name']) ?>?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <small class="text-muted">—</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Подвал -->
<footer>
    &copy; 2024 StudApp. Админка для управления системой.
</footer>

</body>
</html>