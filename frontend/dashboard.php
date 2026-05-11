<?php
session_start();

// Если уже вошли - перенаправляем
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            exit;
        case 'teacher':
            header("Location: teacher/panel.php");
            exit;
        case 'student':
            header("Location: student/panel.php");
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 400px; margin: 0 auto; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .login-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 15px 15px 0 0; text-align: center; }
        .login-body { padding: 40px; }
        .btn-login { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-login:hover { opacity: 0.9; }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-journal-code display-4"></i>
            <h2 class="mt-3 mb-0">Учеба24</h2>
            <p class="mb-0">Вход в систему</p>
        </div>
        <div class="login-body">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>
            
            <form action="auth.php" method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-fill me-1"></i>Логин</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-key-fill me-1"></i>Пароль</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-login w-100 py-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                </button>
            </form>
            
            <hr class="my-4">
            
            <div class="text-center">
                <a href="register_new.php" class="text-decoration-none">
                    <i class="bi bi-person-plus-fill me-1"></i>Регистрация
                </a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>