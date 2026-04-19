<?php
session_start();
require_once 'protected/auth_guard.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudApp - Дистанционное обучение</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>💻</text></svg>">
</head>
<body>
    <header class="header-main">
            <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <i class="bi bi-journal-bookmark-fill"></i> StudApp
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-door me-1"></i>Главная</a></li>
                    <li class="nav-item"><a class="nav-link" href="schedule.php"><i class="bi bi-calendar-event me-1"></i>Расписание</a></li>
                    <li class="nav-item"><a class="nav-link" href="distance-learning.php"><i class="bi bi-laptop me-1"></i>Дистанционное обучение</a></li>
                    <li class="nav-item"><a class="nav-link" href="marketplace.php"><i class="bi bi-shop me-1"></i>Библиотека работ</a></li>
                    <li class="nav-item"><a class="nav-link" href="grades.php"><i class="bi bi-graph-up me-1"></i>Успеваемость</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm position-relative" data-bs-toggle="dropdown">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;">
                            <h6 class="dropdown-header">Уведомления</h6>
                            <div class="notification-item">
                                <div class="d-flex align-items-start mb-2">
                                    <i class="bi bi-exclamation-circle text-warning me-2"></i>
                                    <div><small class="d-block fw-bold">Через 30 минут</small><small class="text-muted">Математический анализ в Ауд. 201</small></div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item text-center small">Показать все</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i> Иван Иванов
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Студент, 3 курс, ИТ-321</h6></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Профиль</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Настройки</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-right me-2"></i>Выйти</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    </header> 

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1 class="display-6 fw-bold"><i class="bi bi-laptop me-2 text-primary"></i>Дистанционное обучение</h1>
                <p class="text-muted">Онлайн-курсы, лекции и материалы для самостоятельного изучения</p>
            </div>

            <!-- Мои курсы -->
            <div class="row mb-5">
                <div class="col-12 mb-4">
                    <h3>Мои курсы</h3>
                </div>
                <!-- Курс 1 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="course-card card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-dark">Программирование</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Основы Python, лекции и задания</p>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" style="width:75%">75%</div>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span>Прогресс: 75%</span>
                                <span>Следующий урок: завтра</span>
                            </div>
                            <button class="btn btn-primary w-100 mt-3">
                                <i class="bi bi-play-circle me-2"></i>Продолжить
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Курс 2 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="course-card card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 text-dark">Математический анализ</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Дифференциальное исчисление</p>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" style="width:100%">100%</div>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span>Прогресс: 100%</span>
                                <span>Оценка: 5</span>
                            </div>
                            <button class="btn btn-outline-success w-100 mt-3">
                                <i class="bi bi-eye me-2"></i>Посмотреть
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Курс 3 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="course-card card h-100">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0 text-dark">Английский язык</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Деловой английский</p>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" style="width:0%">0%</div>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span>Начало: 1 декабря</span>
                            </div>
                            <button class="btn btn-outline-warning w-100 mt-3" disabled>
                                <i class="bi bi-clock me-2"></i>Ожидает
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Онлайн-занятия -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 text-dark"><i class="bi bi-camera-video me-2"></i>Предстоящие онлайн-занятия</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr><th>Время</th><th>Предмет</th><th>Преподаватель</th><th>Платформа</th><th>Действия</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>15:00</strong> (сегодня)</td>
                                            <td>Программирование</td>
                                            <td>Иванов П.С.</td>
                                            <td>Zoom</td>
                                            <td><button class="btn btn-sm btn-primary"><i class="bi bi-link-45deg me-1"></i>Подключиться</button></td>
                                        </tr>
                                        <tr>
                                            <td><strong>10:00</strong> (завтра)</td>
                                            <td>Физика</td>
                                            <td>Петрова А.В.</td>
                                            <td>Teams</td>
                                            <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-calendar me-1"></i>Напомнить</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Материалы для скачивания -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 text-dark"><i class="bi bi-download me-2"></i>Материалы для скачивания</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div><i class="bi bi-file-pdf text-danger me-3"></i>Лекция 5. Основы ООП в Python <small class="text-muted">(PDF, 2.4 МБ)</small></div>
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> Скачать</button>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div><i class="bi bi-file-zip text-warning me-3"></i>Практические задания (архив) <small class="text-muted">(ZIP, 15.2 МБ)</small></div>
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> Скачать</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer-main">
        <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h5><i class="bi bi-journal-bookmark-fill me-2"></i>StudApp</h5>
                <p class="small">Единая платформа для помощи студентам. Организация учебного процесса, отслеживание успеваемости и участие в мероприятиях.</p>
            </div>
            <div class="col-md-3 mb-4">
                <h6>Поддержка</h6>
                <ul class="footer-links">
                    <li><a href="#"><i class="bi bi-question-circle me-1"></i>Помощь</a></li>
                    <li><a href="#"><i class="bi bi-chat-dots me-1"></i>Обратная связь</a></li>
                    <li><a href="#"><i class="bi bi-telephone me-1"></i>Контакты</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h6>Документация</h6>
                <ul class="footer-links">
                    <li><a href="#"><i class="bi bi-file-text me-1"></i>Руководство</a></li>
                    <li><a href="#"><i class="bi bi-shield-check me-1"></i>Политика</a></li>
                    <li><a href="#"><i class="bi bi-journal-code me-1"></i>Условия</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>© 2024 StudApp. Все права защищены. <span class="ms-md-3">Разработано с <i class="bi bi-heart-fill text-danger"></i> для студентов</span></p>
        </div>
    </div>
    </footer> <!-- подвал как в index.html -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>

</body>
</html>