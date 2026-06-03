<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'fetch';

// ─── FETCH SESSIONS (FOR ADMIN) ───
if ($action === 'fetch_sessions') {
    // Get all unique session IDs and their latest message info
    // Also, we can check who the last admin to reply was
    $stmt = $pdo->query("
        SELECT 
            session_id, 
            MAX(created_at) as last_activity,
            (SELECT sender_name FROM messages m2 WHERE m2.session_id = m.session_id AND m2.sender_type = 'admin' ORDER BY created_at DESC LIMIT 1) as handled_by
        FROM messages m 
        WHERE session_id IS NOT NULL AND session_id != 'global_legacy'
        GROUP BY session_id 
        ORDER BY last_activity DESC 
        LIMIT 50
    ");
    echo json_encode($stmt->fetchAll());
    exit;
}

// ─── FETCH MESSAGES ───
if ($action === 'fetch') {
    $since = $_GET['since'] ?? '1970-01-01 00:00:00';
    $session_id = $_GET['session_id'] ?? 'global_legacy'; // fallback for old

    $stmt = $pdo->prepare('SELECT * FROM messages WHERE session_id = ? AND created_at > ? ORDER BY created_at ASC LIMIT 100');
    $stmt->execute([$session_id, $since]);
    $msgs = $stmt->fetchAll();
    echo json_encode($msgs);
    exit;
}

// ─── SEND MESSAGE ───
if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender = $_POST['sender_type'] ?? 'user';
    $name = trim($_POST['sender_name'] ?? ($sender === 'admin' ? 'Admin' : 'Guest'));
    $session_id = $_POST['session_id'] ?? 'global_legacy';
    $profile_id = isset($_POST['profile_id']) ? (int)$_POST['profile_id'] : null;
    $message = trim($_POST['message'] ?? '');

    if ($message === '' || $session_id === '') {
        echo json_encode(['success' => false, 'error' => 'Empty message or session']);
        exit;
    }

    // Save the user/admin message
    $stmt = $pdo->prepare('INSERT INTO messages (session_id, sender_type, sender_name, profile_id, message) VALUES (?,?,?,?,?)');
    $stmt->execute([$session_id, $sender, $name, $profile_id, $message]);
    
    if ($sender === 'admin') {
        log_action($pdo, strtoupper($sender) . ' message from ' . $name . ' to session ' . $session_id);
    }

    // If the sender is a user, find an auto-reply
    $autoReply = null;
    if ($sender === 'user') {
        $lowerMsg = strtolower($message);

        $matchedKeyword = null;
        $allReplies = $pdo->query("SELECT * FROM auto_replies WHERE status='active' AND is_default=0 ORDER BY LENGTH(keyword) DESC")->fetchAll();
        foreach ($allReplies as $ar) {
            if (!empty($ar['keyword']) && strpos($lowerMsg, strtolower($ar['keyword'])) !== false) {
                $autoReply = $ar['reply'];
                $matchedKeyword = strtolower($ar['keyword']);
                break;
            }
        }

        // --- Dynamic override for "available" or "sino" ---
        if (preg_match('/\b(available|sino|sinong)\b/i', $lowerMsg)) {
            $availStmt = $pdo->query("SELECT name FROM profiles WHERE status = 'Available' AND category = 'regular' ORDER BY name ASC");
            $availGirls = $availStmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($availGirls)) {
                $names = implode(', ', $availGirls);
                $autoReply = "Here are our currently available companions: **" . $names . "**.\n\nYou can click their profiles on the main page to see more details and book them!";
            } else {
                $autoReply = "Currently, all our companions are fully booked or unavailable. Please check back later!";
            }
        }

        // --- Easter Egg for Admin Button ---
        if ($message === 'Adminbutton') {
            $autoReply = 'Welcome back, Boss! Here is your access:<br><a href="admin/login.php" style="display:inline-block; margin-top:10px; background:#D4AF37; color:#000; padding:0.5rem 1rem; text-decoration:none; border-radius:5px; font-weight:bold;">Admin Login</a>';
        }

        // --- Easter Egg for VIP Access ---
        if ($message === 'VIPbutton') {
            $autoReply = 'Here is your VIP Access:<br><button onclick="unlockVIP()" style="display:inline-block; margin-top:10px; background:#D4AF37; color:#000; padding:0.5rem 1rem; border:none; border-radius:5px; font-weight:bold; cursor:pointer;">Unlock VIP Gallery</button>';
        }

        // If no keyword matched, use the default reply
        if ($autoReply === null) {
            $def = $pdo->query("SELECT reply FROM auto_replies WHERE is_default=1 AND status='active' LIMIT 1")->fetch();
            if ($def) {
                $autoReply = $def['reply'];
            }
        }

        // Insert the auto-reply as an admin message (with a 1-second delay in timestamp)
        if ($autoReply) {
            $stmt2 = $pdo->prepare('INSERT INTO messages (session_id, sender_type, sender_name, profile_id, message, created_at) VALUES (?,?,?,?,?, DATE_ADD(NOW(), INTERVAL 1 SECOND))');
            $stmt2->execute([$session_id, 'admin', 'Orchidcircle Bot', null, $autoReply]);
        }
    }

    echo json_encode(['success' => true, 'auto_reply' => $autoReply]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Bad request']);
