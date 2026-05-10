// teacher.js - все функции для преподавателя с синхронизацией

// Загрузка данных через Sync
function loadData(key) {
    return window.Sync && Sync[key] ? Sync[key]() : [];
}

// Сохранение данных через Sync
function saveData(key, data) {
    if (window.Sync && Sync['set' + key.charAt(0).toUpperCase() + key.slice(1)]) {
        // Используем синхронизацию если есть
    }
    localStorage.setItem('teacher_' + key, JSON.stringify(data));
}

// Расписание
function loadSchedule() {
    const schedule = window.Sync ? Sync.getSchedule() : loadData('schedule');
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

function addSchedule() {
    const day = prompt('День недели:');
    const group = prompt('Группа:');
    const subject = prompt('Предмет:');
    const time = prompt('Время (например 09:00-10:30):');
    const type = prompt('Тип (Лекция/Практика/Лабораторная):');
    
    if (day && group && subject && time && type) {
        const item = { day, group, subject, time, type };
        if (window.Sync) {
            Sync.addSchedule(item);
        } else {
            const schedule = loadData('schedule');
            schedule.push(item);
            saveData('schedule', schedule);
        }
        loadSchedule();
        Sync && Sync.showNotification('Занятие добавлено!', 'success');
    }
}

// Оценки
function loadGrades() {
    const grades = window.Sync ? Sync.getGrades() : loadData('grades');
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

function addGrade() {
    const student = prompt('ФИО студента:');
    const subject = prompt('Предмет:');
    const grade = prompt('Оценка (2-5):');
    const date = new Date().toLocaleDateString('ru-RU');
    
    if (student && subject && grade) {
        const item = { student, subject, grade, date };
        if (window.Sync) {
            Sync.addGrade(item);
            // Отправить уведомление студенту
            Sync.sendNotification('Новая оценка', `По предмету ${subject} выставлена оценка ${grade}`, 'students');
        } else {
            const grades = loadData('grades');
            grades.push(item);
            saveData('grades', grades);
        }
        loadGrades();
        Sync && Sync.showNotification('Оценка выставлена!', 'success');
    }
}

// Работы студентов
function loadAssignments() {
    const assignments = window.Sync ? Sync.getAssignments() : loadData('assignments');
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
            <td>
                <button class="btn btn-sm btn-success" onclick="checkAssignment(${index})">✓</button>
                <button class="btn btn-sm btn-danger" onclick="rejectAssignment(${index})">✗</button>
            </td>
        </tr>
    `).join('');
}

function checkAssignment(index) {
    const assignments = window.Sync ? Sync.getAssignments() : loadData('assignments');
    const grade = prompt('Введите оценку:');
    if (grade) {
        const data = { status: 'Одобрено', grade: grade };
        if (window.Sync) {
            Sync.updateAssignment(index, data);
            Sync.sendNotification('Работа проверена', `Ваша работа "${assignments[index].title}" одобрена. Оценка: ${grade}`, 'students');
        } else {
            assignments[index] = { ...assignments[index], ...data };
            saveData('assignments', assignments);
        }
        loadAssignments();
        Sync && Sync.showNotification('Работа одобрена!', 'success');
    }
}

function rejectAssignment(index) {
    const assignments = window.Sync ? Sync.getAssignments() : loadData('assignments');
    const data = { status: 'Требует доработки' };
    if (window.Sync) {
        Sync.updateAssignment(index, data);
        Sync.sendNotification('Работа требует доработки', `Ваша работа "${assignments[index].title}" требует исправлений`, 'students');
    } else {
        assignments[index] = { ...assignments[index], ...data };
        saveData('assignments', assignments);
    }
    loadAssignments();
    Sync && Sync.showNotification('Работа отклонена', 'warning');
}

// Журнал
function loadJournal() {
    const journal = window.Sync ? Sync.getJournal() : loadData('journal');
    const tbody = document.querySelector('#journalTable tbody');
    if (!tbody) return;
    
    if (journal.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Нет записей</td></tr>';
        return;
    }
    
    tbody.innerHTML = journal.map(item => `
        <tr>
            <td>${item.date}</td>
            <td>${item.group}</td>
            <td>${item.subject}</td>
            <td>${item.comment}</td>
        </tr>
    `).join('');
}

function addJournal() {
    const date = prompt('Дата:');
    const group = prompt('Группа:');
    const subject = prompt('Предмет:');
    const comment = prompt('Комментарий:');
    
    if (date && group && subject) {
        const item = { date, group, subject, comment: comment || '' };
        if (window.Sync) {
            Sync.addJournal(item);
        } else {
            const journal = loadData('journal');
            journal.push(item);
            saveData('journal', journal);
        }
        loadJournal();
        Sync && Sync.showNotification('Запись добавлена!', 'success');
    }
}

// Группы
function loadGroups() {
    const groups = window.Sync ? Sync.getGroups() : loadData('groups');
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
            <td>
                <button class="btn btn-sm btn-danger" onclick="deleteGroup(${index})">✗</button>
            </td>
        </tr>
    `).join('');
}

function addGroup() {
    const name = prompt('Название группы:');
    const students = prompt('Количество студентов:');
    
    if (name && students) {
        const item = { name, students };
        if (window.Sync) {
            Sync.addGroup(item);
        } else {
            const groups = loadData('groups');
            groups.push(item);
            saveData('groups', groups);
        }
        loadGroups();
        Sync && Sync.showNotification('Группа добавлена!', 'success');
    }
}

function deleteGroup(index) {
    if (confirm('Удалить группу?')) {
        if (window.Sync) {
            Sync.deleteGroup(index);
        } else {
            const groups = loadData('groups');
            groups.splice(index, 1);
            saveData('groups', groups);
        }
        loadGroups();
    }
}

// Уведомления
function loadNotifications() {
    const role = document.body.classList.contains('role-student') ? 'student' : 'teacher';
    const notifications = window.Sync ? Sync.getNotificationsForUser(role) : loadData('notifications');
    const tbody = document.querySelector('#notificationsTable tbody');
    if (!tbody) return;
    
    if (notifications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Нет уведомлений</td></tr>';
        return;
    }
    
    tbody.innerHTML = notifications.map((item, index) => `
        <tr class="${item.read ? '' : 'table-active'}">
            <td>${item.title}</td>
            <td>${item.message}</td>
            <td>${item.date}</td>
        </tr>
    `).join('');
}

function addNotification() {
    const title = prompt('Заголовок:');
    const message = prompt('Сообщение:');
    const target = prompt('Кому: all/students/teachers', 'all');
    const date = new Date().toLocaleString('ru-RU');
    
    if (title && message && target) {
        const item = { title, message, target, date };
        if (window.Sync) {
            Sync.addNotification(item);
        } else {
            const notifications = loadData('notifications');
            notifications.push(item);
            saveData('notifications', notifications);
        }
        loadNotifications();
        Sync && Sync.showNotification('Уведомление создано!', 'success');
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;
    
    if (path.includes('schedule.php')) loadSchedule();
    else if (path.includes('grades.php')) loadGrades();
    else if (path.includes('assignments.php')) loadAssignments();
    else if (path.includes('journal.php')) loadJournal();
    else if (path.includes('groups.php')) loadGroups();
    else if (path.includes('notifications.php')) loadNotifications();
    
    // Автообновление при изменениях в других вкладках
    window.addEventListener('sync-update', function() {
        if (path.includes('schedule.php')) loadSchedule();
        else if (path.includes('grades.php')) loadGrades();
        else if (path.includes('assignments.php')) loadAssignments();
        else if (path.includes('journal.php')) loadJournal();
        else if (path.includes('groups.php')) loadGroups();
        else if (path.includes('notifications.php')) loadNotifications();
    });
});