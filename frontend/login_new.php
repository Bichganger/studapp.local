<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/neural-network.css">
</head>
<body class="role-student">
    <!-- Живой фон -->
    <div class="neural-bg"></div>
    <canvas id="neuralNetworkCanvas"></canvas>
    
    <!-- Карточка входа -->
    <div class="neural-form neural-fade-in">
        <div class="text-center mb-4">
            <div style="font-size: 4rem; animation: iconBounce 2s ease-in-out infinite;">
                <i class="bi bi-journal-code" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
            </div>
            <h2 class="neural-greeting" style="font-size: 2rem; margin-top: 10px;">Учеба24</h2>
            <p class="text-muted">Войдите в свой аккаунт</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger neural-alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="auth.php" method="POST" id="loginForm">
            <div class="mb-4">
                <label class="form-label fw-bold"><i class="bi bi-person-badge-fill text-primary me-2"></i>Логин</label>
                <input type="text" name="username" class="form-control neural-form-input" 
                       placeholder="Ваш логин" required autofocus autocomplete="username">
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold"><i class="bi bi-key-fill text-primary me-2"></i>Пароль</label>
                <input type="password" name="password" class="form-control neural-form-input" 
                       placeholder="Ваш пароль" required autocomplete="current-password">
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe">
                <label class="form-check-label text-muted" for="rememberMe">
                    Запомнить меня
                </label>
            </div>

            <button type="submit" class="btn neural-btn w-100 mb-3 neural-btn-success">
                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
            </button>
        </form>

        <div class="text-center">
            <small class="text-muted">Нет аккаунта? 
                <a href="register.php" class="text-decoration-none fw-bold" style="color: #667eea;">Зарегистрироваться</a>
            </small>
        </div>
        
        <div class="mt-4 text-center">
            <a href="#" class="text-muted small"><i class="bi bi-question-circle me-1"></i>Забыли пароль?</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/neural-network.js"></script>
    <script>
        // Эффект при отправке
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<span class="neural-loader d-inline-block me-2"></span>Вход...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
