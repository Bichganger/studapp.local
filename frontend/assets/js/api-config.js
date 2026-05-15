// === Универсальный путь к API для всех страниц ===
// Работает для любого домена: studapp.local, localhost, frontend и т.д.

(function() {
    const path = window.location.pathname;
    
    // Если страница в подпапке — поднимаемся на уровень выше
    if (path.includes('/student/') || path.includes('/teacher/') || path.includes('/admin/')) {
        window.API_BASE = '../api/sync.php';
    } else {
        // Корень проекта (index.php, dashboard.php)
        window.API_BASE = 'api/sync.php';
    }
    
    console.log('API_BASE:', window.API_BASE, '| path:', path);
})();
