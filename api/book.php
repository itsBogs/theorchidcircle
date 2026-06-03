<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'error'=>'Method not allowed']); exit;
}

$data = $_POST;
// minimal validation
if (empty($data['profile_id']) || empty($data['customer_name']) || empty($data['booking_date'])) {
    http_response_code(400); echo json_encode(['success'=>false,'message'=>'Missing fields']); exit;
}

$stmt = $pdo->prepare('INSERT INTO bookings (profile_id, customer_name, booking_date, hours, message) VALUES (?,?,?,?,?)');
$stmt->execute([ $data['profile_id'], $data['customer_name'], $data['booking_date'], $data['hours'] ?? 1, $data['message'] ?? '' ]);
log_action($pdo, 'New booking by ' . ($data['customer_name'] ?? 'guest'));

echo json_encode(['success'=>true,'message'=>'Booking submitted']);
