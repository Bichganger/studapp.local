<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: /dashboard.php'); exit; }

$pageTitle = 'Настройки';
require_once '../includes/header.php';
?>

<div class="section"><div class="container">
    <div class="section-header"><span class="section-label">Администрирование</span><h1 class="section-title">Настройки</h1></div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card"><div class="card-header"><i class="bi bi-info-circle me-2"></i>Информация о системе</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent d-flex justify-content-between border-secondary"><span>Версия</span><strong>2.0</strong></li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between border-secondary"><span>Пользователь</span><strong><?= e($_SESSION['full_name']) ?></strong></li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between border-secondary"><span>Роль</span><strong>Администратор</strong></li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between border-secondary"><span>БД</span><strong>studapp (MySQL)</strong></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card"><div class="card-header"><i class="bi bi-speedometer2 me-2"></i>Статистика</div>
                <div class="card-body">
                    <?php
                    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                    $totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
                    $totalTeachers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='teacher'")->fetchColumn();
                    $totalWorks = $pdo->query("SELECT COUNT(*) FROM works")->fetchColumn();
                    $totalGrades = $pdo->query("SELECT COUNT(*) FROM grades")->fetchColumn();
                    ?>
                    <p><i class="bi bi-people me-2"></i>Пользователей: <strong><?= $totalUsers ?></strong></p>
                    <p><i class="bi bi-mortarboard me-2"></i>Студентов: <strong><?= $totalStudents ?></strong></p>
                    <p><i class="bi bi-person-badge me-2"></i>Преподавателей: <strong><?= $totalTeachers ?></strong></p>
                    <p><i class="bi bi-journal me-2"></i>Работ: <strong><?= $totalWorks ?></strong></p>
                    <p><i class="bi bi-star me-2"></i>Оценок: <strong><?= $totalGrades ?></strong></p>
                </div>
            </div>
        </div>
    </div>
</div></div>

<?php require_once '../includes/footer.php'; ?>
