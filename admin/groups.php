<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { 
    header('Location: ../dashboard.php'); 
    exit; 
}

$pageTitle = 'Группы';
$groups = $pdo->query("SELECT group_name, COUNT(*) as cnt FROM users WHERE group_name IS NOT NULL AND group_name != '' GROUP BY group_name ORDER BY group_name")->fetchAll();

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Группы</h1></div>
    <div class="row g-4">
        <?php foreach ($groups as $g): ?>
        <div class="col-md-4"><div class="card text-center"><div class="card-body">
            <h5><?= e($g['group_name']) ?></h5>
            <p class="text-muted mb-0">Студентов: <?= $g['cnt'] ?></p>
        </div></div></div>
        <?php endforeach; ?>
    </div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
