# Структура проекта Учеба24

## 📁 Финальная структура

```
studapp.local/
├── 📄 .editorconfig          # Настройки редактора
├── 📄 .env.example           # Пример переменных окружения
├── 📄 .htaccess              # Настройки Apache
├── 📄 README.md              # Главная документация
│
├── 📄 index.php              # Главная страница
├── 📄 dashboard.php          # Страница входа/регистрации
├── 📄 auth.php               # Обработчик аутентификации
├── 📄 register_new.php       # Форма регистрации
├── 📄 logout.php             # Выход из системы
│
├── 📁 admin/                 # Админ-панель
│   ├── dashboard.php
│   ├── users.php
│   ├── teachers.php
│   ├── manage_teachers.php
│   ├── teacher_reviews.php   # Модерация отзывов
│   ├── schedule.php
│   ├── library.php
│   ├── notifications.php
│   ├── tips.php
│   └── settings.php
│
├── 📁 student/               # Кабинет студента
│   ├── panel.php             # Дашборд студента
│   ├── schedule.php
│   ├── grades.php
│   ├── assignments.php
│   ├── library.php
│   ├── map.php
│   ├── teachers.php          # Отзывы о преподавателях
│   ├── notifications.php
│   └── tips.php
│
├── 📁 teacher/               # Кабинет преподавателя
│   ├── panel.php             # Дашборд преподавателя
│   ├── journal.php
│   ├── grades.php
│   ├── assignments.php
│   ├── schedule.php
│   ├── groups.php
│   └── notifications.php
│
├── 📁 config/                # Конфигурация
│   ├── db.php                # Подключение к БД + утилиты
│   └── app.php               # Общие настройки
│
├── 📁 includes/              # Общие компоненты
│   ├── header.php
│   └── footer.php
│
├── 📁 protected/             # Защищённая логика
│   └── auth_guard.php        # Проверка авторизации
│
├── 📁 scripts/               # Скрипты миграции и утилиты
│   ├── README.md
│   ├── add_sample_teachers.php      # Добавление преподавателей
│   ├── apply_review_moderation.php  # Модерация отзывов
│   ├── apply_teachers_migration.php # Миграция преподавателей
│   ├── sync_teachers.php            # Синхронизация
│   └── setup_uploads.sh             # Настройка загрузок
│
├── 📁 database/              # SQL файлы
│   ├── README.md
│   ├── studapp.sql                  # Полный дамп БД
│   ├── add_user_approval.sql        # Миграция одобрения
│   └── migrations/
│       ├── add_review_moderation.sql
│       ├── add_sample_teachers.sql
│       ├── database_optimization.sql
│       └── database_optimize_teachers.sql
│
├── 📁 docs/                  # Документация
│   ├── README.md
│   ├── REGISTRATION_SYSTEM.md
│   ├── MIGRATION_README.md
│   └── README_REVIEW_MODERATION.md
│
├── 📁 assets/                # Статические файлы
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── compass-main.js
│       └── compass-map.js
│
└── 📁 uploads/               # Загруженные файлы
    └── works/                # Студенческие работы
```

## ✅ Что было сделано

### Перемещение файлов
- ✅ Все PHP-скрипты миграции → `scripts/`
- ✅ Все SQL миграции → `database/migrations/`
- ✅ Обработка регистрации → `protected/`
- ✅ Все README → `docs/`
- ✅ Основной дамп БД → `database/`

### Удаление
- ✅ Удалён `yandex-map.js.backup`
- ✅ Удалены дублирующие SQL файлы в `database/`

### Обновление путей
- ✅ `register_new.php` → путь к `reg_process.php` в корне проекта
- ✅ Все скрипты в `scripts/` → правильные пути к `config/db.php`

## 📋 Чеклист чистоты

- [x] Нет `.php` файлов в корне (кроме основных)
- [x] Нет SQL файлов в корне
- [x] Нет README в корне (кроме главного)
- [x] Нет backup файлов
- [x] Нет лишних комментариев в коде
- [x] Все файлы в своих папках
- [x] Правильная структура папок

## 🚀 Использование

### Запуск миграций
```bash
php scripts/apply_review_moderation.php
php scripts/add_sample_teachers.php
```

### Настройка загрузок
```bash
bash scripts/setup_uploads.sh
```

### Доступ к проекту
```
http://studapp.local
```
