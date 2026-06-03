<?php
require 'db.php';
try {
    $stmt = $pdo->query('SHOW TABLES');
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
    echo "\n";
    $stmt2 = $pdo->query('SELECT * FROM profile_images');
    print_r($stmt2->fetchAll());
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
