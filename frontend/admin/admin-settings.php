<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$name = explode(' ', $_SESSION['full_name'])[0] ?? $_SESSION['full_name'];
$fullName = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Настройки — Админка — Учёба.Онлайн</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: sans-serif; }
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
        footer { margin-left: 260px; padding: 20px; text-align: center; font-size: 0.9rem; color: #6c757d; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<!-- Боковое меню -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h5><i class="bi bi-shield-lock"></i> Админка</h5>
        <p class="text-white-50 small">Доступ: полный</p>
    </div>
    <nav>
        <a href="admin-dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="admin-users.php"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="admin-schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="admin-groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="admin-library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="admin-notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="admin-settings.php" class="active"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr class="mx-3">
        <a href="../logout.php" class="text-danger"><i class="bi bi-arrow-left me-2"></i>Выход</a>
    </nav>
</div>

<!-- Основной контент -->
<main class="main-content">
    <a href="admin-dashboard.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Назад
    </a>

    <h2><i class="bi bi-gear"></i> Настройки системы</h2>
    <p class="text-muted">Общие параметры работы платформы.</p>

    <!-- Общие настройки -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Общие настройки</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label">Название системы</label>
                    <input type="text" class="form-control" value="Учёба.Онлайн">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email для уведомлений</label>
                    <input type="email" class="form-control" value="admin@ucheba.online">
                </div>
                <div class="mb-3">
                    <label class="form-label">Часовой пояс</label>
                    <select class="form-select">
                        <option>MSK (Москва)</option>
                        <option>MSK+1</option>
                        <option>MSK+2</option>
                    </select>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="maintenanceMode">
                    <label class="form-check-label" for="maintenanceMode">Режим обслуживания</label>
                    <small class="text-muted d-block">При включении доступ к системе будет ограничен</small>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Сохранить</button>
            </form>
        </div>
    </div>

    <!-- Настройки регистрации -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Настройки регистрации</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="allowRegistration" checked>
                    <label class="form-check-label" for="allowRegistration">Открытая регистрация</label>
                    <small class="text-muted d-block">Пользователи могут регистрироваться самостоятельно</small>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="emailConfirmation" checked>
                    <label class="form-check-label" for="emailConfirmation">Подтверждение email</label>
                    <small class="text-muted d-block">Требуется подтверждение email при регистрации</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Минимальная длина пароля</label>
                    <input type="number" class="form-control" value="6" min="4" max="32">
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Сохранить</button>
            </form>
        </div>
    </div>

    <!-- Настройки уведомлений -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Настройки уведомлений</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="emailNotifications" checked>
                    <label class="form-check-label" for="emailNotifications">Email уведомления</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="smsNotifications">
                    <label class="form-check-label" for="smsNotifications">SMS уведомления</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">SMTP сервер</label>
                    <input type="text" class="form-control" placeholder="smtp.example.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">SMTP порт</label>
                    <input type="number" class="form-control" value="587">
                </div>
                <button type="submit" class="btn btn-info text-white"><i class="bi bi-check-lg me-1"></i>Сохранить</button>
            </form>
        </div>
    </div>

    <!-- Безопасность -->
    <div class="card mb-4">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Безопасность</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label">Время сессии (минут)</label>
                    <input type="number" class="form-control" value="120">
                </div>
                <div class="mb-3">
                    <label class="form-label">Максимум попыток входа</label>
                    <input type="number" class="form-control" value="5">
                </div>
                <div class="mb-3">
                    <label class="form-label">Блокировка после попыток (минут)</label>
                    <input type="number" class="form-control" value="15">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="twoFactorAuth">
                    <label class="form-check-label" for="twoFactorAuth">Двухфакторная аутентификация</label>
                </div>
                <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Сохранить</button>
            </form>
        </div>
    </div>
</main>

<!-- Подвал -->
<footer>&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
