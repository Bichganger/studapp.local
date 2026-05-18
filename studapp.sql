-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 17 2026 г., 18:20
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `studapp`
--

-- --------------------------------------------------------

--
-- Структура таблицы `assignments`
--

CREATE TABLE `assignments` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `group_name` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `file_path` varchar(500) DEFAULT NULL,
  `status` enum('pending','checked','rejected') DEFAULT 'pending',
  `teacher_comment` text,
  `grade` varchar(10) DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `checked_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `assignments`
--

INSERT INTO `assignments` (`id`, `student_id`, `student_name`, `group_name`, `subject`, `title`, `description`, `file_path`, `status`, `teacher_comment`, `grade`, `submitted_at`, `checked_at`) VALUES
(1, 4, 'Сидоров Алексей', 'РУПО 26-21', 'Программирование', 'Лабораторная №1', 'Разработка калькулятора на JavaScript', NULL, 'checked', 'Отличная работа!', '5', '2026-05-17 10:03:04', NULL),
(2, 4, 'Сидоров Алексей', 'РУПО 26-21', 'Базы данных', 'Курсовая работа', 'Проектирование БД для интернет-магазина', NULL, 'checked', 'Хорошо, но есть недочеты', '4', '2026-05-17 10:03:04', NULL),
(3, 5, 'Козлов Дмитрий', 'РУПО 26-21', 'Программирование', 'Лабораторная №1', 'Разработка калькулятора на JavaScript', NULL, 'checked', 'Небольшая ошибка в логике', '4', '2026-05-17 10:03:04', NULL),
(4, 6, 'Новикова Мария', 'РУПО 26-22', 'Веб-разработка', 'Лабораторная №2', 'Создание сайта-визитки', NULL, 'checked', 'Превзошла ожидания!', '5', '2026-05-17 10:03:04', NULL),
(5, 7, 'Петров Иван', 'РУПО 26-22', 'Веб-разработка', 'Лабораторная №2', 'Создание сайта-визитки', NULL, 'pending', NULL, NULL, '2026-05-17 10:03:04', NULL),
(6, 8, 'Соколова Елена', 'ИБ 26-21', 'Алгоритмы и структуры', 'Практическая №3', 'Реализация сортировок', NULL, 'checked', 'Отлично!', '5', '2026-05-17 10:03:04', NULL),
(7, 9, 'Михайлов Андрей', 'ИБ 26-21', 'Сети и телеком', 'Реферат', 'Протоколы OSI модели', NULL, 'checked', 'Хорошая работа', '4', '2026-05-17 10:03:04', NULL),
(8, 10, 'Федорова Ольга', 'ИБ 26-22', 'Операционные системы', 'Лабораторная №4', 'Работа с процессами в Linux', NULL, 'checked', 'Превосходно!', '5', '2026-05-17 10:03:04', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `grades`
--

CREATE TABLE `grades` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `group_name` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `teacher_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `student_name`, `group_name`, `subject`, `grade`, `date`, `teacher_id`, `created_at`) VALUES
(1, 4, 'Сидоров Алексей', 'РУПО 26-21', 'Математический анализ', '5', '2026-01-15', 2, '2026-05-17 10:02:49'),
(2, 4, 'Сидоров Алексей', 'РУПО 26-21', 'Программирование', '4', '2026-01-16', 2, '2026-05-17 10:02:49'),
(3, 5, 'Козлов Дмитрий', 'РУПО 26-21', 'Математический анализ', '4', '2026-01-15', 2, '2026-05-17 10:02:49'),
(4, 5, 'Козлов Дмитрий', 'РУПО 26-21', 'Физика', '5', '2026-01-17', 3, '2026-05-17 10:02:49'),
(5, 6, 'Новикова Мария', 'РУПО 26-22', 'Дискретная математика', '5', '2026-01-18', 2, '2026-05-17 10:02:49'),
(6, 6, 'Новикова Мария', 'РУПО 26-22', 'Веб-разработка', '5', '2026-01-19', 3, '2026-05-17 10:02:49'),
(7, 7, 'Петров Иван', 'РУПО 26-22', 'Дискретная математика', '4', '2026-01-18', 2, '2026-05-17 10:02:49'),
(8, 8, 'Соколова Елена', 'ИБ 26-21', 'Алгоритмы и структуры', '5', '2026-01-15', 2, '2026-05-17 10:02:49'),
(9, 8, 'Соколова Елена', 'ИБ 26-21', 'Сети и телеком', '4', '2026-01-16', 3, '2026-05-17 10:02:49'),
(10, 9, 'Михайлов Андрей', 'ИБ 26-21', 'Алгоритмы и структуры', '4', '2026-01-15', 2, '2026-05-17 10:02:49'),
(11, 10, 'Федорова Ольга', 'ИБ 26-22', 'Операционные системы', '5', '2026-01-20', 2, '2026-05-17 10:02:49'),
(12, 10, 'Федорова Ольга', 'ИБ 26-22', 'Компиляторы', '4', '2026-01-21', 3, '2026-05-17 10:02:49');

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE `groups` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `course` int NOT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `student_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id`, `name`, `course`, `specialty`, `student_count`, `created_at`) VALUES
(1, 'ССА 26-21', 1, '09.02.06 Сетевое и системное администрирование', 0, '2026-05-17 10:02:15'),
(2, 'ССА 26-22', 1, '09.02.06 Сетевое и системное администрирование', 0, '2026-05-17 10:02:15'),
(3, 'РУПО 26-21', 1, '09.02.11 Разработка и управление программным обеспечением', 2, '2026-05-17 10:02:15'),
(4, 'РУПО 26-22', 1, '09.02.11 Разработка и управление программным обеспечением', 2, '2026-05-17 10:02:15'),
(5, 'ИРПТ 26-21', 1, '09.02.13 Интеграция решений с применением технологий ИИ', 0, '2026-05-17 10:02:15'),
(6, 'ИРПТ 26-22', 1, '09.02.13 Интеграция решений с применением технологий ИИ', 0, '2026-05-17 10:02:15'),
(7, 'ИБ 26-21', 1, '10.02.05 Обеспечение информационной безопасности', 2, '2026-05-17 10:02:15'),
(8, 'ИБ 26-22', 1, '10.02.05 Обеспечение информационной безопасности', 1, '2026-05-17 10:02:15'),
(9, 'ОПУТ 26-21', 1, '23.02.01 Организация перевозок и управление на транспорте', 0, '2026-05-17 10:02:15'),
(10, 'ОПУТ 26-22', 1, '23.02.01 Организация перевозок и управление на транспорте', 0, '2026-05-17 10:02:15'),
(11, 'БПЛА 26-21', 1, '25.02.08 Эксплуатация беспилотных авиационных систем', 0, '2026-05-17 10:02:15'),
(12, 'БПЛА 26-22', 1, '25.02.08 Эксплуатация беспилотных авиационных систем', 0, '2026-05-17 10:02:15'),
(13, 'РЕК 26-21', 1, '42.02.01 Реклама', 0, '2026-05-17 10:02:15'),
(14, 'РЕК 26-22', 1, '42.02.01 Реклама', 0, '2026-05-17 10:02:15'),
(15, 'ДИЗ 26-21', 1, '54.02.01 Дизайн (по отраслям)', 0, '2026-05-17 10:02:15'),
(16, 'ДИЗ 26-22', 1, '54.02.01 Дизайн (по отраслям)', 0, '2026-05-17 10:02:15'),
(17, 'ДПНИ 26-21', 1, '54.02.02 Декоративно-прикладное искусство и народные промыслы', 0, '2026-05-17 10:02:15'),
(18, 'ДПНИ 26-22', 1, '54.02.02 Декоративно-прикладное искусство и народные промыслы', 0, '2026-05-17 10:02:15'),
(19, 'РЕС 26-21', 1, '54.01.04 Реставрация', 0, '2026-05-17 10:02:15'),
(20, 'РЕС 26-22', 1, '54.01.04 Реставрация', 0, '2026-05-17 10:02:15'),
(21, 'ЮВЕ 26-21', 1, '54.01.02 Ювелир', 0, '2026-05-17 10:02:15'),
(22, 'ЮВЕ 26-22', 1, '54.01.02 Ювелир', 0, '2026-05-17 10:02:15'),
(23, 'ХМЖ 26-21', 1, '54.01.12 Художник миниатюрной живописи', 0, '2026-05-17 10:02:15'),
(24, 'ХМЖ 26-22', 1, '54.01.12 Художник миниатюрной живописи', 0, '2026-05-17 10:02:15'),
(25, 'ГДИЗ 26-21', 1, '54.01.20 Графический дизайнер', 0, '2026-05-17 10:02:15'),
(26, 'ГДИЗ 26-22', 1, '54.01.20 Графический дизайнер', 0, '2026-05-17 10:02:15'),
(27, 'ОЭВМ 26-21', 1, '16999 Оператор ЭВМ', 0, '2026-05-17 10:02:15'),
(28, 'ОЭВМ 26-22', 1, '16999 Оператор ЭВМ', 0, '2026-05-17 10:02:15');

-- --------------------------------------------------------

--
-- Структура таблицы `journal`
--

CREATE TABLE `journal` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `group_name` varchar(100) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL,
  `teacher_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `journal`
--

INSERT INTO `journal` (`id`, `student_id`, `student_name`, `group_name`, `subject`, `date`, `status`, `teacher_id`, `created_at`) VALUES
(1, 4, 'Сидоров Алексей', 'РУПО 26-21', 'Математический анализ', '2026-01-15', 'present', 2, '2026-05-17 10:03:22'),
(2, 4, 'Сидоров Алексей', 'РУПО 26-21', 'Программирование', '2026-01-16', 'present', 2, '2026-05-17 10:03:22'),
(3, 5, 'Козлов Дмитрий', 'РУПО 26-21', 'Математический анализ', '2026-01-15', 'late', 2, '2026-05-17 10:03:22'),
(4, 5, 'Козлов Дмитрий', 'РУПО 26-21', 'Физика', '2026-01-17', 'present', 3, '2026-05-17 10:03:22'),
(5, 6, 'Новикова Мария', 'РУПО 26-22', 'Дискретная математика', '2026-01-18', 'present', 2, '2026-05-17 10:03:22'),
(6, 7, 'Петров Иван', 'РУПО 26-22', 'Дискретная математика', '2026-01-18', 'absent', 2, '2026-05-17 10:03:22'),
(7, 8, 'Соколова Елена', 'ИБ 26-21', 'Алгоритмы и структуры', '2026-01-15', 'present', 2, '2026-05-17 10:03:22'),
(8, 9, 'Михайлов Андрей', 'ИБ 26-21', 'Сети и телеком', '2026-01-16', 'excused', 3, '2026-05-17 10:03:22'),
(9, 10, 'Федорова Ольга', 'ИБ 26-22', 'Операционные системы', '2026-01-20', 'present', 2, '2026-05-17 10:03:22');

-- --------------------------------------------------------

--
-- Структура таблицы `library`
--

CREATE TABLE `library` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` enum('document','presentation','video','code','other') DEFAULT 'document',
  `uploaded_by` int DEFAULT NULL,
  `group_name` varchar(100) DEFAULT NULL,
  `is_public` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `library`
--

INSERT INTO `library` (`id`, `title`, `description`, `file_path`, `file_type`, `uploaded_by`, `group_name`, `is_public`, `created_at`) VALUES
(1, 'Методические указания по Матану', 'Рекомендации по выполнению домашних заданий', NULL, 'document', 2, 'РУПО 26-21', 1, '2026-05-17 10:03:54'),
(2, 'Презентация по Физике', 'Лекция о квантовой механике', NULL, 'presentation', 3, 'РУПО 26-21', 1, '2026-05-17 10:03:54'),
(3, 'Примеры кода на JavaScript', 'База примеров для лабораторных', NULL, 'code', 2, 'РУПО 26-21', 1, '2026-05-17 10:03:54'),
(4, 'Видеоурок по БД', 'Основы SQL и проектирование БД', NULL, 'video', 3, 'РУПО 26-21', 1, '2026-05-17 10:03:54'),
(5, 'Шпаргалка по Дискретке', 'Основные формулы и теоремы', NULL, 'document', 2, 'РУПО 26-22', 1, '2026-05-17 10:03:54'),
(6, 'Презентация по Веб-разработке', 'HTML, CSS, JavaScript основы', NULL, 'presentation', 3, 'РУПО 26-22', 1, '2026-05-17 10:03:54'),
(7, 'Алгоритмы сортировки', 'Сравнение алгоритмов и их сложность', NULL, 'document', 2, 'ИБ 26-21', 1, '2026-05-17 10:03:54'),
(8, 'Протоколы сети', 'OSI модель и TCP/IP', NULL, 'presentation', 3, 'ИБ 26-21', 1, '2026-05-17 10:03:54'),
(9, 'Linux для начинающих', 'Основные команды и работа с терминалом', NULL, 'document', 2, 'ИБ 26-22', 1, '2026-05-17 10:03:54'),
(10, 'Компиляторы 101', 'Введение в теорию компиляторов', NULL, 'document', 3, 'ИБ 26-22', 1, '2026-05-17 10:03:54');

-- --------------------------------------------------------

--
-- Структура таблицы `locations`
--

CREATE TABLE `locations` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `type` enum('building','cafeteria','bus_stop','other') DEFAULT 'building',
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `locations`
--

INSERT INTO `locations` (`id`, `name`, `lat`, `lng`, `type`, `description`, `created_at`) VALUES
(1, 'Главный корпус', '54.70650000', '20.51080000', 'building', 'ул. Брамса, 9', '2026-05-17 10:26:36'),
(2, 'Корпус 2', '54.70250000', '20.50850000', 'building', 'ул. Спортивная, 6', '2026-05-17 10:26:36'),
(3, 'Корпус 3', '54.70950000', '20.51450000', 'building', 'ул. Озерова, 7', '2026-05-17 10:26:36'),
(4, 'Столовая', '54.70600000', '20.51000000', 'cafeteria', 'Университетская столовая', '2026-05-17 10:26:36'),
(5, 'Автобусная остановка', '54.70700000', '20.51200000', 'bus_stop', 'Остановка \"Университет\"', '2026-05-17 10:26:36');

-- --------------------------------------------------------

--
-- Структура таблицы `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `target_type` enum('all','students','teachers','group') NOT NULL,
  `target_group` varchar(100) DEFAULT NULL,
  `sender_id` int DEFAULT NULL,
  `is_read` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `target_type`, `target_group`, `sender_id`, `is_read`, `created_at`) VALUES
(1, 'Важное объявление', 'Завтра изменено расписание. Проверьте индивидуально!', 'all', NULL, 1, 0, '2026-05-17 10:03:40'),
(2, 'Сдача лабораторной', 'Напоминаем, что дедлайн лабораторной №3 - 25 января', 'students', 'РУПО 26-21', 2, 0, '2026-05-17 10:03:40'),
(3, 'Консультация', 'Состоится консультация по БД в пятницу в 14:00', 'students', 'РУПО 26-21', 3, 0, '2026-05-17 10:03:40'),
(4, 'Новое задание', 'Добавлено новое задание по веб-разработке', 'students', 'РУПО 26-22', 3, 0, '2026-05-17 10:03:40'),
(5, 'Изменение группы', 'Студенты ИБ 26-21 переводятся на новую группу', 'students', 'ИБ 26-21', 1, 0, '2026-05-17 10:03:40'),
(6, 'Собрание преподавателей', 'Еженедельное собрание в понедельник в 15:00', 'teachers', NULL, 1, 0, '2026-05-17 10:03:40');

-- --------------------------------------------------------

--
-- Структура таблицы `schedule`
--

CREATE TABLE `schedule` (
  `id` int NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `teacher_name` varchar(255) DEFAULT NULL,
  `day_of_week` enum('понедельник','вторник','среда','четверг','пятница','суббота') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `classroom` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `schedule`
--

INSERT INTO `schedule` (`id`, `group_name`, `subject`, `teacher_name`, `day_of_week`, `start_time`, `end_time`, `classroom`, `created_at`) VALUES
(1, 'РУПО 26-21', 'Математический анализ', 'Иванов П.С.', 'понедельник', '09:00:00', '10:30:00', '301', '2026-05-17 10:02:32'),
(2, 'РУПО 26-21', 'Физика', 'Смирнова А.В.', 'понедельник', '11:00:00', '12:30:00', '205', '2026-05-17 10:02:32'),
(3, 'РУПО 26-21', 'Программирование', 'Иванов П.С.', 'вторник', '09:00:00', '10:30:00', '401', '2026-05-17 10:02:32'),
(4, 'РУПО 26-21', 'Базы данных', 'Смирнова А.В.', 'среда', '11:00:00', '12:30:00', '302', '2026-05-17 10:02:32'),
(5, 'РУПО 26-22', 'Дискретная математика', 'Иванов П.С.', 'четверг', '09:00:00', '10:30:00', '301', '2026-05-17 10:02:32'),
(6, 'РУПО 26-22', 'Веб-разработка', 'Смирнова А.В.', 'пятница', '11:00:00', '12:30:00', '401', '2026-05-17 10:02:32'),
(7, 'ИБ 26-21', 'Алгоритмы и структуры', 'Иванов П.С.', 'понедельник', '13:00:00', '14:30:00', '303', '2026-05-17 10:02:32'),
(8, 'ИБ 26-21', 'Сети и телеком', 'Смирнова А.В.', 'вторник', '09:00:00', '10:30:00', '201', '2026-05-17 10:02:32'),
(9, 'ИБ 26-22', 'Операционные системы', 'Иванов П.С.', 'среда', '09:00:00', '10:30:00', '304', '2026-05-17 10:02:32'),
(10, 'ИБ 26-22', 'Компиляторы', 'Смирнова А.В.', 'четверг', '11:00:00', '12:30:00', '205', '2026-05-17 10:02:32');

-- --------------------------------------------------------

--
-- Структура таблицы `teachers`
--

CREATE TABLE `teachers` (
  `id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `office` varchar(50) DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `description` text,
  `avatar` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `teacher_reviews`
--

CREATE TABLE `teacher_reviews` (
  `id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `student_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Структура таблицы `tips`
--

CREATE TABLE `tips` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` enum('exam','coursework','lab','general','lifehack') DEFAULT 'general',
  `author_id` int DEFAULT NULL,
  `votes` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `tips`
--

INSERT INTO `tips` (`id`, `title`, `content`, `category`, `author_id`, `votes`, `created_at`) VALUES
(1, 'Как подготовиться к сессии за неделю', 'Разбей материал на части, учи по 2-3 темы в день, не зубри всё в последнюю ночь', 'exam', NULL, 15, '2026-05-17 10:26:36'),
(2, 'Автомат по лабам', 'Сделай все лабы вовремя, не копируй у одногруппников, преподаватели видят', 'lab', NULL, 24, '2026-05-17 10:26:36'),
(3, 'Где поесть дешево', 'Столовая в главном корпусе - завтраки до 10:00 со скидкой 20%', 'lifehack', NULL, 8, '2026-05-17 10:26:36'),
(4, 'Курсовая за 3 дня', 'Бери готовые работы из библиотеки, переделывай введение и заключение, добавляй свои данные', 'coursework', NULL, 33, '2026-05-17 10:26:36'),
(5, 'авава', 'ваыаыаываываываываываываываываываываываываываыыв', 'coursework', 1, 0, '2026-05-17 14:47:34');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `group_name` varchar(100) DEFAULT NULL,
  `course` int DEFAULT '1',
  `email` varchar(255) DEFAULT NULL,
  `accessibility_settings` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `password`, `role`, `group_name`, `course`, `email`, `accessibility_settings`, `created_at`) VALUES
(1, 'Администратор Системы', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, 'admin@studapp.ru', NULL, '2026-05-17 10:01:58'),
(2, 'Иванов Петр Сергеевич', 'teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, NULL, 'ivanov@studapp.ru', NULL, '2026-05-17 10:01:58'),
(3, 'Смирнова Анна Викторовна', 'teacher2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NULL, NULL, 'smirnova@studapp.ru', NULL, '2026-05-17 10:01:58'),
(4, 'Сидоров Алексей Иванович', 'student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'РУПО 26-21', 1, 'sidorov@studapp.ru', NULL, '2026-05-17 10:01:58'),
(5, 'Козлов Дмитрий Михайлович', 'student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'РУПО 26-21', 1, 'kozlov@studapp.ru', NULL, '2026-05-17 10:01:58'),
(6, 'Новикова Мария Александровна', 'student3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'РУПО 26-22', 1, 'novikova@studapp.ru', NULL, '2026-05-17 10:01:58'),
(7, 'Петров Иван Сергеевич', 'student4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'РУПО 26-22', 1, 'petrov@studapp.ru', NULL, '2026-05-17 10:01:58'),
(8, 'Соколова Елена Дмитриевна', 'student5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ИБ 26-21', 1, 'sokolova@studapp.ru', NULL, '2026-05-17 10:01:58'),
(9, 'Михайлов Андрей Владимирович', 'student6', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ИБ 26-21', 1, 'mikhailov@studapp.ru', NULL, '2026-05-17 10:01:58'),
(10, 'Федорова Ольга Николаевна', 'student7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ИБ 26-22', 1, 'fedorova@studapp.ru', NULL, '2026-05-17 10:01:58');

-- --------------------------------------------------------

--
-- Структура таблицы `works`
--

CREATE TABLE `works` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` enum('coursework','lab','referat','other') DEFAULT 'coursework',
  `uploaded_by` int DEFAULT NULL,
  `group_name` varchar(100) DEFAULT NULL,
  `is_public` tinyint DEFAULT '1',
  `downloads` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Индексы таблицы `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `journal`
--
ALTER TABLE `journal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Индексы таблицы `library`
--
ALTER TABLE `library`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Индексы таблицы `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Индексы таблицы `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `teacher_reviews`
--
ALTER TABLE `teacher_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Индексы таблицы `tips`
--
ALTER TABLE `tips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Индексы таблицы `works`
--
ALTER TABLE `works`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT для таблицы `journal`
--
ALTER TABLE `journal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `library`
--
ALTER TABLE `library`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `teacher_reviews`
--
ALTER TABLE `teacher_reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tips`
--
ALTER TABLE `tips`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `works`
--
ALTER TABLE `works`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `journal`
--
ALTER TABLE `journal`
  ADD CONSTRAINT `journal_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `library`
--
ALTER TABLE `library`
  ADD CONSTRAINT `library_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `teacher_reviews`
--
ALTER TABLE `teacher_reviews`
  ADD CONSTRAINT `teacher_reviews_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_reviews_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `tips`
--
ALTER TABLE `tips`
  ADD CONSTRAINT `tips_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `works`
--
ALTER TABLE `works`
  ADD CONSTRAINT `works_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
