<?php
require_once __DIR__ . '/db.php';

// Create auto_replies table
$pdo->exec("CREATE TABLE IF NOT EXISTS auto_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(255) NOT NULL,
    reply TEXT NOT NULL,
    is_default TINYINT(1) DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
echo "Created auto_replies table.\n";

// Seed default auto-replies
$replies = [
    ['hello', 'Hello! 👋 Welcome to Orchidcircle. How can I help you today?', 0],
    ['hi', 'Hi there! 😊 Welcome to Orchidcircle! Feel free to ask anything.', 0],
    ['book', 'To book a profile, just click on the girl you like and hit the "Book Now" button. You can also tell me which profile you\'re interested in!', 0],
    ['price', 'Our rates vary per profile. Please check each profile card for their hourly rate. You can also ask about a specific girl!', 0],
    ['rate', 'Each of our companions has their own rate displayed on their profile. Click on any profile to see full details and pricing.', 0],
    ['available', 'You can see all currently available companions on our main page. Profiles marked as "Available" are ready to be booked!', 0],
    ['vip', 'VIP codes unlock exclusive premium profiles! If you have a code, enter it in the VIP Access bar on the main page. Contact admin for VIP access.', 0],
    ['contact', 'You can reach our admin directly through this chat! Just type your message and our team will respond shortly.', 0],
    ['location', 'For location and meetup details, please submit a booking request first. Our admin will coordinate everything with you privately.', 0],
    ['payment', 'Payment details will be discussed after your booking is confirmed by our admin. We accept various payment methods.', 0],
    ['cancel', 'To cancel or modify a booking, please message us here with your booking details and our admin will assist you.', 0],
    ['thanks', 'You\'re welcome! 😊 Don\'t hesitate to reach out if you need anything else.', 0],
    ['thank you', 'You\'re welcome! 😊 We\'re happy to help. Enjoy your experience with Orchidcircle!', 0],
    ['how', 'Great question! Browse our profiles, pick your favorite, click "Book Now", and fill out the form. Our admin will confirm your booking shortly!', 0],
    ['good morning', 'Good morning! ☀️ Welcome to Orchidcircle. How may we assist you today?', 0],
    ['good evening', 'Good evening! 🌙 Welcome to Orchidcircle. Looking for companionship tonight?', 0],
    ['', 'Thank you for your message! 💬 Our admin will review and reply shortly. In the meantime, feel free to browse our available profiles!', 1],
];

$stmt = $pdo->prepare('INSERT INTO auto_replies (keyword, reply, is_default, status) VALUES (?,?,?,?)');
foreach ($replies as $r) {
    // Check if already exists
    $check = $pdo->prepare('SELECT id FROM auto_replies WHERE keyword=?');
    $check->execute([$r[0]]);
    if (!$check->fetch()) {
        $stmt->execute([$r[0], $r[1], $r[2], 'active']);
        echo "Added auto-reply for: " . ($r[0] ?: '[default]') . "\n";
    }
}

echo "\nAuto-reply seeding complete!";
