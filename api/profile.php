<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { echo json_encode(['error' => 'No ID']); exit; }

$stmt = $pdo->prepare('SELECT * FROM profiles WHERE id=?');
$stmt->execute([$id]);
$profile = $stmt->fetch();

if (!$profile) { echo json_encode(['error' => 'Not found']); exit; }

// Fetch gallery images
$imgs = $pdo->prepare('SELECT image FROM profile_images WHERE profile_id=? ORDER BY sort_order ASC');
$imgs->execute([$id]);
$profile['gallery'] = $imgs->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($profile);
