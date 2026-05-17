<?php
session_start();
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Рассылка уведомлений';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send') {
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $targetGroup = trim($_POST['target_group'] ?? '');
    $targetRole = $_POST['target_role'] ?? 'students';
    
    if (!empty($title) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO notifications (title, message, target_type, target_group, sender_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $message, $targetRole, $targetGroup ?: null, $_SESSION['user_id']]);
        header('Location: notifications.php?success=1');
        exit;
    }
}

$groups = $pdo->query("SELECT DISTINCT group_name FROM users WHERE group_name IS NOT NULL AND group_name != '' ORDER BY group_name")->fetchAll(PDO::FETCH_COLUMN);
$notifications = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 40")->fetchAll();

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Рассылка уведомлений</h1></div>

    <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Уведомление отправлено!</div><?php endif; ?>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card"><div class="card-header"><i class="bi bi-plus-circle me-2"></i>Создать</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="send">
                        <div class="mb-2"><label class="form-label">Заголовок</label><input type="text" name="title" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Сообщение</label><textarea name="message" class="form-control" rows="3" required></textarea></div>
                        <div class="mb-2"><label class="form-label">Кому</label>
                            <select name="target_role" class="form-select">
                                <option value="students">Студенты</option><option value="teachers">Преподаватели</option><option value="admin">Администраторы</option>
                            </select>
                        </div>
                        <div class="mb-2"><label class="form-label">Группа (опционально)</label>
                            <select name="target_group" class="form-select">
                                <option value="">Всем</option>
                                <?php foreach ($groups as $g): ?><option value="<?= e($g) ?>"><?= e($g) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-accent w-100">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card"><div class="card-header"><i class="bi bi-list-ul me-2"></i>История</div>
                <div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0">
                    <thead><tr><th>Заголовок</th><th>Кому</th><th>Дата</th></tr></thead>
                    <tbody>
                        <?php if (empty($notifications)): ?>
                        <tr><td colspan="3" class="text-center">Нет уведомлений</td></tr>
                        <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                        <tr>
                            <td><strong><?= e($n['title']) ?></strong><br><small class="text-muted"><?= e(mb_substr($n['message'], 0, 80)) ?></small></td>
                            <td><?= $n['target_group'] ? e($n['target_group']) : e($n['target_type']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></td>
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
