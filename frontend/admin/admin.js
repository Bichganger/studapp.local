// простой JS для админки
// сделал студент

// уведомления
function showMsg(text, type) {
    const div = document.createElement('div');
    div.className = 'alert alert-' + (type == 'error' ? 'danger' : 'success') + ' alert-dismissible fade show';
    div.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;min-width:250px;';
    div.innerHTML = text + '<button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>';
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

// === ПОЛЬЗОВАТЕЛИ ===
function loadUsers() {
    fetch('api.php?action=get_users')
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector('#usersTable tbody');
            if (!tbody) return;
            tbody.innerHTML = data.users.map(u => `
                <tr>
                    <td>${u.full_name || ''}</td>
                    <td>${u.username || ''}</td>
                    <td><span class="badge bg-${u.role=='admin'?'danger':u.role=='teacher'?'success':'primary'}">${u.role}</span></td>
                    <td>${u.email || '-'}</td>
                    <td>
                        ${u.role != 'admin' ? `<button class="btn btn-sm btn-danger" onclick="delUser(${u.id})">удалить</button>` : '-'}
                    </td>
                </tr>
            `).join('');
        });
}

function addUser() {
    const form = document.getElementById('addUserForm');
    const data = new FormData(form);
    
    fetch('api.php?action=add_user', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showMsg('пользователь добавлен');
            form.reset();
            loadUsers();
        } else {
            showMsg('ошибка', 'error');
        }
    });
}

function delUser(id) {
    if (!confirm('удалить?')) return;
    fetch('api.php?action=delete_user&id=' + id)
        .then(r => r.json())
        .then(() => loadUsers());
}

// === ГРУППЫ ===
function loadGroups() {
    fetch('api.php?action=get_groups')
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector('#groupsTable tbody');
            if (!tbody) return;
            tbody.innerHTML = data.groups.map(g => `
                <tr>
                    <td>${g.group_name || ''}</td>
                    <td>${g.specialty || ''}</td>
                    <td>${g.course || 1} курс</td>
                    <td>${g.student_count || 0}</td>
                    <td><button class="btn btn-sm btn-danger" onclick="delGroup(${g.id})">удалить</button></td>
                </tr>
            `).join('');
        });
}

function addGroup() {
    const form = document.getElementById('addGroupForm');
    const data = new FormData(form);
    
    fetch('api.php?action=add_group', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showMsg('группа добавлена');
            form.reset();
            loadGroups();
        }
    });
}

function delGroup(id) {
    if (!confirm('удалить группу?')) return;
    fetch('api.php?action=delete_group&id=' + id)
        .then(r => r.json())
        .then(() => loadGroups());
}

// === РАСПИСАНИЕ ===
function loadSchedule() {
    fetch('api.php?action=get_schedule')
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector('#scheduleTable tbody');
            if (!tbody) return;
            tbody.innerHTML = data.schedule.map(s => `
                <tr>
                    <td>${s.day || ''}</td>
                    <td>${s.group_name || ''}</td>
                    <td>${s.subject || ''}</td>
                    <td>${s.time_start || ''}</td>
                    <td>${s.type || ''}</td>
                    <td>${s.classroom || '-'}</td>
                    <td>${s.teacher || '-'}</td>
                    <td><button class="btn btn-sm btn-danger" onclick="delSchedule(${s.id})">удалить</button></td>
                </tr>
            `).join('');
        });
}

function addSchedule() {
    const form = document.getElementById('addScheduleForm');
    const data = new FormData(form);
    
    fetch('api.php?action=add_schedule', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showMsg('пара добавлена');
            form.reset();
            loadSchedule();
        }
    });
}

function delSchedule(id) {
    if (!confirm('удалить пару?')) return;
    fetch('api.php?action=delete_schedule&id=' + id)
        .then(r => r.json())
        .then(() => loadSchedule());
}

// === БИБЛИОТЕКА ===
function loadLibrary() {
    fetch('api.php?action=get_library')
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector('#libraryTable tbody');
            if (!tbody) return;
            tbody.innerHTML = data.works.map(w => `
                <tr>
                    <td>${w.title || ''}</td>
                    <td>${w.work_type || ''}</td>
                    <td>${w.student_name || ''}</td>
                    <td>${w.group_name || ''}</td>
                    <td><span class="badge bg-${w.status=='approved'?'success':w.status=='rejected'?'danger':'warning'}">${w.status}</span></td>
                    <td>
                        ${w.status == 'pending' ? `
                            <button class="btn btn-sm btn-success" onclick="approveWork(${w.id})">✔</button>
                            <button class="btn btn-sm btn-danger" onclick="rejectWork(${w.id})">✖</button>
                        ` : ''}
                        <button class="btn btn-sm btn-secondary" onclick="delWork(${w.id})">🗑</button>
                    </td>
                </tr>
            `).join('');
        });
}

function approveWork(id) {
    fetch('api.php?action=approve_work&id=' + id)
        .then(r => r.json())
        .then(() => loadLibrary());
}

function rejectWork(id) {
    fetch('api.php?action=reject_work&id=' + id)
        .then(r => r.json())
        .then(() => loadLibrary());
}

function delWork(id) {
    if (!confirm('удалить работу?')) return;
    fetch('api.php?action=delete_work&id=' + id)
        .then(r => r.json())
        .then(() => loadLibrary());
}

// === УВЕДОМЛЕНИЯ ===
function loadNotifications() {
    fetch('api.php?action=get_notifications')
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector('#notifTable tbody');
            if (!tbody) return;
            tbody.innerHTML = data.notifications.map(n => `
                <tr>
                    <td>${n.created_at ? n.created_at.split(' ')[0] : ''}</td>
                    <td>${n.title || ''}</td>
                    <td>${n.target_type || ''}</td>
                    <td><span class="badge bg-success">отправлено</span></td>
                </tr>
            `).join('');
        });
}

function sendNotification() {
    const form = document.getElementById('notifForm');
    const data = new FormData(form);
    
    fetch('api.php?action=send_notification', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showMsg('уведомление отправлено');
            form.reset();
            loadNotifications();
        }
    });
}

// === НАСТРОЙКИ ===
function loadSettings() {
    fetch('api.php?action=get_settings')
        .then(r => r.json())
        .then(data => {
            const s = data.settings;
            const inputs = {
                'system_name': document.querySelector('input[name="system_name"]'),
                'notification_email': document.querySelector('input[name="notification_email"]'),
                'timezone': document.querySelector('select[name="timezone"]'),
                'allow_registration': document.querySelector('input[name="allow_registration"]'),
                'email_confirmation': document.querySelector('input[name="email_confirmation"]'),
                'min_password_length': document.querySelector('input[name="min_password_length"]'),
            };
            for (let k in inputs) {
                if (inputs[k] && s[k]) {
                    if (inputs[k].type == 'checkbox') {
                        inputs[k].checked = s[k] == '1';
                    } else {
                        inputs[k].value = s[k];
                    }
                }
            }
        });
}

function saveSettings() {
    const form = document.getElementById('settingsForm');
    const data = new FormData(form);
    
    fetch('api.php?action=save_settings', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showMsg('настройки сохранены');
        }
    });
}

// инициализация
document.addEventListener('DOMContentLoaded', function() {
    // проверяем какая страница и загружаем нужное
    if (document.getElementById('usersTable')) loadUsers();
    if (document.getElementById('groupsTable')) loadGroups();
    if (document.getElementById('scheduleTable')) loadSchedule();
    if (document.getElementById('libraryTable')) loadLibrary();
    if (document.getElementById('notifTable')) loadNotifications();
    if (document.querySelector('input[name="system_name"]')) loadSettings();
});
