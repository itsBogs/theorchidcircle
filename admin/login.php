<?php
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$u]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($p, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_user'] = $admin['username'];
        log_action($pdo, 'Admin login: ' . $u);
        header('Location: dashboard.php'); exit;
    } else {
        $err = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login — The Orchid Circle</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cormorant+Garamond:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', system-ui, sans-serif;
      background: #0a0a0a;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      background: #141414;
      border: 1px solid rgba(212,175,55,0.2);
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 20px 60px rgba(0,0,0,0.6);
    }
    .login-logo {
      text-align: center;
      margin-bottom: 2rem;
    }
    .login-logo img {
      height: 54px;
      object-fit: contain;
      margin-bottom: 0.75rem;
    }
    .login-logo h1 {
      font-family: 'Cormorant Garamond', serif;
      color: #D4AF37;
      font-size: 1.5rem;
      font-weight: 500;
      letter-spacing: 0.1em;
    }
    .login-logo p {
      color: rgba(255,255,255,0.35);
      font-size: 0.75rem;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      margin-top: 0.25rem;
    }
    .divider {
      border: none;
      border-top: 1px solid rgba(212,175,55,0.15);
      margin-bottom: 2rem;
    }
    .error-msg {
      background: rgba(220,38,38,0.12);
      border: 1px solid rgba(220,38,38,0.3);
      color: #fca5a5;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      font-size: 0.85rem;
      margin-bottom: 1.5rem;
      text-align: center;
    }
    label {
      display: block;
      color: rgba(255,255,255,0.5);
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      margin-bottom: 0.4rem;
    }
    input {
      width: 100%;
      padding: 0.8rem 1rem;
      background: #0d0d0d;
      border: 1.5px solid rgba(212,175,55,0.2);
      border-radius: 8px;
      color: #e5e5e5;
      font-family: 'Inter', sans-serif;
      font-size: 0.95rem;
      outline: none;
      transition: border 0.2s;
      margin-bottom: 1.2rem;
    }
    input:focus {
      border-color: #D4AF37;
      box-shadow: 0 0 0 3px rgba(212,175,55,0.1);
    }
    input::placeholder { color: rgba(255,255,255,0.2); }
    button[type="submit"] {
      width: 100%;
      padding: 0.9rem;
      background: #D4AF37;
      color: #0a0a0a;
      font-family: 'Inter', sans-serif;
      font-weight: 700;
      font-size: 0.95rem;
      letter-spacing: 0.5px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.2s, box-shadow 0.2s;
      margin-top: 0.4rem;
      text-transform: uppercase;
    }
    button[type="submit"]:hover {
      background: #e8c94a;
      box-shadow: 0 4px 20px rgba(212,175,55,0.3);
    }
    .back-link {
      text-align: center;
      margin-top: 1.75rem;
    }
    .back-link a {
      color: rgba(255,255,255,0.3);
      text-decoration: none;
      font-size: 0.82rem;
      transition: color 0.2s;
    }
    .back-link a:hover { color: #D4AF37; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-logo">
      <img src="../uploads/logo-removebg-preview.png" alt="Logo">
      <h1>The Orchid Circle</h1>
      <p>Admin Portal</p>
    </div>
    <hr class="divider">
    <?php if (!empty($err)) echo '<div class="error-msg">⚠ ' . htmlspecialchars($err) . '</div>'; ?>
    <form method="post">
      <label for="username">Username</label>
      <input id="username" name="username" required autocomplete="username" placeholder="Enter username">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••">
      <button type="submit">Secure Login</button>
    </form>
    <div class="back-link">
      <a href="../index.php">← Back to public site</a>
    </div>
  </div>
</body>
</html>
