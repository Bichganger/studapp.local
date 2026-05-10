// Админ-панель: Библиотека работ
(function() {
    'use strict';

    const API_URL = 'api/library.php';
    let libraryWorks = [];
    let currentFilter = { status: 'pending', type: '', subject: '' };

    // Загрузка работ
    async function loadWorks() {
        try {
            const params = new URLSearchParams(currentFilter);
            const response = await fetch(API_URL + '?' + params.toString());
            const result = await response.json();
            
            if (result.success) {
                libraryWorks = result.data;
                renderWorks();
                updateStats();
            }
        } catch (error) {
            console.error('Ошибка загрузки работ:', error);
            showNotification('Ошибка загрузки работ', 'error');
        }
    }

    // Одобрение работы
    async function approveWork(id) {
        if (!confirm('Одобрить эту работу?')) return;
        
        try {
            const response = await fetch(API_URL + '?action=approve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Работа одобрена', 'success');
                await loadWorks();
            } else {
                showNotification(result.message || 'Ошибка', 'error');
            }
        } catch (error) {
            console.error('Ошибка одобрения:', error);
            showNotification('Ошибка одобрения работы', 'error');
        }
    }

    // Отклонение работы
    async function rejectWork(id) {
        if (!confirm('Отклонить эту работу?')) return;
        
        try {
            const response = await fetch(API_URL + '?action=reject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Работа отклонена', 'success');
                await loadWorks();
            } else {
                showNotification(result.message || 'Ошибка', 'error');
            }
        } catch (error) {
            console.error('Ошибка отклонения:', error);
            showNotification('Ошибка отклонения работы', 'error');
        }
    }

    // Удаление работы
    async function deleteWork(id) {
        if (!confirm('Удалить эту работу?')) return;
        
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
                showNotification('Работа удалена', 'success');
                await loadWorks();
            } else {
                showNotification(result.message || 'Ошибка', 'error');
            }
        } catch (error) {
            console.error('Ошибка удаления:', error);
            showNotification('Ошибка удаления работы', 'error');
        }
    }

    // Скачивание работы (симуляция)
    function downloadWork(work) {
        showNotification(`Скачивание: ${work.title}`, 'info');
        // В реальной реализации здесь будет ссылка на файл
    }

    // Просмотр работы
    function viewWork(work) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        
        const statusMap = {
            'pending': { class: 'warning', text: 'На проверке' },
            'approved': { class: 'success', text: 'Одобрено' },
            'rejected': { class: 'danger', text: 'Отклонено' }
        };
        
        const status = statusMap[work.status] || statusMap.pending;
        
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${escapeHtml(work.title)}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Тип:</strong> ${work.type_ru || work.work_type}</p>
                        <p><strong>Предмет:</strong> ${escapeHtml(work.subject || '-')}</p>
                        <p><strong>Студент:</strong> ${escapeHtml(work.student_name || '-')}</p>
                        ${work.group_name ? `<p><strong>Группа:</strong> ${escapeHtml(work.group_name)}</p>` : ''}
                        <p><strong>Статус:</strong> <span class="badge bg-${status.class}">${status.text}</span></p>
                        <p><strong>Загружено:</strong> ${formatDateTime(work.created_at)}</p>
                        ${work.reviewed_at ? `<p><strong>Проверено:</strong> ${formatDateTime(work.reviewed_at)}</p>` : ''}
                        ${work.file_path ? `
                            <hr>
                            <a href="${escapeHtml(work.file_path)}" class="btn btn-primary" download>
                                <i class="bi bi-download me-1"></i>Скачать файл
                            </a>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        ${work.status === 'pending' ? `
                            <button type="button" class="btn btn-success" onclick="approveWork(${work.id})">
                                <i class="bi bi-check-lg"></i> Одобрить
                            </button>
                            <button type="button" class="btn btn-danger" onclick="rejectWork(${work.id})">
                                <i class="bi bi-x-lg"></i> Отклонить
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modal.addEventListener('hidden.bs.modal', () => modal.remove());
    }

    // Рендер работ
    function renderWorks() {
        const pendingContainer = document.querySelector('.work-card')?.parentElement;
        const approvedContainer = document.querySelector('.mt-4')?.nextElementSibling;
        
        const pendingWorks = libraryWorks.filter(w => w.status === 'pending');
        const approvedWorks = libraryWorks.filter(w => w.status === 'approved');
        const rejectedWorks = libraryWorks.filter(w => w.status === 'rejected');
        
        // Рендер ожидающих модерации
        if (pendingContainer) {
            if (pendingWorks.length === 0) {
                pendingContainer.innerHTML = '<div class="alert alert-info">Нет работ на проверке</div>';
            } else {
                pendingContainer.innerHTML = pendingWorks.map(work => createWorkCard(work)).join('');
            }
        }
        
        // Рендер одобренных (если есть контейнер)
        if (approvedContainer && approvedWorks.length > 0) {
            // Можно добавить секцию с одобренными работами
        }
    }

    // Создание карточки работы
    function createWorkCard(work) {
        const statusClass = {
            'pending': 'status-pending',
            'approved': 'status-approved',
            'rejected': 'status-rejected'
        }[work.status] || 'status-pending';
        
        const statusText = {
            'pending': 'На проверке',
            'approved': 'Одобрено',
            'rejected': 'Отклонено'
        }[work.status] || 'На проверке';
        
        const badgeClass = {
            'lab': 'secondary',
            'coursework': 'info',
            'essay': 'warning'
        }[work.work_type] || 'secondary';
        
        return `
            <div class="work-card">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${escapeHtml(work.title)}</h6>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person me-1"></i>${escapeHtml(work.student_name || 'Не указано')} • 
                                ${work.group_name ? `<i class="bi bi-people me-1"></i>${escapeHtml(work.group_name)} • ` : ''}
                                <i class="bi bi-calendar me-1"></i>Загружено: ${formatDate(work.created_at)}
                            </p>
                            <span class="badge bg-${badgeClass}">${work.type_ru || work.work_type}</span>
                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="downloadWork(${work.id})">
                                <i class="bi bi-download me-1"></i>Скачать
                            </button>
                            <button class="btn btn-sm btn-outline-info ms-1" onclick="viewWork(${work.id})">
                                <i class="bi bi-eye me-1"></i>Подробнее
                            </button>
                        </div>
                        <div class="text-end">
                            <span class="badge ${statusClass} mb-2">${statusText}</span>
                            ${work.status === 'pending' ? `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-success" onclick="approveWork(${work.id})">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="rejectWork(${work.id})">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="deleteWork(${work.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            ` : `
                                <button class="btn btn-sm btn-outline-secondary" onclick="viewWork(${work.id})">
                                    <i class="bi bi-eye"></i>
                                </button>
                            `}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Обновление статистики
    function updateStats() {
        const pendingCount = libraryWorks.filter(w => w.status === 'pending').length;
        const approvedCount = libraryWorks.filter(w => w.status === 'approved').length;
        const rejectedCount = libraryWorks.filter(w => w.status === 'rejected').length;
        
        // Обновляем карточки статистики
        const cards = document.querySelectorAll('.card.text-center h3');
        if (cards.length >= 3) {
            cards[0].textContent = pendingCount;
            cards[1].textContent = approvedCount;
            cards[2].textContent = rejectedCount;
        }
    }

    // Фильтрация
    function applyFilters() {
        const subjectFilter = document.querySelector('[name="subject"]') || document.querySelector('select:first-of-type');
        const typeFilter = document.querySelector('[name="type"]') || document.querySelector('select:nth-of-type(2)');
        const statusFilter = document.querySelector('[name="status"]') || document.querySelector('select:nth-of-type(3)');
        
        currentFilter.subject = subjectFilter?.value || '';
        currentFilter.type = typeFilter?.value || '';
        currentFilter.status = statusFilter?.value || '';
        
        loadWorks();
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
        loadWorks();

        // Фильтры
        const filterBtn = document.querySelector('.btn-primary:has(i.bi-filter)') || document.querySelector('button:contains("Применить")');
        if (filterBtn) {
            filterBtn.addEventListener('click', function(e) {
                e.preventDefault();
                applyFilters();
            });
        }
        
        // Применяем фильтры при изменении select
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', applyFilters);
        });
    });

    // Глобальные функции
    window.approveWork = approveWork;
    window.rejectWork = rejectWork;
    window.deleteWork = deleteWork;
    window.downloadWork = downloadWork;
    window.viewWork = viewWork;
})();
