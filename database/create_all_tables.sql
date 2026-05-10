-- Создание всех таблиц для Учеба24
-- Запустить в phpMyAdmin после создания БД

-- Таблица групп
CREATE TABLE IF NOT EXISTS study_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(50) UNIQUE,
    specialty VARCHAR(100),
    course INT DEFAULT 1,
    student_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица расписания
CREATE TABLE IF NOT EXISTS schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day VARCHAR(20),
    group_name VARCHAR(50),
    subject VARCHAR(200),
    time_start TIME,
    type VARCHAR(20),
    classroom VARCHAR(50),
    teacher VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица работ библиотеки
CREATE TABLE IF NOT EXISTS library_works (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    work_type VARCHAR(20),
    subject VARCHAR(100),
    student_name VARCHAR(255),
    group_name VARCHAR(50),
    file_path VARCHAR(255),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица уведомлений
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    message TEXT,
    target_type VARCHAR(20),
    target_group VARCHAR(50),
    send_email TINYINT(1) DEFAULT 0,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица настроек
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица оценок
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    subject VARCHAR(100),
    grade VARCHAR(10),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Вставка тестовых данных (по желанию)
INSERT INTO study_groups (group_name, specialty, course, student_count) VALUES
('ИТ-321', 'Информационные технологии', 3, 24),
('ИТ-312', 'Информационные технологии', 3, 22),
('ЭК-214', 'Электроника', 2, 20);

INSERT INTO system_settings (setting_key, setting_value) VALUES
('system_name', 'Учеба24'),
('allow_registration', '1'),
('email_confirmation', '1'),
('min_password_length', '6');
