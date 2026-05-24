-- Оптимизация базы данных Учеба24
-- Выполнить один раз в MySQL

-- =====================================================
-- ИНДЕКСЫ ДЛЯ УСКОРЕНИЯ ЗАПРОСОВ
-- =====================================================

-- Индекс для таблицы users (частые запросы по role и group_name)
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_users_role (role);
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_users_group (group_name);
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_users_role_group (role, group_name);
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_users_username (username);

-- Индекс для таблицы schedule (поиск по группе и дню)
ALTER TABLE schedule ADD INDEX IF NOT EXISTS idx_schedule_group (group_name);
ALTER TABLE schedule ADD INDEX IF NOT EXISTS idx_schedule_day (day_of_week);
ALTER TABLE schedule ADD INDEX IF NOT EXISTS idx_schedule_group_day (group_name, day_of_week);

-- Индекс для таблицы grades (поиск по студенту и предмету)
ALTER TABLE grades ADD INDEX IF NOT EXISTS idx_grades_student (student_id);
ALTER TABLE grades ADD INDEX IF NOT EXISTS idx_grades_teacher (teacher_id);
ALTER TABLE grades ADD INDEX IF NOT EXISTS idx_grades_subject (subject);

-- Индекс для таблицы works (поиск по типу и группе)
ALTER TABLE works ADD INDEX IF NOT EXISTS idx_works_type (file_type);
ALTER TABLE works ADD INDEX IF NOT EXISTS idx_works_group (group_name);
ALTER TABLE works ADD INDEX IF NOT EXISTS idx_works_uploaded_by (uploaded_by);

-- Индекс для таблицы notifications (поиск по получателю)
ALTER TABLE notifications ADD INDEX IF NOT EXISTS idx_notifications_target (target_type);
ALTER TABLE notifications ADD INDEX IF NOT EXISTS idx_notifications_group (target_group);

-- Индекс для таблицы assignments (поиск по студенту)
ALTER TABLE assignments ADD INDEX IF NOT EXISTS idx_assignments_student (student_id);
ALTER TABLE assignments ADD INDEX IF NOT EXISTS idx_assignments_status (status);

-- =====================================================
-- ОПТИМИЗАЦИЯ ТАБЛИЦ
-- =====================================================

-- Анализ и оптимизация таблиц
ANALYZE TABLE users;
ANALYZE TABLE schedule;
ANALYZE TABLE grades;
ANALYZE TABLE works;
ANALYZE TABLE notifications;
ANALYZE TABLE assignments;

OPTIMIZE TABLE users;
OPTIMIZE TABLE schedule;
OPTIMIZE TABLE grades;
OPTIMIZE TABLE works;
OPTIMIZE TABLE notifications;
OPTIMIZE TABLE assignments;

-- =====================================================
-- ПРОВЕРКА ЦЕЛОСТНОСТИ
-- =====================================================

-- Проверка внешних ключей (если используются)
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'studapp'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- =====================================================
-- ФАЙЛЫ ДЛЯ ЗАГРУЗКИ (если нужны)
-- =====================================================

-- Пример добавления тестовых данных (раскомментировать при необходимости)
-- INSERT INTO users (full_name, username, password, role, group_name, course) VALUES
-- ('Администратор', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 1);
-- -- Пароль: password

-- =====================================================
-- ЗАВЕРШЕНИЕ
-- =====================================================

SELECT 'Оптимизация базы данных успешно завершена!' AS status;
