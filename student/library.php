<?php
session_start();
require_once '../protected/auth_guard.php';
if (!in_array($_SESSION['role'], ['student', 'admin'])) { header('Location: ../dashboard.php'); exit; }
$pageTitle = 'Библиотека работ';
require_once '../includes/header.php';

$filterCampus = $_GET['campus'] ?? '';
$filterSubject = $_GET['subject'] ?? '';
$filterType = $_GET['type'] ?? '';
$searchQuery = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];
if ($filterCampus) { $where[] = "w.campus = ?"; $params[] = $filterCampus; }
if ($filterSubject) { $where[] = "w.subject = ?"; $params[] = $filterSubject; }
if ($filterType) { $where[] = "w.type = ?"; $params[] = $filterType; }
if ($searchQuery) { $where[] = "(w.title LIKE ? OR w.subject LIKE ? OR u.name LIKE ?)"; $searchLike = "%$searchQuery%"; $params[] = $searchLike; $params[] = $searchLike; $params[] = $searchLike; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countSql = "SELECT COUNT(*) FROM works w LEFT JOIN users u ON w.user_id = u.id $whereSql";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalWorks = $countStmt->fetchColumn();
$totalPages = ceil($totalWorks / $perPage);

$sql = "SELECT w.*, u.name as author_name FROM works w LEFT JOIN users u ON w.user_id = u.id $whereSql ORDER BY w.created_at DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);
$stmt->execute(array_merge($params, [$perPage, $offset]));
$works = $stmt->fetchAll();

$subjects = $pdo->query("SELECT DISTINCT subject FROM works ORDER BY subject")->fetchAll(PDO::FETCH_COLUMN);
$types = $pdo->query("SELECT DISTINCT type FROM works ORDER BY type")->fetchAll(PDO::FETCH_COLUMN);
$campuses = ['Брамса 9', 'Спортивная 6', 'Озерова 7'];

$uploadErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload' && $user) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $type = $_POST['type'] ?? '';
    $campus = $_POST['campus'] ?? '';
    if (empty($title)) $uploadErrors[] = 'Укажите название работы';
    if (empty($subject)) $uploadErrors[] = 'Укажите предмет';
    if (empty($type)) $uploadErrors[] = 'Выберите тип работы';
    if (empty($campus)) $uploadErrors[] = 'Выберите корпус';
    if (empty($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) { $uploadErrors[] = 'Выберите файл'; }
    if (empty($uploadErrors)) {
        $upload = uploadFile($_FILES['file'], 'uploads/works', ['pdf', 'doc', 'docx', 'zip'], 20971520);
        if ($upload['success']) {
            $stmt = $pdo->prepare("INSERT INTO works (user_id, title, description, subject, type, file_path, campus) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $description, $subject, $type, $upload['path'], $campus]);
            redirect('library.php', 'Работа загружена!', 'success');
        } else { $uploadErrors[] = $upload['error']; }
    }
}

if (isset($_GET['download']) && is_numeric($_GET['download'])) {
    $workId = intval($_GET['download']);
    $stmt = $pdo->prepare("SELECT file_path, title FROM works WHERE id = ?");
    $stmt->execute([$workId]);
    $work = $stmt->fetch();
    if ($work && file_exists($work['file_path'])) {
        $pdo->prepare("UPDATE works SET downloads = downloads + 1 WHERE id = ?")->execute([$workId]);
        $ext = pathinfo($work['file_path'], PATHINFO_EXTENSION);
        $filename = preg_replace('/[^a-zA-Z0-9а-яА-Я\s_-]/u', '', $work['title']) . '.' . $ext;
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($work['file_path']));
        readfile($work['file_path']);
        exit;
    }
}

function getCampusClass(string $campus): string {
    return match($campus) { 'Брамса 9' => 'bramsa', 'Спортивная 6' => 'sport', 'Озерова 7' => 'ozerova', default => '' };
}
function buildQuery(array $override): string {
    $params = array_merge($_GET, $override);
    return http_build_query($params);
}
?>
<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div><span class="section-label">Библиотека</span><h1 class="section-title mb-0">Студенческие работы</h1></div>
            <?php if ($user): ?>
            <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="bi bi-cloud-upload me-2"></i>Загрузить работу</button>
            <?php else: ?>
            <a href="../dashboard.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-in-right me-2"></i>Войти для загрузки</a>
            <?php endif; ?>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Поиск..." value="<?= e($searchQuery) ?>">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="campus" class="form-select"><option value="">Все корпуса</option><?php foreach ($campuses as $c): ?><option value="<?= e($c) ?>" <?= $filterCampus === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="subject" class="form-select"><option value="">Все предметы</option><?php foreach ($subjects as $s): ?><option value="<?= e($s) ?>" <?= $filterSubject === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="type" class="form-select"><option value="">Все типы</option><?php foreach ($types as $t): ?><option value="<?= e($t) ?>" <?= $filterType === $t ? 'selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <button type="submit" class="btn btn-accent w-100"><i class="bi bi-funnel me-1"></i>Фильтр</button>
                    </div>
                </form>
            </div>
        </div>
        <?php if (empty($works)): ?>
        <div class="empty-state"><i class="bi bi-journal-x"></i><h4>Работы не найдены</h4><p>Попробуйте изменить фильтры</p></div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($works as $work): ?>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="work-card">
                    <div class="work-preview">
                        <?php $icon = 'bi-file-earmark-text'; $ext = pathinfo($work['file_path'], PATHINFO_EXTENSION); if ($ext === 'pdf') $icon = 'bi-file-earmark-pdf'; elseif (in_array($ext, ['doc', 'docx'])) $icon = 'bi-file-earmark-word'; elseif ($ext === 'zip') $icon = 'bi-file-earmark-zip'; ?>
                        <i class="bi <?= $icon ?>"></i>
                        <span class="work-type"><?= e($work['type']) ?></span>
                    </div>
                    <div class="work-body">
                        <h5 class="work-title"><?= e($work['title']) ?></h5>
                        <div class="work-meta"><span><i class="bi bi-book"></i><?= e($work['subject']) ?></span></div>
                        <p class="work-desc"><?= e(mb_substr($work['description'] ?: 'Нет описания', 0, 100)) ?><?= mb_strlen($work['description'] ?: '') > 100 ? '...' : '' ?></p>
                        <div class="work-footer">
                            <div class="work-stats"><span><i class="bi bi-download"></i><?= $work['downloads'] ?></span><span><i class="bi bi-eye"></i><?= $work['views'] ?></span></div>
                            <a href="?download=<?= $work['id'] ?>" class="btn btn-accent btn-sm"><i class="bi bi-download"></i></a>
                        </div>
                    </div>
                    <div class="px-3 pb-3"><span class="badge-campus <?= getCampusClass($work['campus']) ?>"><i class="bi bi-geo-alt me-1"></i><?= e($work['campus']) ?></span><small class="text-muted ms-2"><?= e($work['author_name'] ?? 'Неизвестно') ?></small></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
        <nav class="mt-5"><ul class="pagination justify-content-center">
            <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?<?= buildQuery(['page' => $page - 1]) ?>"><i class="bi bi-chevron-left"></i></a></li><?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?><li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?<?= buildQuery(['page' => $i]) ?>"><?= $i ?></a></li><?php endfor; ?>
            <?php if ($page < $totalPages): ?><li class="page-item"><a class="page-link" href="?<?= buildQuery(['page' => $page + 1]) ?>"><i class="bi bi-chevron-right"></i></a></li><?php endif; ?>
        </ul></nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
<?php if ($user): ?>
<div class="modal fade" id="uploadModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Загрузить работу</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="modal-body">
            <?php if (!empty($uploadErrors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($uploadErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
            <input type="hidden" name="action" value="upload">
            <div class="mb-3"><label class="form-label">Название *</label><input type="text" name="title" class="form-control" required value="<?= e($_POST['title'] ?? '') ?>"></div>
            <div class="mb-3"><label class="form-label">Описание</label><textarea name="description" class="form-control" rows="3"><?= e($_POST['description'] ?? '') ?></textarea></div>
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Предмет *</label><input type="text" name="subject" class="form-control" list="subjectsList" required value="<?= e($_POST['subject'] ?? '') ?>"><datalist id="subjectsList"><?php foreach ($subjects as $s): ?><option value="<?= e($s) ?>"><?php endforeach; ?></datalist></div>
                <div class="col-md-4 mb-3"><label class="form-label">Тип *</label><select name="type" class="form-select" required><option value="">Выберите...</option><option value="курсовая">Курсовая</option><option value="лабораторная">Лабораторная</option><option value="реферат">Реферат</option><option value="диплом">Диплом</option><option value="конспект">Конспект</option><option value="другое">Другое</option></select></div>
                <div class="col-md-4 mb-3"><label class="form-label">Корпус *</label><select name="campus" class="form-select" required><option value="">Выберите...</option><option value="Брамса 9">Брамса 9</option><option value="Спортивная 6">Спортивная 6</option><option value="Озерова 7">Озерова 7</option></select></div>
            </div>
            <div class="mb-3"><label class="form-label">Файл (PDF, DOC, DOCX, ZIP, макс. 20 МБ) *</label><input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.zip" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Отмена</button><button type="submit" class="btn btn-accent">Загрузить</button></div>
    </form>
</div></div></div>
<?php endif; ?>
<?php require_once '../includes/footer.php'; ?>