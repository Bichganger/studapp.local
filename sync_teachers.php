<?php
/**
 * Синхронизация данных
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// Проверка: только админ
if (($_SESSION['role'] ?? '') !== 'admin') {
    die('Доступ запрещён. Требуется роль администратора.');
}

$output = [];
$errors = [];

try {
    // 1. Добавление колонок если нет
    $columns = ['campus', 'is_strict', 'auto_exam', 'avg_rating'];
    foreach ($columns as $col) {
        $stmt = $pdo->query("SHOW COLUMNS FROM teachers LIKE '$col'");
        if ($stmt->rowCount() == 0) {
            switch ($col) {
                case 'campus':
                    $pdo->exec("ALTER TABLE teachers ADD COLUMN campus VARCHAR(100) DEFAULT NULL AFTER specialty");
                    $output[] = "✓ Добавлена колонка: campus";
                    break;
                case 'is_strict':
                    $pdo->exec("ALTER TABLE teachers ADD COLUMN is_strict TINYINT DEFAULT 0 AFTER campus");
                    $output[] = "✓ Добавлена колонка: is_strict";
                    break;
                case 'auto_exam':
                    $pdo->exec("ALTER TABLE teachers ADD COLUMN auto_exam TINYINT DEFAULT 0 AFTER is_strict");
                    $output[] = "✓ Добавлена колонка: auto_exam";
                    break;
                case 'avg_rating':
                    $pdo->exec("ALTER TABLE teachers ADD COLUMN avg_rating DECIMAL(3,1) DEFAULT 0.0 AFTER auto_exam");
                    $output[] = "✓ Добавлена колонка: avg_rating";
                    break;
            }
        }
    }
    
    // 2. Добавление преподавателей
    $teachers = [
        ['full_name' => 'Иванов Петр Сергеевич', 'short_name' => 'Иванов П.С.', 'email' => 'ivanov@studapp.ru', 'phone' => '+7 (4012) 55-33-22', 'office' => '301', 'specialty' => 'Математический анализ, Программирование, Базы данных', 'description' => 'Добросовестный преподаватель, требует выполнения всех заданий.', 'campus' => 'Брамса 9', 'is_strict' => 0, 'auto_exam' => 1, 'avg_rating' => 4.5],
        ['full_name' => 'Смирнова Анна Викторовна', 'short_name' => 'Смирнова А.В.', 'email' => 'smirnova@studapp.ru', 'phone' => '+7 (4012) 55-33-23', 'office' => '205', 'specialty' => 'Физика, Веб-разработка', 'description' => 'Требовательный преподаватель, но справедливый.', 'campus' => 'Спортивная 6', 'is_strict' => 1, 'auto_exam' => 0, 'avg_rating' => 4.2],
        ['full_name' => 'Петрова Елена Николаевна', 'short_name' => 'Петрова Е.Н.', 'email' => 'petrova@studapp.ru', 'phone' => '+7 (4012) 55-33-24', 'office' => '402', 'specialty' => 'Дискретная математика, Алгоритмы', 'description' => 'Очень добрая, даёт автоматы за активную работу.', 'campus' => 'Брамса 9', 'is_strict' => 0, 'auto_exam' => 1, 'avg_rating' => 4.8],
        ['full_name' => 'Козлов Дмитрий Александрович', 'short_name' => 'Козлов Д.А.', 'email' => 'kozlov@studapp.ru', 'phone' => '+7 (4012) 55-33-25', 'office' => '305', 'specialty' => 'Базы данных, Системное программирование', 'description' => 'Строгий, но честный. Помогает разобраться.', 'campus' => 'Спортивная 6', 'is_strict' => 1, 'auto_exam' => 0, 'avg_rating' => 3.9],
        ['full_name' => 'Соколова Мария Игоревна', 'short_name' => 'Соколова М.И.', 'email' => 'sokolova@studapp.ru', 'phone' => '+7 (4012) 55-33-26', 'office' => '210', 'specialty' => 'Веб-разработка, Графический дизайн', 'description' => 'Творческий подход, много практических заданий.', 'campus' => 'Озерова 7', 'is_strict' => 0, 'auto_exam' => 1, 'avg_rating' => 4.6],
        ['full_name' => 'Новиков Сергей Владимирович', 'short_name' => 'Новиков С.В.', 'email' => 'novikov@studapp.ru', 'phone' => '+7 (4012) 55-33-27', 'office' => '108', 'specialty' => 'Сети и телекоммуникации, Кибербезопасность', 'description' => 'Опытный преподаватель, много практики.', 'campus' => 'Брамса 9', 'is_strict' => 0, 'auto_exam' => 0, 'avg_rating' => 4.3],
    ];
    
    foreach ($teachers as $t) {
        $stmt = $pdo->prepare("INSERT INTO teachers (full_name, short_name, email, phone, office, specialty, description, campus, is_strict, auto_exam, avg_rating, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), specialty=VALUES(specialty), campus=VALUES(campus)");
        $stmt->execute([$t['full_name'], $t['short_name'], $t['email'], $t['phone'], $t['office'], $t['specialty'], $t['description'], $t['campus'], $t['is_strict'], $t['auto_exam'], $t['avg_rating']]);
    }
    $output[] = "✓ Добавлено/обновлено " . count($teachers) . " преподавателей";
    
    // 3. Создание таблицы teacher_reviews
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS teacher_reviews (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, teacher_id INT NOT NULL, student_id INT DEFAULT NULL, rating DECIMAL(3,1) NOT NULL, review_text TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $output[] = "✓ Таблица teacher_reviews создана/обновлена";
    } catch (Exception $e) {
        $errors[] = "⚠ teacher_reviews: " . $e->getMessage();
    }
    
    // 4. Создание таблицы teacher_tips
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS teacher_tips (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, teacher_id INT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, category ENUM('study','exam','lab','career','general') DEFAULT 'general', votes INT DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $output[] = "✓ Таблица teacher_tips создана/обновлена";
    } catch (Exception $e) {
        $errors[] = "⚠ teacher_tips: " . $e->getMessage();
    }
    
    // 5. Синхронизация рейтингов
    $pdo->exec("UPDATE teachers t SET avg_rating = (SELECT ROUND(AVG(rating), 1) FROM teacher_reviews WHERE teacher_id = t.id) WHERE EXISTS (SELECT 1 FROM teacher_reviews WHERE teacher_id = t.id)");
    $output[] = "✓ Рейтинги синхронизированы";
    
    // 6. Вывод результата
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM teachers");
    $total = $stmt->fetch()['count'];
    $output[] = "📊 Всего преподавателей: $total";
    
} catch (Exception $e) {
    $errors[] = "❌ Ошибка: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Синхронизация преподавателей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="bi bi-check-circle me-2"></i>Синхронизация завершена</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($output)): ?>
            <div class="alert alert-info">
                <h5>Результаты:</h5>
                <ul class="mb-0">
                    <?php foreach ($output as $msg): ?>
                    <li><?= $msg ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
            <div class="alert alert-warning">
                <h5>Предупреждения:</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                    <li><?= $err ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <hr>
            <div class="d-flex gap-2">
                <a href="admin/manage_teachers.php" class="btn btn-primary">
                    <i class="bi bi-person-badge me-2"></i>Управление преподавателями
                </a>
                <a href="student/teachers.php" class="btn btn-outline-secondary" target="_blank">
                    <i class="bi bi-eye me-2"></i>Просмотр (студенты)
                </a>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-2"></i>На главную
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
