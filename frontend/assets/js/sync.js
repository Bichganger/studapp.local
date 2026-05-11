// === ПРОЕКТО СИНХРОНИЗАЦИИ С БД ===

const KEYS = {
    NOTIFICATIONS: 'sync_notifications',
    SCHEDULE: 'sync_schedule',
    GRADES: 'sync_grades',
    ASSIGNMENTS: 'sync_assignments',
    JOURNAL: 'sync_journal',
    GROUPS: 'sync_groups',
    LIBRARY: 'sync_library',
    USERS: 'sync_users'
};

// === МОКОВЫЕ ДАННЫЕ (если БД недоступна) ===
const MOCK_DATA = {
    users: [
        {id: 1, username: 'admin', full_name: 'Администратор Системы', role: 'admin', group_name: null},
        {id: 2, username: 'teacher1', full_name: 'Иванов Петр Сергеевич', role: 'teacher', group_name: null},
        {id: 3, username: 'teacher2', full_name: 'Смирнова Анна Викторовна', role: 'teacher', group_name: null},
        {id: 4, username: 'student1', full_name: 'Сидоров Алексей Иванович', role: 'student', group_name: 'ПИ-21'},
        {id: 5, username: 'student2', full_name: 'Козлов Дмитрий Михайлович', role: 'student', group_name: 'ПИ-21'},
        {id: 6, username: 'student3', full_name: 'Новикова Мария Александровна', role: 'student', group_name: 'ПИ-22'},
        {id: 7, username: 'student4', full_name: 'Петров Иван Сергеевич', role: 'student', group_name: 'ПИ-22'},
        {id: 8, username: 'student5', full_name: 'Соколова Елена Дмитриевна', role: 'student', group_name: 'ПО-21'},
        {id: 9, username: 'student6', full_name: 'Михайлов Андрей Владимирович', role: 'student', group_name: 'ПО-21'},
        {id: 10, username: 'student7', full_name: 'Федорова Ольга Николаевна', role: 'student', group_name: 'ПО-22'}
    ],
    groups: [
        {id: 1, name: 'ПИ-21', course: 2, specialty: 'Программная инженерия', student_count: 2},
        {id: 2, name: 'ПИ-22', course: 2, specialty: 'Программная инженерия', student_count: 2},
        {id: 3, name: 'ПО-21', course: 3, specialty: 'Прикладная информатика', student_count: 3},
        {id: 4, name: 'ПО-22', course: 3, specialty: 'Прикладная информатика', student_count: 1}
    ],
    schedule: [
        {id: 1, group_name: 'ПИ-21', subject: 'Математический анализ', teacher_name: 'Иванов П.С.', day_of_week: 'понедельник', start_time: '09:00', end_time: '10:30', classroom: '301'},
        {id: 2, group_name: 'ПИ-21', subject: 'Физика', teacher_name: 'Смирнова А.В.', day_of_week: 'понедельник', start_time: '11:00', end_time: '12:30', classroom: '205'},
        {id: 3, group_name: 'ПИ-21', subject: 'Программирование', teacher_name: 'Иванов П.С.', day_of_week: 'вторник', start_time: '09:00', end_time: '10:30', classroom: '401'},
        {id: 4, group_name: 'ПИ-21', subject: 'Базы данных', teacher_name: 'Смирнова А.В.', day_of_week: 'среда', start_time: '11:00', end_time: '12:30', classroom: '302'},
        {id: 5, group_name: 'ПИ-22', subject: 'Дискретная математика', teacher_name: 'Иванов П.С.', day_of_week: 'четверг', start_time: '09:00', end_time: '10:30', classroom: '301'},
        {id: 6, group_name: 'ПИ-22', subject: 'Веб-разработка', teacher_name: 'Смирнова А.В.', day_of_week: 'пятница', start_time: '11:00', end_time: '12:30', classroom: '401'},
        {id: 7, group_name: 'ПО-21', subject: 'Алгоритмы и структуры', teacher_name: 'Иванов П.С.', day_of_week: 'понедельник', start_time: '13:00', end_time: '14:30', classroom: '303'},
        {id: 8, group_name: 'ПО-21', subject: 'Сети и телеком', teacher_name: 'Смирнова А.В.', day_of_week: 'вторник', start_time: '09:00', end_time: '10:30', classroom: '201'},
        {id: 9, group_name: 'ПО-22', subject: 'Операционные системы', teacher_name: 'Иванов П.С.', day_of_week: 'среда', start_time: '09:00', end_time: '10:30', classroom: '304'},
        {id: 10, group_name: 'ПО-22', subject: 'Компиляторы', teacher_name: 'Смирнова А.В.', day_of_week: 'четверг', start_time: '11:00', end_time: '12:30', classroom: '205'}
    ],
    grades: [
        {id: 1, student_id: 4, student_name: 'Сидоров Алексей', group_name: 'ПИ-21', subject: 'Математический анализ', grade: '5', date: '2026-01-15'},
        {id: 2, student_id: 4, student_name: 'Сидоров Алексей', group_name: 'ПИ-21', subject: 'Программирование', grade: '4', date: '2026-01-16'},
        {id: 3, student_id: 5, student_name: 'Козлов Дмитрий', group_name: 'ПИ-21', subject: 'Математический анализ', grade: '4', date: '2026-01-15'},
        {id: 4, student_id: 5, student_name: 'Козлов Дмитрий', group_name: 'ПИ-21', subject: 'Физика', grade: '5', date: '2026-01-17'},
        {id: 5, student_id: 6, student_name: 'Новикова Мария', group_name: 'ПИ-22', subject: 'Дискретная математика', grade: '5', date: '2026-01-18'},
        {id: 6, student_id: 6, student_name: 'Новикова Мария', group_name: 'ПИ-22', subject: 'Веб-разработка', grade: '5', date: '2026-01-19'},
        {id: 7, student_id: 8, student_name: 'Соколова Елена', group_name: 'ПО-21', subject: 'Алгоритмы и структуры', grade: '5', date: '2026-01-15'},
        {id: 8, student_id: 8, student_name: 'Соколова Елена', group_name: 'ПО-21', subject: 'Сети и телеком', grade: '4', date: '2026-01-16'}
    ],
    assignments: [
        {id: 1, student_id: 4, student_name: 'Сидоров Алексей', group_name: 'ПИ-21', subject: 'Программирование', title: 'Лабораторная №1', description: 'Разработка калькулятора на JavaScript', status: 'checked', teacher_comment: 'Отличная работа!', grade: '5'},
        {id: 2, student_id: 4, student_name: 'Сидоров Алексей', group_name: 'ПИ-21', subject: 'Базы данных', title: 'Курсовая работа', description: 'Проектирование БД для интернет-магазина', status: 'checked', teacher_comment: 'Хорошо, но есть недочеты', grade: '4'},
        {id: 3, student_id: 6, student_name: 'Новикова Мария', group_name: 'ПИ-22', subject: 'Веб-разработка', title: 'Лабораторная №2', description: 'Создание сайта-визитки', status: 'checked', teacher_comment: 'Превзошла ожидания!', grade: '5'},
        {id: 4, student_id: 7, student_name: 'Петров Иван', group_name: 'ПИ-22', subject: 'Веб-разработка', title: 'Лабораторная №2', description: 'Создание сайта-визитки', status: 'pending', teacher_comment: '', grade: ''}
    ],
    notifications: [
        {id: 1, title: 'Важное объявление', message: 'Завтра изменено расписание. Проверьте индивидуально!', target_type: 'all', created_at: '2026-01-20'},
        {id: 2, title: 'Сдача лабораторной', message: 'Напоминаем, что дедлайн лабораторной №3 - 25 января', target_type: 'students', target_group: 'ПИ-21', created_at: '2026-01-21'},
        {id: 3, title: 'Консультация', message: 'Состоится консультация по БД в пятницу в 14:00', target_type: 'students', target_group: 'ПИ-21', created_at: '2026-01-21'}
    ],
    library: [
        {id: 1, title: 'Методические указания по Матану', description: 'Рекомендации по выполнению домашних заданий', file_type: 'document', group_name: 'ПИ-21'},
        {id: 2, title: 'Презентация по Физике', description: 'Лекция о квантовой механике', file_type: 'presentation', group_name: 'ПИ-21'},
        {id: 3, title: 'Примеры кода на JavaScript', description: 'База примеров для лабораторных', file_type: 'code', group_name: 'ПИ-21'},
        {id: 4, title: 'Шпаргалка по Дискретке', description: 'Основные формулы и теоремы', file_type: 'document', group_name: 'ПИ-22'},
        {id: 5, title: 'Алгоритмы сортировки', description: 'Сравнение алгоритмов и их сложность', file_type: 'document', group_name: 'ПО-21'}
    ]
};

// === API ФУНКЦИИ ===
async function fetchFromAPI(action, params = {}) {
    const url = new URL('api/sync.php', window.location.origin);
    url.searchParams.append('action', action);
    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
    
    try {
        const response = await fetch(url);
        const data = await response.json();
        return data;
    } catch (error) {
        console.warn('API недоступна:', error);
        return null;
    }
}

async function postToAPI(action, data) {
    const formData = new FormData();
    formData.append('action', action);
    Object.keys(data).forEach(key => formData.append(key, data[key]));
    
    try {
        const response = await fetch('api/sync.php', { method: 'POST', body: formData });
        const result = await response.json();
        return result;
    } catch (error) {
        console.warn('API недоступна:', error);
        return null;
    }
}

// === TOAST УВЕДОМЛЕНИЯ (вместо alert сверху) ===
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// === ГЛАВНЫЕ ФУНКЦИИ СИНХРОНИЗАЦИИ ===
async function getUsers() {
    const apiData = await fetchFromAPI('get_users');
    if (apiData && apiData.success) return apiData.data;
    const local = localStorage.getItem(KEYS.USERS);
    return local ? JSON.parse(local) : [...MOCK_DATA.users];
}

async function addGroup(group) {
    const result = await postToAPI('add_group', {name: group.name, specialty: group.specialty, course: group.course});
    if (result && result.success) {
        const groups = getGroupsSync();
        groups.push({id: Date.now(), ...group, student_count: 0});
        localStorage.setItem(KEYS.GROUPS, JSON.stringify(groups));
        showToast('Группа добавлена!', 'success');
    }
    return result;
}

function getGroupsSync() {
    const data = localStorage.getItem(KEYS.GROUPS);
    return data ? JSON.parse(data) : [...MOCK_DATA.groups];
}

async function getGroups() {
    const apiData = await fetchFromAPI('get_groups');
    if (apiData && apiData.success) return apiData.data;
    return getGroupsSync();
}

async function getSchedule() {
    const apiData = await fetchFromAPI('get_schedule');
    if (apiData && apiData.success) return apiData.data;
    return [...MOCK_DATA.schedule];
}

async function getGrades() {
    const apiData = await fetchFromAPI('get_grades');
    if (apiData && apiData.success) return apiData.data;
    return [...MOCK_DATA.grades];
}

async function addGrade(grade) {
    const result = await postToAPI('add_grade', grade);
    if (result && result.success) {
        const grades = getGradesSync();
        grades.push({id: Date.now(), ...grade});
        localStorage.setItem(KEYS.GRADES, JSON.stringify(grades));
        showToast('Оценка выставлена!', 'success');
    }
    return result;
}

function getGradesSync() {
    const data = localStorage.getItem(KEYS.GRADES);
    return data ? JSON.parse(data) : [...MOCK_DATA.grades];
}

async function getAssignments() {
    const apiData = await fetchFromAPI('get_assignments');
    if (apiData && apiData.success) return apiData.data;
    return [...MOCK_DATA.assignments];
}

async function addAssignment(assignment) {
    const result = await postToAPI('add_assignment', assignment);
    if (result && result.success) {
        const assignments = getAssignmentsSync();
        assignments.push({id: Date.now(), ...assignment, status: 'pending'});
        localStorage.setItem(KEYS.ASSIGNMENTS, JSON.stringify(assignments));
        showToast('Работа загружена!', 'success');
    }
    return result;
}

async function updateAssignment(id, data) {
    const result = await postToAPI('update_assignment', {id, ...data});
    if (result && result.success) {
        const assignments = getAssignmentsSync();
        const idx = assignments.findIndex(a => a.id === id);
        if (idx !== -1) assignments[idx] = {...assignments[idx], ...data};
        localStorage.setItem(KEYS.ASSIGNMENTS, JSON.stringify(assignments));
        showToast('Работа обновлена!', 'success');
    }
    return result;
}

function getAssignmentsSync() {
    const data = localStorage.getItem(KEYS.ASSIGNMENTS);
    return data ? JSON.parse(data) : [...MOCK_DATA.assignments];
}

async function getJournal() {
    const apiData = await fetchFromAPI('get_journal');
    if (apiData && apiData.success) return apiData.data;
    return [];
}

async function addJournal(entry) {
    const result = await postToAPI('add_journal', entry);
    if (result && result.success) showToast('Запись добавлена!', 'success');
    return result;
}

async function getNotifications() {
    const apiData = await fetchFromAPI('get_notifications');
    if (apiData && apiData.success) return apiData.data;
    return [...MOCK_DATA.notifications];
}

async function addNotification(notification) {
    const result = await postToAPI('add_notification', notification);
    if (result && result.success) showToast('Уведомление отправлено!', 'success');
    return result;
}

async function getLibrary() {
    const apiData = await fetchFromAPI('get_library');
    if (apiData && apiData.success) return apiData.data;
    return [...MOCK_DATA.library];
}

async function addLibrary(item) {
    const result = await postToAPI('add_library', item);
    if (result && result.success) showToast('Материал добавлен!', 'success');
    return result;
}

async function addUser(user) {
    const result = await postToAPI('add_user', user);
    if (result && result.success) showToast('Пользователь добавлен!', 'success');
    return result;
}

window.Sync = {
    // Пользователи
    getUsers, addUser,
    // Группы
    getGroups, addGroup, getGroupsSync,
    // Расписание
    getSchedule,
    // Оценки
    getGrades, addGrade, getGradesSync,
    // Задания
    getAssignments, addAssignment, updateAssignment, getAssignmentsSync,
    // Журнал
    getJournal, addJournal,
    // Уведомления
    getNotifications, addNotification,
    // Библиотека
    getLibrary, addLibrary,
    // Утилиты
    showToast
};