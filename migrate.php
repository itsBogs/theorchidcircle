<?php
require_once __DIR__ . '/db.php';

try {
    $pdo->exec("ALTER TABLE profiles ADD COLUMN IF NOT EXISTS age INT DEFAULT NULL");
    echo "Added age column.\n";
    $pdo->exec("ALTER TABLE profiles ADD COLUMN IF NOT EXISTS height VARCHAR(20) DEFAULT NULL");
    echo "Added height column.\n";
    $pdo->exec("ALTER TABLE profiles ADD COLUMN IF NOT EXISTS waist VARCHAR(20) DEFAULT NULL");
    echo "Added waist column.\n";
    $pdo->exec("ALTER TABLE profiles ADD COLUMN IF NOT EXISTS cup_size VARCHAR(10) DEFAULT NULL");
    echo "Added cup_size column.\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS profile_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        profile_id INT NOT NULL,
        image VARCHAR(255) NOT NULL,
        sort_order INT DEFAULT 0,
        FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE
    )");
    echo "Created profile_images table.\n";
    echo "Schema updated successfully!";
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
