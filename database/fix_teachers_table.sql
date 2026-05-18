-- Добавление недостающих полей в таблицу teachers
ALTER TABLE `teachers` 
    ADD COLUMN `campus` VARCHAR(100) DEFAULT NULL AFTER `specialty`,
    ADD COLUMN `is_strict` TINYINT DEFAULT 0 AFTER `campus`,
    ADD COLUMN `auto_exam` TINYINT DEFAULT 0 AFTER `is_strict`,
    ADD COLUMN `avg_rating` DECIMAL(3,1) DEFAULT 0.0 AFTER `auto_exam`;

-- Добавление недостающего поля в таблицу assignments
ALTER TABLE `assignments`
    ADD COLUMN `due_date` DATE DEFAULT NULL AFTER `grade`;

-- Добавление тестовых данных в таблицу teachers
INSERT INTO `teachers` (`id`, `full_name`, `short_name`, `email`, `phone`, `office`, `specialty`, `description`, `campus`, `is_strict`, `auto_exam`, `avg_rating`, `created_at`) VALUES
(2, 'Иванов Петр Сергеевич', 'Иванов П.С.', 'ivanov@studapp.ru', '+7 (4012) 55-33-22', '301', 'Математический анализ, Программирование, Базы данных', 'Добросовестный преподаватель, требует выполнения всех заданий', 'Брамса 9', 0, 1, 4.5, NOW()),
(3, 'Смирнова Анна Викторовна', 'Смирнова А.В.', 'smirnova@studapp.ru', '+7 (4012) 55-33-23', '205', 'Физика, Веб-разработка', 'Требовательный преподаватель, но справедливый', 'Спортивная 6', 1, 0, 4.2, NOW());

-- Обновление avg_rating на основе teacher_reviews
UPDATE `teachers` t SET `avg_rating` = (
    SELECT ROUND(AVG(rating), 1) FROM `teacher_reviews` WHERE teacher_id = t.id
) WHERE EXISTS (SELECT 1 FROM `teacher_reviews` WHERE teacher_id = t.id);

-- Обновление due_date для существующих записей в assignments
UPDATE `assignments` SET `due_date` = DATE_ADD(submitted_at, INTERVAL 7 DAY) WHERE `due_date` IS NULL;
