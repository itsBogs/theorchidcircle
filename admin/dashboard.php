<?php
require_once __DIR__ . '/../db.php';
require_admin(); // Assuming this is defined in db.php or somewhere included

$today = $pdo->query("SELECT COUNT(*) as c FROM bookings WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$month = $pdo->query("SELECT COUNT(*) as c FROM bookings WHERE MONTH(created_at)=MONTH(CURDATE())")->fetchColumn();
$active_chats = $pdo->query("SELECT COUNT(*) FROM messages WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 DAY)")->fetchColumn();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body style="background: var(--bg-color); margin: 0; padding: 0;">
  <div class="admin-layout">
    <aside class="sidebar">
      <h2 style="display:flex; align-items:center; gap:8px; text-transform:none; letter-spacing:0.5px; font-size:1.1rem;"><img src="../uploads/logo-removebg-preview.png" style="height:28px; object-fit:contain;"> The Orchid Circle</h2>
      <nav style="margin-top: 1rem;">
        <a href="dashboard.php" style="border-left: 4px solid var(--accent-gold); background: rgba(255,255,255,0.05); color: var(--accent-gold);">Dashboard</a>
        <a href="profiles.php">Profiles</a>
        <a href="bookings.php">Bookings</a>
        <a href="messages.php">Messages</a>
        <a href="users.php">Admin Users</a>
        <a href="logout.php" style="margin-top: 2rem; color: #ef4444;">Logout</a>
      </nav>
    </aside>
    
    <main class="admin-content">
      <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--gold);">Dashboard Overview</h1>
        <div style="font-weight: 500; color: var(--text-light);"><?php echo date('F j, Y'); ?></div>
      </header>

      <div class="stats-grid">
        <div class="stat-card">
          <h4>Today's Bookings</h4>
          <div class="value"><?php echo $today; ?></div>
        </div>
        <div class="stat-card">
          <h4>Bookings This Month</h4>
          <div class="value"><?php echo $month; ?></div>
        </div>
        <div class="stat-card">
          <h4>Active Chats (24h)</h4>
          <div class="value"><?php echo $active_chats; ?></div>
        </div>
      </div>
      
      <div class="table-wrapper" style="margin-top: 2rem;">
        <h3 style="margin-top: 0; color: var(--gold);">Recent Activity Log</h3>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Action</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $logs = $pdo->query("SELECT * FROM logs ORDER BY created_at DESC LIMIT 5")->fetchAll();
            if (empty($logs)) {
              echo "<tr><td colspan='3' style='text-align:center;'>No recent logs found.</td></tr>";
            }
            foreach($logs as $log): ?>
            <tr>
              <td>#<?php echo $log['id']; ?></td>
              <td><?php echo htmlspecialchars($log['action']); ?></td>
              <td><?php echo date('M j, g:i A', strtotime($log['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>

