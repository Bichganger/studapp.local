<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudApp - Успеваемость</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📊</text></svg>">
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
                    <li class="nav-item"><a class="nav-link" href="index.html"><i class="bi bi-house-door me-1"></i>Главная</a></li>
                    <li class="nav-item"><a class="nav-link" href="schedule.html"><i class="bi bi-calendar-event me-1"></i>Расписание</a></li>
                    <li class="nav-item"><a class="nav-link" href="distance-learning.html"><i class="bi bi-laptop me-1"></i>Дистанционное обучение</a></li>
                    <li class="nav-item"><a class="nav-link" href="marketplace.html"><i class="bi bi-shop me-1"></i>Библиотека работ</a></li>
                    <li class="nav-item"><a class="nav-link" href="grades.html"><i class="bi bi-graph-up me-1"></i>Успеваемость</a></li>
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
                            <li><a class="dropdown-item" href="profile.html"><i class="bi bi-person me-2"></i>Профиль</a></li>
                            <li><a class="dropdown-item" href="settings.html"><i class="bi bi-gear me-2"></i>Настройки</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="login.html"><i class="bi bi-box-arrow-right me-2"></i>Выйти</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    </header> <!-- шапка -->

    <main class="main-content">
        <div class="gradient-header p-4 mb-4 rounded-4 text-white">
    <h1 class="display-6 fw-bold"><i class="bi bi-graph-up me-2"></i>Успеваемость и оценки</h1>
    <p class="mb-0 ">Статистика, прогресс и рекомендации ИИ</p>
</div>

            <!-- Выбор семестра -->
            <div class="semester-selector card p-3 mb-4">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <button class="semester-btn btn btn-outline-primary active" data-semester="5">5 семестр (текущий)</button>
                    <button class="semester-btn btn btn-outline-primary" data-semester="4">4 семестр</button>
                    <button class="semester-btn btn btn-outline-primary" data-semester="3">3 семестр</button>
                    <button class="btn btn-outline-secondary ms-auto" id="exportGradesBtn"><i class="bi bi-download me-1"></i>Экспорт</button>
                </div>
            </div>

            <!-- Статистика -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stat-card text-center p-3 bg-white rounded-3 shadow-sm">
                        <div class="stat-value text-primary h2">4.6</div>
                        <div class="stat-label">Средний балл</div>
                        <div class="progress mt-2"><div class="progress-bar bg-primary" style="width:92%"></div></div>
                        <small class="text-muted">+0.2 за месяц</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card text-center p-3 bg-white rounded-3 shadow-sm">
                        <div class="stat-value text-success h2">14</div>
                        <div class="stat-label">Сдано предметов</div>
                        <small class="text-muted">из 16 в семестре</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card text-center p-3 bg-white rounded-3 shadow-sm">
                        <div class="stat-value text-warning h2">2</div>
                        <div class="stat-label">Под угрозой</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-card text-center p-3 bg-white rounded-3 shadow-sm">
                        <div class="stat-value text-info h2">85%</div>
                        <div class="stat-label">Посещаемость</div>
                    </div>
                </div>
            </div>

            <!-- Графики -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-dark"><i class="bi bi-bar-chart me-2"></i>Динамика по семестрам</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height:300px;">
                                <canvas id="gradesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 text-dark"><i class="bi bi-pie-chart me-2"></i>Распределение оценок</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height:200px;">
                                <canvas id="gradesDistributionChart"></canvas>
                            </div>
                            <div class="row text-center mt-3">
                                <div class="col-4"><span class="fw-bold text-success">8</span><br><small>Отлично</small></div>
                                <div class="col-4"><span class="fw-bold text-primary">4</span><br><small>Хорошо</small></div>
                                <div class="col-4"><span class="fw-bold text-warning">2</span><br><small>Удовл.</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Оценки по предметам -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-dark"><i class="bi bi-list-check me-2"></i>Оценки по предметам</h5>
                            <div>
                                <button class="btn btn-sm btn-light" id="sortByGrade"><i class="bi bi-sort-numeric-down me-1"></i>По оценке</button>
                                <button class="btn btn-sm btn-light" id="sortBySubject"><i class="bi bi-sort-alpha-down me-1"></i>По предмету</button>
                            </div>
                        </div>
                        <div class="card-body" id="gradesContainer">
                            <!-- Динамически загружаются из grades.js -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- История и рекомендации -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-secondary text-dark"><h5 class="mb-0 text-dark"><i class="bi bi-clock-history me-2"></i>История оценок</h5></div>
                        <div class="card-body">
                            <table class="table">
                                <thead><tr><th>Дата</th><th>Предмет</th><th>Тип работы</th><th>Оценка</th></tr></thead>
                                <tbody id="historyTableBody">
                                    <!-- данные из JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-warning text-white"><h5 class="mb-0 text-dark"><i class="bi bi-robot me-2"></i>Рекомендации ИИ</h5></div>
                        <div class="card-body">
                            <div class="prediction-card p-3 mb-3 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-graph-up-arrow me-2"></i>Прогноз: к концу семестра средний балл составит <strong>4.7</strong>
                            </div>
                            <div class="improvement-suggestion p-3 border-start border-4 border-success mb-2">
                                <strong>Физика требует внимания</strong><br><small>Последняя оценка 3. Рекомендуем уделить больше времени.</small>
                            </div>
                            <div class="improvement-suggestion p-3 border-start border-4 border-info">
                                <strong>Отличная динамика по программированию</strong><br><small>Продолжайте в том же духе!</small>
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
    </footer>

    <!-- Модальные окна -->
    <div class="modal fade" id="subjectDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Детали предмета</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body" id="subjectDetailsBody"></div>
                <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/grades.js"></script>

</body>
</html>