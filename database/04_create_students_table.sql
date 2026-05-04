CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    group_name VARCHAR(100) DEFAULT NULL,
    course INT DEFAULT 1,
    specialty VARCHAR(100) DEFAULT NULL,
    date_enrolled DATE DEFAULT NULL,
    status ENUM('active', 'suspended', 'graduated', 'expelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_full_name (full_name),
    INDEX idx_group_name (group_name),
    INDEX idx_status (status),
    INDEX idx_course (course)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS student_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    student_id INT NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    
    INDEX idx_user (user_id),
    INDEX idx_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO students (full_name, group_name, course, specialty, status) VALUES
('Иванов Иван Петрович', 'ИТ-321', 3, 'Информационные технологии', 'active'),
('Петров Петр Сергеевич', 'ИТ-321', 3, 'Информационные технологии', 'active'),
('Сидорова Анна Ивановна', 'ИТ-321', 3, 'Информационные технологии', 'active'),
('Козлов Дмитрий Алексеевич', 'ИТ-312', 3, 'Информационные технологии', 'active'),
('Смирнова Мария Владимировна', 'ИТ-312', 3, 'Информационные технологии', 'active');
