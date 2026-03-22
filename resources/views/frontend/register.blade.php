<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudApp - Регистрация</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Основные стили -->
    <link rel="stylesheet" href="css/main.css">
    <!-- Фавикон -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📝</text></svg>">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .register-card {
            max-width: 500px;
            width: 100%;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .register-card .card-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-align: center;
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.5rem;
        }
        .register-card .card-body {
            padding: 2rem;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            color: white;
            background: linear-gradient(135deg, #5a67d8, #6b46a1);
        }
        .form-check-label a {
            color: #667eea;
            text-decoration: none;
        }
        .form-check-label a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card register-card">
        <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Регистрация в StudApp</h4>
            <p class="mb-0 mt-2 small opacity-75">Присоединяйся к учебному сообществу</p>
        </div>
        <div class="card-body">
            <form id="registerForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Имя</label>
                        <input type="text" class="form-control" id="firstName" name="first_name" placeholder="Иван" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Фамилия</label>
                        <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Иванов" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Группа</label>
                    <select class="form-select" id="group" name="group" required>
                        <option value="" disabled selected>Выберите группу</option>
                        <option>ИТ-101</option>
                        <option>ИТ-102</option>
                        <option>ИТ-201</option>
                        <option>ИТ-202</option>
                        <option>ЭК-101</option>
                        <option>ЭК-102</option>
                        <option>МТ-101</option>
                        <option>МТ-102</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="student@example.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Минимум 8 символов" required>
                    <div class="form-text">Минимум 8 символов, включая буквы и цифры</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Подтверждение пароля</label>
                    <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" placeholder="Повторите пароль" required>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="agreeTerms" name="agreeTerms" required>
                    <label class="form-check-label" for="agreeTerms">
                        Я согласен с <a href="#" target="_blank">условиями использования</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-register w-100 mb-3">Зарегистрироваться</button>

                <div class="text-center">
                    <span class="text-muted">Уже есть аккаунт?</span>
                    <a href="login.html" class="text-decoration-none ms-1">Войти</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Скрипты -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>