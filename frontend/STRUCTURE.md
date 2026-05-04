# Структура проекта Учёба.Онлайн

## Обзор структуры

Проект организован по принципу разделения по ролям пользователей:

```
frontend/
├── teacher/              # Страницы для преподавателей
│   ├── index.php         # Редирект на teacher.php
│   ├── teacher.php       # Главная страница преподавателя
│   ├── teacher-schedule.php      # Расписание
│   ├── teacher-grades.php        # Выставление оценок
│   ├── teacher-assignments.php   # Работы студентов
│   ├── teacher-groups.php        # Мои группы
│   └── teacher-notifications.php # Уведомления
│
├── student/              # Страницы для студентов
│   ├── index.php         # Редирект на student-dashboard.php
│   ├── student-dashboard.php     # Главная студента
│   ├── student-schedule.php      # Расписание
│   ├── student-grades.php        # Оценки
│   ├── student-assignments.php   # Мои работы
│   ├── student-library.php       # Библиотека
│   └── student-notifications.php # Уведомления
│
├── admin/                # Страницы для администраторов
│   ├── index.php         # Редирект на admin-dashboard.php
│   ├── admin-dashboard.php       # Главная администратора
│   ├── admin-users.php           # Управление пользователями
│   ├── admin-schedule.php        # Управление расписанием
│   ├── admin-groups.php          # Управление группами
│   ├── admin-library.php         # Модерация библиотеки
│   ├── admin-notifications.php   # Рассылка уведомлений
│   └── admin-settings.php        # Настройки системы
│
├── protected/            # Защита страниц
│   └── auth_guard.php    # Проверка авторизации
│
├── config/               # Конфигурация
│   └── db.php            # Подключение к БД
│
├── dashboard.php         # Главная страница (кабинет)
├── login.php             # Страница входа
├── logout.php            # Выход из системы
├── register.php          # Регистрация
├── profile.php           # Профиль пользователя
└── ...                   # Общие страницы
```

## Маршрутизация

### Преподаватель (teacher)
- `/teacher/` или `/teacher/index.php` → `teacher.php`
- `/teacher/teacher.php` → Главная преподавателя
- `/teacher/teacher-schedule.php` → Расписание
- `/teacher/teacher-grades.php` → Выставление оценок
- `/teacher/teacher-assignments.php` → Работы студентов
- `/teacher/teacher-groups.php` → Мои группы
- `/teacher/teacher-notifications.php` → Уведомления

### Студент (student)
- `/student/` или `/student/index.php` → `student-dashboard.php`
- `/student/student-dashboard.php` → Главная студента
- `/student/student-schedule.php` → Расписание
- `/student/student-grades.php` → Оценки
- `/student/student-assignments.php` → Мои работы
- `/student/student-library.php` → Библиотека
- `/student/student-notifications.php` → Уведомления

### Администратор (admin)
- `/admin/` или `/admin/index.php` → `admin-dashboard.php`
- `/admin/admin-dashboard.php` → Главная администратора
- `/admin/admin-users.php` → Управление пользователями
- `/admin/admin-schedule.php` → Управление расписанием
- `/admin/admin-groups.php` → Управление группами
- `/admin/admin-library.php` → Модерация библиотеки
- `/admin/admin-notifications.php` → Рассылка
- `/admin/admin-settings.php` → Настройки

## Стили

Все страницы для каждой роли имеют единый стиль:

### Преподаватель
- Цвет бокового меню: `#343a40` (тёмно-серый)
- Активный элемент: `#495057`

### Студент
- Цвет бокового меню: `#0d6efd` (синий Bootstrap primary)
- Активный элемент: `#0b5ed7`

### Администратор
- Цвет бокового меню: `#2c3e50` (тёмно-синий)
- Активный элемент: `#34495e`

## Авторизация

Все страницы используют `auth_guard.php` для проверки авторизации:
```php
require_once '../protected/auth_guard.php';

if ($_SESSION['role'] !== 'desired_role') {
    header("Location: ../dashboard.php");
    exit;
}
```

## Общие страницы

- `dashboard.php` - главная страница с ролевой навигацией
- `login.php` - страница входа
- `logout.php` - выход из системы
- `register.php` - регистрация
- `profile.php` - профиль пользователя
- `schedule.php` - общее расписание
- `grades.php` - общая успеваемость
- `distance-learning.php` - дистанционное обучение
- `marketplace.php` - библиотека работ
- `admin-panel.php` - перенаправление на admin/admin-dashboard.php

## Примечания

1. Все пути к ресурсам относительные от корня `frontend/`
2. Для доступа к `protected/auth_guard.php` из подпапок используется `../protected/auth_guard.php`
3. Для выхода используется `../logout.php`
4. Для возврата на dashboard используется `../dashboard.php`
