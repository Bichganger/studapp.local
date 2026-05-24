<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$user = getCurrentUser();
$pageTitle = 'Библиотека';

// Загрузка работы
$uploadErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
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
        $allowedTypes = ['pdf', 'doc', 'docx', 'zip', 'ppt', 'pptx'];
        if (!in_array($ext, $allowedTypes)) {
            $uploadErrors[] = 'Допустимы только PDF, DOC, DOCX, ZIP, PPT, PPTX';
        } elseif ($_FILES['file']['size'] > 20971520) {
            $uploadErrors[] = 'Файл слишком большой (макс. 20 МБ)';
        } else {
            $newName = uniqid() . '_' . time() . '.' . $ext;
            $targetPath = $targetDir . '/' . $newName;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                $stmt = $pdo->prepare("INSERT INTO works (title, description, file_path, file_type, uploaded_by, group_name) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, 'uploads/works/' . $newName, $fileType, $_SESSION['user_id'], $groupName ?: null]);
                header('Location: library.php?success=1');
                exit;
            } else {
                $uploadErrors[] = 'Не удалось сохранить файл';
            }
        }
    }
}

$works = $pdo->query("SELECT w.*, u.full_name as author FROM works w LEFT JOIN users u ON w.uploaded_by = u.id ORDER BY w.created_at DESC")->fetchAll();

// Удаление
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT file_path FROM works WHERE id = ?");
    $stmt->execute([intval($_GET['delete'])]);
    $w = $stmt->fetch();
    if ($w && file_exists('../' . $w['file_path'])) unlink('../' . $w['file_path']);
    $pdo->prepare("DELETE FROM works WHERE id = ?")->execute([intval($_GET['delete'])]);
    header('Location: library.php?deleted=1');
    exit;
}

$groups = $pdo->query("SELECT DISTINCT group_name FROM users WHERE group_name IS NOT NULL AND group_name != '' ORDER BY group_name")->fetchAll(PDO::FETCH_COLUMN);
if (empty($groups)) $groups = ['РУПО 26-21', 'РУПО 26-22', 'ИБ 26-21', 'ИБ 26-22'];

$fileTypeLabels = [
    'coursework' => 'Курсовая',
    'lab' => 'Лабораторная',
    'referat' => 'Реферат',
    'other' => 'Другое',
];

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Библиотека работ</h1></div>
    <?php if (isset($_GET['success'])): ?><div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Работа загружена!</div><?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-info">Материал удалён</div><?php endif; ?>
    <?php if (!empty($uploadErrors)): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($uploadErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card"><div class="card-header"><i class="bi bi-cloud-upload me-2"></i>Загрузить работу</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload">
                        <div class="mb-3">
                            <label class="form-label">Название *</label>
                            <input type="text" name="title" class="form-control" required placeholder="Название работы">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Краткое описание"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Тип работы *</label>
                            <select name="file_type" class="form-select" required>
                                <option value="">Выберите</option>
                                <option value="coursework">Курсовая работа</option>
                                <option value="lab">Лабораторная</option>
                                <option value="referat">Реферат</option>
                                <option value="other">Другое</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Группа</label>
                            <select name="group_name" class="form-select">
                                <option value="">Общая</option>
                                <?php foreach ($groups as $g): ?><option value="<?= e($g) ?>"><?= e($g) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Файл *</label>
                            <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.zip,.ppt,.pptx">
                            <small class="text-muted">Макс. 20 МБ</small>
                        </div>
                        <button type="submit" class="btn btn-accent w-100">Загрузить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card"><div class="card-header"><i class="bi bi-list-ul me-2"></i>Все работы (<?= count($works) ?>)</div>
                <div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0">
                    <thead><tr><th>Название</th><th>Тип</th><th>Автор</th><th>Группа</th><th>Скачиваний</th><th></th></tr></thead>
                    <tbody>
                        <?php if (empty($works)): ?>
                        <tr><td colspan="6" class="text-center py-3">Нет материалов</td></tr>
                        <?php else: ?>
                        <?php foreach ($works as $w): ?>
                        <tr>
                            <td><strong><?= e($w['title']) ?></strong><br><small class="text-muted"><?= e(mb_substr($w['description'] ?? '', 0, 40)) ?></small></td>
                            <td><span class="badge bg-info"><?= e($fileTypeLabels[$w['file_type']] ?? $w['file_type']) ?></span></td>
                            <td><?= e($w['author'] ?? '—') ?></td>
                            <td><span class="badge bg-secondary"><?= e($w['group_name'] ?? 'Все') ?></span></td>
                            <td><?= $w['downloads'] ?? 0 ?></td>
                            <td><a href="?delete=<?= $w['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')"><i class="bi bi-trash"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table></div></div>
            </div>
        </div>
    </div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
