<?php
session_start();
require_once 'protected/auth_guard.php';

$fullName = htmlspecialchars($_SESSION['full_name']);
$username = htmlspecialchars($_SESSION['username']);
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Настройки — StudApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
<div class="container mt-5">
    <h2><i class="bi bi-gear"></i> Настройки аккаунта</h2>

    <div class="card mt-4">
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label">ФИО</label>
                    <input type="text" class="form-control" value="<?= $fullName ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Логин</label>
                    <input type="text" class="form-control" value="<?= $username ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Роль</label>
                    <input type="text" class="form-control" 
                           value="<?= 
                               $role === 'student' ? 'Студент' :
                               ($role === 'teacher' ? 'Преподаватель' : 'Администратор') 
                           ?>" 
                           readonly>
                </div>
                <button type="submit" class="btn btn-primary" disabled>Сохранить (пример)</button>
            </form>
        </div>
    </div>

    <a href="dashboard.php" class="btn btn-link mt-3"><i class="bi bi-arrow-left"></i> Назад</a>
</div>
</body>
</html>