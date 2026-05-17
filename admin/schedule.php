<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

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

$schedule = $pdo->query("SELECT * FROM schedule ORDER BY FIELD(day_of_week,'понедельник','вторник','среда','четверг','пятница','суббота'), start_time")->fetchAll();
$groups = $pdo->query("SELECT DISTINCT group_name FROM users WHERE group_name IS NOT NULL AND group_name != '' ORDER BY group_name")->fetchAll(PDO::FETCH_COLUMN);

$days = ['понедельник','вторник','среда','четверг','пятница','суббота'];

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Расписание</h1></div>

    <?php if (isset($_GET['ok'])): ?><div class="alert alert-success">Занятие добавлено!</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-info">Занятие удалено</div><?php endif; ?>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card"><div class="card-header"><i class="bi bi-plus-circle me-2"></i>Добавить занятие</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-2"><label class="form-label">Группа</label>
                            <select name="group_name" class="form-select" required>
                                <?php foreach ($groups as $g): ?><option value="<?= e($g) ?>"><?= e($g) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2"><label class="form-label">Предмет</label><input type="text" name="subject" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Преподаватель</label><input type="text" name="teacher_name" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">День</label>
                            <select name="day_of_week" class="form-select" required>
                                <?php foreach ($days as $d): ?><option value="<?= $d ?>"><?= mb_ucfirst($d) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><label class="form-label">Начало</label><input type="time" name="start_time" class="form-control" required></div>
                            <div class="col-6"><label class="form-label">Конец</label><input type="time" name="end_time" class="form-control" required></div>
                        </div>
                        <div class="mb-2"><label class="form-label">Кабинет</label><input type="text" name="classroom" class="form-control" required></div>
                        <button type="submit" class="btn btn-accent w-100">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card"><div class="card-header"><i class="bi bi-list-ul me-2"></i>Все занятия</div>
                <div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0">
                    <thead><tr><th>Группа</th><th>Предмет</th><th>День</th><th>Время</th><th>Каб.</th><th></th></tr></thead>
                    <tbody>
                        <?php if (empty($schedule)): ?>
                        <tr><td colspan="6" class="text-center">Нет занятий</td></tr>
                        <?php else: ?>
                        <?php foreach ($schedule as $s): ?>
                        <tr>
                            <td><?= e($s['group_name']) ?></td><td><?= e($s['subject']) ?></td>
                            <td><?= e($s['day_of_week']) ?></td>
                            <td><?= substr($s['start_time'],0,5) ?>–<?= substr($s['end_time'],0,5) ?></td>
                            <td><?= e($s['classroom']) ?></td>
                            <td><a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')"><i class="bi bi-trash"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table></div></div>
            </div>
        </div>
    </div>
</div></div>

<?php
function mb_ucfirst($str) { return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1); }
require_once '../includes/footer.php';
?>
