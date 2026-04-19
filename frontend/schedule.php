<?php
session_start();
require_once 'protected/auth_guard.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudApp - Расписание</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Основные стили -->
    <link rel="stylesheet" href="css/main.css">
    <!-- Фавикон -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📅</text></svg>">
</head>
<body>
    <!-- Шапка -->
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
                        <!-- Уведомления -->
                        <div class="dropdown">
                            <button class="btn btn-outline-light btn-sm position-relative" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">2</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;">
                                <h6 class="dropdown-header">Уведомления</h6>
                                <div class="notification-item">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="bi bi-exclamation-circle text-warning me-2"></i>
                                        <div>
                                            <small class="d-block fw-bold">Через 15 минут</small>
                                            <small class="text-muted">Программирование - Комп. класс 3</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="bi bi-info-circle text-info me-2"></i>
                                        <div>
                                            <small class="d-block fw-bold">Изменение расписания</small>
                                            <small class="text-muted">Физика перенесена на 14:00</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item text-center small">Показать все</a>
                            </div>
                        </div>
                        <!-- Профиль -->
                        <div class="dropdown">
                            <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
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

    <!-- Основной контент -->
    <main class="main-content">
        <div class="container">
            <!-- Заголовок -->
            <div class="page-header">
                <h1 class="display-6 fw-bold"><i class="bi bi-calendar-event me-2 text-primary"></i>Расписание занятий</h1>
                <p class="text-muted">Группа ИТ-321, 3 курс. Семестр: весна 2024</p>
            </div>

            <!-- Навигация по неделям -->
            <div class="week-nav card mb-4 p-3">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <span class="current-week fw-semibold"><i class="bi bi-calendar-week me-2"></i>18 - 24 марта 2024</span>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                            <button class="btn btn-outline-primary filter-btn active" data-week="current">Текущая неделя</button>
                            <button class="btn btn-outline-primary filter-btn" data-week="next">Следующая неделя</button>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-filter me-1"></i>Фильтры
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-filter="all">Все предметы</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="lecture">Только лекции</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="practice">Только практики</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" data-filter="today">Сегодня</a></li>
                                    <li><a class="dropdown-item" href="#" data-filter="tomorrow">Завтра</a></li>
                                </ul>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                                <i class="bi bi-plus-circle me-1"></i>Добавить пару
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Расписание по дням -->
            <div class="row">
                <!-- Понедельник -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="day-card active" data-day="monday">
                        <div class="day-header today">
                            <div>
                                <h5 class="mb-0">Понедельник</h5>
                                <small>18 марта</small>
                            </div>
                            <span class="badge bg-secondary">5 пар</span>
                        </div>
                        <div class="day-body">
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="lesson-time fw-semibold">09:00 - 10:30</span>
                                    <span class="lesson-type lecture">Лекция</span>
                                </div>
                                <h6 class="mb-1">Математический анализ</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>Ауд. 201 • Лесная М.А.</p>
                                <small class="text-success time-diff">Через 25 мин</small>
                            </div>
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="lesson-time fw-semibold">10:45 - 12:15</span>
                                    <span class="lesson-type practice">Практика</span>
                                </div>
                                <h6 class="mb-1">Программирование</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>Комп. класс 3 • Петров И.С.</p>
                                <small class="text-muted time-diff">2 ч 10 мин</small>
                            </div>
                            <div class="lesson-item p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="lesson-time fw-semibold">13:00 - 14:30</span>
                                    <span class="lesson-type seminar">Семинар</span>
                                </div>
                                <h6 class="mb-1">Английский язык</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>Ауд. 105 • Иванова О.П.</p>
                                <small class="text-muted time-diff">4 ч 25 мин</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Вторник -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="day-card" data-day="tuesday">
                        <div class="day-header">
                            <div>
                                <h5 class="mb-0">Вторник</h5>
                                <small>19 марта</small>
                            </div>
                            <span class="badge bg-secondary">4 пары</span>
                        </div>
                        <div class="day-body">
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="lesson-time fw-semibold">09:00 - 10:30</span>
                                    <span class="lesson-type lecture">Лекция</span>
                                </div>
                                <h6 class="mb-1">Физика</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>Ауд. 301 • Сидоров А.В.</p>
                            </div>
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="lesson-time fw-semibold">10:45 - 12:15</span>
                                    <span class="lesson-type lab">Лабораторная</span>
                                </div>
                                <h6 class="mb-1">Физика</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>Лаб. 204 • Сидоров А.В.</p>
                            </div>
                            <div class="lesson-item p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="lesson-time fw-semibold">14:00 - 15:30</span>
                                    <span class="lesson-type practice">Практика</span>
                                </div>
                                <h6 class="mb-1">Базы данных</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>Комп. класс 2 • Козлов Д.М.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Среда (добавьте по аналогии) -->
                <!-- Четверг, Пятница, Суббота – для экономии места опущены, но в реальном проекте нужно добавить все дни недели. -->
            </div>
        </div>
    </main>

    <!-- Подвал -->
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
    </footer>

    <!-- Модальное окно добавления пары -->
    <div class="modal fade" id="addLessonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Добавить пару</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addLessonForm">
                        <div class="mb-3">
                            <label class="form-label">Название предмета *</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">День *</label>
                                <select class="form-select" name="day" required>
                                    <option value="">Выберите</option>
                                    <option value="monday">Понедельник</option>
                                    <option value="tuesday">Вторник</option>
                                    <option value="wednesday">Среда</option>
                                    <option value="thursday">Четверг</option>
                                    <option value="friday">Пятница</option>
                                    <option value="saturday">Суббота</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Тип *</label>
                                <select class="form-select" name="type" required>
                                    <option value="lecture">Лекция</option>
                                    <option value="practice">Практика</option>
                                    <option value="lab">Лабораторная</option>
                                    <option value="seminar">Семинар</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Время начала *</label>
                                <input type="time" class="form-control" name="time" value="09:00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Аудитория *</label>
                                <input type="text" class="form-control" name="room" placeholder="Ауд. 201" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Преподаватель</label>
                            <input type="text" class="form-control" name="teacher" placeholder="ФИО">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Продолжительность</label>
                            <select class="form-select" name="duration">
                                <option value="1.5">1.5 часа (1 пара)</option>
                                <option value="3">3 часа (2 пары)</option>
                                <option value="4.5">4.5 часа (3 пары)</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" form="addLessonForm" class="btn btn-primary">Добавить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Скрипты -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/schedule.js"></script>
</body>
</html>