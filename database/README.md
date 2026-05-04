# Настройка базы данных

## 1. Создайте базу данных

```sql
CREATE DATABASE studapp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 2. Выполните скрипты в порядке

```bash
mysql -u root -p studapp_db < 01_create_users_table.sql
mysql -u root -p studapp_db < 02_create_groups_table.sql
mysql -u root -p studapp_db < 03_create_assignments_table.sql
mysql -u root -p studapp_db < 04_create_students_table.sql
```

## 3. Добавьте тестового администратора

```sql
INSERT INTO users (username, password, role, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Администратор');
-- Пароль: password
```

## 4. Добавьте тестовых студентов

Таблица `students` уже заполнена тестовыми данными в скрипте 04_create_students_table.sql

Тестовые студенты:
- Иванов Иван Петрович, группа ИТ-321, 3 курс
- Петров Петр Сергеевич, группа ИТ-321, 3 курс
- Сидорова Анна Ивановна, группа ИТ-321, 3 курс
- Козлов Дмитрий Алексеевич, группа ИТ-312, 3 курс
- Смирнова Мария Владимировна, группа ИТ-312, 3 курс

## 5. Проверьте работу

Попробуйте зарегистрировать одного из тестовых студентов через форму регистрации.