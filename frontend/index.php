<?php
session_start();

// Если уже вошли - перенаправляем по роли
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            exit;
        case 'teacher':
            header("Location: teacher/panel.php");
            exit;
        case 'student':
            header("Location: student/panel.php");
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учеба24 — Главная</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/neural-network.css">
    <style>
        .hero-section {
            min-height: 100vh;
            padding: 80px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .main-icon {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            border: 2px solid rgba(255,255,255,0.3);
        }
        .main-title {
            color: #fff !important;
            text-shadow: 0 4px 20px rgba(0,0,0,0.5);
            font-weight: 800;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px 20px;
            height: 100%;
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="role-student">
    <div class="neural-bg"></div>
    <canvas id="neuralNetworkCanvas"></canvas>
    
    <div class="container-fluid hero-section">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Заголовок -->
                <div class="text-center mb-5">
                    <div class="main-icon">
                        <i class="bi bi-journal-code" style="font-size: 4rem; color: #fff;"></i>
                    </div>
                    <h1 class="main-title display-3 mb-3">Учеба24</h1>
                    <p class="text-white-50 lead fs-4" style="max-width: 600px; margin: 0 auto;">
                        Интеллектуальная платформа для современного образования
                    </p>
                </div>

                <!-- Кнопки -->
                <div class="row justify-content-center mb-5">
                    <div class="col-auto">
                        <div class="d-flex gap-3 flex-wrap justify-content-center">
                            <a href="dashboard.php" class="btn neural-btn btn-lg px-5 py-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                            </a>
                            <a href="register_new.php" class="btn neural-btn btn-lg px-5 py-3" 
                               style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="bi bi-person-plus-fill me-2"></i>Регистрация
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3 карточки преимуществ -->
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card text-center">
                            <div class="mb-3">
                                <i class="bi bi-shield-check" style="font-size: 3rem; color: #667eea;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Безопасность</h5>
                            <p class="text-muted mb-0 small">Шифрование паролей и защита данных</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card text-center">
                            <div class="mb-3">
                                <i class="bi bi-arrow-repeat" style="font-size: 3rem; color: #667eea;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Синхронизация</h5>
                            <p class="text-muted mb-0 small">Все данные обновляются в реальном времени</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card text-center">
                            <div class="mb-3">
                                <i class="bi bi-universal-access" style="font-size: 3rem; color: #667eea;"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Доступность</h5>
                            <p class="text-muted mb-0 small">Поддержка людей с ОВЗ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/neural-network.js"></script>
</body>
</html>
