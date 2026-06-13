<?php
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin': header('Location: admin/dashboard.php'); exit;
        case 'teacher': header('Location: teacher/panel.php'); exit;
        case 'student': header('Location: student/panel.php'); exit;
    }
}
$pageTitle = 'Вход';
require_once 'includes/header.php';
?>
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="auth-card">
                    <h3 class="auth-title"><i class="bi bi-box-arrow-in-right me-2"></i>Вход в систему</h3>
                    <p class="auth-subtitle">Добро пожаловать в Учеба24</p>
                    <?php if (isset($_GET['error'])): ?>
                    <div class="notification error">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div class="notification-content">
                            <p class="notification-text mb-0"><?= e($_GET['error']) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <form action="auth.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Логин</label>
                            <input type="text" name="username" class="form-control" placeholder="ivanov_i" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    <button type="submit" class="btn btn-accent w-100"><i class="bi bi-box-arrow-in-right me-2"></i>Войти</button>
                    </form>
                    <p class="auth-link">Нет аккаунта? <a href="register_new.php" class="text-accent">Зарегистрироваться</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>