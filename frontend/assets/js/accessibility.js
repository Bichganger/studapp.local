// === ПАНЕЛЬ ДОСТУПНОСТИ — СИНХРОНИЗАЦИЯ ЧЕРЕЗ SYNC ===
class AccessibilityPanel {
    constructor() {
        this.settings = {
            fontSize: '100',
            highContrast: false,
            largeButtons: false,
            simplified: false,
            visualAlerts: true,
            visualNotifications: true,  // Для глухих и слабослышащих
            screenFlashing: false,       // Мигание экрана при уведомлениях
            showSoundIcon: true          // Иконка звука для визуализации
        };
        
        this.init();
    }
    
    init() {
        this.loadSettings();
        this.bindEvents();
        this.applySettings();
        
        // Слушатель синхронизации
        window.addEventListener('storage', (e) => {
            if (e.key === 'sync_accessibility') {
                this.loadSettings();
                this.applySettings();
            }
        });
    }
    
    // === ЗАГРУЗКА НАСТРОЕК (из Sync) ===
    loadSettings() {
        const stored = localStorage.getItem('sync_accessibility');
        if (stored) {
            const settings = JSON.parse(stored);
            // Применяем настройки для ТЕКУЩЕГО пользователя
            const userId = sessionStorage.getItem('user_id') || 'guest';
            if (settings[userId]) {
                this.settings = { ...this.settings, ...settings[userId] };
            }
        }
    }
    
    // === СОХРАНЕНИЕ НАСТРОЕК (в Sync) ===
    saveSettings() {
        const userId = sessionStorage.getItem('user_id') || 'guest';
        let allSettings = JSON.parse(localStorage.getItem('sync_accessibility') || '{}');
        allSettings[userId] = this.settings;
        localStorage.setItem('sync_accessibility', JSON.stringify(allSettings));
        
        // Триггер синхронизации
        window.dispatchEvent(new Event('storage'));
    }
    
    // === ПРИМЕНЕНИЕ НАСТРОЕК ===
    applySettings() {
        document.body.classList.remove('text-large-125', 'text-large-150', 'text-large-200');
        document.body.classList.remove('high-contrast', 'large-buttons', 'simplified-ui');
        
        if (this.settings.fontSize !== '100') {
            document.body.classList.add(`text-large-${this.settings.fontSize}`);
        }
        
        if (this.settings.highContrast) document.body.classList.add('high-contrast');
        if (this.settings.largeButtons) document.body.classList.add('large-buttons');
        if (this.settings.simplified) document.body.classList.add('simplified-ui');
        
        this.updateUI();
    }
    
    // === ОБНОВЛЕНИЕ UI МОДАЛЬНОГО ОКНА ===
    updateUI() {
        const modal = document.getElementById('accessibilityModal');
        if (!modal) return;
        
        // Кнопки размера текста
        modal.querySelectorAll('[data-a11y="fontSize"]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.value === this.settings.fontSize);
        });
        
        // Чекбоксы
        modal.querySelectorAll('[data-a11y]').forEach(el => {
            if (el.type === 'checkbox') {
                el.checked = this.settings[el.dataset.a11y];
            }
        });
    }
    
    // === ОБРАБОТКА СОБЫТИЙ ===
    bindEvents() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-a11y]');
            if (!btn) return;
            
            const action = btn.dataset.a11y;
            const value = btn.dataset.value;
            
            if (btn.type === 'checkbox') {
                this.settings[action] = btn.checked;
            } else if (action === 'fontSize') {
                this.settings.fontSize = value;
            }
            
            this.saveSettings();
            this.applySettings();
            
            // Визуальное подтверждение
            if (this.settings.visualAlerts) {
                this.showNotification('Настройки сохранены');
            }
        });
    }
    
    // === ВИЗУАЛЬНОЕ УВЕДОМЛЕНИЕ ДЛЯ ГЛУХИХ ===
    showNotification(message, type = 'info') {
        // Обычное уведомление
        const notif = document.createElement('div');
        notif.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'error' ? '#dc3545' : type === 'warning' ? '#ffc107' : '#667eea'};
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            z-index: 10000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideInRight 0.3s ease-out;
            max-width: 300px;
        `;
        
        // Иконка типа уведомления
        const icons = {
            error: '⚠️',
            warning: '⚡',
            success: '✅',
            info: '🔔'
        };
        
        notif.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.5rem;">${icons[type] || icons.info}</span>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notif);
        
        setTimeout(() => notif.remove(), 4000);
        
        // Мигание экрана если включено
        if (this.settings.screenFlashing && this.settings.visualNotifications) {
            this.flashScreen(type);
        }
        
        // Показ иконки звука
        if (this.settings.showSoundIcon) {
            this.showSoundIndicator(type);
        }
    }
    
    // === МИГАНИЕ ЭКРАНА ПРИ УВЕДОМЛЕНИЯХ ===
    flashScreen(type) {
        const flash = document.createElement('div');
        flash.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: ${type === 'error' ? 'rgba(220, 53, 69, 0.3)' : 'rgba(102, 126, 234, 0.3)'};
            z-index: 9999;
            pointer-events: none;
            animation: flashAnimation 0.5s ease-out;
        `;
        
        document.body.appendChild(flash);
        setTimeout(() => flash.remove(), 500);
    }
    
    // === ИНДИКАТОР ЗВУКА ===
    showSoundIndicator(type) {
        // Удаляем старый если есть
        const old = document.querySelector('.sound-indicator');
        if (old) old.remove();
        
        const indicator = document.createElement('div');
        indicator.className = 'sound-indicator';
        indicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 50px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: fadeIn 0.3s;
        `;
        
        indicator.innerHTML = `
            <i class="bi bi-volume-up-fill" style="color: ${type === 'error' ? '#dc3545' : '#667eea'}"></i>
            <small>Уведомление</small>
        `;
        
        document.body.appendChild(indicator);
        setTimeout(() => indicator.remove(), 3000);
    }
}

// === АНИМАЦИИ ===
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(300px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes flashAnimation {
        from { opacity: 0.8; }
        to { opacity: 0; }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
`;
document.head.appendChild(style);

// === ИНИЦИАЛИЗАЦИЯ ===
const Accessibility = new AccessibilityPanel();

// === ПЕРЕПИСЫВАЕМ Sync.showNotification ДЛЯ ВИЗУАЛИЗАЦИИ ===
if (typeof Sync !== 'undefined' && Sync.showNotification) {
    const originalShowNotification = Sync.showNotification;
    Sync.showNotification = function(message, type = 'info') {
        originalShowNotification(message, type);
        if (Accessibility.settings.visualNotifications) {
            Accessibility.showNotification(message, type);
        }
    };
}