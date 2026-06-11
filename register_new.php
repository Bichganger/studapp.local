<?php
require_once 'config/db.php';
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
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="consentCheckbox" name="consent" required>
                                <label class="form-check-label" for="consentCheckbox">
                                    Я соглашаюсь на <a href="#" class="text-accent" data-bs-toggle="modal" data-bs-target="#consentModal">обработку персональных данных</a>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="role" value="student">
                        <button type="submit" class="btn btn-accent w-100" id="submitBtn" disabled><i class="bi bi-rocket-takeoff-fill me-2"></i>Начать обучение</button>
                    </form>
                    <p class="auth-link">Уже есть аккаунт? <a href="dashboard.php" class="text-accent">Войти</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal согласия на обработку персональных данных -->
<div class="modal fade" id="consentModal" tabindex="-1" aria-labelledby="consentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" id="consentModalLabel" style="color: var(--text-primary);"><i class="bi bi-shield-check me-2"></i>Согласие на обработку персональных данных</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body" style="color: var(--text-secondary); line-height: 1.7;">
                <p><strong>1. Общие положения</strong></p>
                <p>Настоящим я, являясь пользователем сайта «Учеба24», даю своё согласие на обработку моих персональных данных в соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ «О персональных данных».</p>
                
                <p><strong>2. Персональные данные</strong></p>
                <p>Под персональными данными понимаются данные, предоставляемые мной при регистрации и использовании сервиса:</p>
                <ul>
                    <li>ФИО (полное имя)</li>
                    <li>Номер группы и курс обучения</li>
                    <li>Логин (username)</li>
                    <li>Пароль (хранится в зашифрованном виде)</li>
                </ul>
                
                <p><strong>3. Цели обработки</strong></p>
                <p>Обработка персональных данных осуществляется в следующих целях:</p>
                <ul>
                    <li>Регистрация и авторизация пользователя в системе</li>
                    <li>Предоставление доступа к образовательным материалам и сервисам</li>
                    <li>Организация учебного процесса</li>
                    <li>Техническая поддержка и улучшение качества услуг</li>
                </ul>
                
                <p><strong>4. Действия с персональными данными</strong></p>
                <p>Согласие включает право на сбор, хранение, систематизацию, накопление, использование, передачу (в том числе трансграничную) в пределах необходимой инфраструктуры, обезличивание, блокирование и удаление персональных данных.</p>
                
                <p><strong>5. Срок действия</strong></p>
                <p>Согласие действует с момента его предоставления до момента его отзыва в письменной форме. После отзыва согласия обработка данных может продолжаться в случаях, предусмотренных законодательством РФ.</p>
                
                <p><strong>6. Безопасность</strong></p>
                <p>Мы принимаем все необходимые технические и организационные меры для защиты персональных данных от несанкционированного доступа, изменения или уничтожения.</p>
                
                <p><strong>7. Контакты</strong></p>
                <p>По вопросам, связанным с обработкой персональных данных, вы можете обратиться через форму обратной связи в личном кабинете.</p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-accent" data-bs-dismiss="modal">Понятно</button>
            </div>
        </div>
    </div>
</div>
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
    const consent = document.getElementById('consentCheckbox').checked;
    if (password !== confirm) { e.preventDefault(); alert('Пароли не совпадают!'); return false; }
    if (!consent) { e.preventDefault(); alert('Необходимо согласие на обработку персональных данных!'); return false; }
});

// Управление состоянием кнопки регистрации
const consentCheckbox = document.getElementById('consentCheckbox');
const submitBtn = document.getElementById('submitBtn');
consentCheckbox.addEventListener('change', function() {
    submitBtn.disabled = !this.checked;
});
</script>
<?php require_once 'includes/footer.php'; ?>

