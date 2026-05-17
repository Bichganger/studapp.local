<?php
session_start();
require_once '../protected/auth_guard.php';
if (!in_array($_SESSION['role'], ['student', 'admin'])) { header('Location: ../dashboard.php'); exit; }
$pageTitle = 'Карта Калининграда';
require_once '../includes/header.php';
$locations = $pdo->query("SELECT * FROM locations ORDER BY category, name")->fetchAll();
$categories = [
    'eat' => ['name' => 'Где поесть', 'icon' => 'bi-cup-hot', 'color' => '#ff5252'],
    'stationery' => ['name' => 'Канцтовары', 'icon' => 'bi-pencil', 'color' => '#7c4dff'],
    'smoking' => ['name' => 'Места для курения', 'icon' => 'bi-wind', 'color' => '#5a6380'],
    'gift' => ['name' => 'Подарки', 'icon' => 'bi-gift', 'color' => '#e040fb'],
    'transport' => ['name' => 'Транспорт', 'icon' => 'bi-bus-front', 'color' => '#40c4ff'],
];
$campuses = [
    ['name' => 'Брамса 9', 'lat' => 54.7105, 'lng' => 20.5155, 'color' => '#7c4dff'],
    ['name' => 'Спортивная 6', 'lat' => 54.7055, 'lng' => 20.5080, 'color' => '#00e676'],
    ['name' => 'Озерова 7', 'lat' => 54.7020, 'lng' => 20.5200, 'color' => '#40c4ff'],
];
$locationsJson = json_encode($locations, JSON_UNESCAPED_UNICODE);
$campusesJson = json_encode($campuses, JSON_UNESCAPED_UNICODE);
?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Навигация</span>
            <h1 class="section-title">Карта жизни студента</h1>
            <p class="section-subtitle">Всё важное рядом с корпусами — найди за 30 секунд</p>
        </div>
        <div class="map-controls">
            <div class="row align-items-center g-3">
                <div class="col-lg-6">
                    <h6 class="mb-2"><i class="bi bi-layers me-2"></i>Категории</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($categories as $key => $cat): ?>
                        <div class="form-check map-legend-item">
                            <input class="form-check-input category-toggle" type="checkbox" value="<?= e($key) ?>" id="cat_<?= e($key) ?>" checked>
                            <label class="form-check-label d-flex align-items-center gap-2" for="cat_<?= e($key) ?>">
                                <span class="map-legend-color" style="background: <?= $cat['color'] ?>"></span>
                                <i class="bi <?= $cat['icon'] ?>" style="color: <?= $cat['color'] ?>"></i>
                                <?= e($cat['name']) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="mb-2"><i class="bi bi-buildings me-2"></i>Корпуса</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($campuses as $i => $campus): ?>
                        <button class="btn btn-sm btn-outline-light fly-to-campus" data-lat="<?= $campus['lat'] ?>" data-lng="<?= $campus['lng'] ?>" style="border-color: <?= $campus['color'] ?>; color: <?= $campus['color'] ?>">
                            <i class="bi bi-geo-alt-fill me-1"></i><?= e($campus['name']) ?>
                        </button>
                        <?php endforeach; ?>
                        <button class="btn btn-sm btn-accent" id="resetMap"><i class="bi bi-arrows-fullscreen me-1"></i>Весь Калининград</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="map" class="map-container"></div>
    </div>
</section>
<script>
window.mapData = { locations: <?= $locationsJson ?>, campuses: <?= $campusesJson ?>, categories: <?= json_encode($categories, JSON_UNESCAPED_UNICODE) ?> };
</script>
<?php require_once '../includes/footer.php'; ?>
