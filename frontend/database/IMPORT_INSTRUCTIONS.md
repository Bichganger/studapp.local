# Инструкция по импорту базы данных

## 1. Создайте базу данных:
```bash
mysql -u root -p
```
```sql
CREATE DATABASE studapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

## 2. Импортируйте данные:
```bash
mysql -u root -p studapp < frontend/database/full_database.sql
```

## 3. Проверьте импорт:
```bash
mysql -u root -p studapp -e "SELECT COUNT(*) FROM users; SELECT COUNT(*) FROM groups; SELECT COUNT(*) FROM schedule;"
```

## Тестовые учетные записи:

### Администратор:
- Логин: `admin`
- Пароль: `admin123`

### Преподаватель:
- Логин: `teacher1` или `teacher2`
- Пароль: `teacher123`

### Студенты:
- Логин: `student1` - `student7`
- Пароль: `student123`

## Группы:
- ПИ-21 (2 студента)
- ПИ-22 (2 студента)
- ПО-21 (3 студента)
- ПО-22 (1 студент)

## После импорта:
1. Откройте `http://localhost/frontend/`
2. Войдите под любой учетной записью
3. Все данные будут синхронизированы через БД
