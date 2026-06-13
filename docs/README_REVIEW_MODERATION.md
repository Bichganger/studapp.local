# Модерация отзывов о преподавателях

## Проблема
В таблице `teacher_reviews` отсутствуют колонки для модерации:
- `is_approved` - одобрен ли отзыв
- `is_hidden` - скрыт ли отзыв
- `moderated_at` - дата модерации
- `moderated_by` - кто модерировал

## Решение

### Шаг 1: Выполнить SQL-миграцию

Откройте **phpMyAdmin** или **MySQL консоль** и выполните файл:

```
database/add_review_moderation.sql
```

Или скопируйте и выполните следующий SQL:

```sql
USE studapp;

-- Добавляем колонки для модерации в teacher_reviews
ALTER TABLE teacher_reviews 
    ADD COLUMN is_approved TINYINT DEFAULT 0 AFTER created_at,
    ADD COLUMN is_hidden TINYINT DEFAULT 0 AFTER is_approved,
    ADD COLUMN moderated_at TIMESTAMP NULL DEFAULT NULL AFTER is_hidden,
    ADD COLUMN moderated_by INT DEFAULT NULL AFTER moderated_at;

-- Добавляем индексы для производительности
CREATE INDEX idx_reviews_approved ON teacher_reviews(is_approved);
CREATE INDEX idx_reviews_hidden ON teacher_reviews(is_hidden);
CREATE INDEX idx_reviews_teacher_approved ON teacher_reviews(teacher_id, is_approved);

-- Одобрим все существующие отзывы (по умолчанию)
UPDATE teacher_reviews SET is_approved = 1 WHERE is_approved IS NULL;

-- Обновляем рейтинги преподавателей на основе одобренных и не скрытых отзывов
UPDATE teachers t 
SET avg_rating = (
    SELECT ROUND(AVG(rating), 1) 
    FROM teacher_reviews 
    WHERE teacher_id = t.id 
    AND is_approved = 1 
    AND is_hidden = 0
)
WHERE EXISTS (
    SELECT 1 FROM teacher_reviews 
    WHERE teacher_id = t.id 
    AND is_approved = 1 
    AND is_hidden = 0
);
```

### Шаг 2: Проверить результат

Выполните запрос:

```sql
SHOW COLUMNS FROM teacher_reviews;
```

Должны появиться новые колонки:
- `is_approved` - TINYINT, DEFAULT 0
- `is_hidden` - TINYINT, DEFAULT 0
- `moderated_at` - TIMESTAMP, NULL
- `moderated_by` - INT, NULL

### Шаг 3: Проверить работу модерации

1. Откройте `/admin/teacher_reviews.php`
2. Должны отображаться отзывы со статусом "На проверке", "Одобрен", "Скрыт"
3. Новые отзывы студентов создаются со статусом `is_approved = 0` (на проверке)

## Как работает модерация

1. **Студент** оставляет отзыв → отзыв создаётся с `is_approved = 0`
2. **Администратор** заходит в `/admin/teacher_reviews.php`
3. Админ может:
   - **Одобрить** → `is_approved = 1`, отзыв появляется у студентов, рейтинг пересчитывается
   - **Скрыть** → `is_hidden = 1`, отзыв не виден студентам, рейтинг пересчитывается
   - **Удалить** → отзыв удаляется из БД, рейтинг пересчитывается
4. **Рейтинг преподавателя** пересчитывается **только из одобренных и не скрытых отзывов**

## Файлы, которые используют модерацию

Все исправлены и учитывают `is_approved = 1 AND is_hidden = 0`:

| Файл | Описание |
|------|----------|
| `student/teachers.php` | Отображение преподавателей и отзывов студентам |
| `admin/teacher_reviews.php` | Модерация отзывов |
| `admin/manage_teachers.php` | Управление преподавателями |
| `admin/teachers.php` | Список преподавателей для админа |
| `sync_teachers.php` | Синхронизация преподавателей |

## Примечание

Если вы хотите, чтобы отзывы сразу появлялись без модерации, измените в `student/teachers.php` строку 24:

```php
// Было:
$stmt = $pdo->prepare("INSERT INTO teacher_reviews (teacher_id, student_id, rating, comment, is_approved) VALUES (?, ?, ?, ?, 0)");

// Станет (сразу одобренные):
$stmt = $pdo->prepare("INSERT INTO teacher_reviews (teacher_id, student_id, rating, comment, is_approved) VALUES (?, ?, ?, ?, 1)");
```
