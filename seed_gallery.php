<?php
require_once __DIR__ . '/db.php';

$stats = [
    ['age' => 22, 'height' => "5'5\"", 'waist' => '24"', 'cup_size' => '34C'],
    ['age' => 24, 'height' => "5'7\"", 'waist' => '25"', 'cup_size' => '34B'],
    ['age' => 21, 'height' => "5'4\"", 'waist' => '23"', 'cup_size' => '36D'],
    ['age' => 25, 'height' => "5'6\"", 'waist' => '24"', 'cup_size' => '34C'],
    ['age' => 23, 'height' => "5'8\"", 'waist' => '26"', 'cup_size' => '36C'],
    ['age' => 22, 'height' => "5'5\"", 'waist' => '24"', 'cup_size' => '34D'],
    ['age' => 26, 'height' => "5'6\"", 'waist' => '25"', 'cup_size' => '34B'],
    ['age' => 23, 'height' => "5'7\"", 'waist' => '24"', 'cup_size' => '36B'],
];

// Update stats on profiles
$profiles = $pdo->query('SELECT id FROM profiles ORDER BY id ASC LIMIT 8')->fetchAll();
foreach ($profiles as $i => $p) {
    $s = $stats[$i] ?? $stats[0];
    $pdo->prepare('UPDATE profiles SET age=?, height=?, waist=?, cup_size=? WHERE id=?')
        ->execute([$s['age'], $s['height'], $s['waist'], $s['cup_size'], $p['id']]);
    echo "Updated stats for profile ID {$p['id']}\n";
}

// For each profile, seed 7 extra gallery images from randomuser.me using different IDs
$profiles = $pdo->query('SELECT id FROM profiles ORDER BY id ASC LIMIT 8')->fetchAll();
$baseImg = 10; // starting portrait index (same as seed.php)

foreach ($profiles as $pIdx => $p) {
    $pid = $p['id'];
    
    // Check if images already exist for this profile
    $existing = $pdo->prepare('SELECT COUNT(*) FROM profile_images WHERE profile_id=?');
    $existing->execute([$pid]);
    if ($existing->fetchColumn() > 0) {
        echo "Profile ID $pid already has gallery images. Skipping.\n";
        continue;
    }
    
    // Download 7 extra female portraits from a different range for variety
    $startIdx = 20 + ($pIdx * 7);
    for ($j = 0; $j < 7; $j++) {
        $imgId = $startIdx + $j;
        $imageUrl = "https://randomuser.me/api/portraits/women/{$imgId}.jpg";
        $fileName = "gallery_w_{$imgId}.jpg";
        $localPath = __DIR__ . "/uploads/" . $fileName;

        if (!file_exists($localPath)) {
            $imgData = @file_get_contents($imageUrl);
            if ($imgData !== false) {
                file_put_contents($localPath, $imgData);
                echo "  Downloaded gallery image: $fileName\n";
            } else {
                // Fallback: reuse the profile cover image
                $fileName = "profile_w_" . ($baseImg + $pIdx) . ".jpg";
                echo "  Failed download, falling back to cover image.\n";
            }
        }

        $pdo->prepare('INSERT INTO profile_images (profile_id, image, sort_order) VALUES (?,?,?)')
            ->execute([$pid, $fileName, $j]);
    }
    echo "Seeded gallery for profile ID $pid\n";
}

echo "\nGallery seeding complete!";
