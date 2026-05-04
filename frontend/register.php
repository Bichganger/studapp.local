<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация студента — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f0f2f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .reg-card { max-width: 500px; margin: 60px auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        footer { width: 100%; text-align: center; padding: 20px 0; font-size: 0.9rem; color: #6c757d; background: white; border-top: 1px solid #dee2e6; margin-top: 60px; }
        .info-box { background: #e7f3ff; border-left: 4px solid #0d6efd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
<div class="reg-card">
    <div class="card">
        <div class="card-body p-4">
            <h3 class="mb-3 text-center"><i class="bi bi-person-plus-fill text-success"></i> Регистрация студента</h3>
            <div class="info-box">
                <i class="bi bi-shield-check me-1"></i>
                <small>Регистрация доступна только для студентов нашего колледжа.</small>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <form action="reg_process.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">ФИО полностью</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Номер группы</label>
                    <input type="text" name="group_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Курс</label>
                    <select name="course" class="form-select" required>
                        <option value="" selected disabled>Выберите курс</option>
                        <option value="1">1 курс</option>
                        <option value="2">2 курс</option>
                        <option value="3">3 курс</option>
                        <option value="4">4 курс</option>
                    </select>
                </div>
                <hr class="my-4">
                <h6 class="mb-3">Данные для входа</h6>
                <div class="mb-3">
                    <label class="form-label">Логин</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label class="form-label">Подтвердите пароль</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <input type="hidden" name="role" value="student">
                <button type="submit" class="btn btn-success w-100 mb-3">Зарегистрироваться</button>
            </form>
            <div class="text-center">
                <small class="text-muted">Уже есть аккаунт? <a href="login.php">Войти</a></small>
            </div>
        </div>
    </div>
</div>
<footer class="text-center mt-3" style="font-size: 0.9rem; color: #6c757d;">&copy; 2026 Учеба24</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.querySelector('[name="password"]').value;
    const confirm = document.querySelector('[name="password_confirm"]').value;
    if (password !== confirm) {
        e.preventDefault();
        alert('Пароли не совпадают!');
    }
});
</script>
</body>
</html>