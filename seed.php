<?php
require_once __DIR__ . '/db.php';

echo "Seeding database with 8 profiles...\n";

$names = [
    "Elena Rodriguez",
    "Sophia Chen",
    "Isabella Rossi",
    "Mia Thompson",
    "Ava Williams",
    "Chloe Martinez",
    "Lily Anderson",
    "Zoe Taylor"
];

$descriptions = [
    "Professional event companion with a passion for art and culture. Great conversationalist.",
    "Bilingual escort specializing in corporate events and high-end dining experiences.",
    "Friendly and outgoing, perfect for social gatherings and city tours.",
    "Sophisticated and elegant, with a background in fashion and design.",
    "Charming and witty, loves traveling and exploring new cuisines.",
    "Warm and energetic, guaranteed to make your evening unforgettable.",
    "Classy and discreet, ideal for VIP parties and exclusive events.",
    "Articulate and graceful, always the life of the party with a brilliant smile."
];

for ($i = 0; $i < 8; $i++) {
    // We will use randomuser.me portraits for placeholder images
    $imgId = $i + 10; // Use IDs 10 to 17
    $imageUrl = "https://randomuser.me/api/portraits/women/{$imgId}.jpg";
    
    // Create a local filename
    $fileName = "profile_w_{$imgId}.jpg";
    $localPath = __DIR__ . "/uploads/" . $fileName;
    
    // Download image if it doesn't exist
    if (!file_exists($localPath)) {
        $imgData = file_get_contents($imageUrl);
        if ($imgData !== false) {
            file_put_contents($localPath, $imgData);
            echo "Downloaded image: $fileName\n";
        } else {
            echo "Failed to download image: $imageUrl\n";
            $fileName = ''; // fallback
        }
    }
    
    $rate = rand(100, 300) . ".00";
    
    // Insert into database
    $stmt = $pdo->prepare('INSERT INTO profiles (name, description, image, rate_per_hour, status) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $names[$i],
        $descriptions[$i],
        $fileName,
        $rate,
        'Available'
    ]);
    
    echo "Inserted profile: {$names[$i]}\n";
}

echo "Seeding completed successfully!\n";
