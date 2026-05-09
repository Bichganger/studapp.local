<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация — Учеба24</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/neural-network.css">
</head>
<body class="role-student">
    <!-- Живой фон -->
    <div class="neural-bg"></div>
    <canvas id="neuralNetworkCanvas"></canvas>
    
    <!-- Карточка регистрации -->
    <div class="neural-form neural-fade-in">
        <div class="text-center mb-4">
            <div style="font-size: 4rem; animation: iconBounce 2s ease-in-out infinite;">
                <i class="bi bi-mortarboard-fill" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
            </div>
            <h2 class="neural-greeting" style="font-size: 2rem; margin-top: 10px;">Учеба24</h2>
            <p class="text-muted">Присоединяйся к платформе будущего</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger neural-alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success neural-alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <form action="reg_process.php" method="POST" id="regForm">
            <div class="mb-3">
                <label class="form-label fw-bold"><i class="bi bi-person-fill text-primary me-2"></i>ФИО</label>
                <input type="text" name="full_name" class="form-control neural-form-input" 
                       placeholder="Иванов Иван Иванович" required autocomplete="name">
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><i class="bi bi-people-fill text-primary me-2"></i>Группа</label>
                    <input type="text" name="group_name" class="form-control neural-form-input" 
                           placeholder="ПИ-201" required autocomplete="off">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><i class="bi bi-book-fill text-primary me-2"></i>Курс</label>
                    <select name="course" class="form-select neural-form-input" required>
                        <option value="" disabled selected>Выберите</option>
                        <option value="1">1 курс</option>
                        <option value="2">2 курс</option>
                        <option value="3">3 курс</option>
                        <option value="4">4 курс</option>
                    </select>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="mb-3">
                <label class="form-label fw-bold"><i class="bi bi-person-badge-fill text-primary me-2"></i>Логин</label>
                <input type="text" name="username" class="form-control neural-form-input" 
                       placeholder="ivanov_i" required autocomplete="username">
                <div class="form-text">Уникальный идентификатор для входа</div>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold"><i class="bi bi-key-fill text-primary me-2"></i>Пароль</label>
                <input type="password" name="password" class="form-control neural-form-input" 
                       id="password" required autocomplete="new-password" minlength="6">
                <div class="password-strength mt-2">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-danger" id="strengthBar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted" id="strengthText">Слабый пароль</small>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold"><i class="bi bi-shield-check text-primary me-2"></i>Повторите пароль</label>
                <input type="password" name="password_confirm" class="form-control neural-form-input" 
                       required autocomplete="new-password">
            </div>

            <input type="hidden" name="role" value="student">
            
            <button type="submit" class="btn neural-btn w-100 mb-3 neural-btn-success" id="submitBtn">
                <i class="bi bi-rocket-takeoff-fill me-2"></i>Начать обучение
            </button>
        </form>
        
        <div class="text-center">
            <small class="text-muted">Уже есть аккаунт? 
                <a href="login_new.php" class="text-decoration-none fw-bold" style="color: #667eea;">Войти</a>
            </small>
        </div>
        
        <div class="mt-4 p-3 rounded" style="background: rgba(102, 126, 234, 0.1);">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Регистрация только для студентов колледжа. Данные проверяются по базе.
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/neural-network.js"></script>
    <script>
        // Проверка сложности пароля
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
            
            if (strength < 40) {
                strengthBar.className = 'progress-bar bg-danger';
                strengthText.textContent = 'Слабый пароль';
            } else if (strength < 80) {
                strengthBar.className = 'progress-bar bg-warning';
                strengthText.textContent = 'Средний пароль';
            } else {
                strengthBar.className = 'progress-bar bg-success';
                strengthText.textContent = 'Надёжный пароль';
            }
        });
        
        // Проверка совпадения паролей
        document.getElementById('regForm').addEventListener('submit', function(e) {
            const password = document.querySelector('[name="password"]').value;
            const confirm = document.querySelector('[name="password_confirm"]').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('⚠️ Пароли не совпадают!');
                return false;
            }
            
            // Эффект частиц при отправке
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<span class="neural-loader d-inline-block me-2"></span>Обработка...';
        });
    </script>
</body>
</html>
