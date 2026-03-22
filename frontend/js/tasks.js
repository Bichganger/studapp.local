// js/tasks.js - Управление задачами
class TaskManager {
    constructor() {
        this.tasks = this.loadTasks();
        this.taskIdCounter = this.tasks.length > 0 ? Math.max(...this.tasks.map(t => t.id)) + 1 : 1;
        this.init();
    }

    // Загрузка задач из localStorage
    loadTasks() {
        const saved = localStorage.getItem('studapp_tasks');
        if (saved) {
            return JSON.parse(saved);
        }
        
        // Задачи по умолчанию
        return [
            {
                id: 1,
                title: "Лабораторная работа №5",
                subject: "Программирование",
                priority: "high",
                deadline: this.getDateString(1),
                completed: false,
                createdAt: new Date().toISOString()
            },
            {
                id: 2,
                title: "Эссе: История России",
                subject: "История",
                priority: "medium",
                deadline: this.getDateString(7),
                completed: false,
                createdAt: new Date().toISOString()
            },
            {
                id: 3,
                title: "Решение задач 12-24",
                subject: "Математика",
                priority: "low",
                deadline: this.getDateString(3),
                completed: false,
                createdAt: new Date().toISOString()
            }
        ];
    }

    getDateString(daysToAdd) {
        const date = new Date();
        date.setDate(date.getDate() + daysToAdd);
        return date.toISOString().split('T')[0];
    }

    init() {
        this.renderTasks();
        this.setupEventListeners();
        this.updateTaskCount();
    }

    // Сохранение задач
    saveTasks() {
        localStorage.setItem('studapp_tasks', JSON.stringify(this.tasks));
        this.updateTaskCount();
    }

    // Добавление задачи
    addTask(taskData) {
        const newTask = {
            id: this.taskIdCounter++,
            title: taskData.title.trim(),
            subject: taskData.subject.trim(),
            priority: taskData.priority || 'medium',
            deadline: taskData.deadline || null,
            completed: false,
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };

        this.tasks.unshift(newTask); // Добавляем в начало
        this.saveTasks();
        this.renderTasks();
        
        StudApp.showNotification(`Задача "${newTask.title}" добавлена`, 'success');
        return newTask;
    }

    // Удаление задачи
    deleteTask(taskId) {
        this.tasks = this.tasks.filter(task => task.id !== taskId);
        this.saveTasks();
        this.renderTasks();
        StudApp.showNotification('Задача удалена', 'info');
    }

    // Переключение статуса задачи
    toggleTaskStatus(taskId) {
        const task = this.tasks.find(t => t.id === taskId);
        if (task) {
            task.completed = !task.completed;
            task.updatedAt = new Date().toISOString();
            this.saveTasks();
            this.renderTasks();
            
            const status = task.completed ? 'выполнена' : 'в работе';
            StudApp.showNotification(`Задача "${task.title}" ${status}`, 'info');
        }
    }

    // Редактирование задачи
    updateTask(taskId, updatedData) {
        const taskIndex = this.tasks.findIndex(t => t.id === taskId);
        if (taskIndex !== -1) {
            this.tasks[taskIndex] = {
                ...this.tasks[taskIndex],
                ...updatedData,
                updatedAt: new Date().toISOString()
            };
            this.saveTasks();
            this.renderTasks();
            StudApp.showNotification('Задача обновлена', 'success');
            return true;
        }
        return false;
    }

    // Форматирование даты
    formatDate(dateString) {
        if (!dateString) return 'Без срока';
        
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = date - now;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 0) return 'Сегодня';
        if (diffDays === 1) return 'Завтра';
        if (diffDays < 0) return `Просрочено на ${Math.abs(diffDays)} дн.`;
        if (diffDays <= 7) return `Через ${diffDays} дн.`;
        
        return date.toLocaleDateString('ru-RU', { 
            day: 'numeric', 
            month: 'short' 
        });
    }

    // Получение цвета приоритета
    getPriorityColor(priority) {
        const colors = {
            'high': 'danger',
            'medium': 'warning',
            'low': 'info',
            'none': 'secondary'
        };
        return colors[priority] || 'secondary';
    }

    // Получение текста приоритета
    getPriorityText(priority) {
        const texts = {
            'high': 'Высокий',
            'medium': 'Средний',
            'low': 'Низкий',
            'none': 'Без срока'
        };
        return texts[priority] || 'Без срока';
    }

    // Рендеринг задач
    renderTasks() {
        const container = document.getElementById('tasksContainer');
        if (!container) return;

        // Сортируем: сначала невыполненные, затем по дате
        const sortedTasks = [...this.tasks].sort((a, b) => {
            if (a.completed !== b.completed) return a.completed ? 1 : -1;
            if (!a.deadline && b.deadline) return 1;
            if (a.deadline && !b.deadline) return -1;
            return new Date(a.deadline) - new Date(b.deadline);
        });

        container.innerHTML = '';

        sortedTasks.forEach(task => {
            const taskElement = this.createTaskElement(task);
            container.appendChild(taskElement);
        });
    }

    // Создание элемента задачи
    createTaskElement(task) {
        const element = document.createElement('div');
        element.className = `task-item d-flex justify-content-between align-items-center p-3 border-bottom ${task.completed ? 'completed' : ''}`;
        element.dataset.taskId = task.id;

        const priorityColor = this.getPriorityColor(task.priority);
        const priorityText = this.getPriorityText(task.priority);
        const deadlineText = this.formatDate(task.deadline);

        element.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="form-check me-3">
                    <input class="form-check-input task-checkbox" type="checkbox" 
                           ${task.completed ? 'checked' : ''}
                           id="task-${task.id}">
                </div>
                <div>
                    <div class="task-title fw-medium ${task.completed ? 'text-decoration-line-through text-muted' : ''}">
                        ${task.title}
                    </div>
                    <div class="text-muted small">${task.subject}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-${priorityColor}">${priorityText}</span>
                <small class="text-muted">${deadlineText}</small>
                <div class="btn-group btn-group-sm ms-2">
                    <button class="btn btn-outline-primary edit-task-btn" title="Редактировать">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger delete-task-btn" title="Удалить">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;

        // Обработчики событий
        const checkbox = element.querySelector('.task-checkbox');
        checkbox.addEventListener('change', () => this.toggleTaskStatus(task.id));

        const deleteBtn = element.querySelector('.delete-task-btn');
        deleteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (confirm('Удалить эту задачу?')) {
                this.deleteTask(task.id);
            }
        });

        const editBtn = element.querySelector('.edit-task-btn');
        editBtn.addEventListener('click', () => this.openEditModal(task));

        return element;
    }

    // Открытие модального окна редактирования
    openEditModal(task) {
        document.getElementById('editTaskId').value = task.id;
        document.getElementById('editTaskTitle').value = task.title;
        document.getElementById('editTaskSubject').value = task.subject;
        document.getElementById('editTaskPriority').value = task.priority;
        document.getElementById('editTaskDeadline').value = task.deadline || '';
        
        const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
        modal.show();
    }

    // Обновление счетчика задач
    updateTaskCount() {
        const countElement = document.getElementById('taskCount');
        if (countElement) {
            const pendingTasks = this.tasks.filter(t => !t.completed).length;
            countElement.textContent = pendingTasks;
        }
    }

    // Настройка обработчиков событий
    setupEventListeners() {
        // Форма добавления задачи
        const addForm = document.getElementById('addTaskForm');
        if (addForm) {
            addForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleAddTask();
            });
        }

        // Форма редактирования задачи
        const editForm = document.getElementById('editTaskForm');
        if (editForm) {
            editForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleEditTask();
            });
        }

        // Автозаполнение даты (завтра)
        const deadlineInput = document.getElementById('taskDeadline');
        if (deadlineInput) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            deadlineInput.min = new Date().toISOString().split('T')[0];
            deadlineInput.value = tomorrow.toISOString().split('T')[0];
        }

        // Показать/скрыть выполненные
        const toggleBtn = document.getElementById('toggleCompletedBtn');
        if (toggleBtn) {
            let showCompleted = true;
            toggleBtn.addEventListener('click', () => {
                showCompleted = !showCompleted;
                document.querySelectorAll('.task-item.completed').forEach(item => {
                    item.style.display = showCompleted ? 'flex' : 'none';
                });
                toggleBtn.innerHTML = showCompleted ? 
                    '<i class="bi bi-eye-slash me-1"></i>Скрыть выполненные' :
                    '<i class="bi bi-eye me-1"></i>Показать выполненные';
            });
        }

        // Сортировка
        const sortBtn = document.getElementById('sortTasksBtn');
        if (sortBtn) {
            let sortBy = 'deadline';
            sortBtn.addEventListener('click', () => {
                const sorts = ['deadline', 'priority', 'title', 'date'];
                const currentIndex = sorts.indexOf(sortBy);
                sortBy = sorts[(currentIndex + 1) % sorts.length];
                
                this.tasks.sort((a, b) => {
                    if (sortBy === 'deadline') {
                        if (!a.deadline && b.deadline) return 1;
                        if (a.deadline && !b.deadline) return -1;
                        return new Date(a.deadline) - new Date(b.deadline);
                    }
                    if (sortBy === 'priority') {
                        const priorityOrder = { 'high': 1, 'medium': 2, 'low': 3, 'none': 4 };
                        return priorityOrder[a.priority] - priorityOrder[b.priority];
                    }
                    if (sortBy === 'title') {
                        return a.title.localeCompare(b.title);
                    }
                    return new Date(b.createdAt) - new Date(a.createdAt);
                });
                
                this.renderTasks();
                StudApp.showNotification(`Сортировка: ${sortBy === 'deadline' ? 'по дате' : sortBy === 'priority' ? 'по приоритету' : 'по названию'}`, 'info');
            });
        }

        // Экспорт
        const exportBtn = document.getElementById('exportTasksBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                this.exportTasks();
            });
        }
    }

    // Обработка добавления задачи
    handleAddTask() {
        const title = document.getElementById('taskTitle').value.trim();
        const subject = document.getElementById('taskSubject').value.trim();
        const priority = document.getElementById('taskPriority').value;
        const deadline = document.getElementById('taskDeadline').value || null;

        if (!title || !subject) {
            StudApp.showNotification('Заполните название и предмет', 'error');
            return;
        }

        this.addTask({ title, subject, priority, deadline });

        // Закрываем модальное окно
        const modal = bootstrap.Modal.getInstance(document.getElementById('addTaskModal'));
        modal.hide();
        
        // Сбрасываем форму
        document.getElementById('addTaskForm').reset();
        
        // Устанавливаем дату на завтра
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('taskDeadline').value = tomorrow.toISOString().split('T')[0];
    }

    // Обработка редактирования задачи
    handleEditTask() {
        const taskId = parseInt(document.getElementById('editTaskId').value);
        const title = document.getElementById('editTaskTitle').value.trim();
        const subject = document.getElementById('editTaskSubject').value.trim();
        const priority = document.getElementById('editTaskPriority').value;
        const deadline = document.getElementById('editTaskDeadline').value || null;

        if (!title || !subject) {
            StudApp.showNotification('Заполните название и предмет', 'error');
            return;
        }

        if (this.updateTask(taskId, { title, subject, priority, deadline })) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
            modal.hide();
        }
    }

    // Экспорт задач
    exportTasks() {
        if (this.tasks.length === 0) {
            StudApp.showNotification('Нет задач для экспорта', 'warning');
            return;
        }

        const csvContent = [
            ['Название', 'Предмет', 'Приоритет', 'Дедлайн', 'Статус'],
            ...this.tasks.map(task => [
                task.title,
                task.subject,
                this.getPriorityText(task.priority),
                task.deadline ? this.formatDate(task.deadline) : 'Без срока',
                task.completed ? 'Выполнена' : 'В работе'
            ])
        ].map(row => row.join(',')).join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `studapp_tasks_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);

        StudApp.showNotification('Задачи экспортированы в CSV', 'success');
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    window.taskManager = new TaskManager();
});