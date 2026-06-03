<?php
require_once __DIR__ . '/../db.php';
$code = $_GET['code'] ?? '';
$stmt = $pdo->prepare('SELECT * FROM vip_codes WHERE code=? AND status="active"');
$stmt->execute([$code]);
$ok = (bool)$stmt->fetch();
echo json_encode(['valid'=>$ok]);
