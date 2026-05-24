<?php
/**
 * Конфигурация отладки
 * Включать ТОЛЬКО в режиме разработки!
 */

// Включение отображения ошибок (для разработки)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Логирование ошибок (для продакшена)
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../error.log');
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// Время выполнения скрипта (сек)
set_time_limit(30);

// Максимальное время выполнения для тяжелых операций (сек)
// set_time_limit(300);

// Максимальный размер загрузки (МБ)
// ini_set('upload_max_filesize', '20M');
// ini_set('post_max_size', '20M');
