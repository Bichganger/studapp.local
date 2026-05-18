-- ============================================
-- Полная синхронизация всех исправлений
-- ============================================

SET @dbname = DATABASE();

-- 1. Добавление полей в teachers
SET @sql = (SELECT IF(NOT EXISTS(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'teachers' AND COLUMN_NAME = 'campus'), 'ALTER TABLE teachers ADD COLUMN campus VARCHAR(100) DEFAULT NULL AFTER specialty', 'SELECT ""'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(NOT EXISTS(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'teachers' AND COLUMN_NAME = 'is_strict'), 'ALTER TABLE teachers ADD COLUMN is_strict TINYINT DEFAULT 0 AFTER campus', 'SELECT ""'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(NOT EXISTS(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'teachers' AND COLUMN_NAME = 'auto_exam'), 'ALTER TABLE teachers ADD COLUMN auto_exam TINYINT DEFAULT 0 AFTER is_strict', 'SELECT ""'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(NOT EXISTS(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'teachers' AND COLUMN_NAME = 'avg_rating'), 'ALTER TABLE teachers ADD COLUMN avg_rating DECIMAL(3,1) DEFAULT 0.0 AFTER auto_exam', 'SELECT ""'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Добавление поля is_approved в users
SET @sql = (SELECT IF(NOT EXISTS(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_approved'), 'ALTER TABLE users ADD COLUMN is_approved TINYINT DEFAULT 0 AFTER role', 'SELECT ""'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Одобрение существующих admin и teachers
UPDATE users SET is_approved = 1 WHERE role IN ('admin', 'teacher');
UPDATE users SET is_approved = 1 WHERE id = 1;

-- 4. Добавление преподавателей
INSERT INTO teachers (id, full_name, short_name, email, phone, office, specialty, description, campus, is_strict, auto_exam, avg_rating, created_at) VALUES
(100, 'Иванов Петр Сергеевич', 'Иванов П.С.', 'ivanov@studapp.ru', '+7 (4012) 55-33-22', '301', 'Математический анализ, Программирование, Базы данных', 'Добросовестный преподаватель', 'Брамса 9', 0, 1, 4.5, NOW()),
(101, 'Смирнова Анна Викторовна', 'Смирнова А.В.', 'smirnova@studapp.ru', '+7 (4012) 55-33-23', '205', 'Физика, Веб-разработка', 'Требовательный преподаватель', 'Спортивная 6', 1, 0, 4.2, NOW()),
(102, 'Петрова Елена Николаевна', 'Петрова Е.Н.', 'petrova@studapp.ru', '+7 (4012) 55-33-24', '402', 'Дискретная математика, Алгоритмы', 'Очень добрая', 'Брамса 9', 0, 1, 4.8, NOW()),
(103, 'Козлов Дмитрий Александрович', 'Козлов Д.А.', 'kozlov@studapp.ru', '+7 (4012) 55-33-25', '305', 'Базы данных, Системное программирование', 'Строгий, но честный', 'Спортивная 6', 1, 0, 3.9, NOW()),
(104, 'Соколова Мария Игоревна', 'Соколова М.И.', 'sokolova@studapp.ru', '+7 (4012) 55-33-26', '210', 'Веб-разработка, Графический дизайн', 'Творческий подход', 'Озерова 7', 0, 1, 4.6, NOW()),
(105, 'Новиков Сергей Владимирович', 'Новиков С.В.', 'novikov@studapp.ru', '+7 (4012) 55-33-27', '108', 'Сети и телекоммуникации, Кибербезопасность', 'Опытный преподаватель', 'Брамса 9', 0, 0, 4.3, NOW())
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), specialty=VALUES(specialty), campus=VALUES(campus);

-- 5. Создание teacher_reviews если нет
CREATE TABLE IF NOT EXISTS teacher_reviews (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  teacher_id INT NOT NULL,
  student_id INT DEFAULT NULL,
  rating DECIMAL(3,1) NOT NULL,
  review_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Создание teacher_tips если нет
CREATE TABLE IF NOT EXISTS teacher_tips (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  teacher_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  category ENUM('study','exam','lab','career','general') DEFAULT 'general',
  votes INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Обновление works.file_type на корректные значения
ALTER TABLE works MODIFY COLUMN file_type ENUM('coursework','lab','referat','other') DEFAULT 'coursework';

-- 7. Синхронизация рейтингов
UPDATE teachers t SET avg_rating = (SELECT ROUND(AVG(rating), 1) FROM teacher_reviews WHERE teacher_id = t.id) WHERE EXISTS (SELECT 1 FROM teacher_reviews WHERE teacher_id = t.id);

-- 8. Добавление поля due_date в assignments если нет
SET @sql = (SELECT IF(NOT EXISTS(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'assignments' AND COLUMN_NAME = 'due_date'), 'ALTER TABLE assignments ADD COLUMN due_date DATETIME DEFAULT NULL AFTER status', 'SELECT ""'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 9. Добавление поля teacher_id в grades если нет
SET @sql = (SELECT IF(NOT EXISTS(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'grades' AND COLUMN_NAME = 'teacher_id'), 'ALTER TABLE grades ADD COLUMN teacher_id INT DEFAULT NULL AFTER grade', 'SELECT ""'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================
-- Синхронизация завершена!
-- ============================================
