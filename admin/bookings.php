<?php
require_once __DIR__ . '/../db.php';
require_admin();

if (isset($_GET['action']) && isset($_GET['id'])) {
    $a = $_GET['action']; $id = (int)$_GET['id'];
    if (in_array($a, ['Approved','Rejected','Completed'])) {
        $pdo->prepare('UPDATE bookings SET status=? WHERE id=?')->execute([$a,$id]);
        log_action($pdo, "Booking $a: $id");
    }
    header('Location: bookings.php'); exit;
}

$rows = $pdo->query('SELECT b.*, p.name as profile_name FROM bookings b LEFT JOIN profiles p ON p.id=b.profile_id ORDER BY b.created_at DESC')->fetchAll();

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage Bookings</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body style="background: var(--bg-color); margin: 0; padding: 0;">
  <div class="admin-layout">
    <aside class="sidebar">
      <h2 style="display:flex; align-items:center; gap:8px; text-transform:none; letter-spacing:0.5px; font-size:1.1rem;"><img src="../uploads/logo-removebg-preview.png" style="height:28px; object-fit:contain;"> The Orchid Circle</h2>
      <nav style="margin-top: 1rem;">
        <a href="dashboard.php">Dashboard</a>
        <a href="profiles.php">Profiles</a>
        <a href="bookings.php" style="border-left: 4px solid var(--accent-gold); background: rgba(255,255,255,0.05); color: var(--accent-gold);">Bookings</a>
        <a href="messages.php">Messages</a>
        <a href="users.php">Admin Users</a>
        <a href="logout.php" style="margin-top: 2rem; color: #ef4444;">Logout</a>
      </nav>
    </aside>
    
    <main class="admin-content">
      <header style="margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--gold);">Manage Bookings</h1>
      </header>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Profile</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Hours</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td>#<?php echo $r['id']; ?></td>
                <td style="font-weight: 500; color: var(--gold);"><?php echo htmlspecialchars($r['profile_name'] ?? 'Deleted Profile'); ?></td>
                <td><?php echo htmlspecialchars($r['customer_name']); ?></td>
                <td><?php echo $r['booking_date']; ?></td>
                <td><?php echo $r['hours']; ?></td>
                <td><span class="badge <?php echo $r['status']; ?>"><?php echo $r['status']; ?></span></td>
                <td>
                  <?php if ($r['status'] === 'Pending'): ?>
                    <a href="bookings.php?action=Approved&id=<?php echo $r['id']; ?>" style="color: #166534; margin-right: 8px; text-decoration: none; font-weight: 500;">Approve</a>
                    <a href="bookings.php?action=Rejected&id=<?php echo $r['id']; ?>" style="color: #991b1b; text-decoration: none; font-weight: 500;">Reject</a>
                  <?php elseif ($r['status'] === 'Approved'): ?>
                    <a href="bookings.php?action=Completed&id=<?php echo $r['id']; ?>" style="color: #1e40af; text-decoration: none; font-weight: 500;">Mark Completed</a>
                  <?php else: ?>
                    <span style="color: var(--text-light);">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?>
              <tr><td colspan="7" style="text-align: center;">No bookings found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>

