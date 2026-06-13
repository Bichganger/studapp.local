<?php
/**
 * Скрипт для добавления тестовых преподавателей
 * Запустить один раз через браузер или CLI
 */

require_once __DIR__ . '/config/db.php';

echo "=== Добавление преподавателей ===\n\n";

$teachers = [
    [
        'full_name' => 'Мамаев Павел Владимирович',
        'short_name' => 'Мамаев П.В.',
        'email' => 'mamayev@studapp.ru',
        'phone' => '+7 (4012) 55-33-30',
        'office' => '305',
        'specialty' => 'Алгоритмизация, C#',
        'description' => 'Преподаватель с глубокими знаниями в программировании. Помогает разобраться со сложными алгоритмами. Даёт много практики на C#.',
        'campus' => 'Брамса 9',
        'is_strict' => 0,
        'auto_exam' => 1
    ],
    [
        'full_name' => 'Лунина Ангелина Владиславовна',
        'short_name' => 'Лунина А.В.',
        'email' => 'lunina@studapp.ru',
        'phone' => '+7 (4012) 55-33-31',
        'office' => '402',
        'specialty' => 'Unity игры',
        'description' => 'Энтузиаст геймдева. Учит создавать игры на Unity с нуля. Всегда готова помочь с проектами.',
        'campus' => 'Спортивная 6',
        'is_strict' => 0,
        'auto_exam' => 1
    ],
    [
        'full_name' => 'Гегель Пауль Викторович',
        'short_name' => 'Гегель П.В.',
        'email' => 'gegel@studapp.ru',
        'phone' => '+7 (4012) 55-33-32',
        'office' => '308',
        'specialty' => 'Базы данных',
        'description' => 'Профессионал в области БД. Объясняет сложные темы простым языком. Требователен к качеству кода.',
        'campus' => 'Брамса 9',
        'is_strict' => 1,
        'auto_exam' => 1
    ],
    [
        'full_name' => 'Кузнецова Алёна Сергеевна',
        'short_name' => 'Кузнецова А.С.',
        'email' => 'kuznetsova@studapp.ru',
        'phone' => '+7 (4012) 55-33-33',
        'office' => '203',
        'specialty' => 'Веб-Дизайн',
        'description' => 'Творческий подход к обучению. Учит не только техническим аспектам, но и эстетике веб-дизайна.',
        'campus' => 'Озерова 7',
        'is_strict' => 0,
        'auto_exam' => 0
    ],
    [
        'full_name' => 'Бобылёва Марина Андреевна',
        'short_name' => 'Бобылёва М.А.',
        'email' => 'bobyleva@studapp.ru',
        'phone' => '+7 (4012) 55-33-34',
        'office' => '105',
        'specialty' => 'Английский язык',
        'description' => 'Опытный преподаватель английского. Помогает преодолеть языковой барьер. Много практики разговорной речи.',
        'campus' => 'Спортивная 6',
        'is_strict' => 0,
        'auto_exam' => 0
    ]
];

try {
    // Проверяем существование таблицы teachers
    $pdo->query("SELECT 1 FROM teachers LIMIT 1");
} catch (Exception $e) {
    echo "❌ Таблица teachers не существует! Создайте таблицу сначала.\n";
    exit(1);
}

$added = 0;
$skipped = 0;

foreach ($teachers as $t) {
    try {
        // Проверяем, существует ли уже преподаватель с таким email
        $stmt = $pdo->prepare("SELECT id FROM teachers WHERE email = ?");
        $stmt->execute([$t['email']]);
        
        if ($stmt->fetch()) {
            echo "✓ {$t['full_name']} уже существует (пропущено)\n";
            $skipped++;
            continue;
        }
        
        // Добавляем преподавателя
        $stmt = $pdo->prepare("
            INSERT INTO teachers 
            (full_name, short_name, email, phone, office, specialty, description, campus, is_strict, auto_exam, avg_rating)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
        ");
        
        $stmt->execute([
            $t['full_name'],
            $t['short_name'],
            $t['email'],
            $t['phone'],
            $t['office'],
            $t['specialty'],
            $t['description'],
            $t['campus'],
            $t['is_strict'],
            $t['auto_exam']
        ]);
        
        echo "✓ Добавлен: {$t['full_name']} ({$t['specialty']})\n";
        $added++;
        
    } catch (Exception $e) {
        echo "❌ Ошибка добавления {$t['full_name']}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Готово! ===\n";
echo "Добавлено преподавателей: $added\n";
echo "Пропущено (уже существуют): $skipped\n";
echo "\nТеперь можно просмотреть их на странице /admin/manage_teachers.php\n";
