<?php
session_start();
require_once '../config/db.php';
require_once '../protected/auth_guard.php';

$user = getCurrentUser();

// Обработка скачивания - ДО подключения header.php
if (isset($_GET['download']) && is_numeric($_GET['download'])) {
    $workId = intval($_GET['download']);
    $stmt = $pdo->prepare("SELECT file_path, title FROM works WHERE id = ?");
    $stmt->execute([$workId]);
    $work = $stmt->fetch();
    if ($work && file_exists(__DIR__ . '/../' . $work['file_path'])) {
        $pdo->prepare("UPDATE works SET downloads = downloads + 1 WHERE id = ?")->execute([$workId]);
        $ext = pathinfo($work['file_path'], PATHINFO_EXTENSION);
        $filename = preg_replace('/[^a-zA-Z0-9а-яА-Я\s_-]/u', '', $work['title']) . '.' . $ext;
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize(__DIR__ . '/../' . $work['file_path']));
        readfile(__DIR__ . '/../' . $work['file_path']);
        exit;
    }
}

// Обработка загрузки - ДО подключения header.php
$uploadErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload' && $user) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $fileType = $_POST['file_type'] ?? '';
    $groupName = trim($_POST['group_name'] ?? '');
    
    if (empty($title)) $uploadErrors[] = 'Укажите название работы';
    if (empty($fileType)) $uploadErrors[] = 'Выберите тип файла';
    if (empty($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) { $uploadErrors[] = 'Выберите файл'; }
    
    if (empty($uploadErrors)) {
        $targetDir = __DIR__ . '/../uploads/works';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'doc', 'docx', 'zip'];
        if (!in_array($ext, $allowedTypes)) {
            $uploadErrors[] = 'Допустимы только PDF, DOC, DOCX, ZIP';
        } elseif ($_FILES['file']['size'] > 20971520) {
            $uploadErrors[] = 'Файл слишком большой (макс. 20 МБ)';
        } else {
            $newName = uniqid() . '_' . time() . '.' . $ext;
            $targetPath = $targetDir . '/' . $newName;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                $stmt = $pdo->prepare("INSERT INTO works (title, description, file_path, file_type, uploaded_by, group_name) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, 'uploads/works/' . $newName, $fileType, $_SESSION['user_id'], $groupName]);
                header('Location: library.php?success=1');
                exit;
            } else {
                $uploadErrors[] = 'Не удалось сохранить файл';
            }
        }
    }
}

$pageTitle = 'Библиотека работ';
require_once '../includes/header.php';

$searchQuery = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];
if ($searchQuery) { 
    $where[] = "(title LIKE ? OR description LIKE ?)"; 
    $searchLike = "%$searchQuery%"; 
    $params[] = $searchLike; 
    $params[] = $searchLike; 
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countSql = "SELECT COUNT(*) FROM works $whereSql";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalWorks = $countStmt->fetchColumn();
$totalPages = ceil($totalWorks / $perPage);

$sql = "SELECT w.*, u.full_name as author_name FROM works w LEFT JOIN users u ON w.uploaded_by = u.id $whereSql ORDER BY w.created_at DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);
$stmt->execute(array_merge($params, [$perPage, $offset]));
$works = $stmt->fetchAll();

$fileTypes = $pdo->query("SELECT DISTINCT file_type FROM works WHERE file_type IS NOT NULL ORDER BY file_type")->fetchAll(PDO::FETCH_COLUMN);
$groups = $pdo->query("SELECT DISTINCT group_name FROM works WHERE group_name IS NOT NULL ORDER BY group_name")->fetchAll(PDO::FETCH_COLUMN);

$fileTypeLabels = [
    'coursework' => 'Курсовая',
    'lab' => 'Лабораторная',
    'referat' => 'Реферат',
    'other' => 'Другое',
];

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
            <a href="/dashboard.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-in-right me-2"></i>Войти для загрузки</a>
            <?php endif; ?>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-lg-6 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Поиск по названию или описанию..." value="<?= e($searchQuery) ?>">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="file_type" class="form-select"><option value="">Все типы</option><?php foreach ($fileTypeLabels as $val => $label): ?><option value="<?= $val ?>" <?= (isset($_GET['file_type']) && $_GET['file_type'] === $val) ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <button type="submit" class="btn btn-accent w-100"><i class="bi bi-funnel me-1"></i>Фильтр</button>
                    </div>
                </form>
            </div>
        </div>
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Работа успешно загружена!</div>
        <?php endif; ?>
        <?php if (empty($works)): ?>
        <div class="empty-state text-center py-5"><i class="bi bi-journal-x" style="font-size: 3rem;"></i><h4 class="mt-3">Работы не найдены</h4><p class="text-muted">Попробуйте изменить параметры поиска</p></div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($works as $work): ?>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="work-card">
                    <div class="work-preview">
                        <?php 
                        $icon = 'bi-file-earmark-text'; 
                        $ext = pathinfo($work['file_path'], PATHINFO_EXTENSION); 
                        if ($ext === 'pdf') $icon = 'bi-file-earmark-pdf'; 
                        elseif (in_array($ext, ['doc', 'docx'])) $icon = 'bi-file-earmark-word'; 
                        elseif ($ext === 'zip') $icon = 'bi-file-earmark-zip'; 
                        ?>
                        <i class="bi <?= $icon ?>"></i>
                        <span class="work-type"><?= e($work['file_type'] ?? 'Документ') ?></span>
                    </div>
                    <div class="work-body">
                        <h5 class="work-title"><?= e($work['title']) ?></h5>
                        <p class="work-desc"><?= e(mb_substr($work['description'] ?: 'Нет описания', 0, 80)) ?><?= mb_strlen($work['description'] ?: '') > 80 ? '...' : '' ?></p>
                        <div class="work-footer">
                            <div class="work-stats"><span><i class="bi bi-download"></i> <?= $work['downloads'] ?></span></div>
                            <a href="?download=<?= $work['id'] ?>" class="btn btn-accent btn-sm"><i class="bi bi-download"></i></a>
                        </div>
                    </div>
                    <div class="px-3 pb-3">
                        <small class="text-muted"><?= e($work['author_name'] ?? 'Неизвестно') ?> • <?= date('d.m.Y', strtotime($work['created_at'])) ?></small>
                    </div>
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
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Загрузить работу</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="action" value="upload">
                <?php if (!empty($uploadErrors)): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($uploadErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>
                <div class="mb-3"><label class="form-label">Название *</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Описание</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                <div class="mb-3"><label class="form-label">Тип *</label>
                    <select name="file_type" class="form-select" required>
                        <option value="">Выберите...</option>
                        <?php foreach ($fileTypeLabels as $val => $label): ?><option value="<?= $val ?>"><?= $label ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Файл *</label><input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.zip"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button><button type="submit" class="btn btn-accent">Загрузить</button></div>
        </form>
    </div></div>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>