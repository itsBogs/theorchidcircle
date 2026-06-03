<?php
require 'db.php';
try {
    $pdo->exec("ALTER TABLE messages ADD COLUMN session_id VARCHAR(64) DEFAULT NULL AFTER id");
    echo "Added session_id\n";
} catch (Exception $e) {
    echo "session_id exists or error: " . $e->getMessage() . "\n";
}

// Generate a dummy session for existing messages so they don't break
$pdo->exec("UPDATE messages SET session_id = 'global_legacy' WHERE session_id IS NULL");
echo "Done.\n";
