-- Миграция для добавления модерации отзывов
-- Выполнить один раз в phpMyAdmin или MySQL консоли

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

-- Вывод результата
SELECT 
    'Миграция завершена!' as status,
    (SELECT COUNT(*) FROM teacher_reviews) as total_reviews,
    (SELECT COUNT(*) FROM teacher_reviews WHERE is_approved = 1) as approved_reviews,
    (SELECT COUNT(*) FROM teacher_reviews WHERE is_approved = 0) as pending_reviews;
