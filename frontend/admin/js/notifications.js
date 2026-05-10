// Админ-панель: Уведомления
(function() {
    'use strict';

    const API_URL = 'api/notifications.php';
    let notificationsHistory = [];

    // Загрузка истории уведомлений
    async function loadNotifications() {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();
            
            if (result.success) {
                notificationsHistory = result.data;
                renderHistoryTable();
            }
        } catch (error) {
            console.error('Ошибка загрузки уведомлений:', error);
        }
    }

    // Отправка уведомления
    async function sendNotification(formData) {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Уведомление отправлено', 'success');
                resetForm();
                await loadNotifications();
                return true;
            } else {
                showNotification(result.message || 'Ошибка отправки', 'error');
                return false;
            }
        } catch (error) {
            console.error('Ошибка отправки:', error);
            showNotification('Ошибка отправки уведомления', 'error');
            return false;
        }
    }

    // Просмотр уведомления
    async function viewNotification(id) {
        try {
            const response = await fetch(API_URL + '?action=view', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            
            const result = await response.json();
            
            if (result.success) {
                const notification = result.data;
                showNotificationModal(notification);
            } else {
                showNotification(result.message || 'Ошибка', 'error');
            }
        } catch (error) {
            console.error('Ошибка просмотра:', error);
            showNotification('Ошибка просмотра уведомления', 'error');
        }
    }

    // Показ модального окна уведомления
    function showNotificationModal(notification) {
        const targetMap = {
            'all': 'Все пользователи',
            'students': 'Студенты',
            'teachers': 'Преподаватели',
            'group': `Группа: ${notification.target_group}`
        };
        
        const statusMap = {
            'pending': { class: 'warning', text: 'В ожидании' },
            'sent': { class: 'success', text: 'Отправлено' },
            'failed': { class: 'danger', text: 'Ошибка' }
        };
        
        const status = statusMap[notification.status] || statusMap.pending;
        
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${escapeHtml(notification.title)}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Дата:</strong> ${formatDateTime(notification.created_at)}</p>
                        <p><strong>Получатели:</strong> ${targetMap[notification.target_type] || notification.target_type}</p>
                        ${notification.target_group ? `<p><strong>Группа:</strong> ${escapeHtml(notification.target_group)}</p>` : ''}
                        <p><strong>Email рассылка:</strong> ${notification.send_email ? 'Да' : 'Нет'}</p>
                        <p><strong>Статус:</strong> <span class="badge bg-${status.class}">${status.text}</span></p>
                        ${notification.sent_at ? `<p><strong>Отправлено:</strong> ${formatDateTime(notification.sent_at)}</p>` : ''}
                        <hr>
                        <h6>Текст сообщения:</h6>
                        <p style="white-space: pre-wrap;">${escapeHtml(notification.message)}</p>
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
        document.querySelector('form').reset();
        document.getElementById('groupSelect').style.display = 'none';
    }

    // Рендер таблицы истории
    function renderHistoryTable() {
        const tbody = document.querySelector('.history-table tbody');
        if (!tbody) return;
        
        if (notificationsHistory.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">История пуста</td></tr>';
            return;
        }
        
        const statusMap = {
            'pending': { class: 'warning', text: 'В ожидании' },
            'sent': { class: 'success', text: 'Отправлено' },
            'failed': { class: 'danger', text: 'Ошибка' }
        };
        
        const targetMap = {
            'all': 'Все',
            'students': 'Студенты',
            'teachers': 'Преподаватели',
            'group': (n) => n.target_group || 'Группа'
        };
        
        tbody.innerHTML = notificationsHistory.map(notif => {
            const status = statusMap[notif.status] || statusMap.pending;
            const target = typeof targetMap[notif.target_type] === 'function' 
                ? targetMap[notif.target_type](notif)
                : targetMap[notif.target_type] || notif.target_type;
            
            return `
                <tr>
                    <td>${formatDate(notif.created_at)}</td>
                    <td>${escapeHtml(notif.title)}</td>
                    <td>${escapeHtml(target)}</td>
                    <td><span class="badge bg-${status.class}">${status.text}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary" onclick="viewNotification(${notif.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
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

    function formatDateTime(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleString('ru-RU');
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
        loadNotifications();

        // Обработчик выбора получателей
        const receiverSelect = document.querySelector('[name="receivers"]') || document.querySelector('select.form-select');
        if (receiverSelect) {
            receiverSelect.addEventListener('change', function() {
                const groupSelect = document.getElementById('groupSelect');
                if (groupSelect) {
                    groupSelect.style.display = this.value === 'group' ? 'block' : 'none';
                }
            });
        }

        // Форма отправки уведомления
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = {
                    title: form.querySelector('[name="title"]')?.value || form.querySelectorAll('input[type="text"]')[0]?.value,
                    message: form.querySelector('[name="message"]')?.value || form.querySelector('textarea')?.value,
                    target_type: form.querySelector('[name="target_type"]')?.value || form.querySelector('select')?.value,
                    target_group: document.getElementById('groupSelect').style.display === 'block' 
                        ? (form.querySelector('#groupSelect select')?.value || form.querySelectorAll('select')[1]?.value)
                        : null,
                    send_email: form.querySelector('[name="send_email"]')?.checked || document.getElementById('sendEmail')?.checked
                };
                
                // Корректировка для разных версий формы
                if (!formData.target_type) {
                    const selects = form.querySelectorAll('select');
                    if (selects.length >= 2) {
                        formData.target_type = selects[0].value;
                    }
                }
                if (!formData.title) {
                    const inputs = form.querySelectorAll('input[type="text"]');
                    if (inputs.length >= 1) {
                        formData.title = inputs[0].value;
                    }
                }
                if (!formData.message) {
                    const textareas = form.querySelectorAll('textarea');
                    if (textareas.length >= 1) {
                        formData.message = textareas[0].value;
                    }
                }
                
                await sendNotification(formData);
            });
        }
    });

    // Глобальная функция
    window.viewNotification = viewNotification;
})();
