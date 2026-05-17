/**
 * Студенческий компас - Основной JavaScript
 * Ванильный JS + Bootstrap
 */

document.addEventListener('DOMContentLoaded', function() {
    // Автоматическое скрытие flash-алертов через 5 секунд
    const flashAlerts = document.querySelectorAll('.flash-alert');
    flashAlerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

    // Анимация появления элементов при скролле
    const animateElements = document.querySelectorAll('.feature-card, .work-card, .teacher-card, .tip-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });

    // Подтверждение перед скачиванием (если не авторизован)
    const downloadLinks = document.querySelectorAll('a[href*="download="]');
    downloadLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Проверяем, есть ли data-authorized
            if (this.dataset.requireAuth === 'true') {
                e.preventDefault();
                showAuthModal();
            }
        });
    });

    // Кнопка "Наверх"
    createScrollToTop();
});

/**
 * Показать модал для авторизации
 */
function showAuthModal() {
    const modalHtml = `
        <div class="modal fade" id="authModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Требуется авторизация</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="bi bi-lock" style="font-size: 3rem; color: var(--accent);"></i>
                        <p class="mt-3">Для выполнения этого действия необходимо войти в аккаунт.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <a href="auth/login.php" class="btn btn-accent">Войти</a>
                        <a href="auth/register.php" class="btn btn-outline-light">Регистрация</a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Удаляем старый модал если есть
    const oldModal = document.getElementById('authModal');
    if (oldModal) oldModal.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('authModal'));
    modal.show();
}

/**
 * Кнопка "Наверх"
 */
function createScrollToTop() {
    const btn = document.createElement('button');
    btn.innerHTML = '<i class="bi bi-arrow-up"></i>';
    btn.className = 'btn-scroll-top';
    btn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--gradient-primary);
        border: none;
        color: var(--bg-dark);
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(0, 230, 118, 0.3);
    `;
    
    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    document.body.appendChild(btn);
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            btn.style.opacity = '1';
            btn.style.visibility = 'visible';
        } else {
            btn.style.opacity = '0';
            btn.style.visibility = 'hidden';
        }
    });
}

/**
 * Копирование в буфер обмена
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Можно добавить тост-уведомление
        console.log('Скопировано:', text);
    });
}
