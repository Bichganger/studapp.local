<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'teacher') { 
    header('Location: ../dashboard.php'); 
    exit; 
}

$pageTitle = 'Мои группы';
$groups = $pdo->query("SELECT group_name, COUNT(*) as cnt FROM users WHERE role='student' AND group_name IS NOT NULL AND group_name != '' GROUP BY group_name ORDER BY group_name")->fetchAll();

require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Преподавание</span><h1 class="section-title">Мои группы</h1></div>
    
    <div class="row g-4">
        <?php foreach ($groups as $g): ?>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5><?= e($g['group_name']) ?></h5>
                    <p class="text-muted mb-2">Студентов: <?= $g['cnt'] ?></p>
                    <a href="journal.php" class="btn btn-sm btn-outline-light">Смотреть успеваемость</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
