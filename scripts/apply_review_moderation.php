<?php
/**
 * Скрипт для добавления колонок модерации отзывов
 * Запустить один раз через браузер или CLI
 */

require_once __DIR__ . '/../config/db.php';

echo "=== Добавление колонок модерации отзывов ===\n\n";

try {
    // Проверяем существующие колонки
    $cols = $pdo->query("SHOW COLUMNS FROM teacher_reviews")->fetchAll(PDO::FETCH_COLUMN);
    
    $columnsToAdd = [];
    
    if (!in_array('is_approved', $cols)) {
        $columnsToAdd[] = "is_approved TINYINT DEFAULT 0";
    }
    if (!in_array('is_hidden', $cols)) {
        $columnsToAdd[] = "is_hidden TINYINT DEFAULT 0";
    }
    if (!in_array('moderated_at', $cols)) {
        $columnsToAdd[] = "moderated_at TIMESTAMP NULL DEFAULT NULL";
    }
    if (!in_array('moderated_by', $cols)) {
        $columnsToAdd[] = "moderated_by INT DEFAULT NULL";
    }
    
    if (empty($columnsToAdd)) {
        echo "✓ Все колонки уже существуют!\n";
    } else {
        echo "Добавляем колонки: " . implode(', ', $columnsToAdd) . "\n";
        
        $sql = "ALTER TABLE teacher_reviews ADD COLUMN " . implode(", ADD COLUMN ", $columnsToAdd);
        $pdo->exec($sql);
        echo "✓ Колонки добавлены успешно!\n";
    }
    
    // Создаём индексы
    $indexes = [
        ['name' => 'idx_reviews_approved', 'sql' => "CREATE INDEX idx_reviews_approved ON teacher_reviews(is_approved)"],
        ['name' => 'idx_reviews_hidden', 'sql' => "CREATE INDEX idx_reviews_hidden ON teacher_reviews(is_hidden)"],
        ['name' => 'idx_reviews_teacher_approved', 'sql' => "CREATE INDEX idx_reviews_teacher_approved ON teacher_reviews(teacher_id, is_approved)"]
    ];
    
    foreach ($indexes as $index) {
        try {
            $pdo->exec($index['sql']);
            echo "✓ Индекс {$index['name']} создан\n";
        } catch (Exception $e) {
            echo "✓ Индекс {$index['name']} уже существует\n";
        }
    }
    
    // Одобрим все существующие отзывы
    $updated = $pdo->exec("UPDATE teacher_reviews SET is_approved = 1 WHERE is_approved IS NULL");
    echo "✓ Одобрено старых отзывов: $updated\n";
    
    // Обновляем рейтинги
    $pdo->exec("
        UPDATE teachers t 
        SET avg_rating = (
            SELECT ROUND(AVG(rating), 1) 
            FROM teacher_reviews 
            WHERE teacher_id = t.id 
            AND is_approved = 1 
            AND is_hidden = 0
        )
        WHERE EXISTS (
            SELECT 1 FROM teacher_reviews 
            WHERE teacher_id = t.id 
            AND is_approved = 1 
            AND is_hidden = 0
        )
    ");
    echo "✓ Рейтинги преподавателей обновлены\n";
    
    // Вывод статистики
    $total = $pdo->query("SELECT COUNT(*) FROM teacher_reviews")->fetchColumn();
    $approved = $pdo->query("SELECT COUNT(*) FROM teacher_reviews WHERE is_approved = 1")->fetchColumn();
    $pending = $pdo->query("SELECT COUNT(*) FROM teacher_reviews WHERE is_approved = 0")->fetchColumn();
    
    echo "\n=== Статистика ===\n";
    echo "Всего отзывов: $total\n";
    echo "Одобрено: $approved\n";
    echo "Ожидают модерации: $pending\n";
    
    echo "\n=== Миграция завершена! ===\n";
    echo "\nТеперь можно использовать:\n";
    echo "- Страницу модерации: /admin/teacher_reviews.php\n";
    echo "- Страницу преподавателей: /student/teachers.php\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}
