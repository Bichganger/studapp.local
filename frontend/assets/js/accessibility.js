// === ПАНЕЛЬ ДОСТУПНОСТИ ===
class AccessibilityPanel {
    constructor() {
        this.settings = {
            fontSize: 'normal',
            highContrast: false,
            largeButtons: false,
            textToSpeech: false,
            visualAlerts: true,
            simplified: false
        };
        
        this.init();
    }
    
    init() {
        this.loadSettings();
        this.createToggleButton();
        this.createPanel();
        this.bindEvents();
    }
    
    // === СОХРАНЕНИЕ НАСТРОЕК ===
    loadSettings() {
        const saved = localStorage.getItem('accessibility_settings');
        if (saved) {
            this.settings = { ...this.settings, ...JSON.parse(saved) };
        }
        this.applySettings();
    }
    
    saveSettings() {
        localStorage.setItem('accessibility_settings', JSON.stringify(this.settings));
    }
    
    // === СОЗДАНИЕ КНОПКИ ===
    createToggleButton() {
        const btn = document.createElement('button');
        btn.className = 'accessibility-toggle';
        btn.innerHTML = '<i class="bi bi-universal-access"></i>';
        btn.title = 'Панель доступности';
        btn.setAttribute('aria-label', 'Панель доступности');
        document.body.appendChild(btn);
        
        btn.addEventListener('click', () => {
            document.querySelector('.accessibility-panel').classList.toggle('active');
        });
    }
    
    // === СОЗДАНИЕ ПАНЕЛИ ===
    createPanel() {
        const panel = document.createElement('div');
        panel.className = 'accessibility-panel';
        panel.innerHTML = `
            <h5><i class="bi bi-universal-access"></i> Доступность</h5>
            
            <div class="accessibility-section">
                <h6><i class="bi bi-text-resize"></i> Размер текста</h6>
                <button class="accessibility-btn" data-action="fontSize" data-value="normal">
                    <i class="bi bi-text-left"></i> Обычный
                </button>
                <button class="accessibility-btn" data-action="fontSize" data-value="125">
                    <i class="bi bi-text-paragraph"></i> 125%
                </button>
                <button class="accessibility-btn" data-action="fontSize" data-value="150">
                    <i class="bi bi-text-paragraph"></i> 150%
                </button>
                <button class="accessibility-btn" data-action="fontSize" data-value="200">
                    <i class="bi bi-text-paragraph"></i> 200%
                </button>
            </div>
            
            <div class="accessibility-section">
                <h6><i class="bi bi-palette"></i> Визуальный режим</h6>
                <button class="accessibility-btn" data-action="highContrast">
                    <i class="bi bi-circle-half"></i> Высокая контрастность
                </button>
                <button class="accessibility-btn" data-action="largeButtons">
                    <i class="bi bi-arrows-expand"></i> Увеличенные кнопки
                </button>
                <button class="accessibility-btn" data-action="simplified">
                    <i class="bi bi-ui-checks"></i> Упрощённый интерфейс
                </button>
            </div>
            
            <div class="accessibility-section">
                <h6><i class="bi bi-volume-up"></i> Аудио</h6>
                <button class="accessibility-btn" data-action="textToSpeech">
                    <i class="bi bi-mic"></i> Чтение вслух
                </button>
            </div>
            
            <div class="accessibility-section">
                <h6><i class="bi bi-bell"></i> Уведомления</h6>
                <button class="accessibility-btn" data-action="visualAlerts">
                    <i class="bi bi-eye"></i> Визуальные уведомления
                </button>
            </div>
        `;
        document.body.appendChild(panel);
    }
    
    // === ПРИМЕНЕНИЕ НАСТРОЕК ===
    applySettings() {
        document.body.classList.remove('text-large-125', 'text-large-150', 'text-large-200');
        document.body.classList.remove('high-contrast', 'large-cursor', 'large-buttons', 'simplified-ui');
        
        if (this.settings.fontSize !== 'normal') {
            document.body.classList.add(`text-large-${this.settings.fontSize}`);
        }
        
        if (this.settings.highContrast) document.body.classList.add('high-contrast');
        if (this.settings.largeButtons) document.body.classList.add('large-buttons');
        if (this.settings.simplified) document.body.classList.add('simplified-ui');
        
        this.updateActiveButtons();
    }
    
    updateActiveButtons() {
        document.querySelectorAll('.accessibility-btn').forEach(btn => {
            const action = btn.dataset.action;
            const value = btn.dataset.value;
            
            if (action === 'fontSize') {
                btn.classList.toggle('active', value === this.settings.fontSize);
            } else {
                btn.classList.toggle('active', this.settings[action]);
            }
        });
    }
    
    // === ОБРАБОТКА СОБЫТИЙ ===
    bindEvents() {
        document.querySelector('.accessibility-panel').addEventListener('click', (e) => {
            const btn = e.target.closest('.accessibility-btn');
            if (!btn) return;
            
            const action = btn.dataset.action;
            const value = btn.dataset.value;
            
            if (action === 'fontSize') {
                this.settings.fontSize = value;
            } else {
                this.settings[action] = !this.settings[action];
            }
            
            this.saveSettings();
            this.applySettings();
            
            if (action === 'textToSpeech') {
                this.toggleTextToSpeech();
            }
        });
    }
    
    // === ЧТЕНИЕ Вслух ===
    toggleTextToSpeech() {
        if (!this.settings.textToSpeech) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const text = document.querySelector('.main-content')?.textContent || '';
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'ru-RU';
                utterance.rate = 0.9;
                window.speechSynthesis.speak(utterance);
            }
        } else {
            window.speechSynthesis.cancel();
        }
    }
    
    // === ВИЗУАЛЬНОЕ УВЕДОМЛЕНИЕ ===
    showVisualAlert(message, type = 'info') {
        if (!this.settings.visualAlerts) return;
        
        const alert = document.createElement('div');
        alert.className = 'visual-alert';
        
        const colors = {
            info: '#667eea',
            success: '#11998e',
            warning: '#f093fb',
            error: '#f5576c'
        };
        
        alert.style.borderLeftColor = colors[type] || colors.info;
        alert.innerHTML = `
            <strong>${type.toUpperCase()}</strong>
            <p>${message}</p>
        `;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}

// === ИНИЦИАЛИЗАЦИЯ ===
const Accessibility = new AccessibilityPanel();

// === МОНИТОРИНГ УВЕДОМЛЕНИЙ ===
const originalShowNotification = Sync.showNotification;
Sync.showNotification = function(message, type = 'info') {
    originalShowNotification(message, type);
    Accessibility.showVisualAlert(message, type);
};