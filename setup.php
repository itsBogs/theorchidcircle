<?php
require_once __DIR__ . '/db.php';

// Create tables
$sql = file_get_contents(__DIR__ . '/install.sql');
try {
    $pdo->exec($sql);
    echo "Tables created or already exist.<br>";
} catch (Exception $e) {
    echo "Error creating tables: " . $e->getMessage();
}

// Create default admin if not exists
$stmt = $pdo->prepare('SELECT id FROM admins WHERE username = ?');
$stmt->execute(['admin']);
if (!$stmt->fetch()) {
    $pass = password_hash('ChangeMe123!', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO admins (username, password) VALUES (?, ?)');
    $stmt->execute(['admin', $pass]);
    echo "Default admin created (username: admin, password: ChangeMe123!).<br>";
} else {
    echo "Admin user already exists.<br>";
}

echo "Setup complete. Remove or protect setup.php after use.";
