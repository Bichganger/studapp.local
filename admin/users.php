<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') { 
    header('Location: ../dashboard.php'); 
    exit;
}

$user = getCurrentUser();
$pageTitle = 'Управление пользователями';

// Проверка наличия колонки is_approved
$hasIsApproved = false;
try {
    $cols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    $hasIsApproved = in_array('is_approved', $cols);
} catch (Exception $e) {}

// Одобрение/отклонение пользователя
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    if ($hasIsApproved) {
        $pdo->prepare("UPDATE users SET is_approved = 1 WHERE id = ?")->execute([intval($_GET['approve'])]);
    }
    header('Location: users.php?approved=1');
    exit;
}

if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
    if ($hasIsApproved) {
        $pdo->prepare("UPDATE users SET is_approved = 0 WHERE id = ?")->execute([intval($_GET['reject'])]);
    }
    header('Location: users.php?rejected=1');
    exit;
}

// Редактирование пользователя
$editErrors = [];
$editingUser = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $id = intval($_POST['id'] ?? 0);
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? 'student';
    $group_name = trim($_POST['group_name'] ?? '') ?: null;
    $course = intval($_POST['course'] ?? 1);
    $email = trim($_POST['email'] ?? '');
    $is_approved = $hasIsApproved ? (int)($_POST['is_approved'] ?? 0) : 1;
    
    if ($id > 0 && !empty($full_name) && !empty($username)) {
        $fields = ['full_name', 'username', 'role', 'group_name', 'course', 'email'];
        $values = [$full_name, $username, $role, $group_name, $course, $email];
        
        if ($hasIsApproved) { $fields[] = 'is_approved'; $values[] = $is_approved; }
        
        $sql = "UPDATE users SET " . implode('=?, ', $fields) . "=? WHERE id = ?";
        $values[] = $id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        header('Location: users.php?success=1');
        exit;
    }
}

// Удаление пользователя
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        header('Location: users.php?deleted=1');
        exit;
    }
}

// Добавление пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student';
    $group_name = trim($_POST['group_name'] ?? '') ?: null;
    $course = intval($_POST['course'] ?? 1);
    $email = trim($_POST['email'] ?? '');
    
    if (empty($full_name) || empty($username) || empty($password)) {
        $editErrors[] = 'Заполните все обязательные поля';
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $fields = ['full_name', 'username', 'password', 'role', 'group_name', 'course', 'email'];
        $values = [$full_name, $username, $passwordHash, $role, $group_name, $course, $email];
        
        if ($hasIsApproved) { $fields[] = 'is_approved'; $values[] = 1; }
        
        $sql = "INSERT INTO users (" . implode(',', $fields) . ") VALUES (" . str_repeat('?,', count($fields)-1) . "?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        header('Location: users.php?success=1');
        exit;
    }
}

// Получение списка пользователей
$users = $pdo->query("SELECT * FROM users ORDER BY role DESC, full_name")->fetchAll();
$groups = $pdo->query("SELECT DISTINCT group_name FROM users WHERE group_name IS NOT NULL AND group_name != '' ORDER BY group_name")->fetchAll(PDO::FETCH_COLUMN);
if (empty($groups)) $groups = ['РУПО 26-21', 'РУПО 26-22', 'ИБ 26-21', 'ИБ 26-22'];

// Фильтр
$filterRole = $_GET['role'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$filteredUsers = $users;
if ($filterRole) {
    $filteredUsers = array_filter($filteredUsers, fn($u) => $u['role'] === $filterRole);
}
if ($filterStatus === 'pending' && $hasIsApproved) {
    $filteredUsers = array_filter($filteredUsers, fn($u) => empty($u['is_approved']));
} elseif ($filterStatus === 'approved' && $hasIsApproved) {
    $filteredUsers = array_filter($filteredUsers, fn($u) => !empty($u['is_approved']));
}

// Получение выбранного пользователя для редактирования
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editingUser = $stmt->fetch();
}

require_once '../includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Администрирование</span>
            <h1 class="section-title">Управление пользователями</h1>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="notification success">
            <i class="bi bi-check-circle"></i>
            <div class="notification-content">
                <p class="notification-text mb-0">Данные сохранены!</p>
            </div>
        </div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
        <div class="notification info">
            <i class="bi bi-trash"></i>
            <div class="notification-content">
                <p class="notification-text mb-0">Пользователь удалён</p>
            </div>
        </div>
        <?php endif; ?>
        <?php if (isset($_GET['approved'])): ?>
        <div class="notification success">
            <i class="bi bi-check-circle"></i>
            <div class="notification-content">
                <p class="notification-text mb-0">Пользователь одобрен</p>
            </div>
        </div>
        <?php endif; ?>
        <?php if (isset($_GET['rejected'])): ?>
        <div class="notification warning">
            <i class="bi bi-x-circle"></i>
            <div class="notification-content">
                <p class="notification-text mb-0">Доступ отклонён</p>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($editErrors)): ?>
        <div class="notification error">
            <i class="bi bi-exclamation-triangle"></i>
            <div class="notification-content">
                <ul class="mb-0"><?php foreach ($editErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-person-plus me-2"></i><?= $editingUser ? 'Редактировать' : 'Добавить' ?> пользователя
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?= $editingUser ? 'edit_user' : 'add_user' ?>">
                            <?php if ($editingUser): ?><input type="hidden" name="id" value="<?= $editingUser['id'] ?>"><?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label">ФИО *</label>
                                <input type="text" name="full_name" class="form-control" required 
                                       value="<?= e($editingUser['full_name'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Имя пользователя *</label>
                                <input type="text" name="username" class="form-control" required 
                                       value="<?= e($editingUser['username'] ?? '') ?>">
                            </div>
                            
                            <?php if (!$editingUser): ?>
                            <div class="mb-3">
                                <label class="form-label">Пароль *</label>
                                <input type="password" name="password" class="form-control" required 
                                       minlength="6" placeholder="Минимум 6 символов">
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Роль *</label>
                                <select name="role" class="form-select" required>
                                    <option value="student" <?= ($editingUser['role'] ?? '') === 'student' ? 'selected' : '' ?>>Студент</option>
                                    <option value="teacher" <?= ($editingUser['role'] ?? '') === 'teacher' ? 'selected' : '' ?>>Преподаватель</option>
                                    <option value="admin" <?= ($editingUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Администратор</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Группа</label>
                                <select name="group_name" class="form-select">
                                    <option value="">—</option>
                                    <?php foreach ($groups as $g): ?>
                                    <option value="<?= e($g) ?>" <?= ($editingUser['group_name'] ?? '') === $g ? 'selected' : '' ?>><?= e($g) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Курс</label>
                                <input type="number" name="course" class="form-control" min="1" max="5" 
                                       value="<?= $editingUser['course'] ?? 1 ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= e($editingUser['email'] ?? '') ?>">
                            </div>
                            
                            <?php if ($hasIsApproved): ?>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_approved" class="form-check-input" value="1" 
                                       <?= !empty($editingUser['is_approved']) ? 'checked' : '' ?>>
                                <label class="form-check-label">Одобрён (может войти)</label>
                            </div>
                            <?php endif; ?>
                            
                            <button type="submit" class="btn btn-accent w-100">
                                <?= $editingUser ? 'Сохранить' : 'Добавить' ?>
                            </button>
                            <?php if ($editingUser): ?>
                            <a href="users.php" class="btn btn-outline-light w-100 mt-2">Отмена</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-funnel me-2"></i>Фильтр
                        <span class="badge bg-secondary ms-2"><?= count($filteredUsers) ?> пользователей</span>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-2">
                            <div class="col-6 col-md-5">
                                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Все роли</option>
                                    <option value="admin" <?= $filterRole === 'admin' ? 'selected' : '' ?>>Администраторы</option>
                                    <option value="teacher" <?= $filterRole === 'teacher' ? 'selected' : '' ?>>Преподаватели</option>
                                    <option value="student" <?= $filterRole === 'student' ? 'selected' : '' ?>>Студенты</option>
                                </select>
                            </div>
                            <?php if ($hasIsApproved): ?>
                            <div class="col-6 col-md-5">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Все</option>
                                    <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Ожидают одобрения</option>
                                    <option value="approved" <?= $filterStatus === 'approved' ? 'selected' : '' ?>>Одобрены</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="col-12 col-md-2">
                                <a href="users.php" class="btn btn-outline-secondary btn-sm w-100">Сброс</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-2"></i>Список пользователей
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>ФИО</th>
                                        <th>Роль</th>
                                        <th>Группа</th>
                                        <?php if ($hasIsApproved): ?><th>Статус</th><?php endif; ?>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($filteredUsers)): ?>
                                    <tr><td colspan="<?= $hasIsApproved ? 5 : 4 ?>" class="text-center py-3">Нет пользователей</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($filteredUsers as $u): ?>
                                    <tr>
                                        <td><strong><?= e($u['full_name']) ?></strong><br><small class="text-muted">@<?= e($u['username']) ?></small></td>
                                        <td><span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'teacher' ? 'info' : 'secondary') ?>"><?= ucfirst($u['role']) ?></span></td>
                                        <td><?= e($u['group_name'] ?? '—') ?></td>
                                        <?php if ($hasIsApproved): ?>
                                        <td>
                                            <?php if (!empty($u['is_approved'])): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Одобрён</span>
                                            <?php else: ?>
                                            <span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>Ожидает</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                        <td>
                                            <a href="?edit=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary" title="Редактировать"><i class="bi bi-pencil"></i></a>
                                            <?php if ($hasIsApproved && empty($u['is_approved']) && $u['id'] != $_SESSION['user_id']): ?>
                                            <a href="?approve=<?= $u['id'] ?>" class="btn btn-sm btn-outline-success" title="Одобрить"><i class="bi bi-check"></i></a>
                                            <?php endif; ?>
                                            <?php if ($hasIsApproved && !empty($u['is_approved']) && $u['id'] != $_SESSION['user_id']): ?>
                                            <a href="?reject=<?= $u['id'] ?>" class="btn btn-sm btn-outline-warning" title="Отклонить"><i class="bi bi-x"></i></a>
                                            <?php endif; ?>
                                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить?')" title="Удалить"><i class="bi bi-trash"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
