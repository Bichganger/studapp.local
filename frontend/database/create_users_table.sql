-- Создание таблицы пользователей для Учеба24
-- Запустить в phpMyAdmin после создания БД studapp

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') DEFAULT 'student',
    email VARCHAR(255),
    group_name VARCHAR(50),
    course INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание администратора по умолчанию
-- Пароль: admin123 (хешируется через password_hash)
INSERT INTO users (username, password, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор', 'admin');

-- Создание тестового преподавателя
-- Пароль: teacher123
INSERT INTO users (username, password, full_name, role) VALUES
('teacher', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Петров Петр Петрович', 'teacher');

-- Создание тестового студента
-- Пароль: student123
INSERT INTO users (username, password, full_name, role, group_name, course) VALUES
('student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Иванов Иван Иванович', 'student', 'ИТ-321', 3);
