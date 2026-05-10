// Админ-панель: Расписание
(function() {
    'use strict';

    const API_URL = 'api/schedule.php';
    const daysOrder = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
    let currentSchedule = [];
    let editingId = null;

    // Загрузка расписания
    async function loadSchedule() {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();
            
            if (result.success) {
                currentSchedule = result.data;
                renderScheduleTable();
                updateGroupOptions();
            }
        } catch (error) {
            console.error('Ошибка загрузки расписания:', error);
            showNotification('Ошибка загрузки расписания', 'error');
        }
    }

    // Добавление/обновление пары
    async function saveSchedule(formData) {
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
                showNotification(editingId ? 'Пара обновлена' : 'Пара добавлена', 'success');
                resetForm();
                await loadSchedule();
            } else {
                showNotification(result.message || 'Ошибка', 'error');
            }
        } catch (error) {
            console.error('Ошибка сохранения:', error);
            showNotification('Ошибка сохранения', 'error');
        }
    }

    // Удаление пары
    async function deleteSchedule(id) {
        if (!confirm('Удалить эту пару?')) return;
        
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
                showNotification('Пара удалена', 'success');
                await loadSchedule();
            } else {
                showNotification(result.message || 'Ошибка', 'error');
            }
        } catch (error) {
            console.error('Ошибка удаления:', error);
            showNotification('Ошибка удаления', 'error');
        }
    }

    // Редактирование пары
    function editSchedule(id) {
        const item = currentSchedule.find(s => s.id === id);
        if (!item) return;
        
        editingId = id;
        
        // Заполняем форму
        document.querySelector('[name="day"]').value = item.day_of_week;
        document.querySelector('[name="group"]').value = item.group_name;
        document.querySelector('[name="subject"]').value = item.subject;
        document.querySelector('[name="start"]').value = item.start_time;
        document.querySelector('[name="end"]').value = item.end_time || '';
        document.querySelector('[name="type"]').value = item.type;
        document.querySelector('[name="classroom"]').value = item.classroom || '';
        document.querySelector('[name="teacher"]').value = item.teacher || '';
        
        // Скролл к форме
        document.querySelector('.card-header').scrollIntoView({ behavior: 'smooth' });
    }

    // Сброс формы
    function resetForm() {
        editingId = null;
        document.querySelector('form').reset();
    }

    // Рендер таблицы расписания
    function renderScheduleTable() {
        const tbody = document.querySelector('.schedule-table tbody');
        if (!tbody) return;
        
        if (currentSchedule.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Расписание пусто</td></tr>';
            return;
        }
        
        // Сортировка по дням и времени
        const sorted = [...currentSchedule].sort((a, b) => {
            const dayDiff = daysOrder.indexOf(a.day_of_week) - daysOrder.indexOf(b.day_of_week);
            if (dayDiff !== 0) return dayDiff;
            return a.start_time.localeCompare(b.start_time);
        });
        
        tbody.innerHTML = sorted.map(item => {
            const badgeClass = {
                'lecture': 'info',
                'practice': 'success',
                'lab': 'warning',
                'seminar': 'secondary'
            }[item.type] || 'secondary';
            
            const timeEnd = item.end_time || calculateEndTime(item.start_time);
            
            return `
                <tr>
                    <td>${item.day_of_week}</td>
                    <td>${formatTime(item.start_time)} - ${formatTime(timeEnd)}</td>
                    <td>${item.group_name}</td>
                    <td>${escapeHtml(item.subject)}</td>
                    <td><span class="badge bg-${badgeClass}">${item.type_ru || item.type}</span></td>
                    <td>${escapeHtml(item.classroom || '-')}</td>
                    <td>${escapeHtml(item.teacher || '-')}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editSchedule(${item.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule(${item.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Обновление опций групп
    function updateGroupOptions() {
        const groups = [...new Set(currentSchedule.map(s => s.group_name))];
        const groupSelect = document.querySelector('[name="group"]');
        if (!groupSelect) return;
        
        // Сохраняем текущее значение
        const currentValue = groupSelect.value;
        
        // Получаем все опции (включая статические)
        const staticOptions = Array.from(groupSelect.options).filter(opt => !opt.value || opt.value.startsWith('ИТ') || opt.value.startsWith('ЭК') || opt.value.startsWith('ПС'));
        
        // Добавляем новые группы
        groups.forEach(group => {
            if (!Array.from(groupSelect.options).some(opt => opt.value === group)) {
                const option = document.createElement('option');
                option.value = group;
                option.textContent = group;
                groupSelect.appendChild(option);
            }
        });
    }

    // Вспомогательные функции
    function calculateEndTime(startTime) {
        const [hours, minutes] = startTime.split(':').map(Number);
        const endTime = new Date(0, 0, 0, hours, minutes + 90); // 90 минут пара
        return String(endTime.getHours()).padStart(2, '0') + ':' + String(endTime.getMinutes()).padStart(2, '0');
    }

    function formatTime(timeStr) {
        if (!timeStr) return '';
        return timeStr.substring(0, 5);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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
        loadSchedule();

        // Форма добавления/редактирования
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = {
                    day_of_week: form.querySelector('[name="day"]').value,
                    group_name: form.querySelector('[name="group"]').value,
                    subject: form.querySelector('[name="subject"]').value,
                    start_time: form.querySelector('[name="start"]').value,
                    end_time: form.querySelector('[name="end"]').value,
                    type: form.querySelector('[name="type"]').value,
                    classroom: form.querySelector('[name="classroom"]').value,
                    teacher: form.querySelector('[name="teacher"]').value
                };
                
                await saveSchedule(formData);
            });
        }

        // Кнопка экспорта
        const exportBtn = document.querySelector('button[title="Экспорт"], button:has(i.bi-download)');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                exportToCSV();
            });
        }
    });

    // Экспорт в CSV
    function exportToCSV() {
        if (currentSchedule.length === 0) {
            showNotification('Нет данных для экспорта', 'error');
            return;
        }
        
        const headers = ['День', 'Время начала', 'Время конца', 'Группа', 'Предмет', 'Тип', 'Аудитория', 'Преподаватель'];
        const rows = currentSchedule.map(item => [
            item.day_of_week,
            item.start_time,
            item.end_time || calculateEndTime(item.start_time),
            item.group_name,
            item.subject,
            item.type_ru || item.type,
            item.classroom || '',
            item.teacher || ''
        ]);
        
        const csvContent = [
            headers.join(';'),
            ...rows.map(row => row.map(cell => `"${cell}"`).join(';'))
        ].join('\n');
        
        const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'schedule.csv';
        link.click();
        
        showNotification('Расписание экспортировано', 'success');
    }

    // Глобальная функция для редактирования
    window.editSchedule = editSchedule;
    window.deleteSchedule = deleteSchedule;
})();
