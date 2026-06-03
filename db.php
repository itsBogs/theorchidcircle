<?php
// DB and helper functions
session_start();

// Configure these for your environment
$DB_HOST = '127.0.0.1';
$DB_NAME = 'orchidcircle';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    die('DB connection failed: ' . $e->getMessage());
}

function is_admin() {
    return !empty($_SESSION['admin_id']);
}

function require_admin() {
    if (!is_admin()) {
        header('Location: ../admin/login.php');
        exit;
    }
}

function log_action($pdo, $action) {
    $stmt = $pdo->prepare('INSERT INTO logs (action, created_at) VALUES (?, NOW())');
    $stmt->execute([$action]);
}
