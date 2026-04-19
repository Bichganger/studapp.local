<?php
session_start();
require_once 'protected/auth_guard.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudApp - Библиотека работ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📚</text></svg>">
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
    </header> <!-- шапка с активным пунктом "Библиотека работ" -->

    <main class="main-content">
        <div class="container">
            <div class="gradient-header p-5 rounded-4 mb-5 text-white">
                <h1 class="display-5 fw-bold">📚 Библиотека студенческих работ</h1>
                <p class="lead">Бесплатный обмен учебными материалами внутри колледжа.</p>
                <div class="mt-3">
                    <span class="badge bg-secondary">5 пар</span>
                    <span class="badge bg-secondary">5 пар</span>
                    <span class="badge bg-secondary">5 пар</span>
                </div>
            </div>

            <div class="row">
                <!-- Фильтры -->
                <div class="col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><i class="bi bi-funnel me-2"></i>Фильтры</h5>
                            <div class="mb-3">
                                <label class="form-label">Поиск</label>
                                <input type="text" class="form-control" placeholder="Название, предмет...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Предметы</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filter1" checked>
                                    <label class="form-check-label" for="filter1">Программирование</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filter2">
                                    <label class="form-check-label" for="filter2">Математика</label>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary w-100">Сбросить</button>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-body">
                            <h6 class="text-center">Статистика</h6>
                            <div class="d-flex justify-content-between"><span>Всего работ:</span> <strong class="text-primary">347</strong></div>
                            <div class="d-flex justify-content-between"><span>Авторов:</span> <strong class="text-success">189</strong></div>
                            <div class="d-flex justify-content-between"><span>Скачиваний:</span> <strong class="text-warning">2,458</strong></div>
                        </div>
                    </div>
                </div>

                <!-- Список работ -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">🏆 Рекомендуемые работы</h4>
                            <p class="text-muted">Лучшие работы по версии студентов</p>
                        </div>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addWorkModal">
                            <i class="bi bi-cloud-upload me-2"></i>Поделиться
                        </button>
                    </div>

                    <div class="row g-4">
                        <!-- Работа 1 -->
                        <div class="col-md-6 col-lg-4">
                            <div class="work-card">
                                <div class="work-image code"><i class="bi bi-code-slash"></i></div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6 class="card-title">Интернет-магазин на Django</h6>
                                        <span class="badge bg-primary">Программирование</span>
                                    </div>
                                    <p class="small text-muted">Полнофункциональный магазин с корзиной, оплатой.</p>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="https://i.pravatar.cc/150?img=1" class="author-avatar me-2" width="32" height="32">
                                        <div>
                                            <small class="d-block">Алексей Петров</small>
                                            <small class="text-warning">★★★★☆</small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-secondary">3 курс</span>
                                        <button class="btn btn-sm btn-success download-work">Скачать</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Работа 2 (аналогично) -->
                        <!-- ... -->
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

    <!-- Модальное окно добавления работы -->
    <div class="modal fade" id="addWorkModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Поделиться работой</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3"><label class="form-label">Название</label><input type="text" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Предмет</label><select class="form-select"><option>Программирование</option></select></div>
                        <div class="mb-3"><label class="form-label">Описание</label><textarea class="form-control" rows="3"></textarea></div>
                        <div class="mb-3"><label class="form-label">Файл</label><input type="file" class="form-control"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button class="btn btn-success">Опубликовать</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/marketplace.js"></script>
</body>
</html>