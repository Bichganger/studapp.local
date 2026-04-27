<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация — Учёба.Онлайн</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f0f2f5; height: 100vh; display: flex; align-items: center; }
        .reg-card { max-width: 460px; margin: auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="reg-card">
    <div class="card">
        <div class="card-body p-5 text-center">
            <h3 class="mb-4"><i class="bi bi-person-plus-fill text-success"></i> Регистрация</h3>
            <p class="text-muted mb-4">Создайте аккаунт студента</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <form action="reg_process.php" method="POST">
                <div class="mb-3 text-start">
                    <label class="form-label">ФИО</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Логин</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <input type="hidden" name="role" value="student">
                <button type="submit" class="btn btn-success w-100 mb-3">Зарегистрироваться</button>
            </form>

            <small class="text-muted">Уже есть аккаунт? <a href="login.php">Войти</a></small>
        </div>
    </div>
</div>

<footer class="text-center mt-3" style="font-size: 0.9rem; color: #6c757d;">&copy; 2026 Учёба.Онлайн. Образование будущего.</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>