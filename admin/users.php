<?php
session_start();
require_once '../protected/auth_guard.php';
if ($_SESSION['role'] !== 'admin') { header('Location: ../dashboard.php'); exit; }
$name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пользователи — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar-header { border-bottom-color: var(--danger); }
        .sidebar-header small { color: var(--danger); }
        .sidebar a:hover, .sidebar a.active { background: linear-gradient(90deg, #dc3545 0%, #c82333 100%); }
        .welcome-card { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; }
        .card-header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
        .table thead th { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
        .btn-danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; }
        .btn-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); border: none; }
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
        <a href="users.php" class="active"><i class="bi bi-people me-2"></i>Пользователи</a>
        <a href="groups.php"><i class="bi bi-people-fill me-2"></i>Группы</a>
        <a href="schedule.php"><i class="bi bi-calendar me-2"></i>Расписание</a>
        <a href="library.php"><i class="bi bi-journal me-2"></i>Библиотека</a>
        <a href="notifications.php"><i class="bi bi-bell me-2"></i>Рассылка</a>
        <a href="settings.php"><i class="bi bi-gear me-2"></i>Настройки</a>
        <hr>
        <a href="#" data-bs-toggle="modal" data-bs-target="#accessibilityModal"><i class="bi bi-universal-access me-2"></i>Доступность</a>
        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Выход</a>
    </nav>
</div>

<main class="main-content">
    <div class="p-4 welcome-card">
        <h3><i class="bi bi-people"></i> Управление пользователями</h3>
        <p class="mb-0">Добавление, редактирование и удаление пользователей</p>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul me-2"></i>Список пользователей</span>
            <button class="btn btn-sm btn-light" onclick="showAddUserModal()">
                <i class="bi bi-person-plus"></i> Добавить
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Логин</th>
                            <th>Роль</th>
                            <th>Группа</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <tr><td colspan="6" class="text-center">Загрузка...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<footer>&copy; 2026 Учеба24</footer>

<!-- Модальное окно пользователя -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Добавить пользователя</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId">
                    <div class="mb-3">
                        <label class="form-label">Полное имя</label>
                        <input type="text" class="form-control" id="fullName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Логин</label>
                        <input type="text" class="form-control" id="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password">
                        <small class="text-muted">Оставьте пустым при редактировании</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Роль</label>
                        <select class="form-select" id="role" required>
                            <option value="student">Студент</option>
                            <option value="teacher">Преподаватель</option>
                            <option value="admin">Администратор</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Группа</label>
                        <select class="form-select" id="group">
                            <option value="">Не указана</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" onclick="saveUser()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно доступности -->
<div class="modal fade" id="accessibilityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-universal-access"></i> Доступность</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Размер текста</h6>
                <div class="btn-group w-100 mb-3">
                    <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="100">100%</button>
                    <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="125">125%</button>
                    <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="150">150%</button>
                    <button class="btn btn-outline-danger" data-a11y="fontSize" data-value="200">200%</button>
                </div>
                <h6>Визуальный режим</h6>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="highContrast" id="highContrast"><label class="form-check-label" for="highContrast">Высокая контрастность</label></div>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-a11y="largeButtons" id="largeButtons"><label class="form-check-label" for="largeButtons">Увеличенные кнопки</label></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Назад</button>
                <button type="button" class="btn btn-danger" onclick="saveAccessibility()">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/api-config.js"></script>
<script>
let usersData = [];
let groupsData = [];
let groupsLoaded = false;
const userModal = new bootstrap.Modal(document.getElementById('userModal'));

async function loadUsers() {
    try {
        const response = await fetch(API_BASE + '?action=get_users');
        const result = await response.json();
        usersData = result.success ? result.data : [];
        renderUsers();
    } catch (error) {
        console.error('loadUsers error:', error);
        showToast('Ошибка загрузки пользователей', 'error');
    }
}

async function loadGroups(force = false) {
    if (groupsLoaded && !force) return;
    try {
        const response = await fetch(API_BASE + '?action=get_groups');
        const result = await response.json();
        groupsData = result.success ? result.data : [];
        groupsLoaded = true;
        populateGroupSelect();
    } catch (error) {
        console.error('loadGroups error:', error);
        groupsData = [];
    }
}

function populateGroupSelect() {
    const select = document.getElementById('group');
    select.innerHTML = '<option value="">Не указана</option>';
    groupsData.forEach(g => {
        const opt = document.createElement('option');
        opt.value = g.name;
        opt.textContent = g.name;
        select.appendChild(opt);
    });
}

function renderUsers() {
    const tbody = document.getElementById('usersTableBody');
    if (usersData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Нет пользователей</td></tr>';
        return;
    }
    
    tbody.innerHTML = usersData.map(u => `
        <tr>
            <td>${u.id}</td>
            <td>${u.full_name}</td>
            <td>${u.username}</td>
            <td><span class="badge bg-${u.role === 'admin' ? 'danger' : (u.role === 'teacher' ? 'info' : 'success')}">${u.role}</span></td>
            <td>${u.group_name || '-'}</td>
            <td>
                <button class="btn btn-sm btn-primary me-1" onclick="editUser(${u.id})"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-danger" onclick="deleteUser(${u.id})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

async function showAddUserModal() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('userModalTitle').textContent = 'Добавить пользователя';
    await loadGroups();
    populateGroupSelect();
    userModal.show();
}

async function editUser(id) {
    const user = usersData.find(u => u.id === id);
    if (!user) return;
    
    // Гарантированно загрузить группы перед открытием
    await loadGroups();
    
    document.getElementById('userId').value = user.id;
    document.getElementById('fullName').value = user.full_name;
    document.getElementById('username').value = user.username;
    document.getElementById('password').value = '';
    document.getElementById('role').value = user.role;
    populateGroupSelect();
    document.getElementById('group').value = user.group_name || '';
    document.getElementById('userModalTitle').textContent = 'Редактировать пользователя';
    userModal.show();
}

async function saveUser() {
    const id = document.getElementById('userId').value;
    const userData = {
        full_name: document.getElementById('fullName').value.trim(),
        username: document.getElementById('username').value.trim(),
        password: document.getElementById('password').value,
        role: document.getElementById('role').value,
        group: document.getElementById('group').value
    };
    
    if (!userData.full_name || !userData.username || !userData.role) {
        showToast('Заполните все обязательные поля', 'error');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', id ? 'update_user' : 'add_user');
        if (id) formData.append('id', id);
        Object.keys(userData).forEach(key => formData.append(key, userData[key]));
        
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        const text = await response.text();
        console.log('saveUser raw response:', text);
        let result;
        try { result = JSON.parse(text); } catch(e) { result = { success: false, error: 'Невалидный JSON: ' + text.substring(0,100) }; }
        
        if (result.success) {
            showToast(id ? 'Пользователь обновлён!' : 'Пользователь добавлен!', 'success');
            userModal.hide();
            loadUsers();
        } else {
            console.error('saveUser error result:', result);
            showToast(result.message || result.error || 'Ошибка сохранения', 'error');
        }
    } catch (error) {
        console.error('saveUser catch:', error);
        showToast('Ошибка соединения', 'error');
    }
}

async function deleteUser(id) {
    if (!confirm('Удалить этого пользователя?')) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete_user');
        formData.append('id', id);
        
        const response = await fetch(API_BASE, {
            method: 'POST',
            body: formData
        });
        const text = await response.text();
        console.log('deleteUser raw response:', text);
        let result;
        try { result = JSON.parse(text); } catch(e) { result = { success: false, error: 'Невалидный JSON: ' + text.substring(0,100) }; }
        
        if (result.success) {
            showToast('Пользователь удалён!', 'success');
            loadUsers();
        } else {
            console.error('deleteUser error result:', result);
            showToast(result.message || result.error || 'Ошибка удаления', 'error');
        }
    } catch (error) {
        console.error('deleteUser catch:', error);
        showToast('Ошибка соединения', 'error');
    }
}

function saveAccessibility() {
    const settings = {
        fontSize: localStorage.getItem('a11y_fontSize') || '100',
        highContrast: document.getElementById('highContrast').checked,
        largeButtons: document.getElementById('largeButtons').checked
    };
    localStorage.setItem('accessibility_settings', JSON.stringify(settings));
    showToast('Настройки доступности сохранены!', 'success');
    bootstrap.Modal.getInstance(document.getElementById('accessibilityModal')).hide();
}

function showToast(message, type) {
    const container = document.querySelector('.toast-container');
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0`;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    container.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

// Загрузка данных при старте
loadUsers();
loadGroups();
</script>
</body>
</html>