<?php
session_start();

// Если уже вошли - перенаправляем по роли
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
    <link rel="stylesheet" href="assets/css/neural-network.css">
</head>
<body class="role-student">
    <!-- Живой фон -->
    <div class="neural-bg"></div>
    <canvas id="neuralNetworkCanvas"></canvas>
    
    <!-- Карточка входа -->
    <div class="neural-form neural-fade-in">
        <div class="text-center mb-4">
            <div class="logo-icon" style="animation: iconBounce 2s ease-in-out infinite;">
                <i class="bi bi-journal-code"></i>
            </div>
            <h2 class="neural-greeting" style="font-size: 2rem; margin-top: 10px;">Учеба24</h2>
            <p class="text-muted">Вход в систему</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger neural-alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <form action="auth.php" method="POST" id="loginForm">
            <div class="mb-3">
                <label class="form-label fw-bold"><i class="bi bi-person-badge-fill text-primary me-2"></i>Логин</label>
                <input type="text" name="username" class="form-control neural-form-input" 
                       placeholder="ivanov_i" required autocomplete="username" autofocus>
                <div class="form-text">Ваш уникальный идентификатор</div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold"><i class="bi bi-key-fill text-primary me-2"></i>Пароль</label>
                <input type="password" name="password" class="form-control neural-form-input" 
                       placeholder="••••••••" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn neural-btn w-100 mb-3" id="submitBtn">
                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
            </button>
        </form>
        
        <div class="text-center">
            <small class="text-muted">Нет аккаунта? 
                <a href="register_new.php" class="text-decoration-none fw-bold" style="color: #667eea;">Зарегистрироваться</a>
            </small>
        </div>
        
        <div class="mt-4 info-box">
            <small>
                <i class="bi bi-shield-check me-1"></i>
                Безопасный вход с шифрованием паролей
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/neural-network.js"></script>
    <script>
        // Эффект при отправке формы
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<span class="neural-loader d-inline-block me-2" style="width:20px;height:20px;border-width:2px;"></span>Вход...';
        });
    </script>
</body>
</html>