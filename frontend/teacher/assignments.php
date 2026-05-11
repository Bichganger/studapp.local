<?php
session_start();
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../dashboard.php");
    exit;
}

$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Работы — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/teacher-style.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-mortarboard"></i> Учеба24</h5>
        <small>Кабинет преподавателя</small>
    </div>
    <nav class="mt-3">
        <a href="panel.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="journal.php"><i class="bi bi-journal-text me-2"></i>Журнал</a>
        <a href="grades.php"><i class="bi bi-star me-2"></i>Оценки</a>
        <a href="assignments.php" class="active"><i class="bi bi-file-text me-2"></i>Работы</a>
        <a href="groups.php"><i class="bi bi-people me-2"></i>Группы</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Уведомления</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal" class="accessibility-menu-btn"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<!-- Модальное окно доступности -->
<div class="modal fade" id="accessibilityModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-universal-access"></i> Доступность</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <h6>Размер текста</h6>
            <div class="btn-group w-100 mb-3">
                <button class="btn btn-outline-primary" data-a11y="fontSize" data-value="100">100%</button>
                <button class="btn btn-outline-primary" data-a11y="fontSize" data-value="125">125%</button>
                <button class="btn btn-outline-primary" data-a11y="fontSize" data-value="150">150%</button>
                <button class="btn btn-outline-primary" data-a11y="fontSize" data-value="200">200%</button>
            </div>
            <h6>Визуальный режим</h6>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="highContrast" id="highContrast"><label class="form-check-label" for="highContrast">Высокая контрастность</label></div>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="largeButtons" id="largeButtons"><label class="form-check-label" for="largeButtons">Увеличенные кнопки</label></div>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="simplified" id="simplified"><label class="form-check-label" for="simplified">Упрощённый интерфейс</label></div>
            <h6>Уведомления</h6>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="visualAlerts" id="visualAlerts" checked><label class="form-check-label" for="visualAlerts">Визуальные уведомления</label></div>
        </div>
    </div></div>
</div>

<footer>&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sync.js"></script>
<script src="../assets/js/accessibility.js"></script>
</body>
</html>