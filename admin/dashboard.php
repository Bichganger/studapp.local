<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { 
    header('Location: ../dashboard.php'); 
    exit; 
}

$pageTitle = 'Админ-панель';

// Основная статистика
$usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$studentsCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$teachersCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role='teacher'")->fetchColumn();
$groupsCount = $pdo->query("SELECT COUNT(DISTINCT group_name) FROM users WHERE group_name IS NOT NULL AND group_name != ''")->fetchColumn();
$libraryCount = $pdo->query("SELECT COUNT(*) FROM library")->fetchColumn();

// Новые пользователи за сегодня
$newUsersToday = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()")->fetchColumn();

// Последние действия
$stmt = $pdo->query("SELECT u.full_name, g.subject, g.grade, g.created_at FROM grades g JOIN users u ON g.student_id = u.id ORDER BY g.created_at DESC LIMIT 5");
$recentGrades = $stmt->fetchAll();

$stmt = $pdo->query("SELECT l.title, u.full_name, l.created_at FROM library l JOIN users u ON l.uploaded_by = u.id ORDER BY l.created_at DESC LIMIT 5");
$recentLibrary = $stmt->fetchAll();

// Неодобршенные пользователи (если есть колонка)
try {
    $cols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('is_approved', $cols)) {
        $pendingUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_approved = 0")->fetchColumn();
    } else {
        $pendingUsers = 0;
    }
} catch (Exception $e) {
    $pendingUsers = 0;
}

require_once '../includes/header.php';
?>

<style>
    .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card h2 { color: var(--accent); margin: 0; }
    .stat-icon { font-size: 2rem; opacity: 0.8; }
    .activity-list { list-style: none; padding: 0; margin: 0; }
    .activity-list li { padding: 10px 0; border-bottom: 1px solid var(--border-color); }
    .activity-list li:last-child { border-bottom: none; }
    .activity-time { font-size: 0.8rem; color: var(--text-muted); }
    .progress-bar-custom { height: 8px; border-radius: 4px; background: var(--border-color); overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 4px; transition: width 0.3s; }
</style>

<div class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Администрирование</span>
            <h1 class="section-title">Панель управления</h1>
        </div>

        <!-- Основная статистика -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Всего пользователей</h6>
                        <h2><?= $usersCount ?></h2>
                        <small class="text-success"><i class="bi bi-arrow-up"></i> +<?= $newUsersToday ?> за сегодня</small>
                    </div>
                    <i class="bi bi-people stat-icon text-primary"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Студенты</h6>
                        <h2><?= $studentsCount ?></h2>
                        <small class="text-muted"><?= round($studentsCount / max($usersCount, 1) * 100) ?>% от всех</small>
                    </div>
                    <i class="bi bi-mortarboard stat-icon text-info"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Преподаватели</h6>
                        <h2><?= $teachersCount ?></h2>
                        <small class="text-muted"><?= round($teachersCount / max($usersCount, 1) * 100) ?>% от всех</small>
                    </div>
                    <i class="bi bi-person-badge stat-icon text-warning"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Групп</h6>
                        <h2><?= $groupsCount ?></h2>
                        <small class="text-muted"><?= round($studentsCount / max($groupsCount, 1)) ?> студ. в группе</small>
                    </div>
                    <i class="bi bi-collection stat-icon text-success"></i>
                </div>
            </div>
        </div>

        <!-- Важные уведомления -->
        <?php if ($pendingUsers > 0): ?>
        <div class="notification warning">
            <i class="bi bi-exclamation-triangle"></i>
            <div class="notification-content">
                <div class="notification-title">Внимание!</div>
                <p class="notification-text"><?= $pendingUsers ?> пользователь(ей) ожидают одобрения. <a href="users.php?status=pending">Перейти к списку</a></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Основная сетка -->
        <div class="row g-4">
            <!-- Последние оценки -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-star me-2"></i>Последние оценки</span>
                        <a href="users.php" class="btn btn-sm btn-outline-light">Все</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentGrades)): ?>
                            <p class="text-muted mb-0">Нет оценок за последнее время</p>
                        <?php else: ?>
                            <ul class="activity-list">
                                <?php foreach ($recentGrades as $g): ?>
                                <li>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong><?= e($g['full_name']) ?></strong>
                                            <span class="text-muted"> — <?= e($g['subject']) ?></span>
                                        </div>
                                        <span class="badge bg-<?= $g['grade'] >= 4 ? 'success' : ($g['grade'] == 3 ? 'warning' : 'danger') ?>"><?= $g['grade'] ?></span>
                                    </div>
                                    <small class="activity-time"><?= date('d.m.H:i', strtotime($g['created_at'])) ?></small>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Свежие материалы -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-journal me-2"></i>Новые материалы</span>
                        <a href="library.php" class="btn btn-sm btn-outline-light">Все</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentLibrary)): ?>
                            <p class="text-muted mb-0">Нет новых материалов</p>
                        <?php else: ?>
                            <ul class="activity-list">
                                <?php foreach ($recentLibrary as $l): ?>
                                <li>
                                    <div>
                                        <strong><?= e($l['title']) ?></strong>
                                        <br><small class="text-muted">Загрузил: <?= e($l['full_name']) ?></small>
                                    </div>
                                    <small class="activity-time"><?= date('d.m.H:i', strtotime($l['created_at'])) ?></small>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><i class="bi bi-lightning me-2"></i>Быстрые действия</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6 col-md-2">
                                <a href="users.php" class="btn btn-outline-light w-100 h-100 d-flex flex-column align-items-center py-2">
                                    <i class="bi bi-people fs-4"></i>
                                    <small>Пользователи</small>
                                </a>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="groups.php" class="btn btn-outline-light w-100 h-100 d-flex flex-column align-items-center py-2">
                                    <i class="bi bi-collection fs-4"></i>
                                    <small>Группы</small>
                                </a>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="schedule.php" class="btn btn-outline-light w-100 h-100 d-flex flex-column align-items-center py-2">
                                    <i class="bi bi-calendar fs-4"></i>
                                    <small>Расписание</small>
                                </a>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="library.php" class="btn btn-outline-light w-100 h-100 d-flex flex-column align-items-center py-2">
                                    <i class="bi bi-journal fs-4"></i>
                                    <small>Библиотека</small>
                                </a>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="teachers.php" class="btn btn-outline-light w-100 h-100 d-flex flex-column align-items-center py-2">
                                    <i class="bi bi-person-badge fs-4"></i>
                                    <small>Преподаватели</small>
                                </a>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="settings.php" class="btn btn-outline-light w-100 h-100 d-flex flex-column align-items-center py-2">
                                    <i class="bi bi-gear fs-4"></i>
                                    <small>Настройки</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
