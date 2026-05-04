# Структура проекта Учеба24

## Файловая структура

```
frontend/
├── index.php                 # Главная страница (лендинг)
├── login.php                 # Страница входа
├── register.php              # Регистрация студентов
├── auth.php                  # Обработка входа
├── logout.php                # Выход из системы
├── dashboard.php             # Ролевая главная страница
├── profile.php               # Профиль пользователя
├── settings.php              # Настройки аккаунта
├── schedule.php              # Общее расписание
├── grades.php                # Общая успеваемость
├── distance-learning.php     # Дистанционное обучение
├── marketplace.php           # Библиотека работ
├── admin-panel.php           # Перенаправление на админку
├── STRUCTURE.md              # Документация структуры
├── NAVIGATION.md             # Документация навигации
│
├── protected/                # Защищённые файлы
│   └── auth_guard.php        # Проверка авторизации
│
├── config/                   # Конфигурация
│   └── db.php                # Подключение к БД
│
├── teacher/                  # Кабинет преподавателя
│   ├── index.php             # Редирект на teacher.php
│   ├── teacher.php           # Главная преподавателя
│   ├── teacher-schedule.php  # Расписание
│   ├── teacher-journal.php   # Журнал пар
│   ├── teacher-grades.php    # Выставление оценок
│   ├── teacher-assignments.php # Работы студентов
│   ├── teacher-groups.php    # Кураторские группы
│   └── teacher-notifications.php # Уведомления
│
├── student/                  # Кабинет студента
│   ├── index.php             # Редирект на student-dashboard.php
│   ├── student-dashboard.php # Главная студента
│   ├── student-schedule.php  # Расписание
│   ├── student-grades.php    # Оценки
│   ├── student-assignments.php # Мои работы
│   ├── student-library.php   # Библиотека
│   └── student-notifications.php # Уведомления
│
└── admin/                    # Панель администратора
    ├── index.php             # Редирект на admin-dashboard.php
    ├── admin-dashboard.php   # Главная администратора
    ├── admin-users.php       # Управление пользователями
    ├── admin-schedule.php    # Управление расписанием
    ├── admin-groups.php      # Управление группами
    ├── admin-library.php     # Модерация библиотеки
    ├── admin-notifications.php # Рассылка
    └── admin-settings.php    # Настройки системы
```

## Ролевая маршрутизация

### После входа (auth.php):

| Роль | Перенаправление |
|------|-----------------|
| admin | admin/admin-dashboard.php |
| teacher | teacher/teacher.php |
| student | student/student-dashboard.php |

### Через dashboard.php:

| Роль | Перенаправление |
|------|-----------------|
| admin | admin/admin-dashboard.php |
| teacher | teacher/teacher.php |
| student | student/student-dashboard.php |

## Путь к ресурсам

| Файл | Из корня | Из подпапок |
|------|----------|-------------|
| logout.php | `logout.php` | `../logout.php` |
| auth_guard.php | `protected/auth_guard.php` | `../protected/auth_guard.php` |
| dashboard.php | `dashboard.php` | `../dashboard.php` |
| db.php | `config/db.php` | `../config/db.php` |
| login.php | `login.php` | `../login.php` |

## Защита страниц

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

## Регистрация

- **Студенты**: через register.php (проверка по базе данных по ФИО + группе + курсу)
- **Преподаватели**: только через админ-панель или напрямую в БД
- **Администраторы**: только напрямую в БД