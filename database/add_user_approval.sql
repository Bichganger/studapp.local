-- ============================================
-- Модерация пользователей
-- ============================================

-- 1. Добавление поля is_approved в таблицу users
SET @dbname = DATABASE();
SET @sql = (
    SELECT IF(
        NOT EXISTS (
            SELECT * FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = @dbname 
            AND TABLE_NAME = 'users' 
            AND COLUMN_NAME = 'is_approved'
        ),
        'ALTER TABLE users ADD COLUMN is_approved TINYINT DEFAULT 0 AFTER role',
        'SELECT ""'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Одобрить существующих пользователей (admin и teachers)
UPDATE users SET is_approved = 1 WHERE role IN ('admin', 'teacher');

-- 3. Одобрить текущего админа
UPDATE users SET is_approved = 1 WHERE id = 1;

-- ============================================
-- Конец миграции
-- ============================================
