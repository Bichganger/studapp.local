#!/bin/bash
# Скрипт для импорта тестовых данных в БД Учеба24
# Запустите: bash import_database.sh

echo "🔧 Импорт базы данных Учеба24..."
echo ""

# 1. Проверка наличия MySQL
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL не най! Установите MySQL/MariaDB"
    exit 1
fi

echo "✅ MySQL доступен"

# 2. Создаем базу данных
echo ""
echo "📦 Создание базы данных studapp..."
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS studapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ $? -eq 0 ]; then
    echo "✅ База данных создана"
else
    echo "❌ Ошибка создания БД"
    exit 1
fi

# 3. Импортируем данные
echo ""
echo "📥 Импорт тестовых данных..."
mysql -u root -p studapp < full_database.sql

if [ $? -eq 0 ]; then
    echo "✅ Данные импортированы"
else
    echo "❌ Ошибка импорта"
    exit 1
fi

# 4. Проверяем
echo ""
echo "📊 Проверка данных..."
mysql -u root -p studapp -e "
SELECT 'Пользователи:' as 'Таблица', COUNT(*) as 'Записи' FROM users
UNION ALL
SELECT 'Группы:', COUNT(*) FROM groups
UNION ALL
SELECT 'Расписание:', COUNT(*) FROM schedule
UNION ALL
SELECT 'Оценки:', COUNT(*) FROM grades
UNION ALL
SELECT 'Задания:', COUNT(*) FROM assignments
UNION ALL
SELECT 'Журнал:', COUNT(*) FROM journal
UNION ALL
SELECT 'Уведомления:', COUNT(*) FROM notifications
UNION ALL
SELECT 'Библиотека:', COUNT(*) FROM library;
"

echo ""
echo "🎉 Импорт завершен успешно!"
echo ""
echo "🔐 Тестовые учетные записи:"
echo "   Admin: admin / admin123"
echo "   Teacher: teacher1 / teacher123"
echo "   Student: student1 / student123"
echo ""
echo "🌐 Откройте: http://localhost/frontend/"
