<?php
session_start();
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin': header('Location: admin/dashboard.php'); exit;
        case 'teacher': header('Location: teacher/panel.php'); exit;
        case 'student': header('Location: student/panel.php'); exit;
    }
}
$pageTitle = 'Главная';
require_once 'includes/header.php';
$user = getCurrentUser();
?>
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <div class="hero-badge"><i class="bi bi-compass-fill me-2"></i>Студенческий компас Калининграда</div>
                    <h1 class="hero-title">Учеба24</h1>
                    <p class="hero-subtitle">Всё, что нужно студенту — в одном месте. Библиотека работ, карта корпусов, отзывы о преподавателях и советы от выпускников.</p>
                    <div class="hero-quote">
                        <i class="bi bi-chat-quote me-2"></i>
                        <strong>«Этот сайт сэкономит вам месяцы учёбы!»</strong> — выпускник 2026
                    </div>
                    <div class="hero-buttons justify-content-center d-flex">
                        <?php if ($user): ?>
                        <a href="/dashboard.php" class="btn btn-accent btn-lg px-4"><i class="bi bi-speedometer2 me-2"></i>В личный кабинет</a>
                        <?php else: ?>
                        <a href="/dashboard.php" class="btn btn-accent btn-lg px-4"><i class="bi bi-box-arrow-in-right me-2"></i>Войти</a>
                        <a href="/register_new.php" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-person-plus me-2"></i>Регистрация</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-journal-bookmark"></i></div>
                            <h5 class="feature-title">Библиотека работ</h5>
                            <p class="feature-text">Тысячи студенческих работ: курсовые, лабы, рефераты. Скачивай и учишься!</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-geo-alt"></i></div>
                            <h5 class="feature-title">Интерактивная карта</h5>
                            <p class="feature-text">Корпуса, столовые, транспорт — найди всё за 30 секунд.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-people"></i></div>
                            <h5 class="feature-title">Преподаватели</h5>
                            <p class="feature-text">Честные отзывы от выпускников. Кто добрый, а кто — нет.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-lightbulb"></i></div>
                            <h5 class="feature-title">Советы выпускников</h5>
                            <p class="feature-text">Лайфхаки по сессии, автоматам и выживанию в колледже.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Статистика</span>
            <h2 class="section-title">Платформа для студентов</h2>
            <p class="section-subtitle">Числа говорят сами за себя</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3"><div class="stat-card"><div class="stat-number">1500+</div><div class="stat-label">Студентов</div></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number">3</div><div class="stat-label">Корпуса</div></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number">200+</div><div class="stat-label">Отзывов</div></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number">500+</div><div class="stat-label">Работ в библиотеке</div></div></div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>

