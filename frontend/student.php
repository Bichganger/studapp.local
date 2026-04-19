<?php
session_start();
require_once 'protected/auth_guard.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>StudApp - Студент</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Боковое меню -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-primary text-white sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center py-3 border-bottom">
                        <h5 class="mb-0">Студент</h5>
                        <small>StudApp</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#dashboard">
                                <i class="bi bi-speedometer2 me-2"></i> Главная
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#courses">
                                <i class="bi bi-book me-2"></i> Мои курсы
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#grades">
                                <i class="bi bi-star me-2"></i> Мои оценки
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#schedule">
                                <i class="bi bi-calendar me-2"></i> Расписание
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tasks">
                                <i class="bi bi-journal-text me-2"></i> Задания
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="bi bi-box-arrow-left me-2"></i> Выйти
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Основной контент -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Хедер -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded-3 shadow-sm mb-4">
                    <div class="container-fluid">
                        <h1 class="h4 mb-0">Панель студента</h1>
                        <div class="navbar-nav">
                            <a class="nav-link" href="#">
                                <i class="bi bi-bell"></i> <span class="badge bg-danger">3</span>
                            </a>
                            <a class="nav-link" href="#">
                                <i class="bi bi-person-circle"></i> Студент
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Главная панель -->
                <section id="dashboard" class="mb-4">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted">Мои курсы</h6>
                                            <h4 class="card-title mb-0">5</h4>
                                        </div>
                                        <div class="text-primary">
                                            <i class="bi bi-book-fill" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted">Средний балл</h6>
                                            <h4 class="card-title mb-0">8.2</h4>
                                        </div>
                                        <div class="text-warning">
                                            <i class="bi bi-star-fill" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted">Последняя оценка</h6>
                                            <h4 class="card-title mb-0">9</h4>
                                        </div>
                                        <div class="text-success">
                                            <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted">Домашние задания</h6>
                                            <h4 class="card-title mb-0">7</h4>
                                        </div>
                                        <div class="text-info">
                                            <i class="bi bi-journal-text" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Мои курсы -->
                <section id="courses" class="d-none mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Мои курсы</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Веб-разработка</h6>
                                            <p class="card-text">Современные технологии фронтенда и бэкенда.</p>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Преподаватель: Иван Иванов</small>
                                                <button class="btn btn-sm btn-outline-primary">Открыть</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Базы данных</h6>
                                            <p class="card-text">SQL, NoSQL, проектирование и оптимизация.</p>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Преподаватель: Петр Петров</small>
                                                <button class="btn btn-sm btn-outline-primary">Открыть</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Программирование на Python</h6>
                                            <p class="card-text">Основы и продвинутые темы Python.</p>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Преподаватель: Мария Сидорова</small>
                                                <button class="btn btn-sm btn-outline-primary">Открыть</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Алгоритмы и структуры данных</h6>
                                            <p class="card-text">Фундаментальные алгоритмы и структуры.</p>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Преподаватель: Алексей Смирнов</small>
                                                <button class="btn btn-sm btn-outline-primary">Открыть</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Английский для IT-специалистов</h6>
                                            <p class="card-text">Специализированный английский язык.</p>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Преподаватель: Анна Кузнецова</small>
                                                <button class="btn btn-sm btn-outline-primary">Открыть</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Мои оценки -->
                <section id="grades" class="d-none mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Мои оценки</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Курс</th>
                                            <th>Оценка</th>
                                            <th>Дата</th>
                                            <th>Комментарий</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Веб-разработка</td>
                                            <td><span class="badge bg-success">9</span></td>
                                            <td>15.03.2024</td>
                                            <td>Отличная работа!</td>
                                        </tr>
                                        <tr>
                                            <td>Базы данных</td>
                                            <td><span class="badge bg-warning">7</span></td>
                                            <td>12.03.2024</td>
                                            <td>Хорошо, но можно лучше</td>
                                        </tr>
                                        <tr>
                                            <td>Программирование на Python</td>
                                            <td><span class="badge bg-success">10</span></td>
                                            <td>10.03.2024</td>
                                            <td>Идеально!</td>
                                        </tr>
                                        <tr>
                                            <td>Алгоритмы и структуры данных</td>
                                            <td><span class="badge bg-success">8</span></td>
                                            <td>08.03.2024</td>
                                            <td>Хорошо выполнено</td>
                                        </tr>
                                        <tr>
                                            <td>Английский для IT-специалистов</td>
                                            <td><span class="badge bg-success">9</span></td>
                                            <td>05.03.2024</td>
                                            <td>Отличное произношение</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>