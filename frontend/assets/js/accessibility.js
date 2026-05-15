// === Доступность для студентов и преподавателей (сохранение в БД) ===

// Загрузка настроек из БД при старте
async function loadAccessibility() {
    try {
        const r = await fetch(API_BASE + '?action=get_accessibility').then(r => r.json());
        if (r.success && r.data) {
            const s = r.data;
            localStorage.setItem('accessibility_settings', JSON.stringify(s));
            localStorage.setItem('a11y_fontSize', s.fontSize || '100');
            applySettings(s);
        }
    } catch (e) {
        console.error('loadAccessibility error:', e); 
    }
}

function applySettings(s) {
    if (!s) return;
    document.body.style.fontSize = (s.fontSize / 100) + 'em';
    document.body.classList.toggle('high-contrast', !!s.highContrast);
    document.body.classList.toggle('simplified', !!s.simplified);
    document.querySelectorAll('.btn').forEach(b => b.classList.toggle('btn-lg', !!s.largeButtons));

    // Обновить состояние кнопок в модалке
    document.querySelectorAll('[data-a11y="fontSize"]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.value === s.fontSize);
    });
    const hc = document.getElementById('highContrast');
    const lb = document.getElementById('largeButtons');
    if (hc) hc.checked = !!s.highContrast;
    if (lb) lb.checked = !!s.largeButtons;
}

async function saveAccessibility() {
    const settings = {
        fontSize: document.querySelector('[data-a11y="fontSize"].active')?.dataset.value || '100',
        highContrast: document.getElementById('highContrast')?.checked || false,
        largeButtons: document.getElementById('largeButtons')?.checked || false
    };

    try {
        const fd = new FormData();
        fd.append('action', 'save_accessibility');
        fd.append('settings', JSON.stringify(settings));

        const r = await fetch(API_BASE, { method: 'POST', body: fd });
        const result = await r.json();

        if (result.success) {
            localStorage.setItem('accessibility_settings', JSON.stringify(settings));
            localStorage.setItem('a11y_fontSize', settings.fontSize);
            applySettings(settings);
            showToast('Настройки доступности сохранены!', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('accessibilityModal'));
            if (modal) modal.hide();
        } else {
            showToast(result.error || 'Ошибка сохранения', 'error');
        }
    } catch (e) {
        showToast('Ошибка соединения', 'error');
        console.error(e);
    }
}

function showToast(m, t) {
    const c = document.querySelector('.toast-container');
    if (!c) return;
    const el = document.createElement('div');
    el.className = `toast align-items-center text-white bg-${t === 'success' ? 'success' : t === 'error' ? 'danger' : 'primary'} border-0`;
    el.innerHTML = `<div class="d-flex"><div class="toast-body">${m}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    c.appendChild(el);
    new bootstrap.Toast(el, { delay: 3000 }).show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

// Обработчик кнопок размера текста
document.addEventListener('DOMContentLoaded', () => {
    loadAccessibility();

    document.querySelectorAll('[data-a11y="fontSize"]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('[data-a11y="fontSize"]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
