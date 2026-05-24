<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { 
    header('Location: ../dashboard.php'); 
    exit;
}

$pageTitle = 'Управление расписанием';

// Добавление занятия
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $stmt = $pdo->prepare("INSERT INTO schedule (group_name, subject, teacher_name, day_of_week, start_time, end_time, classroom) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([
        $_POST['group_name'], $_POST['subject'], $_POST['teacher_name'],
        $_POST['day_of_week'], $_POST['start_time'] . ':00', $_POST['end_time'] . ':00', $_POST['classroom']
    ]);
    header('Location: schedule.php?ok=1');
    exit;
}

// Удаление
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM schedule WHERE id = ?")->execute([intval($_GET['delete'])]);
    header('Location: schedule.php?deleted=1');
    exit;
}

// Фильтрация по группе
$filterGroup = $_GET['group'] ?? '';
$whereGroup = $filterGroup ? "WHERE group_name = ?" : "";
$paramsGroup = $filterGroup ? [$filterGroup] : [];

$scheduleStmt = $pdo->prepare("SELECT * FROM schedule $whereGroup ORDER BY FIELD(day_of_week,'понедельник','вторник','среда','четверг','пятница','суббота'), start_time");
$scheduleStmt->execute($paramsGroup);
$schedule = $scheduleStmt->fetchAll();

$groups = $pdo->query("SELECT DISTINCT group_name FROM users WHERE group_name IS NOT NULL AND group_name != '' ORDER BY group_name")->fetchAll(PDO::FETCH_COLUMN);
$days = ['понедельник','вторник','среда','четверг','пятница','суббота'];

// Группировка расписания по дням
$daysRu = ['понедельник' => 'Пн', 'вторник' => 'Вт', 'среда' => 'Ср', 'четверг' => 'Чт', 'пятница' => 'Пт', 'суббота' => 'Сб'];
$scheduleByDay = [];
foreach ($schedule as $s) {
    $scheduleByDay[$s['day_of_week']][] = $s;
}

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header">
        <span class="section-label">Администрирование</span>
        <h1 class="section-title">Расписание</h1>
        <p class="section-subtitle">Управление занятиями по группам</p>
    </div>

    <?php if (isset($_GET['ok'])): ?><div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Занятие добавлено!</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-info"><i class="bi bi-trash me-2"></i>Занятие удалено</div><?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card"><div class="card-header"><i class="bi bi-plus-circle me-2"></i>Добавить занятие</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3"><label class="form-label">Группа *</label>
                            <select name="group_name" class="form-select" required>
                                <option value="">Выберите группу...</option>
                                <?php foreach ($groups as $g): ?><option value="<?= e($g) ?>"><?= e($g) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Предмет *</label><input type="text" name="subject" class="form-control" placeholder="Математика" required></div>
                        <div class="mb-3"><label class="form-label">Преподаватель *</label><input type="text" name="teacher_name" class="form-control" placeholder="Иванов И.И." required></div>
                        <div class="mb-3"><label class="form-label">День недели *</label>
                            <select name="day_of_week" class="form-select" required>
                                <?php foreach ($days as $d): ?><option value="<?= $d ?>"><?= mb_ucfirst($d) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="form-label">Начало *</label><input type="time" name="start_time" class="form-control" required></div>
                            <div class="col-6"><label class="form-label">Конец *</label><input type="time" name="end_time" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Кабинет *</label><input type="text" name="classroom" class="form-control" placeholder="101" required></div>
                        <button type="submit" class="btn btn-accent w-100"><i class="bi bi-plus-circle me-2"></i>Добавить</button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-3"><div class="card-header"><i class="bi bi-funnel me-2"></i>Фильтр по группе</div>
                <div class="card-body">
                    <form method="GET">
                        <select name="group" class="form-select" onchange="this.form.submit()">
                            <option value="">Все группы</option>
                            <?php foreach ($groups as $g): ?>
                            <option value="<?= e($g) ?>" <?= $filterGroup === $g ? 'selected' : '' ?>><?= e($g) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($filterGroup): ?>
                        <a href="schedule.php" class="btn btn-outline-secondary btn-sm w-100 mt-2"><i class="bi bi-x-circle me-2"></i>Сбросить</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card"><div class="card-header">
                <i class="bi bi-calendar-week me-2"></i>Расписание 
                <?php if ($filterGroup): ?><span class="badge bg-success ms-2"><?= e($filterGroup) ?></span><?php endif; ?>
                <span class="badge bg-secondary ms-2"><?= count($schedule) ?> занятий</span>
            </div>
                <div class="card-body p-0">
                    <?php if (empty($schedule)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x" style="font-size: 3rem; color: var(--text-muted);"></i>
                        <p class="text-muted mt-3">Нет занятий<?= $filterGroup ? ' для группы ' . e($filterGroup) : '' ?></p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>День</th>
                                    <th>Время</th>
                                    <th>Предмет</th>
                                    <th>Преподаватель</th>
                                    <th>Группа</th>
                                    <th>Каб.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedule as $s): ?>
                                <tr>
                                    <td><span class="badge bg-info"><?= e($daysRu[$s['day_of_week']]) ?></span></td>
                                    <td><small><?= substr($s['start_time'],0,5) ?>–<?= substr($s['end_time'],0,5) ?></small></td>
                                    <td><strong><?= e($s['subject']) ?></strong></td>
                                    <td><?= e($s['teacher_name']) ?></td>
                                    <td><span class="badge bg-secondary"><?= e($s['group_name']) ?></span></td>
                                    <td><?= e($s['classroom']) ?></td>
                                    <td>
                                        <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить занятие?')" title="Удалить">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div></div>

<?php
function mb_ucfirst($str) { return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1); }
require_once '../includes/footer.php';
?>
