<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
        body {
            background: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .login-card {
            max-width: 400px;
            margin: 60px auto;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        footer {
            width: 100%;
            text-align: center;
            padding: 20px 0;
            font-size: 0.9rem;
            color: #6c757d;
            background: white;
            border-top: 1px solid #dee2e6;
            margin-top: 60px;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="card">
        <div class="card-body p-5 text-center">
            <h3 class="mb-4"><i class="bi bi-journal-code text-primary"></i> Учеба24</h3>
            <p class="text-muted mb-4">Введите данные для входа</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <div class="mb-3 text-start">
                    <label class="form-label">Логин</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Войти</button>
            </form>

            <small class="text-muted">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></small>
        </div>
    </div>
</div>

<footer class="text-center mt-3" style="font-size: 0.9rem; color: #6c757d;">&copy; 2026 Учеба24</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>