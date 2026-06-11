# Инструкция по применению миграции для системы преподавателей

## Проблема
Миграция базы данных не была применена автоматически, поэтому новый дизайн рейтинга и система модерации не работают.

## Решение (выберите один из способов)

### Способ 1: Через phpMyAdmin (рекомендуется)
1. Откройте phpMyAdmin
2. Выберите базу данных `studapp`
3. Перейдите на вкладку "SQL"
4. Скопируйте содержимое файла `database_optimize_teachers.sql`
5. Нажмите "Вперед" (Go)

### Способ 2: Через консоль MySQL
```bash
mysql -u root -p studapp < database_optimize_teachers.sql
```

### Способ 3: Вручную по командам
Откройте MySQL консоль и выполните по очереди:

```sql
USE studapp;

ALTER TABLE teachers ADD COLUMN avg_rating DECIMAL(3,1) DEFAULT 0;
ALTER TABLE teachers ADD COLUMN campus VARCHAR(50) DEFAULT NULL;
ALTER TABLE teachers ADD COLUMN is_strict TINYINT DEFAULT 0;
ALTER TABLE teachers ADD COLUMN auto_exam TINYINT DEFAULT 0;

ALTER TABLE teacher_reviews ADD COLUMN is_approved TINYINT DEFAULT 1;
ALTER TABLE teacher_reviews ADD COLUMN is_hidden TINYINT DEFAULT 0;
ALTER TABLE teacher_reviews ADD COLUMN moderated_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE teacher_reviews ADD COLUMN moderated_by INT DEFAULT NULL;

CREATE INDEX idx_teachers_campus ON teachers(campus);
CREATE INDEX idx_teachers_strict ON teachers(is_strict);
CREATE INDEX idx_reviews_approved ON teacher_reviews(is_approved);
CREATE INDEX idx_reviews_teacher ON teacher_reviews(teacher_id, is_approved);

UPDATE teachers SET avg_rating = (SELECT ROUND(AVG(rating), 1) FROM teacher_reviews WHERE teacher_id = teachers.id AND is_approved = 1);
```

## После миграции

1. **Проверьте страницу преподавателей**: `/student/teachers.php`
   - Теперь должен отображаться круговой индикатор рейтинга вместо звёзд
   
2. **Проверьте модерацию отзывов**: `/admin/teacher_reviews.php`
   - Новая страница для одобрения/скрытия отзывов

3. **Проверьте управление преподавателями**: `/admin/manage_teachers.php`
   - Обновлён дизайн с круговым индикатором

## Если возникла ошибка "Duplicate column name"
Это означает, что колонка уже существует. Просто пропустите эту команду и продолжите выполнение остальных.

## Проверка результата
Выполните в MySQL:
```sql
SHOW COLUMNS FROM teachers;
SHOW COLUMNS FROM teacher_reviews;
```

Должны появиться новые колонки:
- teachers: `avg_rating`, `campus`, `is_strict`, `auto_exam`
- teacher_reviews: `is_approved`, `is_hidden`, `moderated_at`, `moderated_by`
