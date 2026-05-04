# Система регистрации

## Архитектура

- `users` - общая таблица пользователей (студенты, преподаватели, администраторы)
- `students` - информация о студентах (ФИО, группа, курс)
- `student_accounts` - связь пользователей со студентами

## Регистрация студентов

Студент вводит:
- ФИО
- Номер группы
- Курс
- Логин и пароль

Система проверяет:
- Все поля заполнены
- Пароли совпадают
- Логин свободен
- Студент есть в таблице students (по ФИО + группе + курсу)
- Студент ещё не зарегистрирован

## Добавление преподавателей

Преподаватели не могут регистрироваться самостоятельно. Добавляются через админ-панель или напрямую в БД.

## Структура БД

```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    group_id INT DEFAULT NULL,
    group_name VARCHAR(100) DEFAULT NULL,
    course INT DEFAULT 1,
    specialty VARCHAR(100) DEFAULT NULL,
    status ENUM('active', 'suspended', 'graduated', 'expelled') DEFAULT 'active'
);

CREATE TABLE student_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    student_id INT NOT NULL UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
```

## Инициализация

```bash
mysql -u root -p studapp_db < database/04_create_students_table.sql
```

```sql
INSERT INTO students (full_name, group_name, course, specialty, status) VALUES
('Иванов Иван Петрович', 'ИТ-321', 3, 'Информационные технологии', 'active');
```

## Безопасность

- Пароли хешируются через password_hash()
- SQL-инъекции предотвращаются через PDO prepare/execute
- XSS предотвращается через htmlspecialchars()
