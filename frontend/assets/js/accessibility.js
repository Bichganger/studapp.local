// === ПАНЕЛЬ ДОСТУПНОСТИ — СИНХРОНИЗАЦИЯ ЧЕРЕЗ SYNC ===
class AccessibilityPanel {
    constructor() {
        this.settings = {
            fontSize: '100',
            highContrast: false,
            largeButtons: false,
            simplified: false,
            visualAlerts: true
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
    
    // === УВЕДОМЛЕНИЕ ===
    showNotification(message) {
        const notif = document.createElement('div');
        notif.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #667eea;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            z-index: 10000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        `;
        notif.textContent = message;
        document.body.appendChild(notif);
        
        setTimeout(() => notif.remove(), 3000);
    }
}

// === ИНИЦИАЛИЗАЦИЯ ===
const Accessibility = new AccessibilityPanel();

// === СИНХРОНИЗАЦИЯ С Sync.showNotification ===
if (typeof Sync !== 'undefined' && Sync.showNotification) {
    const originalShowNotification = Sync.showNotification;
    Sync.showNotification = function(message, type = 'info') {
        originalShowNotification(message, type);
        if (Accessibility.settings.visualAlerts) {
            Accessibility.showNotification(message);
        }
    };
}