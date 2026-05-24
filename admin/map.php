<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { 
    header('Location: ../dashboard.php'); 
    exit; 
}

$pageTitle = 'Карта корпусов';
require_once '../includes/header.php';

// Категории мест
$categories = [
    'building'  => ['name' => 'Корпуса',       'icon' => 'bi-building',     'color' => '#7c4dff'],
    'cafeteria' => ['name' => 'Где поесть',    'icon' => 'bi-cup-hot',      'color' => '#ff5252'],
    'bus_stop'  => ['name' => 'Транспорт',     'icon' => 'bi-bus-front',    'color' => '#40c4ff'],
    'other'     => ['name' => 'Прочее',        'icon' => 'bi-geo-alt',      'color' => '#e040fb'],
];

// Три реальных корпуса в Калининграде
$campuses = [
    ['name' => 'Брамса 9', 'address' => 'ул. Брамса, 9', 'lat' => 54.7148, 'lng' => 20.4912, 'color' => '#7c4dff'],
    ['name' => 'Спортивная 6', 'address' => 'ул. Спортивная, 6', 'lat' => 54.7125, 'lng' => 20.4860, 'color' => '#00e676'],
    ['name' => 'Озерова 7', 'address' => 'ул. Озерова, 7', 'lat' => 54.7180, 'lng' => 20.4885, 'color' => '#40c4ff'],
];
?>

<style>
#yandex-map-admin {
    width: 100%;
    height: 65vh;
    min-height: 450px;
    margin: 0;
    padding: 0;
}

.map-container-wrapper {
    position: relative;
    width: 100%;
    max-width: 1280px;
    margin: 0 auto;
    border-radius: var(--radius);
    overflow: hidden;
    border: 1px solid var(--border-color);
}

/* Контроллер карты - плавающий сверху */
.map-nav-controls {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 100;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 10px 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
    max-width: 95%;
    backdrop-filter: blur(10px);
}

.map-nav-controls button {
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--bg-input);
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.85rem;
    white-space: nowrap;
    font-weight: 500;
}

.map-nav-controls button:hover {
    background: var(--accent);
    color: var(--bg-dark);
    border-color: var(--accent);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.map-nav-controls button.active {
    background: var(--gradient-primary);
    color: var(--bg-dark);
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(124, 77, 255, 0.3);
}

/* Стили для админ-панели управления картой */
.admin-map-panel {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    padding: 20px;
    margin-top: 20px;
}

.admin-map-panel h5 {
    color: var(--text-primary);
    margin-bottom: 15px;
}

.code-input-area textarea {
    background: var(--bg-input);
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    border-radius: 8px;
    padding: 12px;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    min-height: 120px;
    width: 100%;
}
</style>

<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Администрирование</span>
            <h1 class="section-title">Карта</h1>
            <p class="section-subtitle">Управление локациями и корпусами</p>
        </div>

        <!-- Панель управления картой для админа -->
        <div class="admin-map-panel mb-4">
            <h5><i class="bi bi-gear me-2"></i>Настройка карты</h5>
            <p class="text-muted mb-3">Вставьте код из Яндекс.Конструктора карт для обновления меток:</p>
            <div class="code-input-area">
                <textarea id="mapConstructorCode" class="form-control" placeholder='Вставьте сюда код скрипта из Яндекс.Конструктора, например:<br><script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A..."></script>'></textarea>
            </div>
            <div class="mt-3">
                <button class="btn btn-accent" onclick="updateMapCode()"><i class="bi bi-check-circle me-2"></i>Обновить карту</button>
                <button class="btn btn-outline-secondary" onclick="clearMapCode()"><i class="bi bi-x-circle me-2"></i>Сбросить</button>
            </div>
            <small class="text-muted d-block mt-2">
                <i class="bi bi-info-circle me-1"></i>
                Как получить код: Зайдите в <a href="https://yandex.ru/map-constructor/" target="_blank">Яндекс.Конструктор карт</a> → Создайте/отредактируйте карту → Поделиться → Встроить на сайт → Скопируйте код
            </small>
        </div>

        <!-- Контейнер для карты -->
        <div class="map-container-wrapper">
            <!-- Кнопки навигации поверх карты -->
            <div class="map-nav-controls">
                <?php foreach ($campuses as $i => $campus): ?>
                <button class="campus-nav-btn" data-lat="<?= $campus['lat'] ?>" data-lng="<?= $campus['lng'] ?>">
                    <i class="bi bi-building me-1"></i><?= e($campus['name']) ?>
                </button>
                <?php endforeach; ?>
                <button class="campus-nav-btn active" id="resetMapView">
                    <i class="bi bi-arrows-move me-1"></i>Обзор
                </button>
            </div>
            
            <!-- Карта из Яндекс.Конструктора -->
            <div id="yandex-map-admin">
                <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A5547ee495eda499ca87c24a525d7bb19b0cbe616a9c586f1273a3f4fd8533458&amp;width=100%25&amp;height=100%25&amp;lang=ru_RU&amp;scroll=true"></script>
            </div>
        </div>
        
        <div class="row g-4 mt-4">
            <div class="col-md-6">
                <div class="card"><div class="card-header"><i class="bi bi-geo-alt me-2"></i>Все локации</div>
                    <div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0">
                        <thead><tr><th>Название</th><th>Тип</th><th>Координаты</th></tr></thead>
                        <tbody>
                            <tr><td colspan="3" class="text-center py-3">
                                <i class="bi bi-info-circle text-muted me-2"></i>
                                Локации настраиваются через Яндекс.Конструктор карт
                            </td></tr>
                        </tbody>
                    </table></div></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card"><div class="card-header"><i class="bi bi-building me-2"></i>Корпуса</div>
                    <div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0">
                        <thead><tr><th>Название</th><th>Адрес</th></tr></thead>
                        <tbody>
                            <?php foreach ($campuses as $c): ?>
                            <tr><td><?= e($c['name']) ?></td><td><?= e($c['address']) ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Функция обновления кода карты
function updateMapCode() {
    const code = document.getElementById('mapConstructorCode').value;
    if (!code.trim()) {
        alert('Пожалуйста, введите код карты из Яндекс.Конструктора');
        return;
    }
    
    // Сохраняем в localStorage
    localStorage.setItem('customMapCode', code);
    
    // Обновляем карту
    const mapContainer = document.getElementById('yandex-map-admin');
    mapContainer.innerHTML = code;
    
    alert('Карта обновлена!');
}

function clearMapCode() {
    localStorage.removeItem('customMapCode');
    document.getElementById('mapConstructorCode').value = '';
    
    // Восстанавливаем стандартную карту
    const mapContainer = document.getElementById('yandex-map-admin');
    mapContainer.innerHTML = '<script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A5547ee495eda499ca87c24a525d7bb19b0cbe616a9c586f1273a3f4fd8533458&amp;width=100%25&amp;height=100%25&amp;lang=ru_RU&amp;scroll=true"><\/script>';
    
    alert('Настройки карты сброшены!');
}

// Загрузка сохранённой карты при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const savedCode = localStorage.getItem('customMapCode');
    if (savedCode) {
        document.getElementById('mapConstructorCode').value = savedCode;
        const mapContainer = document.getElementById('yandex-map-admin');
        mapContainer.innerHTML = savedCode;
    }
    
    // Навигация по кнопкам на карте
    document.querySelectorAll('.campus-nav-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Активный класс
            document.querySelectorAll('.campus-nav-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Прокрутка к карте с плавным эффектом
            const mapContainer = document.querySelector('.map-container-wrapper');
            if (mapContainer) {
                mapContainer.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        });
    });
    
    // Кнопка сброса вида
    document.getElementById('resetMapView').addEventListener('click', function() {
        document.querySelectorAll('.campus-nav-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const mapContainer = document.querySelector('.map-container-wrapper');
        if (mapContainer) {
            mapContainer.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
    });
});
</script>
<?php require_once '../includes/footer.php'; ?>
