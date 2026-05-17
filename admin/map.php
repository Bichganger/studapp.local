<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

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

// Дополнительные POI вокруг корпусов
$locations = [
    ['name' => 'Столовая «Сытно»', 'lat' => 54.7152, 'lng' => 20.4905, 'type' => 'cafeteria', 'address' => 'ул. Баха, 7', 'hours' => '09:00–17:00'],
    ['name' => 'Остановка «Ул. Баха»', 'lat' => 54.7150, 'lng' => 20.4900, 'type' => 'bus_stop', 'address' => 'ул. Баха', 'hours' => ''],
    ['name' => 'Кофейня «Bean&Grain»', 'lat' => 54.7140, 'lng' => 20.4925, 'type' => 'cafeteria', 'address' => 'ул. Баха, 12', 'hours' => '08:00–21:00'],
    ['name' => 'Столовая колледжа', 'lat' => 54.7120, 'lng' => 20.4870, 'type' => 'cafeteria', 'address' => 'ул. Спортивная, 6', 'hours' => '09:00–16:00'],
    ['name' => 'Остановка «Спортивная»', 'lat' => 54.7130, 'lng' => 20.4875, 'type' => 'bus_stop', 'address' => 'ул. Спортивная', 'hours' => ''],
    ['name' => 'Канцтовары «Тетрадка»', 'lat' => 54.7115, 'lng' => 20.4850, 'type' => 'other', 'address' => 'ул. Спортивная, 2', 'hours' => '10:00–20:00'],
    ['name' => 'Бургерная «GrillHouse»', 'lat' => 54.7185, 'lng' => 20.4875, 'type' => 'cafeteria', 'address' => 'ул. Озерова, 5', 'hours' => '11:00–23:00'],
    ['name' => 'Остановка «Ул. Озерова»', 'lat' => 54.7175, 'lng' => 20.4890, 'type' => 'bus_stop', 'address' => 'ул. Озерова', 'hours' => ''],
    ['name' => 'Магазин «Продукты 24»', 'lat' => 54.7190, 'lng' => 20.4895, 'type' => 'other', 'address' => 'ул. Озерова, 10', 'hours' => 'Круглосуточно'],
];

$locationsJson = json_encode($locations, JSON_UNESCAPED_UNICODE);
$campusesJson  = json_encode($campuses, JSON_UNESCAPED_UNICODE);
$categoriesJson = json_encode($categories, JSON_UNESCAPED_UNICODE);
?>

<style>
#map {
    height: 65vh;
    min-height: 450px;
    border-radius: var(--radius);
    overflow: hidden;
    border: 1px solid var(--border-color);
    margin-top: 16px;
}
.map-controls {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    padding: 16px 20px;
}
</style>

<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Администрирование</span>
            <h1 class="section-title">Карта</h1>
            <p class="section-subtitle">Управление локациями и корпусами</p>
        </div>

        <div class="map-controls">
            <div class="row align-items-center g-3">
                <div class="col-lg-6">
                    <h6 class="mb-2" style="color: var(--text-primary);"><i class="bi bi-layers me-2"></i>Категории</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($categories as $key => $cat): ?>
                        <div class="form-check map-legend-item">
                            <input class="form-check-input category-toggle" type="checkbox" value="<?= e($key) ?>" id="cat_<?= e($key) ?>" checked>
                            <label class="form-check-label d-flex align-items-center gap-2" for="cat_<?= e($key) ?>" style="color: var(--text-primary);">
                                <span class="map-legend-color" style="background:<?= $cat['color'] ?>;width:12px;height:12px;border-radius:50%;display:inline-block;"></span>
                                <i class="bi <?= $cat['icon'] ?>" style="color:<?= $cat['color'] ?>"></i>
                                <?= e($cat['name']) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="mb-2" style="color: var(--text-primary);"><i class="bi bi-buildings me-2"></i>Корпуса</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($campuses as $campus): ?>
                        <button class="btn btn-sm btn-outline-light fly-to-campus"
                                data-lat="<?= $campus['lat'] ?>" data-lng="<?= $campus['lng'] ?>"
                                style="border-color:<?= $campus['color'] ?>; color:<?= $campus['color'] ?>">
                            <i class="bi bi-geo-alt-fill me-1"></i><?= e($campus['name']) ?>
                        </button>
                        <?php endforeach; ?>
                        <button class="btn btn-sm btn-accent" id="resetMap">
                            <i class="bi bi-arrows-fullscreen me-1"></i>Весь Калининград
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="map"></div>
        
        <div class="row g-4 mt-4">
            <div class="col-md-6">
                <div class="card"><div class="card-header"><i class="bi bi-geo-alt me-2"></i>Все локации</div>
                    <div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0">
                        <thead><tr><th>Название</th><th>Тип</th><th>Координаты</th></tr></thead>
                        <tbody>
                            <?php foreach ($locations as $loc): ?>
                            <tr>
                                <td><?= e($loc['name']) ?></td>
                                <td><span class="badge bg-info"><?= e($loc['type']) ?></span></td>
                                <td><?= $loc['lat'] ?>, <?= $loc['lng'] ?></td>
                            </tr>
                            <?php endforeach; ?>
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

<script src="https://api-maps.yandex.ru/2.1/?apikey=&lang=ru_RU"></script>
<script>
window.mapData = {
    locations: <?= $locationsJson ?>,
    campuses: <?= $campusesJson ?>,
    categories: <?= $categoriesJson ?>
};
</script>
<script src="/assets/js/yandex-map.js"></script>
<?php require_once '../includes/footer.php'; ?>
