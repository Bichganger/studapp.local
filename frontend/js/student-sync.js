// student-sync.js - функции студента с синхронизацией

// Загрузка уведомлений для студента
function loadStudentNotifications() {
    const userGroup = null; // Можно добавить определение группы студента
    const notifications = window.Sync ? Sync.getNotificationsForUser('student', userGroup) : [];
    const tbody = document.querySelector('#notificationsTable tbody');
    if (!tbody) return;
    
    if (notifications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Нет уведомлений</td></tr>';
        return;
    }
    
    tbody.innerHTML = notifications.map(notif => `
        <tr class="${notif.read ? '' : 'table-active'}">
            <td><strong>${notif.title}</strong></td>
            <td>${notif.message}</td>
            <td>${notif.date}</td>
        </tr>
    `).join('');
}

// Загрузка оценок студента
function loadStudentGrades() {
    const allGrades = window.Sync ? Sync.getGrades() : [];
    // Фильтруем оценки для текущего студента (по имени из сессии)
    const studentName = document.querySelector('.welcome-card')?.textContent?.match(/Добро пожаловать, ([^!]+)/)?.[1]?.trim() || '';
    const grades = allGrades.filter(g => g.student && g.student.includes(studentName));
    
    const tbody = document.querySelector('#gradesTable tbody');
    if (!tbody) return;
    
    if (grades.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Нет оценок</td></tr>';
        return;
    }
    
    tbody.innerHTML = grades.map(item => `
        <tr>
            <td>${item.student}</td>
            <td>${item.subject}</td>
            <td><span class="badge bg-success">${item.grade}</span></td>
            <td>${item.date}</td>
        </tr>
    `).join('');
}

// Загрузка работ студента
function loadStudentAssignments() {
    const allAssignments = window.Sync ? Sync.getAssignments() : [];
    const studentName = document.querySelector('.welcome-card')?.textContent?.match(/Добро пожаловать, ([^!]+)/)?.[1]?.trim() || '';
    const assignments = allAssignments.filter(a => a.student && a.student.includes(studentName));
    
    const tbody = document.querySelector('#assignmentsTable tbody');
    if (!tbody) return;
    
    if (assignments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Нет работ</td></tr>';
        return;
    }
    
    tbody.innerHTML = assignments.map((item, index) => `
        <tr>
            <td>${item.student}</td>
            <td>${item.title}</td>
            <td>${item.date}</td>
            <td><span class="badge bg-warning">${item.status}</span></td>
            <td>-</td>
        </tr>
    `).join('');
}

// Загрузка расписания
function loadStudentSchedule() {
    const schedule = window.Sync ? Sync.getSchedule() : [];
    const tbody = document.querySelector('#scheduleTable tbody');
    if (!tbody) return;
    
    if (schedule.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Нет занятий</td></tr>';
        return;
    }
    
    tbody.innerHTML = schedule.map(item => `
        <tr>
            <td>${item.day}</td>
            <td>${item.group}</td>
            <td>${item.subject}</td>
            <td>${item.time}</td>
            <td><span class="badge bg-info">${item.type}</span></td>
        </tr>
    `).join('');
}

// Инициализация
document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;
    
    if (path.includes('student-notifications.php')) {
        loadStudentNotifications();
    } else if (path.includes('student-grades.php')) {
        loadStudentGrades();
    } else if (path.includes('student-assignments.php')) {
        loadStudentAssignments();
    } else if (path.includes('student-schedule.php')) {
        loadStudentSchedule();
    }
    
    // Автообновление при изменениях
    window.addEventListener('sync-update', function() {
        if (path.includes('student-notifications.php')) loadStudentNotifications();
        if (path.includes('student-grades.php')) loadStudentGrades();
        if (path.includes('student-assignments.php')) loadStudentAssignments();
        if (path.includes('student-schedule.php')) loadStudentSchedule();
    });
});

// Глобальные функции
window.loadStudentNotifications = loadStudentNotifications;
window.loadStudentGrades = loadStudentGrades;
window.loadStudentAssignments = loadStudentAssignments;
window.loadStudentSchedule = loadStudentSchedule;