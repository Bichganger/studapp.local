<?php
require_once '../config/db.php';
require_once '../protected/auth_guard.php';

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
    ['name' => 'Брамса 9', 'address' => 'ул. Иоганна Себастьяна Баха, 9', 'lat' => 54.7148, 'lng' => 20.4912, 'color' => '#7c4dff'],
    ['name' => 'Спортивная 6', 'address' => 'ул. Спортивная, 6', 'lat' => 54.7125, 'lng' => 20.4860, 'color' => '#00e676'],
    ['name' => 'Озерова 7', 'address' => 'ул. Озерова, 7', 'lat' => 54.7180, 'lng' => 20.4885, 'color' => '#40c4ff'],
];
?>

<style>
#yandex-map {
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
</style>

<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Навигация</span>
            <h1 class="section-title">Карта жизни студента</h1>
            <p class="section-subtitle">Всё важное рядом с корпусами — найди за 30 секунд</p>
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
            <div id="yandex-map">
                <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A5547ee495eda499ca87c24a525d7bb19b0cbe616a9c586f1273a3f4fd8533458&amp;width=100%25&amp;height=100%25&amp;lang=ru_RU&amp;scroll=true"></script>
            </div>
        </div>
    </div>
</section>

<script>
// Данные для навигации
window.mapPoints = {
    campuses: <?= json_encode($campuses, JSON_UNESCAPED_UNICODE) ?>
};

// Обработчики навигации
document.addEventListener('DOMContentLoaded', function() {
    let mapLoaded = false;
    
    // Навигация по кнопкам на карте
    document.querySelectorAll('.campus-nav-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const lat = parseFloat(this.dataset.lat);
            const lng = parseFloat(this.dataset.lng);
            
            // Активный класс
            document.querySelectorAll('.campus-nav-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Если карта загрузилась, пробуем переместиться
            if (mapLoaded) {
                // Пытаемся найти iframe карты и прокрутить к нему
                const mapContainer = document.getElementById('yandex-map');
                if (mapContainer) {
                    // Прокрутка к карте с плавным эффектом
                    mapContainer.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
            } else {
                // Если карта ещё не загрузилась - просто скроллим
                const mapContainer = document.getElementById('yandex-map');
                if (mapContainer) {
                    mapContainer.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
            }
        });
    });
    
    // Кнопка сброса
    document.getElementById('resetMapView').addEventListener('click', function() {
        document.querySelectorAll('.campus-nav-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const mapContainer = document.getElementById('yandex-map');
        if (mapContainer) {
            mapContainer.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
    });
    
    // Отслеживаем загрузку карты
    setTimeout(function() {
        mapLoaded = true;
        console.log('Карта готова к навигации');
    }, 3000);
});
</script>
<?php require_once '../includes/footer.php'; ?>
