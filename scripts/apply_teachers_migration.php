<?php
/**
 * Скрипт для применения миграции базы данных для системы преподавателей
 * Запустить один раз через браузер или CLI: php apply_teachers_migration.php
 */

require_once 'config/db.php';

$migrationFile = 'database_optimize_teachers.sql';

if (!file_exists($migrationFile)) {
    die("Файл миграции не найден: $migrationFile\n");
}

$sql = file_get_contents($migrationFile);
$statements = array_filter(array_map('trim', explode(';', $sql)));

echo "Применение миграции для системы преподавателей...\n\n";

foreach ($statements as $index => $statement) {
    if (empty($statement)) continue;
    
    try {
        $pdo->exec($statement);
        echo "[" . ($index + 1) . "] OK: " . substr($statement, 0, 60) . "...\n";
    } catch (PDOException $e) {
        // Игнорируем ошибки если колонки уже существуют
        if (strpos($e->getMessage(), 'Duplicate column') !== false || 
            strpos($e->getMessage(), 'already exists') !== false) {
            echo "[" . ($index + 1) . "] SKIP (уже существует): " . substr($statement, 0, 50) . "...\n";
        } else {
            echo "[" . ($index + 1) . "] ERROR: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nМиграция завершена!\n";
echo "\nТеперь можно использовать:\n";
echo "- Страницу модерации отзывов: /admin/teacher_reviews.php\n";
echo "- Обновлённую страницу преподавателей: /student/teachers.php\n";
echo "- Управление преподавателями: /admin/manage_teachers.php\n";
