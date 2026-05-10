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
├── assets/
│   ├── css/
│   │   ├── student-style.css
│   │   ├── teacher-style.css
│   │   └── accessibility.css  ← ДОСТУПНОСТЬ
│   └── js/
│       ├── sync.js        # Синхронизация
│       └── accessibility.js ← ДОСТУПНОСТЬ
│
├── config/
└── protected/
```

## Панель доступности

Система для людей с нарушениями зрения и слуха.

### Как активировать:
Панель **всегда доступна** — плавающая кнопка ♿ в правом нижнем углу экрана.

### Для слабовидящих:
- **Увеличение текста**: 125%, 150%, 200%
- **Высокая контрастность**: чёрно-белый режим
- **Увеличенные кнопки**: большие элементы управления
- **Упрощённый интерфейс**: только главное
- **Чтение вслух**: Text-to-Speech

### Для слабослышащих:
- **Визуальные уведомления**: вместо звуков — яркие плашки
- **Индикация событий**: заметные предупреждения

### Особенности:
- Все настройки сохраняются в браузере
- Работает на всех страницах
- Не требует перезагрузки

## Запуск

1. Откройте `index.php`
2. Войдите через `auth.php`
3. Админ → `admin/dashboard.php`
4. Преподаватель → `teacher/panel.php`
5. Студент → `student/panel.php`

## Синхронизация

Все данные через `Sync` API в `assets/js/sync.js`