-- Дополнительные таблицы для функционала

-- Таблица работ (works)
CREATE TABLE IF NOT EXISTS works (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    file_type ENUM('coursework', 'lab', 'referat', 'other') DEFAULT 'coursework',
    uploaded_by INT,
    group_name VARCHAR(100),
    is_public TINYINT DEFAULT 1,
    downloads INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица местоположений (locations) - для карты
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    lat DECIMAL(10, 8) NOT NULL,
    lng DECIMAL(11, 8) NOT NULL,
    type ENUM('building', 'cafeteria', 'bus_stop', 'other') DEFAULT 'building',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовые локации (Калининград)
INSERT INTO locations (name, lat, lng, type, description) VALUES
('Главный корпус', 54.7065, 20.5108, 'building', 'ул. Брамса, 9'),
('Корпус 2', 54.7025, 20.5085, 'building', 'ул. Спортивная, 6'),
('Корпус 3', 54.7095, 20.5145, 'building', 'ул. Озерова, 7'),
('Столовая', 54.7060, 20.5100, 'cafeteria', 'Университетская столовая'),
('Автобусная остановка', 54.7070, 20.5120, 'bus_stop', 'Остановка "Университет"');

-- Таблица преподавателей (teachers)
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    short_name VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(50),
    office VARCHAR(50),
    specialty VARCHAR(255),
    description TEXT,
    avatar VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица отзывов о преподавателях (teacher_reviews)
CREATE TABLE IF NOT EXISTS teacher_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    student_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица советов (tips)
CREATE TABLE IF NOT EXISTS tips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('exam', 'coursework', 'lab', 'general', 'lifehack') DEFAULT 'general',
    author_id INT,
    votes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовые советы
INSERT INTO tips (title, content, category, votes) VALUES
('Как подготовиться к сессии за неделю', 'Разбей материал на части, учи по 2-3 темы в день, не зубри всё в последнюю ночь', 'exam', 15),
('Автомат по лабам', 'Сделай все лабы вовремя, не копируй у одногруппников, преподаватели видят', 'lab', 23),
('Где поесть дешево', 'Столовая в главном корпусе - завтраки до 10:00 со скидкой 20%', 'lifehack', 8),
('Курсовая за 3 дня', 'Бери готовые работы из библиотеки, переделывай введение и заключение, добавляй свои данные', 'coursework', 31);
