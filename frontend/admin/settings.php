<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'admin') { header("Location: ../dashboard.php"); exit; }
$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar-common.css">
    <style>
        .sidebar-header { border-bottom-color: #dc3545; }
        .sidebar-header small { color: #dc3545; }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #dc3545 0%, #c82333 100%); }
        .welcome-card { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; }
        .card-header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
        .btn-danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
    </style>
</head>
<body class="role-admin">

<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-shield-lock"></i> Учеба24</h5>
        <small>Админ-панель</small>
    </div>
    <nav class="mt-3">
        <a href="dashboard.php"><i class="bi bi-house me-2"></i>Главная</a>
        <a href="users.php"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="settings.php" class="active"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-gear"></i> Настройки системы</h3>
        <p class="mb-0">Конфигурация платформы и доступность</p>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-universal-access me-2"></i>Доступность</div>
                <div class="card-body">
                    <form id="a11yForm">
                        <h6 class="mb-3">Размер текста</h6>
                        <div class="btn-group w-100 mb-4">
                            <input type="radio" class="btn-check" name="fontSize" id="fs100" value="100" autocomplete="off">
                            <label class="btn btn-outline-danger" for="fs100">100%</label>
                            <input type="radio" class="btn-check" name="fontSize" id="fs125" value="125" autocomplete="off">
                            <label class="btn btn-outline-danger" for="fs125">125%</label>
                            <input type="radio" class="btn-check" name="fontSize" id="fs150" value="150" autocomplete="off">
                            <label class="btn btn-outline-danger" for="fs150">150%</label>
                            <input type="radio" class="btn-check" name="fontSize" id="fs200" value="200" autocomplete="off">
                            <label class="btn btn-outline-danger" for="fs200">200%</label>
                        </div>
                        <h6 class="mb-3">Визуальный режим</h6>
                        <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="highContrast"><label class="form-check-label" for="highContrast">Высокая контрастность</label></div>
                        <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="largeButtons"><label class="form-check-label" for="largeButtons">Увеличенные кнопки</label></div>
                        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="simplified"><label class="form-check-label" for="simplified">Упрощённый интерфейс</label></div>
                        <button type="button" class="btn btn-danger w-100" onclick="saveSettings()">Сохранить настройки</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-info-circle me-2"></i>Информация о системе</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span>Версия</span><strong>2.0</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Роль</span><strong>Администратор</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Пользователь</span><strong><?= $name ?></strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Сессия</span><strong>Активна</strong></li>
                    </ul>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>Настройки доступности применяются ко всем страницам системы
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>

<div class="modal fade" id="accessibilityModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-universal-access"></i> Доступность</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <h6>Размер текста</h6>
            <div class="btn-group w-100 mb-3">
                <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="100">100%</button>
                <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="125">125%</button>
                <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="150">150%</button>
                <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="200">200%</button>
            </div>
            <h6>Визуальный режим</h6>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="highContrast" id="highContrast2"><label class="form-check-label" for="highContrast2">Высокая контрастность</label></div>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="largeButtons" id="largeButtons2"><label class="form-check-label" for="largeButtons2">Увеличенные кнопки</label></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Назад</button>
            <button type="button" class="btn btn-danger" onclick="saveAccessibility()">Сохранить</button>
        </div>
    </div></div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Загрузка сохраненных настроек
document.addEventListener('DOMContentLoaded', () => {
    const s = JSON.parse(localStorage.getItem('accessibility_settings')||'{}');
    if(s.fontSize) document.getElementById('fs'+s.fontSize).checked = true;
    if(s.highContrast) document.getElementById('highContrast').checked = true;
    if(s.largeButtons) document.getElementById('largeButtons').checked = true;
    if(s.simplified) document.getElementById('simplified').checked = true;
});

function saveSettings() {
    const fontSize = document.querySelector('input[name="fontSize"]:checked')?.value || '100';
    const settings = {
        fontSize: fontSize,
        highContrast: document.getElementById('highContrast').checked,
        largeButtons: document.getElementById('largeButtons').checked,
        simplified: document.getElementById('simplified').checked
    };
    localStorage.setItem('accessibility_settings', JSON.stringify(settings));
    localStorage.setItem('a11y_fontSize', fontSize);
    applySettings(settings);
    showToast('Настройки сохранены!','success');
}

function applySettings(s) {
    document.body.style.fontSize = (s.fontSize/100) + 'em';
    if(s.highContrast) document.body.classList.add('high-contrast');
    if(s.largeButtons) document.querySelectorAll('.btn').forEach(b=>b.classList.add('btn-lg'));
    if(s.simplified) document.body.classList.add('simplified');
}

function saveAccessibility() {
    const s = {
        fontSize: localStorage.getItem('a11y_fontSize')||'100',
        highContrast: document.getElementById('highContrast2').checked,
        largeButtons: document.getElementById('largeButtons2').checked
    };
    localStorage.setItem('accessibility_settings',JSON.stringify(s));
    showToast('Настройки сохранены!','success');
    bootstrap.Modal.getInstance(document.getElementById('accessibilityModal')).hide();
}

function showToast(m,t) {
    const c=document.querySelector('.toast-container'),tEl=document.createElement('div');
    tEl.className=`toast align-items-center text-white bg-${t==='success'?'success':t==='error'?'danger':'primary'} border-0`;
    tEl.innerHTML=`<div class="d-flex"><div class="toast-body">${m}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    c.appendChild(tEl); new bootstrap.Toast(tEl,{delay:3000}).show(); tEl.addEventListener('hidden.bs.toast',()=>tEl.remove());
}
</script>
</body>
</html>