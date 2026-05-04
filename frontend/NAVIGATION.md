# Навигация проекта Учеба24

## Общие страницы (доступны всем авторизованным пользователям)

| Файл | Описание | Ссылки на |
|------|----------|-----------|
| `index.php` | Главная страница (лендинг) | `login.php`, `register.php` |
| `login.php` | Страница входа | `auth.php`, `register.php` |
| `register.php` | Регистрация | `auth.php`, `login.php` |
| `dashboard.php` | Кабинет пользователя (ролевой) | Зависит от роли |
| `profile.php` | Профиль пользователя | `settings.php`, ролевой кабинет |
| `settings.php` | Настройки аккаунта | `dashboard.php` |
| `logout.php` | Выход из системы | `login.php` |
| `schedule.php` | Общее расписание | `dashboard.php`, `distance-learning.php`, `grades.php` |
| `grades.php` | Общая успеваемость | `dashboard.php`, `schedule.php`, `distance-learning.php` |
| `distance-learning.php` | Дистанционное обучение | `dashboard.php`, `schedule.php`, `grades.php` |
| `marketplace.php` | Библиотека работ | `dashboard.php` |
| `admin-panel.php` | Перенаправление на админку | `admin/admin-dashboard.php` |

## Страницы преподавателя (`frontend/teacher/`)

| Файл | Описание | Навигация |
|------|----------|-----------|
| `index.php` | Редирект на `teacher.php` | - |
| `teacher.php` | Главная преподавателя | `teacher-schedule.php`, `teacher-grades.php`, `teacher-assignments.php`, `teacher-groups.php`, `teacher-notifications.php`, `../logout.php` |
| `teacher-schedule.php` | Расписание | `teacher.php`, другие разделы teacher/, `../logout.php` |
| `teacher-grades.php` | Выставление оценок | `teacher.php`, другие разделы teacher/, `../logout.php` |
| `teacher-assignments.php` | Работы студентов | `teacher.php`, другие разделы teacher/, `../logout.php` |
| `teacher-groups.php` | Мои группы | `teacher.php`, другие разделы teacher/, `../logout.php` |
| `teacher-notifications.php` | Уведомления | `teacher.php`, другие разделы teacher/, `../logout.php` |

## Страницы студента (`frontend/student/`)

| Файл | Описание | Навигация |
|------|----------|-----------|
| `index.php` | Редирект на `student-dashboard.php` | - |
| `student-dashboard.php` | Главная студента | `student-schedule.php`, `student-grades.php`, `student-assignments.php`, `student-library.php`, `student-notifications.php`, `../logout.php` |
| `student-schedule.php` | Расписание | `student-dashboard.php`, другие разделы student/, `../logout.php` |
| `student-grades.php` | Оценки | `student-dashboard.php`, другие разделы student/, `../logout.php` |
| `student-assignments.php` | Мои работы | `student-dashboard.php`, другие разделы student/, `../logout.php` |
| `student-library.php` | Библиотека | `student-dashboard.php`, другие разделы student/, `../logout.php` |
| `student-notifications.php` | Уведомления | `student-dashboard.php`, другие разделы student/, `../logout.php` |

## Страницы администратора (`frontend/admin/`)

| Файл | Описание | Навигация |
|------|----------|-----------|
| `index.php` | Редирект на `admin-dashboard.php` | - |
| `admin-dashboard.php` | Главная администратора | `admin-users.php`, `admin-schedule.php`, `admin-groups.php`, `admin-library.php`, `admin-notifications.php`, `admin-settings.php`, `../logout.php` |
| `admin-users.php` | Управление пользователями | `admin-dashboard.php`, другие разделы admin/, `../logout.php` |
| `admin-schedule.php` | Управление расписанием | `admin-dashboard.php`, другие разделы admin/, `../logout.php` |
| `admin-groups.php` | Управление группами | `admin-dashboard.php`, другие разделы admin/, `../logout.php` |
| `admin-library.php` | Модерация библиотеки | `admin-dashboard.php`, другие разделы admin/, `../logout.php` |
| `admin-notifications.php` | Рассылка | `admin-dashboard.php`, другие разделы admin/, `../logout.php` |
| `admin-settings.php` | Настройки | `admin-dashboard.php`, другие разделы admin/, `../logout.php` |

## Ролевая маршрутизация в dashboard.php

### Преподаватель (`role === 'teacher'`)
- `teacher/teacher.php` — Кабинет преподавателя

### Студент (`role === 'student'`)
- `student/student-dashboard.php` — Кабинет студента
- `student/student-schedule.php` — Расписание
- `student/student-grades.php` — Успеваемость

### Администратор (`role === 'admin'`)
- `admin/admin-dashboard.php` — Админ-панель
- `admin/admin-users.php` — Пользователи

## Путь к ресурсам

- `auth_guard.php`: `protected/auth_guard.php` (из корня), `../protected/auth_guard.php` (из подпапок)
- `logout.php`: `logout.php` (из корня), `../logout.php` (из подпапок)
- `dashboard.php`: `dashboard.php` (из корня), `../dashboard.php` (из подпапок)
- `db.php`: `config/db.php` (из корня), `../config/db.php` (из подпапок)

## Защита страниц

Все защищённые страницы начинаются с:
```php
<?php
session_start();
require_once 'protected/auth_guard.php'; // или ../protected/auth_guard.php

// Опционально: проверка роли
if ($_SESSION['role'] !== 'required_role') {
    header("Location: ../dashboard.php");
    exit;
}
?>
```
