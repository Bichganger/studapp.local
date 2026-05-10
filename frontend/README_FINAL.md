# Учеба24 - Образовательная платформа

## Структура проекта

```
frontend/
├── index.php              # Главная страница
├── dashboard.php          # Панель входа
├── auth.php               # Обработка входа
├── logout.php             # Выход
├── register_new.php       # Регистрация
├── README.md              # Документация
│
├── admin/                 # Администратор (7 страниц)
│   ├── dashboard.php
│   ├── users.php
│   ├── groups.php
│   ├── schedule.php
│   ├── library.php
│   ├── notifications.php
│   └── settings.php
│
├── teacher/               # Преподаватель (6 страниц)
│   ├── panel.php
│   ├── journal.php
│   ├── grades.php
│   ├── assignments.php
│   ├── groups.php
│   └── notifications.php
│
├── student/               # Студент (6 страниц)
│   ├── panel.php
│   ├── schedule.php
│   ├── grades.php
│   ├── assignments.php
│   ├── library.php
│   └── notifications.php
│
├── assets/                # Ресурсы
│   ├── css/
│   │   ├── student-style.css
│   │   └── teacher-style.css
│   └── js/
│       └── sync.js        # Синхронизация
│
├── config/                # База данных
│   └── db.php
│
└── protected/             # Авторизация
    └── auth_guard.php
```

## Запуск

1. Откройте `index.php`
2. Войдите через `auth.php`
3. Админ → `admin/dashboard.php`
4. Преподаватель → `teacher/panel.php`
5. Студент → `student/panel.php`

## Синхронизация

Все данные через `Sync` API в `assets/js/sync.js`