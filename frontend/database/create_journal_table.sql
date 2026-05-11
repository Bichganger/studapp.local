-- Таблица журнала посещаемости
CREATE TABLE IF NOT EXISTS journal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    group_name VARCHAR(50) NOT NULL,
    subject VARCHAR(200),
    comment TEXT,
    present INT DEFAULT 0,
    absent INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_date (date),
    INDEX idx_group (group_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавление поля comment в library_works если нет
ALTER TABLE library_works ADD COLUMN IF NOT EXISTS comment TEXT AFTER status;

-- Добавление поля teacher_id в grades если нет
ALTER TABLE grades ADD COLUMN IF NOT EXISTS teacher_id INT AFTER user_id;
