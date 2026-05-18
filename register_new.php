<?php
session_start();
$pageTitle = 'Регистрация';
require_once 'includes/header.php';
?>
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="auth-card">
                    <h3 class="auth-title"><i class="bi bi-mortarboard-fill me-2"></i>Регистрация</h3>
                    <p class="auth-subtitle">Присоединяйся к Учеба24</p>
                    <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($_GET['error']) ?></div><?php endif; ?>
                    <?php if (isset($_GET['success'])): ?><div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i><?= e($_GET['success']) ?></div><?php endif; ?>
                    <?php if (isset($_GET['pending'])): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-hourglass-split me-2"></i>Ваш аккаунт создан и ожидает одобрения администратора.
                        <br><small>После одобрения вы сможете войти в систему.</small>
                    </div>
                    <?php endif; ?>
                    <form action="reg_process.php" method="POST" id="regForm">
                        <div class="mb-3"><label class="form-label">ФИО</label><input type="text" name="full_name" class="form-control" placeholder="Иванов Иван Иванович" required></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Группа</label><input type="text" name="group_name" class="form-control" placeholder="ПИ-201" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Курс</label><select name="course" class="form-select" required><option value="" disabled selected>Выберите</option><option value="1">1 курс</option><option value="2">2 курс</option><option value="3">3 курс</option><option value="4">4 курс</option></select></div>
                        </div>
                        <hr class="my-4">
                        <div class="mb-3"><label class="form-label">Логин</label><input type="text" name="username" class="form-control" placeholder="ivanov_i" required></div>
                        <div class="mb-3"><label class="form-label">Пароль</label><input type="password" name="password" class="form-control" id="password" required minlength="6"><div class="progress mt-2" style="height:4px"><div class="progress-bar bg-danger" id="strengthBar" style="width:0%"></div></div><small class="text-muted" id="strengthText">Слабый пароль</small></div>
                        <div class="mb-4"><label class="form-label">Повторите пароль</label><input type="password" name="password_confirm" class="form-control" required></div>
                        <input type="hidden" name="role" value="student">
                        <button type="submit" class="btn btn-accent w-100"><i class="bi bi-rocket-takeoff-fill me-2"></i>Начать обучение</button>
                    </form>
                    <p class="auth-link">Уже есть аккаунт? <a href="/dashboard.php" class="text-accent">Войти</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
const passwordInput = document.getElementById('password');
const strengthBar = document.getElementById('strengthBar');
const strengthText = document.getElementById('strengthText');
passwordInput.addEventListener('input', function() {
    const password = this.value;
    let strength = 0;
    if (password.length >= 6) strength += 20;
    if (password.length >= 10) strength += 20;
    if (/[A-Z]/.test(password)) strength += 20;
    if (/[0-9]/.test(password)) strength += 20;
    if (/[^A-Za-z0-9]/.test(password)) strength += 20;
    strengthBar.style.width = strength + '%';
    if (strength < 40) { strengthBar.className = 'progress-bar bg-danger'; strengthText.textContent = 'Слабый пароль'; }
    else if (strength < 80) { strengthBar.className = 'progress-bar bg-warning'; strengthText.textContent = 'Средний пароль'; }
    else { strengthBar.className = 'progress-bar bg-success'; strengthText.textContent = 'Надёжный пароль'; }
});
document.getElementById('regForm').addEventListener('submit', function(e) {
    const password = document.querySelector('[name="password"]').value;
    const confirm = document.querySelector('[name="password_confirm"]').value;
    if (password !== confirm) { e.preventDefault(); alert('Пароли не совпадают!'); return false; }
});
</script>
<?php require_once 'includes/footer.php'; ?>

