# Скрипты и утилиты

Эта папка содержит служебные скрипты для миграции и настройки базы данных.

## Список скриптов

| Файл | Описание | Как запустить |
|------|----------|---------------|
| `apply_review_moderation.php` | Добавление колонок модерации отзывов | `php apply_review_moderation.php` |
| `apply_teachers_migration.php` | Создание таблицы преподавателей | `php apply_teachers_migration.php` |
| `add_sample_teachers.php` | Добавление тестовых преподавателей | `php add_sample_teachers.php` |
| `sync_teachers.php` | Синхронизация данных преподавателей | `php sync_teachers.php` |

## Запуск

### Через CLI:
```bash
php scripts/apply_review_moderation.php
```

### Через браузер:
```
http://studapp.local/scripts/apply_review_moderation.php
```

**Внимание:** Большинство скриптов предназначены для однократного выполнения!
