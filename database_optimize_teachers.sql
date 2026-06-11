-- Миграция для улучшения системы преподавателей и отзывов
-- Выполнить один раз в phpMyAdmin или MySQL консоли

-- Добавляем колонку avg_rating для кэширования среднего рейтинга
ALTER TABLE teachers ADD COLUMN avg_rating DECIMAL(3,1) DEFAULT 0;

-- Добавляем колонки для фильтрации
ALTER TABLE teachers ADD COLUMN campus VARCHAR(50) DEFAULT NULL;
ALTER TABLE teachers ADD COLUMN is_strict TINYINT DEFAULT 0;
ALTER TABLE teachers ADD COLUMN auto_exam TINYINT DEFAULT 0;

-- Добавляем колонки в teacher_reviews для модерации
ALTER TABLE teacher_reviews ADD COLUMN is_approved TINYINT DEFAULT 1;
ALTER TABLE teacher_reviews ADD COLUMN is_hidden TINYINT DEFAULT 0;
ALTER TABLE teacher_reviews ADD COLUMN moderated_at TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE teacher_reviews ADD COLUMN moderated_by INT DEFAULT NULL;

-- Индексы для производительности
CREATE INDEX idx_teachers_campus ON teachers(campus);
CREATE INDEX idx_teachers_strict ON teachers(is_strict);
CREATE INDEX idx_reviews_approved ON teacher_reviews(is_approved);
CREATE INDEX idx_reviews_teacher ON teacher_reviews(teacher_id, is_approved);

-- Обновляем рейтинги существующих преподавателей
UPDATE teachers SET avg_rating = (SELECT ROUND(AVG(rating), 1) FROM teacher_reviews WHERE teacher_id = teachers.id AND is_approved = 1);
