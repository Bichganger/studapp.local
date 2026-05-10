// teacher.js - все функции для преподавателя

// Загрузка данных из localStorage
function loadData(key) {
    const data = localStorage.getItem('teacher_' + key);
    return data ? JSON.parse(data) : [];
}

// Сохранение данных в localStorage
function saveData(key, data) {
    localStorage.setItem('teacher_' + key, JSON.stringify(data));
}

// Расписание
function loadSchedule() {
    const schedule = loadData('schedule');
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
        const schedule = loadData('schedule');
        schedule.push({ day, group, subject, time, type });
        saveData('schedule', schedule);
        loadSchedule();
        alert('Занятие добавлено!');
    }
}

// Оценки
function loadGrades() {
    const grades = loadData('grades');
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
        const grades = loadData('grades');
        grades.push({ student, subject, grade, date });
        saveData('grades', grades);
        loadGrades();
        alert('Оценка выставлена!');
    }
}

// Работы студентов
function loadAssignments() {
    const assignments = loadData('assignments');
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
    const assignments = loadData('assignments');
    assignments[index].status = 'Одобрено';
    assignments[index].grade = prompt('Введите оценку:');
    saveData('assignments', assignments);
    loadAssignments();
}

function rejectAssignment(index) {
    const assignments = loadData('assignments');
    assignments[index].status = 'Требует доработки';
    saveData('assignments', assignments);
    loadAssignments();
}

// Журнал
function loadJournal() {
    const journal = loadData('journal');
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
        const journal = loadData('journal');
        journal.push({ date, group, subject, comment: comment || '' });
        saveData('journal', journal);
        loadJournal();
        alert('Запись добавлена!');
    }
}

// Группы
function loadGroups() {
    const groups = loadData('groups');
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
        const groups = loadData('groups');
        groups.push({ name, students });
        saveData('groups', groups);
        loadGroups();
        alert('Группа добавлена!');
    }
}

function deleteGroup(index) {
    if (confirm('Удалить группу?')) {
        const groups = loadData('groups');
        groups.splice(index, 1);
        saveData('groups', groups);
        loadGroups();
    }
}

// Уведомления
function loadNotifications() {
    const notifications = loadData('notifications');
    const tbody = document.querySelector('#notificationsTable tbody');
    if (!tbody) return;
    
    if (notifications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center">Нет уведомлений</td></tr>';
        return;
    }
    
    tbody.innerHTML = notifications.map((item, index) => `
        <tr>
            <td>${item.title}</td>
            <td>${item.message}</td>
            <td>${item.date}</td>
        </tr>
    `).join('');
}

function addNotification() {
    const title = prompt('Заголовок:');
    const message = prompt('Сообщение:');
    const date = new Date().toLocaleString('ru-RU');
    
    if (title && message) {
        const notifications = loadData('notifications');
        notifications.push({ title, message, date });
        saveData('notifications', notifications);
        loadNotifications();
        alert('Уведомление создано!');
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Автоматически загружаем данные для текущей страницы
    const path = window.location.pathname;
    
    if (path.includes('schedule.php')) loadSchedule();
    else if (path.includes('grades.php')) loadGrades();
    else if (path.includes('assignments.php')) loadAssignments();
    else if (path.includes('journal.php')) loadJournal();
    else if (path.includes('groups.php')) loadGroups();
    else if (path.includes('notifications.php')) loadNotifications();
});