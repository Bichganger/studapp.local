// Админ-панель: Настройки
(function() {
    'use strict';

    const API_URL = 'api/settings.php';

    // Загрузка настроек
    async function loadSettings() {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();
            
            if (result.success && result.data) {
                const settings = result.data;
                
                // Общие настройки
                fillInputByName('system_name', settings.system_name || 'Учеба24');
                fillInputByName('notification_email', settings.notification_email || 'admin@ucheba.online');
                fillSelectByValue('timezone', settings.timezone || 'MSK');
                document.getElementById('maintenanceMode').checked = settings.maintenance_mode === '1';
                
                // Настройки регистрации
                document.getElementById('allowRegistration').checked = settings.allow_registration !== '0';
                document.getElementById('emailConfirmation').checked = settings.email_confirmation !== '0';
                fillInputByName('min_password_length', settings.min_password_length || '6');
                
                // Настройки уведомлений
                document.getElementById('emailNotifications').checked = settings.email_notifications !== '0';
                document.getElementById('smsNotifications').checked = settings.sms_notifications === '1';
                fillInputByName('smtp_server', settings.smtp_server || '');
                fillInputByName('smtp_port', settings.smtp_port || '587');
                
                // Безопасность
                fillInputByName('session_duration', settings.session_duration || '120');
                fillInputByName('max_login_attempts', settings.max_login_attempts || '5');
                fillInputByName('lockout_duration', settings.lockout_duration || '15');
                document.getElementById('twoFactorAuth').checked = settings.two_factor_auth === '1';
            }
        } catch (error) {
            console.error('Ошибка загрузки настроек:', error);
            showNotification('Ошибка загрузки настроек', 'error');
        }
    }

    // Сохранение настроек
    async function saveSettings(formData) {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ settings: formData })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Настройки сохранены', 'success');
                return true;
            } else {
                showNotification(result.message || 'Ошибка сохранения', 'error');
                return false;
            }
        } catch (error) {
            console.error('Ошибка сохранения:', error);
            showNotification('Ошибка сохранения настроек', 'error');
            return false;
        }
    }

    // Сбор данных формы
    function collectFormData() {
        return {
            // Общие настройки
            system_name: getInputValueByName('system_name'),
            notification_email: getInputValueByName('notification_email'),
            timezone: getSelectValue('timezone'),
            maintenance_mode: document.getElementById('maintenanceMode').checked ? '1' : '0',
            
            // Регистрация
            allow_registration: document.getElementById('allowRegistration').checked ? '1' : '0',
            email_confirmation: document.getElementById('emailConfirmation').checked ? '1' : '0',
            min_password_length: getInputValueByName('min_password_length'),
            
            // Уведомления
            email_notifications: document.getElementById('emailNotifications').checked ? '1' : '0',
            sms_notifications: document.getElementById('smsNotifications').checked ? '1' : '0',
            smtp_server: getInputValueByName('smtp_server'),
            smtp_port: getInputValueByName('smtp_port'),
            
            // Безопасность
            session_duration: getInputValueByName('session_duration'),
            max_login_attempts: getInputValueByName('max_login_attempts'),
            lockout_duration: getInputValueByName('lockout_duration'),
            two_factor_auth: document.getElementById('twoFactorAuth').checked ? '1' : '0'
        };
    }

    // Вспомогательные функции
    function getInputValueByName(name) {
        const input = document.querySelector(`[name="${name}"]`);
        return input ? input.value : '';
    }

    function fillInputByName(name, value) {
        const input = document.querySelector(`[name="${name}"]`);
        if (input) input.value = value;
    }

    function getSelectValue(id) {
        const select = document.getElementById(id);
        return select ? select.value : '';
    }

    function fillSelectByValue(id, value) {
        const select = document.getElementById(id);
        if (select) select.value = value;
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
        
        setTimeout(() => notification.remove(), 5000);
    }

    // Инициализация
    document.addEventListener('DOMContentLoaded', function() {
        // Загружаем настройки при загрузке страницы
        loadSettings();

        // Обработчики форм
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = collectFormData();
                await saveSettings(formData);
            });
        });

        // Авто-сохранение при изменении чекбоксов (демонстрация)
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', async function() {
                const formData = collectFormData();
                await saveSettings(formData);
            });
        });
    });
})();
