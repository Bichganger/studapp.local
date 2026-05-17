<?php
session_start();
require_once '../config/db.php';
if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Пользователи';
$users = $pdo->query("SELECT id, full_name, username, role, group_name, course, created_at FROM users ORDER BY created_at DESC")->fetchAll();

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Пользователи</h1></div>
    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>ID</th><th>ФИО</th><th>Логин</th><th>Роль</th><th>Группа</th><th>Курс</th><th>Дата</th></tr></thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <?php $rb = match($u['role']) { 'admin' => 'bg-danger', 'teacher' => 'bg-primary', default => 'bg-success' }; ?>
            <tr>
                <td><?= $u['id'] ?></td><td><?= e($u['full_name']) ?></td><td><?= e($u['username']) ?></td>
                <td><span class="badge <?= $rb ?>"><?= e($u['role']) ?></span></td>
                <td><?= e($u['group_name'] ?? '—') ?></td><td><?= $u['course'] ?></td>
                <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div></div></div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
