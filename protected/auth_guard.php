<?php
// session_start() вызывается в config/app.php при подключении db.php
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../dashboard.php');
    exit;
}
