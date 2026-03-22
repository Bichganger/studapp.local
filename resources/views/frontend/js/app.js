// Глобальный объект приложения
const StudApp = {
    // Показ уведомлений
    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container') || (() => {
            const div = document.createElement('div');
            div.id = 'notification-container';
            div.style.cssText = 'position:fixed; top:20px; right:20px; z-index:9999;';
            document.body.appendChild(div);
            return div;
        })();

        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };

        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi ${icons[type] || icons.info} me-2"></i>
                <div>${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        container.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    },

    // Валидация формы
    validateForm(form) {
        let isValid = true;
        form.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        return isValid;
    },

    // Форматирование даты
    formatDate(dateString) {
        if (!dateString) return 'Без срока';
        const date = new Date(dateString);
        const now = new Date();
        const diffDays = Math.ceil((date - now) / (1000 * 60 * 60 * 24));
        if (diffDays === 0) return 'Сегодня';
        if (diffDays === 1) return 'Завтра';
        if (diffDays < 0) return `Просрочено на ${Math.abs(diffDays)} дн.`;
        if (diffDays <= 7) return `Через ${diffDays} дн.`;
        return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' });
    },

    // Загрузка компонентов (если потребуется)
    loadComponent(url, containerId) {
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById(containerId).innerHTML = html;
            })
            .catch(err => console.error('Ошибка загрузки компонента:', err));
    }
};

// Инициализация общих обработчиков
document.addEventListener('DOMContentLoaded', () => {
    // Закрытие уведомлений по кнопке (динамически созданные уведомления обрабатываются внутри showNotification, но на всякий случай добавим)
    document.querySelectorAll('.alert .btn-close').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.alert').remove();
        });
    });

    // Подтверждение удаления для элементов с атрибутом data-confirm
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});

// Экспортируем в глобальную область
window.StudApp = StudApp;

// Установка текущей даты на страницах
document.addEventListener('DOMContentLoaded', function() {
    // Элемент с id="currentDate" (для главной)
    const dateElement = document.getElementById('currentDate');
    if (dateElement) {
        const today = new Date();
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        dateElement.textContent = today.toLocaleDateString('ru-RU', options);
    }
    
    // Элемент с id="currentDateTime" (для grades.html - обновлено сегодня)
    const dateTimeElement = document.getElementById('currentDateTime');
    if (dateTimeElement) {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
        dateTimeElement.textContent = `Обновлено сегодня ${timeStr}`;
    }
    
    // Элемент с id="todayDate" (для schedule.html)
    const todayElement = document.getElementById('todayDate');
    if (todayElement) {
        const today = new Date();
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        todayElement.textContent = today.toLocaleDateString('ru-RU', options);
    }
});

// Обработчик формы входа
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    StudApp.showNotification(result.message, 'success');
                    // Задержка перед перенаправлением, чтобы уведомление было видно
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    StudApp.showNotification(result.message, 'error');
                }
            } catch (error) {
                StudApp.showNotification('Ошибка соединения с сервером', 'error');
                console.error('Login error:', error);
            }
        });
    }

    // Обработчик формы регистрации
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const group = document.getElementById('group').value;
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const agreeTerms = document.getElementById('agreeTerms').checked;

            // Простая валидация
            if (!firstName || !lastName || !group || !email || !password || !confirmPassword) {
                StudApp.showNotification('Заполните все поля', 'error');
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                StudApp.showNotification('Введите корректный email', 'error');
                return;
            }

            if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/[0-9]/.test(password)) {
                StudApp.showNotification('Пароль должен быть минимум 8 символов, содержать буквы и цифры', 'error');
                return;
            }

            if (password !== confirmPassword) {
                StudApp.showNotification('Пароли не совпадают', 'error');
                return;
            }

            if (!agree) {
                StudApp.showNotification('Необходимо согласиться с условиями', 'error');
                return;
            }
            
            const data = {
                first_name: firstName,
                last_name: lastName,
                email: email,
                group: group,
                password: password,
                password_confirmation: confirmPassword
            };
            
            try {
                const response = await fetch('/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    StudApp.showNotification(result.message, 'success');
                    // Задержка перед перенаправлением, чтобы уведомление было видно
                    setTimeout(() => {
                        window.location.href = '/login.html';
                    }, 2000);
                } else {
                    if (result.errors) {
                        // Показываем первую ошибку
                        const errors = Object.values(result.errors);
                        StudApp.showNotification(errors[0][0], 'error');
                    } else {
                        StudApp.showNotification(result.message, 'error');
                    }
                }
            } catch (error) {
                StudApp.showNotification('Ошибка соединения с сервером', 'error');
                console.error('Registration error:', error);
            }
        });
    }
});

// Функция для получения CSRF токена
function getCsrfToken() {
    let token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : '';
}