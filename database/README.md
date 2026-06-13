# База данных

## Структура

- `studapp.sql` - Полный дамп базы данных с тестовыми данными
- `migrations/` - SQL-миграции для изменения схемы БД

## Миграции

| Файл | Описание |
|------|----------|
| `add_review_moderation.sql` | Добавление модерации отзывов |
| `add_sample_teachers.sql` | Добавление тестовых преподавателей |
| `database_optimization.sql` | Оптимизация БД |
| `database_optimize_teachers.sql` | Оптимизация таблицы преподавателей |

## Установка

1. Создайте базу данных `studapp`
2. Импортируйте `studapp.sql`

```sql
CREATE DATABASE studapp CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE studapp;
SOURCE studapp.sql;
```

3. Выполните необходимые миграции из папки `migrations/`

## Таблицы

- `users` - Пользователи системы
- `teachers` - Преподаватели
- `teacher_reviews` - Отзывы о преподавателях
- `students` - Студенты (объединено с users)
- `schedule` - Расписание занятий
- `grades` - Оценки
- `assignments` - Задания/работы
- `journal` - Журнал посещаемости
- `library` - Библиотека работ
- `works` - Работы студентов
- `notifications` - Уведомления
- `tips` - Советы
- `locations` - Локации корпусов
- `groups` - Группы студентов
