-- ============================================
-- Синхронизация и инициализация преподавателей
-- ============================================

-- 1. Проверка и добавление недостающих колонок
SET @dbname = DATABASE();

-- Добавляем campus если нет
SET @sql = (
    SELECT IF(
        NOT EXISTS (
            SELECT * FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = @dbname 
            AND TABLE_NAME = 'teachers' 
            AND COLUMN_NAME = 'campus'
        ),
        'ALTER TABLE teachers ADD COLUMN campus VARCHAR(100) DEFAULT NULL AFTER specialty',
        'SELECT ""'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Добавляем is_strict если нет
SET @sql = (
    SELECT IF(
        NOT EXISTS (
            SELECT * FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = @dbname 
            AND TABLE_NAME = 'teachers' 
            AND COLUMN_NAME = 'is_strict'
        ),
        'ALTER TABLE teachers ADD COLUMN is_strict TINYINT DEFAULT 0 AFTER campus',
        'SELECT ""'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Добавляем auto_exam если нет
SET @sql = (
    SELECT IF(
        NOT EXISTS (
            SELECT * FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = @dbname 
            AND TABLE_NAME = 'teachers' 
            AND COLUMN_NAME = 'auto_exam'
        ),
        'ALTER TABLE teachers ADD COLUMN auto_exam TINYINT DEFAULT 0 AFTER is_strict',
        'SELECT ""'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Добавляем avg_rating если нет
SET @sql = (
    SELECT IF(
        NOT EXISTS (
            SELECT * FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = @dbname 
            AND TABLE_NAME = 'teachers' 
            AND COLUMN_NAME = 'avg_rating'
        ),
        'ALTER TABLE teachers ADD COLUMN avg_rating DECIMAL(3,1) DEFAULT 0.0 AFTER auto_exam',
        'SELECT ""'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Добавление преподавателей (минимум 5 человек)
INSERT INTO `teachers` (`id`, `full_name`, `short_name`, `email`, `phone`, `office`, `specialty`, `description`, `campus`, `is_strict`, `auto_exam`, `avg_rating`, `created_at`) VALUES
(100, 'Иванов Петр Сергеевич', 'Иванов П.С.', 'ivanov@studapp.ru', '+7 (4012) 55-33-22', '301', 'Математический анализ, Программирование, Базы данных', 'Добросовестный преподаватель, требует выполнения всех заданий. Любит когда студенты приходят подготовленными.', 'Брамса 9', 0, 1, 4.5, NOW()),
(101, 'Смирнова Анна Викторовна', 'Смирнова А.В.', 'smirnova@studapp.ru', '+7 (4012) 55-33-23', '205', 'Физика, Веб-разработка', 'Требовательный преподаватель, но справедливый. Всегда идёт на контакт если видит старание.', 'Спортивная 6', 1, 0, 4.2, NOW()),
(102, 'Петрова Елена Николаевна', 'Петрова Е.Н.', 'petrova@studapp.ru', '+7 (4012) 55-33-24', '402', 'Дискретная математика, Алгоритмы', 'Очень добрая, даёт автоматы за активную работу на семинарах. Советы по подготовке всегда помогают.', 'Брамса 9', 0, 1, 4.8, NOW()),
(103, 'Козлов Дмитрий Александрович', 'Козлов Д.А.', 'kozlov@studapp.ru', '+7 (4012) 55-33-25', '305', 'Базы данных, Системное программирование', 'Строгий, но честный. Не любит списывание, но помогает разобраться в сложных темах.', 'Спортивная 6', 1, 0, 3.9, NOW()),
(104, 'Соколова Мария Игоревна', 'Соколова М.И.', 'sokolova@studapp.ru', '+7 (4012) 55-33-26', '210', 'Веб-разработка, Графический дизайн', 'Творческий подход, много практических заданий. Всегда делится полезными ресурсами.', 'Озерова 7', 0, 1, 4.6, NOW()),
(105, 'Новиков Сергей Владимирович', 'Новиков С.В.', 'novikov@studapp.ru', '+7 (4012) 55-33-27', '108', 'Сети и телекоммуникации, Кибербезопасность', 'Опытный преподаватель, много практики. Даёт автоматы за участие в олимпиадах.', 'Брамса 9', 0, 0, 4.3, NOW())
ON DUPLICATE KEY UPDATE 
    `full_name` = VALUES(`full_name`),
    `short_name` = VALUES(`short_name`),
    `email` = VALUES(`email`),
    `phone` = VALUES(`phone`),
    `office` = VALUES(`office`),
    `specialty` = VALUES(`specialty`),
    `description` = VALUES(`description`),
    `campus` = VALUES(`campus`),
    `is_strict` = VALUES(`is_strict`),
    `auto_exam` = VALUES(`auto_exam`);

-- 3. Создание таблицы teacher_reviews если нет
CREATE TABLE IF NOT EXISTS `teacher_reviews` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `teacher_id` INT NOT NULL,
  `student_id` INT DEFAULT NULL,
  `rating` DECIMAL(3,1) NOT NULL,
  `review_text` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Добавление отзывов (если нет)
INSERT INTO `teacher_reviews` (`teacher_id`, `student_id`, `rating`, `review_text`, `created_at`) VALUES
(100, NULL, 5, 'Отличный преподаватель! Помог разобраться с БД, всегда на связи в мессенджерах.'),
(100, NULL, 4, 'Хороший, но требует много работать. Автоматы даёт честно.'),
(101, NULL, 4, 'Строгая, но справедливая. Если учить материал - всё будет хорошо.'),
(101, NULL, 5, 'Понятно объясняет, всегда готова помочь с дополнительными материалами.'),
(102, NULL, 5, 'Самый добрый преподаватель! Автоматы за активность на парах.'),
(102, NULL, 5, 'Рекомендую! Всегда помогает и поддерживает студентов.'),
(103, NULL, 3, 'Сложный предмет, преподаватель строгий. Но если разобраться - интересно.'),
(103, NULL, 4, 'Хороший преподаватель, требует самостоятельной работы.'),
(104, NULL, 5, 'Очень креативный подход к преподаванию. Задания интересные.'),
(104, NULL, 5, 'Любимый преподаватель! Всегда делится полезными советами по дизайну.'),
(105, NULL, 4, 'Опытный специалист, много практических знаний.'),
(105, NULL, 4, 'Хороший преподаватель, даёт полезные материалы для самообразования.')
ON DUPLICATE KEY UPDATE `rating` = VALUES(`rating`);

-- 5. Создание таблицы teacher_tips если нет
CREATE TABLE IF NOT EXISTS `teacher_tips` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `teacher_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `category` ENUM('study','exam','lab','career','general') DEFAULT 'general',
  `votes` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Добавление советов преподавателей
INSERT INTO `teacher_tips` (`teacher_id`, `title`, `content`, `category`, `votes`, `created_at`) VALUES
(100, 'Как подготовиться к экзамену по БД', 'Решайте все практические задания из лабораторных. На экзамене будет 3 задачи: проектирование БД, SQL запросы, нормализация. Уделите внимание индексам и транзакциям.', 'exam', 25, NOW()),
(100, 'Совет по программированию', 'Не копируйте код у одногруппников - преподаватель видит. Лучше потратьте время и напишите сами, даже если получится не идеально.', 'study', 18, NOW()),
(101, 'Физика без стресса', 'Для лаб по физике обязательно делайте предварительные расчёты дома. На практической это экономит 30 минут времени.', 'lab', 22, NOW()),
(102, 'Дискретка для чайников', 'Начните с теоретических вопросов за 2 недели до сессии. Все формулы и доказательства записывайте в отдельную тетрадь.', 'study', 31, NOW()),
(102, 'Как получить автомат', 'Активность на семинарах = автомат. Задавайте вопросы, участвуйте в обсуждениях, не прячьтесь на задних рядах.', 'exam', 45, NOW()),
(103, 'Базы данных - это просто', 'Нарисуйте схему БД на бумаге перед тем как писать SQL. Это поможет избежать ошибок в JOIN и WHERE.', 'study', 19, NOW()),
(104, 'Веб-разработка с нуля', 'Изучите сначала HTML и CSS на базовом уровне, потом переходите к JavaScript. Не пытайтесь выучить всё сразу.', 'study', 28, NOW()),
(105, 'Карьера в IT', 'Стажировки важнее оценок. Ищите практику уже на 2 курсе. Портфолио проектов важнее диплома.', 'career', 37, NOW())
ON DUPLICATE KEY UPDATE `votes` = VALUES(`votes`);

-- 7. Синхронизация рейтингов преподавателей
UPDATE `teachers` t SET `avg_rating` = (
    SELECT ROUND(AVG(rating), 1) FROM `teacher_reviews` WHERE teacher_id = t.id
) WHERE EXISTS (SELECT 1 FROM `teacher_reviews` WHERE teacher_id = t.id);

-- 8. Вывод результата
SELECT 
    COUNT(*) as total_teachers,
    COUNT(DISTINCT campus) as campuses_count
FROM teachers;

-- ============================================
-- Синхронизация завершена!
-- ============================================
