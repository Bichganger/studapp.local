// Админ-панель: Группы
(function() {
    'use strict';

    const API_URL = 'api/groups.php';
    let currentGroups = [];
    let editingId = null;

    // Загрузка групп
    async function loadGroups() {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();
            
            if (result.success) {
                currentGroups = result.data;
                renderGroups();
            }
        } catch (error) {
            console.error('Ошибка загрузки групп:', error);
            showNotification('Ошибка загрузки групп', 'error');
        }
    }

    // Создание/обновление группы
    async function saveGroup(formData) {
        try {
            const url = editingId ? `${API_URL}` : API_URL;
            const method = editingId ? 'PUT' : 'POST';
            
            const data = { ...formData };
            if (editingId) data.id = editingId;
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification(editingId ? 'Группа обновлена' : 'Группа создана', 'success');
                resetForm();
                await loadGroups();
            } else {
                showNotification(result.message || 'Ошибка', 'error');
            }
        } catch (error) {
            console.error('Ошибка сохранения:', error);
            showNotification('Ошибка сохранения', 'error');
        }
    }

    // Удаление группы
    async function deleteGroup(id) {
        if (!confirm('Удалить эту группу? Все связанные данные могут быть потеряны.')) return;
        
        try {
            const response = await fetch(API_URL, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Группа удалена', 'success');
                await loadGroups();
            } else {
                showNotification(result.message || 'Ошибка', 'error');
            }
        } catch (error) {
            console.error('Ошибка удаления:', error);
            showNotification('Ошибка удаления', 'error');
        }
    }

    // Редактирование группы
    function editGroup(id) {
        const group = currentGroups.find(g => g.id === id);
        if (!group) return;
        
        editingId = id;
        
        // Заполняем форму
        document.querySelector('[name="group_name"]').value = group.group_name;
        document.querySelector('[name="specialty"]').value = group.specialty || '';
        document.querySelector('[name="course"]').value = group.course || 1;
        document.querySelector('[name="student_count"]').value = group.student_count || 0;
        document.querySelector('[name="head_of_department"]').value = group.head_of_department || '';
        
        // Скролл к форме
        document.querySelector('.card-header').scrollIntoView({ behavior: 'smooth' });
        
        // Меняем текст кнопки
        const submitBtn = document.querySelector('form button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Сохранить';
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-primary');
        }
    }

    // Просмотр группы
    function viewGroup(id) {
        const group = currentGroups.find(g => g.id === id);
        if (!group) return;
        
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Информация о группе</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Название:</strong> ${escapeHtml(group.group_name)}</p>
                        <p><strong>Специальность:</strong> ${escapeHtml(group.specialty || '-')}</p>
                        <p><strong>Курс:</strong> ${group.course} курс</p>
                        <p><strong>Студентов:</strong> ${group.student_count || 0}</p>
                        <p><strong>Зав. кафедрой:</strong> ${escapeHtml(group.head_of_department || '-')}</p>
                        <p><strong>Создана:</strong> ${formatDate(group.created_at)}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modal.addEventListener('hidden.bs.modal', () => modal.remove());
    }

    // Сброс формы
    function resetForm() {
        editingId = null;
        document.querySelector('form').reset();
        
        const submitBtn = document.querySelector('form button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Создать группу';
            submitBtn.classList.add('btn-success');
            submitBtn.classList.remove('btn-primary');
        }
    }

    // Рендер карточек групп
    function renderGroups() {
        const container = document.querySelector('.row.g-3');
        if (!container) return;
        
        // Находим контейнер для карточек (пропускаем форму)
        let groupsContainer = container;
        const formCard = container.querySelector('.card');
        if (formCard) {
            groupsContainer = container.nextElementSibling?.querySelector('.row.g-3') || container;
        }
        
        if (currentGroups.length === 0) {
            groupsContainer.innerHTML = '<div class="col-12"><div class="alert alert-info">Групп пока нет. Создайте первую группу!</div></div>';
            return;
        }
        
        const colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
        
        groupsContainer.innerHTML = currentGroups.map((group, index) => {
            const color = colors[index % colors.length];
            
            return `
                <div class="col-md-6">
                    <div class="group-card">
                        <div class="p-3 bg-${color} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">${escapeHtml(group.group_name)}</h5>
                                <span class="badge bg-light text-${color}">${group.student_count || 0} студентов</span>
                            </div>
                        </div>
                        <div class="p-3">
                            <p class="mb-2"><i class="bi bi-mortarboard me-1"></i><strong>${escapeHtml(group.specialty || 'Не указано')}</strong></p>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-calendar me-1"></i>${group.course} курс • 
                                Создана: ${formatDate(group.created_at)}
                            </p>
                            <div class="d-flex gap-2 mt-3">
                                <button class="btn btn-sm btn-outline-primary flex-grow-1" onclick="viewGroup(${group.id})">
                                    <i class="bi bi-eye me-1"></i>Просмотреть
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="editGroup(${group.id})">
                                    <i class="bi bi-pencil me-1"></i>Редактировать
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteGroup(${group.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Вспомогательные функции
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('ru-RU');
    }

    function showNotification(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show fixed-top`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => notification.remove(), 3000);
    }

    // Инициализация
    document.addEventListener('DOMContentLoaded', function() {
        loadGroups();

        // Форма создания группы
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = {
                    group_name: form.querySelector('[name="group_name"]').value,
                    specialty: form.querySelector('[name="specialty"]').value,
                    course: parseInt(form.querySelector('[name="course"]').value) || 1,
                    student_count: parseInt(form.querySelector('[name="student_count"]').value) || 0,
                    head_of_department: form.querySelector('[name="head_of_department"]').value
                };
                
                await saveGroup(formData);
            });
        }
    });

    // Глобальные функции
    window.viewGroup = viewGroup;
    window.editGroup = editGroup;
    window.deleteGroup = deleteGroup;
})();
