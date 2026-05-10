// admin-sync.js - функции админа с синхронизацией

// Отправка уведомления всем/студентам/преподавателям/группе
function sendNotificationToAll() {
    const title = prompt('Заголовок уведомления:');
    const message = prompt('Сообщение:');
    const target = prompt('Кому: all (все), students (студенты), teachers (преподаватели), group (группа)', 'all');
    
    if (title && message && target) {
        let group = null;
        if (target === 'group') {
            group = prompt('Название группы:');
        }
        
        const notification = {
            id: Date.now(),
            title: title,
            message: message,
            target: target,
            group: group,
            date: new Date().toLocaleString('ru-RU'),
            read: false
        };
        
        if (window.Sync) {
            Sync.addNotification(notification);
            Sync.showNotification('Уведомление отправлено!', 'success');
        } else {
            const notifications = localStorage.getItem('sync_notifications') ? 
                JSON.parse(localStorage.getItem('sync_notifications')) : [];
            notifications.push(notification);
            localStorage.setItem('sync_notifications', JSON.stringify(notifications));
        }
    }
}

// Загрузка уведомлений
function loadNotifications() {
    const notifications = window.Sync ? Sync.getNotifications() : [];
    const tbody = document.querySelector('#notificationsTable tbody');
    if (!tbody) return;
    
    if (notifications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Нет уведомлений</td></tr>';
        return;
    }
    
    const targetMap = {
        'all': 'Все',
        'students': 'Студенты',
        'teachers': 'Преподаватели',
        'group': 'Группа'
    };
    
    tbody.innerHTML = notifications.map(notif => `
        <tr>
            <td>${notif.date}</td>
            <td>${notif.title}</td>
            <td>${targetMap[notif.target] || notif.target}</td>
            <td>${notif.group || '-'}</td>
            <td><button class="btn btn-sm btn-info" onclick="viewNotification(${notif.id})">👁</button></td>
        </tr>
    `).join('');
}

// Просмотр уведомления
function viewNotification(id) {
    const notifications = window.Sync ? Sync.getNotifications() : [];
    const notif = notifications.find(n => n.id === id);
    if (notif) {
        alert(`Заголовок: ${notif.title}\n\nСообщение: ${notif.message}\n\nКому: ${notif.target}\nДата: ${notif.date}`);
    }
}

// Загрузка пользователей (заглушка)
function loadUsers() {
    console.log('Загрузка пользователей...');
}

// Загрузка групп
function loadGroups() {
    const groups = window.Sync ? Sync.getGroups() : [];
    const tbody = document.querySelector('#groupsTable tbody');
    if (!tbody) return;
    
    if (groups.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Нет групп</td></tr>';
        return;
    }
    
    tbody.innerHTML = groups.map((item, index) => `
        <tr>
            <td>${item.name}</td>
            <td>${item.students}</td>
            <td><button class="btn btn-sm btn-danger" onclick="deleteGroupAdmin(${index})">✗</button></td>
        </tr>
    `).join('');
}

function deleteGroupAdmin(index) {
    if (confirm('Удалить группу?')) {
        if (window.Sync) {
            Sync.deleteGroup(index);
        }
        loadGroups();
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;
    
    if (path.includes('admin-dashboard.php')) {
        console.log('Админ панель загружена');
    } else if (path.includes('admin-notifications.php')) {
        loadNotifications();
    } else if (path.includes('admin-groups.php')) {
        loadGroups();
    }
    
    // Автообновление
    window.addEventListener('sync-update', function() {
        if (path.includes('admin-notifications.php')) loadNotifications();
        if (path.includes('admin-groups.php')) loadGroups();
    });
});

// Глобальные функции
window.sendNotificationToAll = sendNotificationToAll;
window.viewNotification = viewNotification;
window.deleteGroupAdmin = deleteGroupAdmin;