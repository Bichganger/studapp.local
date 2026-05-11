-- Полная база данных для Учеба24 с тестовыми данными
-- Создание базы данных
CREATE DATABASE IF NOT EXISTS studapp CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE studapp;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    group_name VARCHAR(100),
    course INT DEFAULT 1,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовые пользователи
-- Пароли хешированы через PHP password_hash(): admin123, teacher123, student123
INSERT INTO users (full_name, username, password, role, group_name, course, email) VALUES
('Администратор Системы', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, 'admin@studapp.ru'),
('Иванов Петр Сергеевич', 'teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, NULL, 'ivanov@studapp.ru'),
('Смирнова Анна Викторовна', 'teacher2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, NULL, 'smirnova@studapp.ru'),
('Сидоров Алексей Иванович', 'student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ПИ-21', 2, 'sidorov@studapp.ru'),
('Козлов Дмитрий Михайлович', 'student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ПИ-21', 2, 'kozlov@studapp.ru'),
('Новикова Мария Александровна', 'student3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ПИ-22', 2, 'novikova@studapp.ru'),
('Петров Иван Сергеевич', 'student4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ПИ-22', 2, 'petrov@studapp.ru'),
('Соколова Елена Дмитриевна', 'student5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ПО-21', 3, 'sokolova@studapp.ru'),
('Михайлов Андрей Владимирович', 'student6', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ПО-21', 3, 'mikhailov@studapp.ru'),
('Федорова Ольга Николаевна', 'student7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ПО-22', 3, 'fedorova@studapp.ru');

-- Таблица групп
CREATE TABLE IF NOT EXISTS groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    course INT NOT NULL,
    specialty VARCHAR(100),
    student_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовые группы
INSERT INTO groups (name, course, specialty, student_count) VALUES
('ПИ-21', 2, 'Программная инженерия', 2),
('ПИ-22', 2, 'Программная инженерия', 2),
('ПО-21', 3, 'Прикладная информатика', 3),
('ПО-22', 3, 'Прикладная информатика', 3);

-- Таблица расписания
CREATE TABLE IF NOT EXISTS schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    teacher_name VARCHAR(255),
    day_of_week ENUM('понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    classroom VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовое расписание
INSERT INTO schedule (group_name, subject, teacher_name, day_of_week, start_time, end_time, classroom) VALUES
('ПИ-21', 'Математический анализ', 'Иванов П.С.', 'понедельник', '09:00:00', '10:30:00', '301'),
('ПИ-21', 'Физика', 'Смирнова А.В.', 'понедельник', '11:00:00', '12:30:00', '205'),
('ПИ-21', 'Программирование', 'Иванов П.С.', 'вторник', '09:00:00', '10:30:00', '401'),
('ПИ-21', 'Базы данных', 'Смирнова А.В.', 'среда', '11:00:00', '12:30:00', '302'),
('ПИ-22', 'Дискретная математика', 'Иванов П.С.', 'четверг', '09:00:00', '10:30:00', '301'),
('ПИ-22', 'Веб-разработка', 'Смирнова А.В.', 'пятница', '11:00:00', '12:30:00', '401'),
('ПО-21', 'Алгоритмы и структуры', 'Иванов П.С.', 'понедельник', '13:00:00', '14:30:00', '303'),
('ПО-21', 'Сети и телеком', 'Смирнова А.В.', 'вторник', '09:00:00', '10:30:00', '201'),
('ПО-22', 'Операционные системы', 'Иванов П.С.', 'среда', '09:00:00', '10:30:00', '304'),
('ПО-22', 'Компиляторы', 'Смирнова А.В.', 'четверг', '11:00:00', '12:30:00', '205');

-- Таблица оценок
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    group_name VARCHAR(100),
    subject VARCHAR(255) NOT NULL,
    grade VARCHAR(10) NOT NULL,
    date DATE NOT NULL,
    teacher_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовые оценки
INSERT INTO grades (student_id, student_name, group_name, subject, grade, date, teacher_id) VALUES
(4, 'Сидоров Алексей', 'ПИ-21', 'Математический анализ', '5', '2026-01-15', 2),
(4, 'Сидоров Алексей', 'ПИ-21', 'Программирование', '4', '2026-01-16', 2),
(5, 'Козлов Дмитрий', 'ПИ-21', 'Математический анализ', '4', '2026-01-15', 2),
(5, 'Козлов Дмитрий', 'ПИ-21', 'Физика', '5', '2026-01-17', 3),
(6, 'Новикова Мария', 'ПИ-22', 'Дискретная математика', '5', '2026-01-18', 2),
(6, 'Новикова Мария', 'ПИ-22', 'Веб-разработка', '5', '2026-01-19', 3),
(7, 'Петров Иван', 'ПИ-22', 'Дискретная математика', '4', '2026-01-18', 2),
(8, 'Соколова Елена', 'ПО-21', 'Алгоритмы и структуры', '5', '2026-01-15', 2),
(8, 'Соколова Елена', 'ПО-21', 'Сети и телеком', '4', '2026-01-16', 3),
(9, 'Михайлов Андрей', 'ПО-21', 'Алгоритмы и структуры', '4', '2026-01-15', 2),
(10, 'Федорова Ольга', 'ПО-22', 'Операционные системы', '5', '2026-01-20', 2),
(10, 'Федорова Ольга', 'ПО-22', 'Компиляторы', '4', '2026-01-21', 3);

-- Таблица заданий/работ
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    group_name VARCHAR(100),
    subject VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    status ENUM('pending', 'checked', 'rejected') DEFAULT 'pending',
    teacher_comment TEXT,
    grade VARCHAR(10),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    checked_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовые задания
INSERT INTO assignments (student_id, student_name, group_name, subject, title, description, status, teacher_comment, grade) VALUES
(4, 'Сидоров Алексей', 'ПИ-21', 'Программирование', 'Лабораторная №1', 'Разработка калькулятора на JavaScript', 'checked', 'Отличная работа!', '5'),
(4, 'Сидоров Алексей', 'ПИ-21', 'Базы данных', 'Курсовая работа', 'Проектирование БД для интернет-магазина', 'checked', 'Хорошо, но есть недочеты', '4'),
(5, 'Козлов Дмитрий', 'ПИ-21', 'Программирование', 'Лабораторная №1', 'Разработка калькулятора на JavaScript', 'checked', 'Небольшая ошибка в логике', '4'),
(6, 'Новикова Мария', 'ПИ-22', 'Веб-разработка', 'Лабораторная №2', 'Создание сайта-визитки', 'checked', 'Превзошла ожидания!', '5'),
(7, 'Петров Иван', 'ПИ-22', 'Веб-разработка', 'Лабораторная №2', 'Создание сайта-визитки', 'pending', NULL, NULL),
(8, 'Соколова Елена', 'ПО-21', 'Алгоритмы и структуры', 'Практическая №3', 'Реализация сортировок', 'checked', 'Отлично!', '5'),
(9, 'Михайлов Андрей', 'ПО-21', 'Сети и телеком', 'Реферат', 'Протоколы OSI модели', 'checked', 'Хорошая работа', '4'),
(10, 'Федорова Ольга', 'ПО-22', 'Операционные системы', 'Лабораторная №4', 'Работа с процессами в Linux', 'checked', 'Превосходно!', '5');

-- Таблица журнала посещаемости
CREATE TABLE IF NOT EXISTS journal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    group_name VARCHAR(100),
    subject VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'excused') NOT NULL,
    teacher_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовая посещаемость
INSERT INTO journal (student_id, student_name, group_name, subject, date, status, teacher_id) VALUES
(4, 'Сидоров Алексей', 'ПИ-21', 'Математический анализ', '2026-01-15', 'present', 2),
(4, 'Сидоров Алексей', 'ПИ-21', 'Программирование', '2026-01-16', 'present', 2),
(5, 'Козлов Дмитрий', 'ПИ-21', 'Математический анализ', '2026-01-15', 'late', 2),
(5, 'Козлов Дмитрий', 'ПИ-21', 'Физика', '2026-01-17', 'present', 3),
(6, 'Новикова Мария', 'ПИ-22', 'Дискретная математика', '2026-01-18', 'present', 2),
(7, 'Петров Иван', 'ПИ-22', 'Дискретная математика', '2026-01-18', 'absent', 2),
(8, 'Соколова Елена', 'ПО-21', 'Алгоритмы и структуры', '2026-01-15', 'present', 2),
(9, 'Михайлов Андрей', 'ПО-21', 'Сети и телеком', '2026-01-16', 'excused', 3),
(10, 'Федорова Ольга', 'ПО-22', 'Операционные системы', '2026-01-20', 'present', 2);

-- Таблица уведомлений
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    target_type ENUM('all', 'students', 'teachers', 'group') NOT NULL,
    target_group VARCHAR(100),
    sender_id INT,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовые уведомления
INSERT INTO notifications (title, message, target_type, target_group, sender_id) VALUES
('Важное объявление', 'Завтра изменено расписание. Проверьте индивидуально!', 'all', NULL, 1),
('Сдача лабораторной', 'Напоминаем, что дедлайн лабораторной №3 - 25 января', 'students', 'ПИ-21', 2),
('Консультация', 'Состоится консультация по БД в пятницу в 14:00', 'students', 'ПИ-21', 3),
('Новое задание', 'Добавлено новое задание по веб-разработке', 'students', 'ПИ-22', 3),
('Изменение группы', 'Студенты ПО-21 переводятся на новую группу', 'students', 'ПО-21', 1),
('Собрание преподавателей', 'Еженедельное собрание в понедельник в 15:00', 'teachers', NULL, 1);

-- Таблица библиотеки
CREATE TABLE IF NOT EXISTS library (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    file_type ENUM('document', 'presentation', 'video', 'code', 'other') DEFAULT 'document',
    uploaded_by INT,
    group_name VARCHAR(100),
    is_public TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовые материалы библиотеки
INSERT INTO library (title, description, file_type, uploaded_by, group_name, is_public) VALUES
('Методические указания по Матану', 'Рекомендации по выполнению домашних заданий', 'document', 2, 'ПИ-21', 1),
('Презентация по Физике', 'Лекция о квантовой механике', 'presentation', 3, 'ПИ-21', 1),
('Примеры кода на JavaScript', 'База примеров для лабораторных', 'code', 2, 'ПИ-21', 1),
('Видеоурок по БД', 'Основы SQL и проектирование БД', 'video', 3, 'ПИ-21', 1),
('Шпаргалка по Дискретке', 'Основные формулы и теоремы', 'document', 2, 'ПИ-22', 1),
('Презентация по Веб-разработке', 'HTML, CSS, JavaScript основы', 'presentation', 3, 'ПИ-22', 1),
('Алгоритмы сортировки', 'Сравнение алгоритмов и их сложность', 'document', 2, 'ПО-21', 1),
('Протоколы сети', 'OSI модель и TCP/IP', 'presentation', 3, 'ПО-21', 1),
('Linux для начинающих', 'Основные команды и работа с терминалом', 'document', 2, 'ПО-22', 1),
('Компиляторы 101', 'Введение в теорию компиляторов', 'document', 3, 'ПО-22', 1);

-- Обновление статистики групп
UPDATE groups g SET student_count = (
    SELECT COUNT(*) FROM users WHERE users.group_name = g.name AND users.role = 'student'
);

-- Коммит
COMMIT;

-- Сообщение об успехе
SELECT '✅ База данных studapp успешно создана с тестовыми данными!' AS message;
