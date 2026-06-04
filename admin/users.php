<?php
require_once __DIR__ . '/../db.php';
require_admin();

$success = '';
$error   = '';

// ADD ADMIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (!$username || !$password) {
        $error = 'Username and password are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $check = $pdo->prepare('SELECT id FROM admins WHERE username = ?');
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = 'Username already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $pdo->prepare('INSERT INTO admins (username, password) VALUES (?,?)')->execute([$username, $hash]);
            log_action($pdo, 'Added admin user: ' . $username);
            $success = "Admin \"$username\" added successfully!";
        }
    }
}

// DELETE ADMIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $del_id = (int)($_POST['del_id'] ?? 0);
    // Prevent deleting yourself
    if ($del_id === (int)$_SESSION['admin_id']) {
        $error = 'You cannot delete your own account.';
    } else {
        $pdo->prepare('DELETE FROM admins WHERE id = ?')->execute([$del_id]);
        log_action($pdo, 'Deleted admin user ID: ' . $del_id);
        $success = 'Admin account deleted.';
    }
}

$admins = $pdo->query('SELECT id, username FROM admins ORDER BY id ASC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage Admins &mdash; Orchidcircle</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .users-table { width:100%; border-collapse:collapse; margin-top:1.5rem; }
    .users-table th, .users-table td { padding:0.85rem 1rem; text-align:left; border-bottom:1px solid #e2e8f0; font-size:0.9rem; }
    .users-table th { background:#f8fafc; font-weight:600; color:#475569; text-transform:uppercase; font-size:0.78rem; letter-spacing:0.5px; }
    .users-table td .you-badge { background:#dcfce7; color:#166534; font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:999px; margin-left:8px; }
    .del-btn { background:#fee2e2; color:#991b1b; border:none; padding:0.4rem 0.9rem; border-radius:6px; font-size:0.82rem; font-weight:600; cursor:pointer; transition:background 0.2s; }
    .del-btn:hover { background:#fca5a5; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-weight:500; margin-bottom:0.4rem; font-size:0.9rem; }
    .form-group input { width:100%; padding:0.75rem; border:1px solid #e2e8f0; border-radius:8px; font-family:var(--font-family); font-size:0.95rem; }
    .form-group input:focus { outline:none; border-color:var(--accent-gold); box-shadow:0 0 0 3px rgba(212,175,55,0.15); }
    .alert-success { background:#dcfce7; color:#166534; padding:0.85rem 1.2rem; border-radius:8px; border-left:4px solid #22c55e; margin-bottom:1.5rem; font-weight:500; }
    .alert-error   { background:#fee2e2; color:#991b1b;  padding:0.85rem 1.2rem; border-radius:8px; border-left:4px solid #ef4444; margin-bottom:1.5rem; font-weight:500; }
    .users-admin-page { max-width: 960px; width: 100%; box-sizing: border-box; }
    .users-card { max-width: 720px; }
    .add-admin-card { max-width: 500px; }
    @media (max-width: 760px) {
      .admin-layout { flex-direction: column; }
      .sidebar { position: relative; width: 100%; height: auto; }
      .users-admin-page { padding: 1.25rem; }
      .users-card, .add-admin-card { max-width: none; }
    }
  </style>
</head>
<body style="background:var(--bg-color); margin:0; padding:0;">
  <div class="admin-layout">
    <aside class="sidebar">
      <h2 style="display:flex; align-items:center; gap:8px; text-transform:none; letter-spacing:0.5px; font-size:1.1rem;"><img src="../uploads/logo-removebg-preview.png" style="height:28px; object-fit:contain;"> The Orchid Circle</h2>
      <nav style="margin-top:1rem;">
        <a href="dashboard.php">Dashboard</a>
        <a href="profiles.php">Profiles</a>
        <a href="bookings.php">Bookings</a>
        <a href="messages.php">Messages</a>
        <a href="users.php" style="border-left:4px solid var(--accent-gold); background:rgba(255,255,255,0.05); color:var(--accent-gold);">Admin Users</a>
        <a href="logout.php" style="margin-top:auto; color:#ef4444;">Logout</a>
      </nav>
    </aside>

    <main class="admin-content users-admin-page">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem;">
        <div>
          <h1 style="margin:0; font-size:1.8rem;">Admin Users</h1>
          <p style="margin:0.3rem 0 0; color:#64748b; font-size:0.9rem;">Manage who has access to this admin panel.</p>
        </div>
      </div>

      <?php if ($success): ?>
        <div class="alert-success">Success: <?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert-error">Error: <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- CURRENT ADMINS LIST -->
      <div class="card users-card" style="padding:2rem; margin-bottom:2rem;">
        <h3 style="margin-top:0; color:var(--gold);">Current Admin Accounts</h3>
        <table class="users-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($admins as $i => $a): ?>
            <tr>
              <td style="color:#94a3b8;"><?= $i + 1 ?></td>
              <td>
                <?= htmlspecialchars($a['username']) ?>
                <?php if ($a['id'] == $_SESSION['admin_id']): ?>
                  <span class="you-badge">YOU</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($a['id'] != $_SESSION['admin_id']): ?>
                <form method="post" onsubmit="return confirm('Delete admin ' + JSON.stringify(<?= json_encode($a['username']) ?>) + '? This cannot be undone.');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="del_id" value="<?= $a['id'] ?>">
                  <button type="submit" class="del-btn">Delete</button>
                </form>
                <?php else: ?>
                  <span style="color:#94a3b8; font-size:0.8rem;">&mdash;</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- ADD NEW ADMIN FORM -->
      <div class="card add-admin-card" style="padding:2rem;">
        <h3 style="margin-top:0; color:var(--gold);">Add New Admin</h3>
        <form method="post">
          <input type="hidden" name="action" value="add">
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="e.g. orchidadmin2" required autocomplete="off">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Min. 6 characters" required autocomplete="new-password">
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm" placeholder="Repeat password" required autocomplete="new-password">
          </div>
          <button type="submit" class="btn-gold" style="width:100%; padding:1rem; font-size:1rem; margin-top:0.5rem;">
            Add Admin Account
          </button>
        </form>
      </div>

    </main>
  </div>
</body>
</html>
