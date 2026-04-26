<?php
session_start();
require_once 'protected/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

require_once 'config/db.php';

// === Обработка удаления пользователя ===
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Нельзя удалить себя
    if ($id === $_SESSION['user_id']) {
        header("Location: admin-panel.php?error=Нельзя удалить свой аккаунт");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            header("Location: admin-panel.php?error=Пользователь не найден");
            exit;
        }

        if ($user['role'] === 'admin') {
            header("Location: admin-panel.php?error=Нельзя удалить другого администратора");
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: admin-panel.php?success=Пользователь удалён");
        exit;

    } catch (Exception $e) {
        header("Location: admin-panel.php?error=Ошибка сервера");
        exit;
    }
}

// === Обработка добавления пользователя ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($full_name) || empty($username) || empty($password)) {
        header("Location: admin-panel.php?error=Заполните все поля");
        exit;
    }

    if (!in_array($role, ['student', 'teacher'])) {
        header("Location: admin-panel.php?error=Недопустимая роль");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            header("Location: admin-panel.php?error=Логин уже занят");
            exit;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashed, $role, $full_name]);

        header("Location: admin-panel.php?success=Пользователь добавлен");
        exit;

    } catch (Exception $e) {
        header("Location: admin-panel.php?error=Ошибка сервера");
        exit;
    }
}

// === Статистика ===
try {
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $studentCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
    $teacherCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
} catch (Exception $e) {
    $userCount = $studentCount = $teacherCount = 0;
}

// Получаем пользователей
try {
    $stmt = $pdo->query("SELECT id, full_name, username, role, created_at FROM users ORDER BY role DESC, full_name");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $users = [];
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
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            width: 260px;
            left: 0;
            top: 0;
            padding: 20px 0;
        }
        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            margin: 5px 10px;
            border-radius: 5px;
            display: block;
            padding: 8px 15px;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #34495e;
            color: white;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        .tab-content { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        footer { margin-left: 260px; padding: 20px; text-align: center; font-size: 0.9rem; color: #6c757d; }
        .card-icon { font-size: 2rem; color: #6c757d; }
    </style>
</head>
<body>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-shield-lock"></i> Админка</h5>
    </div>
    <nav>
        <a href="#" class="active" data-bs-toggle="tab" data-bs-target="#users"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="#" data-bs-toggle="tab" data-bs-target="#schedule"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="#" data-bs-toggle="tab" data-bs-target="#groups"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="#" data-bs-toggle="tab" data-bs-target="#library"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="#" data-bs-toggle="tab" data-bs-target="#notifications"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="#" data-bs-toggle="tab" data-bs-target="#settings"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr class="mx-3">
        <a href="../frontend/dashboard.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Назад</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <div class="tab-content">

        <!-- Вкладка: Пользователи -->
<div class="tab-pane fade show active" id="users">
    <h2><i class="bi bi-people"></i> Управление пользователями</h2>

    <!-- Статистика -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6><i class="bi bi-people"></i> Всего</h6>
                    <p class="display-6 mb-0"><?= $userCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6><i class="bi bi-mortarboard"></i> Студенты</h6>
                    <p class="display-6 mb-0"><?= $studentCount ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6><i class="bi bi-person-badge"></i> Преподаватели</h6>
                    <p class="display-6 mb-0"><?= $teacherCount ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Форма добавления -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Добавить пользователя</h5>
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">ФИО *</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Логин *</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Пароль *</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Роль</label>
                    <select name="role" class="form-select">
                        <option value="student">Студент</option>
                        <option value="teacher">Преподаватель</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">➕ Добавить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица пользователей -->
    <h5><i class="bi bi-list"></i> Список пользователей</h5>
    <?php if (empty($users)): ?>
        <div class="alert alert-info">Нет пользователей.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
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
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td>
                                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'teacher' ? 'success' : 'primary') ?>">
                                    <?= ['student' => 'Студент', 'teacher' => 'Преподаватель', 'admin' => 'Администратор'][$user['role']] ?>
                                </span>
                            </td>
                            <td><?= date("d.m.Y", strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <a href="?delete=<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Удалить <?= addslashes($user['full_name']) ?>?')">
                                        <i class="bi bi-trash"></i> Удалить
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

        <!-- Вкладка: Расписание -->
        <div class="tab-pane fade" id="schedule">
            <h2><i class="bi bi-calendar"></i> Управление расписанием</h2>
            <p class="text-muted">Добавьте пары, выбрав день и группу.</p>
            <form class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">День</label>
                        <select class="form-select">
                            <option>Понедельник</option>
                            <option>Вторник</option>
                            <option>Среда</option>
                            <option>Четверг</option>
                            <option>Пятница</option>
                            <option>Суббота</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Группа</label>
                        <select class="form-select">
                            <option>ИТ-321</option>
                            <option>ЭК-214</option>
                            <option>ПС-133</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Время</label>
                        <input type="time" class="form-control">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">Добавить</button>
                    </div>
                </div>
            </form>
            <div class="alert alert-light">Здесь будет таблица расписания (можно добавить позже).</div>
        </div>

        <!-- Вкладка: Группы -->
        <div class="tab-pane fade" id="groups">
            <h2><i class="bi bi-people-fill"></i> Управление группами</h2>
            <p class="text-muted">Создавайте и редактируйте учебные группы.</p>
            <form class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Название группы *</label>
                        <input type="text" class="form-control" placeholder="ИТ-321">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Специальность *</label>
                        <input type="text" class="form-control" placeholder="Информационные технологии">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Создать группу</button>
                    </div>
                </div>
            </form>
            <div class="alert alert-light">Список групп появится здесь.</div>
        </div>

        <!-- Вкладка: Библиотека -->
        <div class="tab-pane fade" id="library">
            <h2><i class="bi bi-journal"></i> Модерация библиотеки работ</h2>
            <p class="text-muted">Проверяйте и одобряйте загруженные студентами материалы.</p>
            <div class="alert alert-warning">Нет новых материалов для модерации.</div>
        </div>

        <!-- Вкладка: Рассылка -->
        <div class="tab-pane fade" id="notifications">
            <h2><i class="bi bi-bell"></i> Отправка уведомлений</h2>
            <p class="text-muted">Отправляйте сообщения всем или отдельным группам.</p>
            <form>
                <div class="mb-3">
                    <label class="form-label">Кому отправить?</label>
                    <select class="form-select">
                        <option>Всем студентам</option>
                        <option>Группе ИТ-321</option>
                        <option>Преподавателям</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Текст сообщения *</label>
                    <textarea class="form-control" rows="4" placeholder="Введите текст уведомления..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div>

        <!-- Вкладка: Настройки -->
        <div class="tab-pane fade" id="settings">
            <h2><i class="bi bi-gear"></i> Настройки системы</h2>
            <p class="text-muted">Общие параметры работы платформы.</p>
            <form>
                <div class="mb-3">
                    <label class="form-label">Название системы</label>
                    <input type="text" class="form-control" value="StudApp">
                </div>
                <div class="mb-3">
                    <label class="form-label">Режим обслуживания</label>
                    <select class="form-select">
                        <option>Выключен</option>
                        <option>Включён</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Сохранить настройки</button>
            </form>
        </div>

    </div>
</main>

<!-- Модальное окно: Добавить пользователя -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="admin_add_user.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Добавить пользователя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ФИО *</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Логин *</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Пароль *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Роль</label>
                        <select name="role" class="form-select">
                            <option value="student">Студент</option>
                            <option value="teacher">Преподаватель</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Подвал -->
<footer>
    &copy; 2024 StudApp. Админ-панель в одном файле.
</footer>

<!-- Скрипты -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>